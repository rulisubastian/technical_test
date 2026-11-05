<?php

declare(strict_types=1);

namespace App\Services;

use App\Domain\Order\Money;
use App\Domain\Order\OrderStateMachine;
use App\Enums\OrderStatus;
use App\Enums\StockMovementType;
use App\Exceptions\InsufficientStock;
use App\Jobs\NotifyLowStock;
use App\Jobs\ProcessPaidOrder;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Support\Facades\DB;

final class OrderService
{
    public function __construct(
        private readonly StockService $stock,
        private readonly OrderNumberGenerator $numbers,
        private readonly OrderStateMachine $machine,
        private readonly DashboardService $dashboard,
        private readonly AuditLogger $audit,
    ) {}

    /**
     * Buat order multi-item dalam SATU transaksi:
     * kunci produk (urut id) -> validasi stok semua item -> snapshot harga -> hitung total di server -> kurangi stok + ledger.
     *
     * @param  array{customer_id: string, items: list<array{product_id: int, quantity: int}>}  $data
     */
    public function create(User $actor, array $data): Order
    {
        $order = DB::transaction(function () use ($actor, $data): Order {
            $items = $data['items'];
            $products = $this->stock->lockProducts(array_map(fn (array $i): int => (int) $i['product_id'], $items));

            $errors = [];
            foreach ($items as $index => $item) {
                $product = $products->get((int) $item['product_id']);
                if ($product === null) {
                    $errors["items.$index.product_id"] = ['Produk tidak ditemukan'];
                } elseif ($product->stock < (int) $item['quantity']) {
                    $errors["items.$index.quantity"] = ["Stok {$product->product_name} tersisa {$product->stock}, diminta {$item['quantity']}"];
                }
            }
            if ($errors !== []) {
                throw new InsufficientStock($errors);
            }

            $now = now();
            $order = Order::query()->create([
                'order_number' => $this->numbers->next($now),
                'customer_id' => $data['customer_id'],
                'created_by' => $actor->id,
                'status' => OrderStatus::Pending,
                'total_amount' => '0.00',
                'order_date' => $now,
            ]);

            $lines = [];
            foreach ($items as $item) {
                $product = $products->get((int) $item['product_id']);
                assert($product !== null);
                $qty = (int) $item['quantity'];
                $wasLow = $product->isLowStock();

                OrderItem::query()->create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $qty,
                    'unit_price' => $product->price, // snapshot harga
                    'subtotal' => Money::subtotal($product->price, $qty),
                ]);
                $lines[] = ['unit_price' => $product->price, 'quantity' => $qty];

                $this->stock->apply($product, -$qty, StockMovementType::OrderOut, 'order', $order->id, null, $actor->id);

                // Notifikasi hanya saat stok MELEWATI batas (bukan tiap order) untuk menghindari spam.
                if (! $wasLow && $product->isLowStock()) {
                    NotifyLowStock::dispatch($product->id)->afterCommit();
                }
            }

            $order->total_amount = Money::total($lines);
            $order->save();

            return $order;
        });

        $this->dashboard->flush();

        return $order->load(['customer', 'creator', 'items.product']);
    }

    public function pay(int $orderId, User $actor): Order
    {
        return $this->transition($orderId, OrderStatus::Paid, $actor);
    }

    public function ship(int $orderId, User $actor): Order
    {
        return $this->transition($orderId, OrderStatus::Shipped, $actor);
    }

    public function complete(int $orderId, User $actor): Order
    {
        return $this->transition($orderId, OrderStatus::Completed, $actor);
    }

    public function cancel(int $orderId, User $actor, string $reason): Order
    {
        return $this->transition($orderId, OrderStatus::Cancelled, $actor, $reason);
    }

    private function transition(int $orderId, OrderStatus $to, User $actor, ?string $reason = null): Order
    {
        $order = DB::transaction(function () use ($orderId, $to, $actor, $reason): Order {
            // Kunci baris order: request ganda (mis. cancel 2x) diserialisasi; yang kedua melihat status baru -> 409.
            $order = Order::query()->lockForUpdate()->findOrFail($orderId);
            $this->machine->assert($order->status, $to);

            $order->status = $to;
            $column = $to->timestampColumn();
            if ($column !== null) {
                $order->setAttribute($column, now());
            }

            if ($to === OrderStatus::Cancelled) {
                $order->cancel_reason = $reason;
                $this->restoreStock($order, $actor);
            }
            $order->save();

            if ($to === OrderStatus::Paid) {
                ProcessPaidOrder::dispatch($order->id)->afterCommit();
            }
            if ($to === OrderStatus::Cancelled) {
                $this->audit->log($actor, 'order.cancelled', 'order', $order->id, ['reason' => $reason]);
            }

            return $order;
        });

        $this->dashboard->flush();

        return $order->load(['customer', 'creator', 'items.product']);
    }

    private function restoreStock(Order $order, User $actor): void
    {
        $items = $order->items()->orderBy('product_id')->get();
        $products = $this->stock->lockProducts($items->pluck('product_id')->map(fn ($id): int => (int) $id)->all(), withTrashed: true);

        foreach ($items as $item) {
            $product = $products->get($item->product_id);
            assert($product !== null);
            $this->stock->apply($product, $item->quantity, StockMovementType::CancelRestore, 'order', $order->id, 'Pembatalan order', $actor->id);
        }
    }
}

<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\StockMovementType;
use App\Exceptions\InsufficientStock;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Support\Collection;

/**
 * Satu-satunya pintu perubahan stok. WAJIB dipanggil di dalam DB transaction
 * dan setelah baris produk dikunci lewat lockProducts().
 */
final class StockService
{
    /**
     * Kunci baris produk (SELECT ... FOR UPDATE) SELALU urut id ascending.
     * Urutan konsisten antar-transaksi = tidak ada siklus tunggu = bebas deadlock pada order multi-item.
     *
     * @param  list<int>  $ids
     * @return Collection<int, Product>
     */
    public function lockProducts(array $ids, bool $withTrashed = false): Collection
    {
        $query = Product::query()->whereIn('id', array_values(array_unique($ids)))->orderBy('id')->lockForUpdate();
        if ($withTrashed) {
            $query->withTrashed();
        }

        return $query->get()->keyBy('id');
    }

    public function apply(
        Product $product,
        int $change,
        StockMovementType $type,
        ?string $referenceType = null,
        string|int|null $referenceId = null,
        ?string $note = null,
        ?int $userId = null,
    ): StockMovement {
        $after = $product->stock + $change;
        if ($after < 0) {
            throw new InsufficientStock(
                ['quantity_change' => ["Stok {$product->product_name} tersisa {$product->stock}, perubahan {$change} membuat stok negatif"]],
            );
        }

        $product->stock = $after;
        $product->save();

        return StockMovement::query()->create([
            'product_id' => $product->id,
            'type' => $type,
            'quantity_change' => $change,
            'stock_after' => $after,
            'reference_type' => $referenceType,
            'reference_id' => $referenceId === null ? null : (string) $referenceId,
            'note' => $note,
            'created_by' => $userId,
        ]);
    }
}

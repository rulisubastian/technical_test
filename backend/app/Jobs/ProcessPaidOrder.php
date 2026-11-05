<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Enums\OrderStatus;
use App\Mail\InvoiceMail;
use App\Models\Order;
use App\Services\InvoiceService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

/**
 * Dijalankan saat order menjadi PAID: buat invoice + kirim email ke customer.
 * - Retry dengan backoff eksponensial; kegagalan permanen -> failed() + tabel failed_jobs.
 * - Aman dijalankan ulang: invoice_sent_at menjadi penanda "sudah terkirim"; WithoutOverlapping mencegah dua worker
 *   memproses order yang sama bersamaan.
 */
class ProcessPaidOrder implements ShouldQueue
{
    use Queueable;

    public int $tries = 5;

    public function __construct(public readonly int $orderId) {}

    /** @return list<int> */
    public function backoff(): array
    {
        return [10, 30, 60, 120];
    }

    /** @return list<object> */
    public function middleware(): array
    {
        return [(new WithoutOverlapping((string) $this->orderId))->releaseAfter(15)->expireAfter(300)];
    }

    public function handle(InvoiceService $invoices): void
    {
        $order = Order::query()->with(['customer', 'items.product'])->find($this->orderId);

        if ($order === null || $order->invoice_sent_at !== null || $order->status === OrderStatus::Cancelled) {
            return; // idempotent: sudah diproses / tidak relevan lagi
        }

        $invoices->ensureGenerated($order);

        $email = $order->customer->email;
        if ($email !== null && $email !== '') {
            Mail::to($email)->send(new InvoiceMail($order));
        }

        $order->forceFill(['invoice_sent_at' => now()])->save();
    }

    public function failed(Throwable $e): void
    {
        Log::error('Job invoice gagal permanen', ['order_id' => $this->orderId, 'error' => $e->getMessage()]);
    }
}

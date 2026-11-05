<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Enums\UserRole;
use App\Models\Product;
use App\Models\User;
use App\Notifications\LowStockNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Throwable;

class NotifyLowStock implements ShouldQueue
{
    use Queueable;

    public int $tries = 5;

    public function __construct(public readonly int $productId) {}

    /** @return list<int> */
    public function backoff(): array
    {
        return [10, 30, 60, 120];
    }

    public function handle(): void
    {
        $product = Product::query()->find($this->productId);
        if ($product === null || ! $product->isLowStock()) {
            return; // stok sudah dipulihkan / produk dihapus
        }

        // Dedup: satu notifikasi per produk per jam walau job dijalankan ulang.
        $key = "low-stock-alert:{$product->id}";
        if (! Cache::add($key, 1, now()->addHour())) {
            return;
        }

        try {
            $admins = User::query()->where('role', UserRole::Admin->value)->get();
            Notification::send($admins, new LowStockNotification($product));
        } catch (Throwable $e) {
            Cache::forget($key); // izinkan retry mengirim ulang
            throw $e;
        }
    }

    public function failed(Throwable $e): void
    {
        Log::error('Job notifikasi low stock gagal permanen', ['product_id' => $this->productId, 'error' => $e->getMessage()]);
    }
}

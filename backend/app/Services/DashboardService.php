<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\Cache;

final class DashboardService
{
    private const TAG = 'dashboard';

    /**
     * Cache-aside di Redis dengan TTL pendek (jaring pengaman) + invalidasi eksplisit via tag
     * setiap kali order/produk/stok berubah (lihat flush()).
     *
     * @return array<string, mixed>
     */
    public function summary(): array
    {
        return Cache::tags([self::TAG])->remember('summary', (int) config('shop.dashboard_cache_ttl'), fn (): array => $this->compute());
    }

    public function flush(): void
    {
        Cache::tags([self::TAG])->flush();
    }

    /** @return array<string, mixed> */
    private function compute(): array
    {
        $today = now()->startOfDay();
        $revenueStatuses = OrderStatus::revenueValues();

        $today_ = Order::query()
            ->whereIn('status', $revenueStatuses)
            ->where('order_date', '>=', $today)
            ->selectRaw('COUNT(*) as orders, COALESCE(SUM(total_amount), 0) as revenue')
            ->first();

        $last30 = Order::query()
            ->whereIn('status', $revenueStatuses)
            ->where('order_date', '>=', now()->subDays(30)->startOfDay())
            ->selectRaw('COUNT(*) as orders, COALESCE(SUM(total_amount), 0) as revenue')
            ->first();

        /** @var array<string, int> $byStatus */
        $byStatus = Order::query()->selectRaw('status, COUNT(*) as total')->groupBy('status')->pluck('total', 'status')->map(fn ($v): int => (int) $v)->all();

        return [
            'today' => ['orders' => (int) $today_?->orders, 'revenue' => number_format((float) $today_?->revenue, 2, '.', '')],
            'last_30_days' => ['orders' => (int) $last30?->orders, 'revenue' => number_format((float) $last30?->revenue, 2, '.', '')],
            'orders_by_status' => array_merge(
                array_fill_keys(array_map(fn (OrderStatus $s): string => $s->value, OrderStatus::cases()), 0),
                $byStatus,
            ),
            'low_stock_products' => Product::query()->whereColumn('stock', '<', 'low_stock_threshold')->count(),
            'generated_at' => now()->toIso8601String(),
        ];
    }
}

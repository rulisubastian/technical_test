<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\OrderStatus;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;

/** Seluruh agregasi dilakukan di database (GROUP BY), satu query per laporan => tanpa N+1. */
final class ReportService
{
    /** @return list<array{period: string, total_orders: int, revenue: string}> */
    public function sales(CarbonInterface $from, CarbonInterface $to, string $groupBy): array
    {
        $driver = DB::connection()->getDriverName();
        $pg = $driver === 'pgsql';
        $expr = $groupBy === 'month'
            ? ($pg ? "to_char(order_date, 'YYYY-MM')" : "DATE_FORMAT(order_date, '%Y-%m')")
            : ($pg ? "to_char(order_date, 'YYYY-MM-DD')" : "DATE_FORMAT(order_date, '%Y-%m-%d')");

        $rows = DB::table('orders')
            ->whereIn('status', OrderStatus::revenueValues())
            ->whereBetween('order_date', [$from->copy()->startOfDay(), $to->copy()->endOfDay()])
            ->selectRaw("{$expr} as period, COUNT(*) as total_orders, SUM(total_amount) as revenue")
            ->groupByRaw($expr)
            ->orderBy('period')
            ->get();

        return $rows->map(fn ($r): array => [
            'period' => (string) $r->period,
            'total_orders' => (int) $r->total_orders,
            'revenue' => number_format((float) $r->revenue, 2, '.', ''),
        ])->all();
    }

    /** @return list<array{product_id: int, product_code: string, product_name: string, total_quantity: int, revenue: string}> */
    public function topProducts(CarbonInterface $from, CarbonInterface $to, int $limit): array
    {
        $rows = DB::table('order_items')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->join('products', 'products.id', '=', 'order_items.product_id')
            ->whereIn('orders.status', OrderStatus::revenueValues())
            ->whereBetween('orders.order_date', [$from->copy()->startOfDay(), $to->copy()->endOfDay()])
            ->groupBy('products.id', 'products.product_code', 'products.product_name')
            ->selectRaw('products.id as product_id, products.product_code, products.product_name, SUM(order_items.quantity) as total_quantity, SUM(order_items.subtotal) as revenue')
            ->orderByDesc('total_quantity')
            ->orderByDesc('revenue')
            ->limit($limit)
            ->get();

        return $rows->map(fn ($r): array => [
            'product_id' => (int) $r->product_id,
            'product_code' => (string) $r->product_code,
            'product_name' => (string) $r->product_name,
            'total_quantity' => (int) $r->total_quantity,
            'revenue' => number_format((float) $r->revenue, 2, '.', ''),
        ])->all();
    }
}

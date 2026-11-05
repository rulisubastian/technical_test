<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Exceptions\InsufficientStock;
use App\Models\User;
use App\Services\OrderService;
use Illuminate\Console\Command;

/** Hanya untuk test concurrency (proses terpisah = koneksi DB terpisah). Tidak aktif di luar env testing. */
class PlaceTestOrder extends Command
{
    protected $signature = 'orders:place-test {user} {customer} {items : format "productId:qty,productId:qty"}';

    protected $description = 'Probe concurrency: membuat order lewat OrderService (hanya env testing)';

    protected $hidden = true;

    public function handle(OrderService $orders): int
    {
        abort_unless(app()->environment('testing'), 403);

        $items = array_map(function (string $pair): array {
            [$product, $qty] = explode(':', $pair);

            return ['product_id' => (int) $product, 'quantity' => (int) $qty];
        }, explode(',', (string) $this->argument('items')));

        try {
            $orders->create(User::query()->findOrFail((int) $this->argument('user')), [
                'customer_id' => (string) $this->argument('customer'),
                'items' => $items,
            ]);
            $this->line('OK');

            return self::SUCCESS;
        } catch (InsufficientStock) {
            $this->line('INSUFFICIENT');

            return self::FAILURE;
        }
    }
}

<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Exceptions\InvalidOrderTransition;
use App\Models\User;
use App\Services\OrderService;
use Illuminate\Console\Command;

/** Hanya untuk test concurrency pembatalan ganda. Tidak aktif di luar env testing. */
class CancelTestOrder extends Command
{
    protected $signature = 'orders:cancel-test {user} {order}';

    protected $description = 'Probe concurrency: membatalkan order (hanya env testing)';

    protected $hidden = true;

    public function handle(OrderService $orders): int
    {
        abort_unless(app()->environment('testing'), 403);

        try {
            $orders->cancel((int) $this->argument('order'), User::query()->findOrFail((int) $this->argument('user')), 'Tes concurrency');
            $this->line('OK');

            return self::SUCCESS;
        } catch (InvalidOrderTransition) {
            $this->line('CONFLICT');

            return self::FAILURE;
        }
    }
}

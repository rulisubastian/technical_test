<?php

declare(strict_types=1);

namespace App\Domain\Order;

use App\Enums\OrderStatus;
use App\Exceptions\InvalidOrderTransition;

/**
 * State machine murni (tanpa I/O) sehingga mudah di-unit-test.
 * Otorisasi role ada di OrderPolicy; kelayakan transisi hanya di sini.
 */
final class OrderStateMachine
{
    /** @return array<string, list<OrderStatus>> */
    private function map(): array
    {
        return [
            OrderStatus::Pending->value => [OrderStatus::Paid, OrderStatus::Cancelled],
            OrderStatus::Paid->value => [OrderStatus::Shipped, OrderStatus::Cancelled],
            OrderStatus::Shipped->value => [OrderStatus::Completed],
            OrderStatus::Completed->value => [],
            OrderStatus::Cancelled->value => [],
        ];
    }

    /** @return list<OrderStatus> */
    public function next(OrderStatus $from): array
    {
        return $this->map()[$from->value];
    }

    public function can(OrderStatus $from, OrderStatus $to): bool
    {
        return in_array($to, $this->next($from), true);
    }

    public function assert(OrderStatus $from, OrderStatus $to): void
    {
        if (! $this->can($from, $to)) {
            throw new InvalidOrderTransition($from, $to);
        }
    }
}

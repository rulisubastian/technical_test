<?php

declare(strict_types=1);

namespace App\Enums;

enum OrderStatus: string
{
    case Pending = 'PENDING';
    case Paid = 'PAID';
    case Shipped = 'SHIPPED';
    case Completed = 'COMPLETED';
    case Cancelled = 'CANCELLED';

    /** Kolom timestamp yang diisi saat order masuk ke status ini. */
    public function timestampColumn(): ?string
    {
        return match ($this) {
            self::Pending => null,
            self::Paid => 'paid_at',
            self::Shipped => 'shipped_at',
            self::Completed => 'completed_at',
            self::Cancelled => 'cancelled_at',
        };
    }

    /** @return list<string> status yang dihitung sebagai omzet. */
    public static function revenueValues(): array
    {
        return [self::Paid->value, self::Shipped->value, self::Completed->value];
    }
}

<?php

declare(strict_types=1);

namespace App\Domain\Order;

/** Aritmetika uang dengan integer sen agar bebas error floating point. */
final class Money
{
    public static function toCents(string|int|float $amount): int
    {
        return (int) round(((float) $amount) * 100);
    }

    public static function fromCents(int $cents): string
    {
        return number_format($cents / 100, 2, '.', '');
    }

    public static function subtotal(string|int|float $unitPrice, int $quantity): string
    {
        return self::fromCents(self::toCents($unitPrice) * $quantity);
    }

    /** @param iterable<array{unit_price: string|int|float, quantity: int}> $lines */
    public static function total(iterable $lines): string
    {
        $cents = 0;
        foreach ($lines as $line) {
            $cents += self::toCents($line['unit_price']) * $line['quantity'];
        }

        return self::fromCents($cents);
    }
}

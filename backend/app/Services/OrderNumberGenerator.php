<?php

declare(strict_types=1);

namespace App\Services;

use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;

/** Format INV-YYYYMMDD-NNNN, urut per hari. Harus dipanggil di dalam transaksi. */
final class OrderNumberGenerator
{
    public function next(CarbonInterface $date): string
    {
        $day = $date->toDateString();

        DB::table('order_sequences')->insertOrIgnore(['seq_date' => $day, 'last_number' => 0]);

        // Baris sequence dikunci sampai commit -> nomor unik & berurutan tanpa celah.
        $row = DB::table('order_sequences')->where('seq_date', $day)->lockForUpdate()->first();
        $number = ((int) $row->last_number) + 1;
        DB::table('order_sequences')->where('seq_date', $day)->update(['last_number' => $number]);

        return sprintf('INV-%s-%04d', $date->format('Ymd'), $number);
    }
}

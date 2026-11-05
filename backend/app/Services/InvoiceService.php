<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Storage;

final class InvoiceService
{
    /** Buat invoice HTML bila belum ada (aman dipanggil berulang). Mengembalikan path relatif di disk. */
    public function ensureGenerated(Order $order): string
    {
        $path = "invoices/{$order->order_number}.html";
        $disk = Storage::disk('local');

        if (! $disk->exists($path)) {
            $order->loadMissing(['customer', 'items.product']);
            $disk->put($path, view('invoices.html', ['order' => $order])->render());
        }

        if ($order->invoice_path !== $path) {
            $order->forceFill(['invoice_path' => $path])->save();
        }

        return $path;
    }
}

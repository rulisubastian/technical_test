<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\Order;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class InvoiceMail extends Mailable
{
    public function __construct(public readonly Order $order) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: "Invoice {$this->order->order_number}");
    }

    public function content(): Content
    {
        return new Content(view: 'mail.invoice');
    }

    /** @return list<Attachment> */
    public function attachments(): array
    {
        return $this->order->invoice_path === null
            ? []
            : [Attachment::fromStorageDisk('local', $this->order->invoice_path)->as("{$this->order->order_number}.html")->withMime('text/html')];
    }
}

<?php
// app/Mail/InvoiceGeneratedMail.php

namespace App\Mail;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InvoiceGeneratedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Invoice $invoice) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Rent Invoice ' . $this->invoice->invoice_number . ' — MakaziHub',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.invoice-generated',
        );
    }
}

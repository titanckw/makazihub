<?php
// app/Mail/OverdueReminderMail.php

namespace App\Mail;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OverdueReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Invoice $invoice, public int $daysOverdue) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Overdue Rent Notice — ' . $this->invoice->invoice_number . ' — MakaziHub',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.overdue-reminder',
        );
    }
}

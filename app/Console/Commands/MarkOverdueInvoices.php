<?php
// app/Console/Commands/MarkOverdueInvoices.php

namespace App\Console\Commands;

use App\Services\InvoiceService;
use Illuminate\Console\Command;

class MarkOverdueInvoices extends Command
{
    protected $signature   = 'invoices:mark-overdue';
    protected $description = 'Mark unpaid invoices past their due date as overdue (run daily)';

    public function handle(InvoiceService $service): void
    {
        $count = $service->markOverdueInvoices();
        $this->info("Marked {$count} invoice(s) as overdue.");
    }
}

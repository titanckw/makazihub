<?php
// app/Console/Commands/GenerateMonthlyInvoices.php

namespace App\Console\Commands;

use App\Services\InvoiceService;
use Illuminate\Console\Command;

class GenerateMonthlyInvoices extends Command
{
    protected $signature   = 'invoices:generate-monthly';
    protected $description = 'Auto-generate rent invoices for all active leases (run on 1st of each month)';

    public function handle(InvoiceService $service): void
    {
        $count = $service->generateMonthly();
        $this->info("Generated {$count} invoice(s) for " . now()->format('F Y') . '.');
    }
}

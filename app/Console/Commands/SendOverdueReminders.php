<?php
// app/Console/Commands/SendOverdueReminders.php

namespace App\Console\Commands;

use App\Models\Invoice;
use App\Services\NotificationService;
use Illuminate\Console\Command;

class SendOverdueReminders extends Command
{
    protected $signature   = 'makazihub:send-overdue-reminders {--days=1,7,14 : Comma-separated days overdue to send reminders}';
    protected $description = 'Send SMS and email reminders to tenants with overdue invoices';

    public function handle(NotificationService $notifications): int
    {
        $dayThresholds = array_map('intval', explode(',', $this->option('days')));

        $overdue = Invoice::with(['tenant.user', 'property', 'unit'])
            ->where('status', 'overdue')
            ->get();

        if ($overdue->isEmpty()) {
            $this->info('No overdue invoices found.');
            return self::SUCCESS;
        }

        $sent = 0;

        foreach ($overdue as $invoice) {
            $daysOverdue = (int) $invoice->due_date->diffInDays(now());

            // Only send on specific day thresholds (1, 7, 14 days)
            if (!in_array($daysOverdue, $dayThresholds)) {
                continue;
            }

            $this->line("  → Reminding {$invoice->tenant->user->name} ({$invoice->invoice_number}) — {$daysOverdue} day(s) overdue");

            $notifications->overdueReminder($invoice);
            $sent++;
        }

        $this->info("Overdue reminders sent: {$sent}");
        return self::SUCCESS;
    }
}

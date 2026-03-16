<?php
// app/Console/Commands/SendLeaseExpiryWarnings.php

namespace App\Console\Commands;

use App\Models\Lease;
use App\Services\SmsService;
use Illuminate\Console\Command;

class SendLeaseExpiryWarnings extends Command
{
    protected $signature   = 'makazihub:lease-expiry-warnings {--days=30,14,7 : Comma-separated days before expiry to warn}';
    protected $description = 'Send SMS warnings for leases expiring soon';

    public function handle(SmsService $sms): int
    {
        $dayThresholds = array_map('intval', explode(',', $this->option('days')));

        $expiringLeases = Lease::with(['tenant.user', 'unit'])
            ->where('status', 'active')
            ->whereNotNull('end_date')
            ->whereBetween('end_date', [now(), now()->addDays(max($dayThresholds))])
            ->get();

        if ($expiringLeases->isEmpty()) {
            $this->info('No expiring leases found.');
            return self::SUCCESS;
        }

        $sent = 0;

        foreach ($expiringLeases as $lease) {
            $daysLeft = (int) now()->diffInDays($lease->end_date);

            if (!in_array($daysLeft, $dayThresholds)) {
                continue;
            }

            $user = $lease->tenant->user;
            $this->line("  → Warning {$user->name} — lease expires in {$daysLeft} days");

            $sms->leaseExpirySoon(
                phone:      $user->phone ?? '',
                tenantName: $user->name,
                unitName:   $lease->unit->unit_number,
                expiryDate: $lease->end_date->format('d M Y'),
                daysLeft:   $daysLeft,
                userId:     $user->id,
            );

            $sent++;
        }

        $this->info("Lease expiry warnings sent: {$sent}");
        return self::SUCCESS;
    }
}

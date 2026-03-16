<?php
// app/Http/Controllers/Tenant/LeaseController.php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\View\View;

class LeaseController extends Controller
{
    public function show(): View
    {
        $tenant = Tenant::where('user_id', auth()->id())
            ->with([
                'activeLease.unit.property',
                'leases' => fn($q) => $q->latest(),
            ])
            ->firstOrFail();

        $lease = $tenant->activeLease;

        // Days remaining on lease
        $daysRemaining = $lease?->end_date ? (int) now()->diffInDays($lease->end_date, false) : null;

        return view('tenant.lease.show', compact('tenant', 'lease', 'daysRemaining'));
    }
}

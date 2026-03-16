<?php
// app/Http/Controllers/Tenant/DashboardController.php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\NotificationLog;
use App\Models\Tenant;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        $tenant = Tenant::where('user_id', $user->id)
            ->with(['activeLease.unit.property', 'activeLease.unit'])
            ->firstOrFail();

        // Recent invoices
        $recentInvoices = Invoice::where('tenant_id', $tenant->id)
            ->latest()
            ->take(5)
            ->get();

        // Stats
        $totalPaid = Invoice::where('tenant_id', $tenant->id)->where('status', 'paid')->sum('amount_paid');
        $totalOwing = Invoice::where('tenant_id', $tenant->id)->whereIn('status', ['unpaid', 'overdue', 'partial'])->sum('balance');
        $overdueCount = Invoice::where('tenant_id', $tenant->id)->where('status', 'overdue')->count();
        $totalInvoices = Invoice::where('tenant_id', $tenant->id)->count();

        // Recent notifications
        $recentNotifications = NotificationLog::where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get();

        return view('tenant.dashboard.index', compact(
            'tenant',
            'recentInvoices',
            'totalPaid',
            'totalOwing',
            'overdueCount',
            'totalInvoices',
            'recentNotifications'
        ));
    }
}

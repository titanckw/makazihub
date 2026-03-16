<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Property;
use App\Models\Invoice;
use App\Models\Payment;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_managers'    => User::role('manager')->count(),
            'total_tenants'     => User::role('tenant')->count(),
            'total_properties'  => Property::count(),
            'total_revenue'     => Payment::where('status', 'confirmed')->sum('amount'),
            'invoices_paid'     => Invoice::where('status', 'paid')->count(),
            'invoices_overdue'  => Invoice::where('status', 'overdue')->count(),
            'invoices_pending'  => Invoice::whereIn('status', ['unpaid', 'partial'])->count(),
            'monthly_revenue'   => Payment::where('status', 'confirmed')
                                    ->whereMonth('payment_date', now()->month)
                                    ->whereYear('payment_date', now()->year)
                                    ->sum('amount'),
        ];

        $recentPayments = Payment::with(['tenant.user', 'invoice'])
            ->where('status', 'confirmed')
            ->latest()
            ->take(10)
            ->get();

        $overdueInvoices = Invoice::with(['tenant.user', 'unit.property'])
            ->where('status', 'overdue')
            ->latest()
            ->take(10)
            ->get();

        return view('superadmin.dashboard', compact('stats', 'recentPayments', 'overdueInvoices'));
    }
}

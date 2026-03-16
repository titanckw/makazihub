<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\Unit;
use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $managerId = Auth::id();

        // Only show properties belonging to this manager
        $propertyIds = Property::where('manager_id', $managerId)->pluck('id');
        $unitIds     = Unit::whereIn('property_id', $propertyIds)->pluck('id');

        $stats = [
            'total_properties'  => $propertyIds->count(),
            'total_units'       => $unitIds->count(),
            'occupied_units'    => Unit::whereIn('id', $unitIds)->where('status', 'occupied')->count(),
            'vacant_units'      => Unit::whereIn('id', $unitIds)->where('status', 'vacant')->count(),
            'maintenance_units' => Unit::whereIn('id', $unitIds)->where('status', 'maintenance')->count(),
            'invoices_paid'     => Invoice::whereIn('unit_id', $unitIds)->where('status', 'paid')->count(),
            'invoices_overdue'  => Invoice::whereIn('unit_id', $unitIds)->where('status', 'overdue')->count(),
            'invoices_partial'  => Invoice::whereIn('unit_id', $unitIds)->where('status', 'partial')->count(),
            'invoices_unpaid'   => Invoice::whereIn('unit_id', $unitIds)->where('status', 'unpaid')->count(),
            'monthly_revenue'   => Payment::whereIn('invoice_id',
                                        Invoice::whereIn('unit_id', $unitIds)->pluck('id')
                                    )->where('status', 'confirmed')
                                    ->whereMonth('payment_date', now()->month)
                                    ->whereYear('payment_date', now()->year)
                                    ->sum('amount'),
        ];

        $overdueInvoices = Invoice::with(['tenant.user', 'unit.property'])
            ->whereIn('unit_id', $unitIds)
            ->where('status', 'overdue')
            ->latest()
            ->take(8)
            ->get();

        $recentPayments = Payment::with(['tenant.user', 'invoice.unit'])
            ->whereIn('invoice_id', Invoice::whereIn('unit_id', $unitIds)->pluck('id'))
            ->where('status', 'confirmed')
            ->latest()
            ->take(8)
            ->get();

        $properties = Property::where('manager_id', $managerId)
            ->withCount(['units', 'units as occupied_count' => fn($q) => $q->where('status', 'occupied')])
            ->get();

        return view('manager.dashboard', compact('stats', 'overdueInvoices', 'recentPayments', 'properties'));
    }
}

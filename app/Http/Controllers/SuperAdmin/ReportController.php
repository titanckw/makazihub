<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Property;
use App\Models\Unit;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    /**
     * Platform-wide reports overview.
     */
    public function index()
    {
        $stats = [
            'total_revenue'     => Payment::where('status', 'confirmed')->sum('amount'),
            'monthly_revenue'   => Payment::where('status', 'confirmed')
                                    ->whereMonth('payment_date', now()->month)
                                    ->whereYear('payment_date', now()->year)
                                    ->sum('amount'),
            'total_units'       => Unit::count(),
            'occupied_units'    => Unit::where('status', 'occupied')->count(),
            'vacant_units'      => Unit::where('status', 'vacant')->count(),
            'invoices_overdue'  => Invoice::where('status', 'overdue')->count(),
            'invoices_pending'  => Invoice::whereIn('status', ['unpaid', 'partial'])->count(),
        ];

        $occupancyRate = $stats['total_units'] > 0
            ? round(($stats['occupied_units'] / $stats['total_units']) * 100, 1)
            : 0;

        $revenueByMonth = Payment::where('status', 'confirmed')
            ->where('payment_date', '>=', now()->subMonths(5)->startOfMonth())
            ->selectRaw('DATE_FORMAT(payment_date, "%Y-%m") as month, SUM(amount) as total')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $topProperties = Property::withCount([
                'units',
                'units as occupied_count' => fn ($q) => $q->where('status', 'occupied'),
            ])
            ->orderByDesc('units_count')
            ->take(5)
            ->get();

        return view('superadmin.reports.index', compact('stats', 'occupancyRate', 'revenueByMonth', 'topProperties'));
    }

    /**
     * Revenue report (confirmed payments grouped by month).
     */
    public function revenue(Request $request)
    {
        $payments = Payment::with(['tenant.user', 'invoice.unit.property'])
            ->where('status', 'confirmed')
            ->latest('payment_date')
            ->paginate(20);

        $totalRevenue = Payment::where('status', 'confirmed')->sum('amount');

        return view('superadmin.reports.revenue', compact('payments', 'totalRevenue'));
    }

    /**
     * Occupancy report across all properties.
     */
    public function occupancy()
    {
        $properties = Property::with('manager')
            ->withCount([
                'units',
                'units as occupied_count' => fn ($q) => $q->where('status', 'occupied'),
                'units as vacant_count'   => fn ($q) => $q->where('status', 'vacant'),
            ])
            ->get();

        return view('superadmin.reports.occupancy', compact('properties'));
    }

    /**
     * Export a simple CSV of confirmed payments.
     */
    public function export()
    {
        $payments = Payment::with(['tenant.user', 'invoice.unit.property'])
            ->where('status', 'confirmed')
            ->latest('payment_date')
            ->get();

        $filename = 'revenue-report-' . now()->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($payments) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Date', 'Tenant', 'Property', 'Unit', 'Amount', 'Method', 'Reference']);

            foreach ($payments as $payment) {
                fputcsv($handle, [
                    optional($payment->payment_date)->format('Y-m-d'),
                    optional(optional($payment->tenant)->user)->name,
                    optional(optional(optional($payment->invoice)->unit)->property)->name,
                    optional(optional($payment->invoice)->unit)->unit_number,
                    $payment->amount,
                    $payment->payment_method,
                    $payment->reference,
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}

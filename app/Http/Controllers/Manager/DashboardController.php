<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\AttendanceLog;
use App\Models\ChatMessage;
use App\Models\LeaveRequest;
use App\Models\Property;
use App\Models\Staff;
use App\Models\StaffDocument;
use App\Models\Unit;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Tenant;
use App\Models\MaintenanceRequest;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $managerId = Auth::id();

        // Only show properties belonging to this manager
        $propertyIds = Property::where('manager_id', $managerId)->pluck('id');
        $unitIds = Unit::whereIn('property_id', $propertyIds)->pluck('id');

        $stats = [
            'total_properties' => $propertyIds->count(),
            'total_units' => $unitIds->count(),
            'total_tenants' => Tenant::whereIn('unit_id', $unitIds)->count(),
            'occupied_units' => Unit::whereIn('id', $unitIds)->where('status', 'occupied')->count(),
            'vacant_units' => Unit::whereIn('id', $unitIds)->where('status', 'vacant')->count(),
            'maintenance_units' => Unit::whereIn('id', $unitIds)->where('status', 'maintenance')->count(),
            'invoices_paid' => Invoice::whereIn('unit_id', $unitIds)->where('status', 'paid')->count(),
            'invoices_overdue' => Invoice::whereIn('unit_id', $unitIds)->where('status', 'overdue')->count(),
            'invoices_partial' => Invoice::whereIn('unit_id', $unitIds)->where('status', 'partial')->count(),
            'invoices_unpaid' => Invoice::whereIn('unit_id', $unitIds)->where('status', 'unpaid')->count(),
            'maintenance_requests_total' => MaintenanceRequest::whereIn('unit_id', $unitIds)->count(),
            'monthly_revenue' => Payment::whereIn(
                'invoice_id',
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

        $maintenanceRequests = MaintenanceRequest::whereIn('unit_id', $unitIds)
            ->with(['tenant.user', 'unit'])
            ->where(function($query) {
                $query->where('status', 'under_review')
                      ->orWhere('status', 'pending_repairs');
            })
            ->latest()
            ->take(5)
            ->get();

        $properties = Property::where('manager_id', $managerId)
            ->withCount(['units', 'units as occupied_count' => fn($q) => $q->where('status', 'occupied')])
            ->get();

        // ---------------------------------------------------------------
        // Staff Management summary (attendance, leave, chat, documents)
        // ---------------------------------------------------------------
        $staffIds = Staff::where('manager_id', $managerId)->pluck('id');

        $staffStats = [
            'total_staff' => Staff::where('manager_id', $managerId)->where('status', 'active')->count(),
            'present_today' => AttendanceLog::whereIn('staff_id', $staffIds)
                ->whereDate('date', now()->toDateString())
                ->whereIn('status', ['present', 'late'])
                ->count(),
            'pending_leave' => LeaveRequest::whereIn('staff_id', $staffIds)->where('status', 'pending')->count(),
            'total_documents' => StaffDocument::whereIn('staff_id', $staffIds)->count(),
            'unread_messages' => ChatMessage::where('recipient_id', $managerId)->whereNull('read_at')->count(),
        ];

        $pendingLeaveRequests = LeaveRequest::with('staff.user')
            ->whereIn('staff_id', $staffIds)
            ->where('status', 'pending')
            ->latest()
            ->take(5)
            ->get();

        return view('manager.dashboard', compact(
            'stats', 'overdueInvoices', 'recentPayments', 'maintenanceRequests', 'properties',
            'staffStats', 'pendingLeaveRequests'
        ));
    }
}

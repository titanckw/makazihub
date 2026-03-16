<?php
// app/Http/Controllers/Manager/NotificationController.php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\NotificationLog;
use App\Models\Tenant;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function __construct(protected NotificationService $notifications)
    {
    }

    /**
     * Notifications log + send panel.
     */
    public function index(Request $request): View
    {
        $managerId = auth()->id();

        // My tenants' user IDs for filtering logs
        $tenantUserIds = Tenant::whereHas('activeLease.unit.property', fn($q) => $q->where('manager_id', $managerId))
            ->with('user')
            ->get()
            ->pluck('user.id');

        $logs = NotificationLog::whereIn('user_id', $tenantUserIds)
            ->when($request->channel, fn($q, $v) => $q->where('channel', $v))
            ->when($request->type, fn($q, $v) => $q->where('type', $v))
            ->when($request->status, fn($q, $v) => $q->where('status', $v))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        // Stats
        $totalSent = NotificationLog::whereIn('user_id', $tenantUserIds)->where('status', 'sent')->count();
        $totalFailed = NotificationLog::whereIn('user_id', $tenantUserIds)->where('status', 'failed')->count();
        $smsSent = NotificationLog::whereIn('user_id', $tenantUserIds)->where('channel', 'sms')->where('status', 'sent')->count();
        $emailSent = NotificationLog::whereIn('user_id', $tenantUserIds)->where('channel', 'email')->where('status', 'sent')->count();

        // Tenants for the custom send panel
        $tenants = Tenant::whereHas('activeLease.unit.property', fn($q) => $q->where('manager_id', $managerId))
            ->with('user')
            ->get();

        return view('manager.notifications.index', compact('logs', 'totalSent', 'totalFailed', 'smsSent', 'emailSent', 'tenants'));
    }

    /**
     * Re-send invoice notification.
     */
    public function sendInvoice(Invoice $invoice): RedirectResponse
    {
        $this->authorizeInvoice($invoice);
        $this->notifications->invoiceGenerated($invoice);
        return back()->with('success', "Invoice notification sent to {$invoice->tenant->user->name}.");
    }

    /**
     * Send overdue reminder for a specific invoice.
     */
    public function sendOverdue(Invoice $invoice): RedirectResponse
    {
        $this->authorizeInvoice($invoice);
        $this->notifications->overdueReminder($invoice);
        return back()->with('success', "Overdue reminder sent to {$invoice->tenant->user->name}.");
    }

    /**
     * Broadcast a custom SMS to selected tenants.
     */
    public function sendCustom(Request $request): RedirectResponse
    {
        $request->validate([
            'message' => 'required|string|max:160',
            'tenant_ids' => 'required|array|min:1',
            'tenant_ids.*' => 'integer|exists:tenants,id',
        ]);

        $managerId = auth()->id();
        $tenants = Tenant::whereIn('id', $request->tenant_ids)
            ->whereHas('activeLease.unit.property', fn($q) => $q->where('manager_id', $managerId))
            ->with('user')
            ->get();

        $sent = $this->notifications->customSms($tenants, $request->message);

        return back()->with('success', "Custom SMS sent to {$sent} tenant(s).");
    }

    /**
     * Bulk send overdue reminders for all overdue invoices.
     */
    public function sendAllOverdue(): RedirectResponse
    {
        $managerId = auth()->id();

        $overdueInvoices = Invoice::with(['tenant.user', 'property', 'unit'])
            ->where('status', 'overdue')
            ->whereHas('property', fn($q) => $q->where('manager_id', $managerId))
            ->get();

        $count = 0;
        foreach ($overdueInvoices as $invoice) {
            $this->notifications->overdueReminder($invoice);
            $count++;
        }

        return back()->with('success', "Overdue reminders sent for {$count} invoice(s).");
    }

    protected function authorizeInvoice(Invoice $invoice): void
    {
        abort_unless($invoice->property->manager_id === auth()->id(), 403);
    }
}

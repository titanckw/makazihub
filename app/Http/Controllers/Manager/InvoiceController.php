<?php
// app/Http/Controllers/Manager/InvoiceController.php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Lease;
use App\Models\Property;
use App\Services\InvoiceService;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{
    public function __construct(private InvoiceService $invoiceService)
    {
    }

    /**
     * Color-coded invoice dashboard.
     */
    public function index(Request $request)
    {
        $manager = auth()->user();

        $query = Invoice::with(['tenant.user', 'unit.property', 'lease'])
            ->whereHas('property', fn($q) => $q->where('manager_id', $manager->id));

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('property_id')) {
            $query->where('property_id', $request->property_id);
        }

        if ($request->filled('month')) {
            $query->whereYear('period_start', substr($request->month, 0, 4))
                ->whereMonth('period_start', substr($request->month, 5, 2));
        }

        // Summary counts for KPI cards
        $allInvoices = Invoice::whereHas('property', fn($q) => $q->where('manager_id', $manager->id));
        $summary = [
            'paid' => (clone $allInvoices)->where('status', 'paid')->count(),
            'unpaid' => (clone $allInvoices)->where('status', 'unpaid')->count(),
            'partial' => (clone $allInvoices)->where('status', 'partial')->count(),
            'overdue' => (clone $allInvoices)->where('status', 'overdue')->count(),
            'total_outstanding' => (clone $allInvoices)->whereIn('status', ['unpaid', 'partial', 'overdue'])
                ->selectRaw('SUM(total_amount - amount_paid) as total')->value('total') ?? 0,
            'total_collected_month' => (clone $allInvoices)->where('status', 'paid')
                ->whereYear('updated_at', now()->year)
                ->whereMonth('updated_at', now()->month)
                ->sum('total_amount'),
        ];

        $invoices = $query->latest('due_date')->paginate(20)->withQueryString();
        $properties = Property::where('manager_id', $manager->id)->get();

        return view('manager.invoices.index', compact('invoices', 'properties', 'summary'));
    }

    /**
     * Show invoice detail.
     */
    public function show(Invoice $invoice)
    {
        $this->authorizeInvoice($invoice);
        $invoice->load(['tenant.user', 'unit.property', 'lease', 'payments.recorder', 'receipt']);
        return view('manager.invoices.show', compact('invoice'));
    }

    /**
     * Manually generate invoice for a specific lease.
     */
    public function generate(Request $request)
    {
        $request->validate([
            'lease_id' => 'required|exists:leases,id',
            'month' => 'required|date_format:Y-m',
        ]);

        $manager = auth()->user();
        $lease = Lease::whereHas('unit.property', fn($q) => $q->where('manager_id', $manager->id))
            ->where('status', 'active')
            ->findOrFail($request->lease_id);

        $forDate = \Carbon\Carbon::createFromFormat('Y-m', $request->month)->startOfMonth();
        $invoice = $this->invoiceService->generateForLease($lease, $forDate);

        return redirect()->route('manager.invoices.show', $invoice)
            ->with('success', 'Invoice ' . $invoice->invoice_number . ' generated successfully.');
    }

    /**
     * Manually mark an invoice as overdue.
     */
    public function markOverdue(Invoice $invoice)
    {
        $this->authorizeInvoice($invoice);

        if (!in_array($invoice->status, ['unpaid', 'partial'])) {
            return back()->with('error', 'Only unpaid or partial invoices can be marked overdue.');
        }

        $invoice->update(['status' => 'overdue']);
        return back()->with('success', 'Invoice marked as overdue.');
    }

    /**
     * Download invoice as PDF.
     */
    public function pdf(Invoice $invoice)
    {
        $this->authorizeInvoice($invoice);
        $invoice->load(['tenant.user', 'unit.property', 'lease', 'payments']);

        $pdf = Pdf::loadView('manager.invoices.pdf', compact('invoice'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('invoice-' . $invoice->invoice_number . '.pdf');
    }

    private function authorizeInvoice(Invoice $invoice): void
    {
        $ok = Property::where('manager_id', auth()->id())
            ->where('id', $invoice->property_id)
            ->exists();
        if (!$ok)
            abort(403);
    }
}

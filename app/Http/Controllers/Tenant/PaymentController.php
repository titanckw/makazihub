<?php
// app/Http/Controllers/Tenant/PaymentController.php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Tenant;
use App\Services\InvoiceService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function __construct(private InvoiceService $invoiceService)
    {
    }

    protected function tenant(): Tenant
    {
        return Tenant::where('user_id', auth()->id())->firstOrFail();
    }

    public function index(): View
    {
        $tenant = $this->tenant();

        $payments = Payment::whereHas('invoice', fn($q) => $q->where('tenant_id', $tenant->id))
            ->with(['invoice.unit', 'invoice.property'])
            ->latest()
            ->paginate(15);

        $totalPaid   = $payments->sum('amount');
        $mpesaCount  = Payment::whereHas('invoice', fn($q) => $q->where('tenant_id', $tenant->id))
            ->where('payment_method', 'mpesa')->count();

        return view('tenant.payments.index', compact('tenant', 'payments', 'totalPaid', 'mpesaCount'));
    }

    public function store(Request $request)
    {
        $tenant = $this->tenant();

        $request->validate([
            'invoice_id'               => 'required|exists:invoices,id',
            'amount'                   => 'required|numeric|min:1',
            'payment_method'           => 'required|in:mpesa,cash,bank_transfer,cheque',
            'reference'                => 'nullable|string|max:100',
            'mpesa_receipt'            => 'nullable|string|max:20',
            'phone_number'             => 'nullable|string|max:15',
            'paid_at'                  => 'required|date',
            'expected_completion_date' => 'nullable|date|after_or_equal:paid_at',
            'notes'                    => 'nullable|string|max:500',
        ]);

        $invoice = Invoice::findOrFail($request->invoice_id);
        abort_unless($invoice->tenant_id === $tenant->id, 403);

        if ($request->amount > $invoice->balance) {
            return back()->withInput()->with('error', 'Amount exceeds outstanding balance of KES ' . number_format($invoice->balance, 2));
        }

        $this->invoiceService->recordPayment($invoice, array_merge($request->all(), [
            'recorded_by' => auth()->id(),
        ]));

        return redirect()->route('tenant.invoices.show', $invoice)
            ->with('success', 'Payment recorded successfully. Your updated balance is KES ' . number_format($invoice->fresh()->balance, 2));
    }

    public function show(Payment $payment): View
    {
        $tenant = $this->tenant();
        abort_unless($payment->invoice->tenant_id === $tenant->id, 403);
        $payment->load(['invoice.unit', 'invoice.property', 'invoice.receipt']);
        return view('tenant.payments.show', compact('payment'));
    }
}

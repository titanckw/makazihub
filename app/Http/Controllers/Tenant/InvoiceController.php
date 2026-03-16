<?php
// app/Http/Controllers/Tenant/InvoiceController.php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Tenant;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InvoiceController extends Controller
{
    protected function tenant(): Tenant
    {
        return Tenant::where('user_id', auth()->id())->firstOrFail();
    }

    public function index(Request $request): View
    {
        $tenant = $this->tenant();

        $invoices = Invoice::where('tenant_id', $tenant->id)
            ->with(['unit', 'property', 'payments'])
            ->when($request->status, fn($q, $v) => $q->where('status', $v))
            ->when($request->search, fn($q, $v) => $q->where('invoice_number', 'like', "%{$v}%"))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('tenant.invoices.index', compact('tenant', 'invoices'));
    }

    public function show(Invoice $invoice): View
    {
        $this->authorizeInvoice($invoice);
        $invoice->load(['unit', 'property', 'payments', 'receipt', 'lease']);
        return view('tenant.invoices.show', compact('invoice'));
    }

    public function download(Invoice $invoice)
    {
        $this->authorizeInvoice($invoice);
        $invoice->load(['unit', 'property', 'tenant.user', 'payments', 'lease']);

        $pdf = Pdf::loadView('manager.invoices.pdf', compact('invoice'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('invoice-' . $invoice->invoice_number . '.pdf');
    }

    protected function authorizeInvoice(Invoice $invoice): void
    {
        $tenant = $this->tenant();
        abort_unless($invoice->tenant_id === $tenant->id, 403);
    }

    public function stkPush(Invoice $invoice, \App\Services\MpesaService $mpesa): \Illuminate\Http\RedirectResponse
    {
        $this->authorizeInvoice($invoice);

        $phone = request('phone');
        $amount = request('amount');

        $result = $mpesa->stkPush($phone, $amount, $invoice->invoice_number);

        if ($result['success']) {
            return back()->with('success', 'STK Push sent! Check your phone and enter your M-Pesa PIN.');
        }

        return back()->with('error', 'STK Push failed: ' . ($result['message'] ?? 'Unknown error'));
    }
}

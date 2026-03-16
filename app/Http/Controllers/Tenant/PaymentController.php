<?php
// app/Http/Controllers/Tenant/PaymentController.php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Tenant;
use Illuminate\View\View;

class PaymentController extends Controller
{
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

    public function show(Payment $payment): View
    {
        $tenant = $this->tenant();
        abort_unless($payment->invoice->tenant_id === $tenant->id, 403);
        $payment->load(['invoice.unit', 'invoice.property', 'invoice.receipt']);
        return view('tenant.payments.show', compact('payment'));
    }
}

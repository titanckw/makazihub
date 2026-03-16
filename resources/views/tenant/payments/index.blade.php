{{-- resources/views/tenant/payments/index.blade.php --}}
@extends('layouts.tenant')

@section('title', 'Payment History')

@section('content')
<div class="space-y-6">

    <div>
        <h1 class="text-2xl font-bold text-primary">Payment History</h1>
        <p class="text-sm text-muted mt-0.5">All payments made on your account</p>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 gap-4">
        <div class="bg-card rounded-2xl p-5 border-t-4 border-brand-600 shadow-sm">
            <p class="text-sm text-muted">Total Paid</p>
            <p class="text-2xl font-bold text-primary mt-1">KES {{ number_format($totalPaid, 2) }}</p>
        </div>
        <div class="bg-card rounded-2xl p-5 border-t-4 border-info shadow-sm">
            <p class="text-sm text-muted">M-Pesa Payments</p>
            <p class="text-2xl font-bold text-primary mt-1">{{ $mpesaCount }}</p>
        </div>
    </div>

    {{-- Payments table --}}
    <div class="bg-card rounded-2xl shadow-sm border border-border overflow-hidden">
        <div class="p-5 border-b border-border">
            <h2 class="font-semibold text-primary">All Payments</h2>
        </div>

        @if($payments->isEmpty())
        <div class="p-12 text-center text-muted">
            <p class="font-medium">No payments recorded yet.</p>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-border bg-surface">
                        <th class="text-left px-5 py-3 text-xs font-semibold text-muted uppercase tracking-wide">Date</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-muted uppercase tracking-wide">Invoice</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-muted uppercase tracking-wide">Method</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-muted uppercase tracking-wide">M-Pesa Code</th>
                        <th class="text-right px-5 py-3 text-xs font-semibold text-muted uppercase tracking-wide">Amount</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-muted uppercase tracking-wide">Receipt</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @foreach($payments as $payment)
                    <tr class="hover:bg-surface/60 transition-colors">
                        <td class="px-5 py-3.5 text-muted text-xs whitespace-nowrap">
                            {{ \Carbon\Carbon::parse($payment->paid_at ?? $payment->created_at)->format('d M Y, H:i') }}
                        </td>
                        <td class="px-5 py-3.5">
                            <a href="{{ route('tenant.invoices.show', $payment->invoice) }}"
                               class="font-medium text-brand-600 hover:text-brand-500 font-mono text-xs">
                                {{ $payment->invoice->invoice_number }}
                            </a>
                        </td>
                        <td class="px-5 py-3.5">
                            <span class="px-2.5 py-1 rounded-full text-xs font-semibold
                                @if($payment->payment_method === 'mpesa') bg-success-bg text-success
                                @else @endif">
                                {{ strtoupper($payment->payment_method) }}
                            </span>
                        </td>
                        <td class="px-5 py-3.5 font-mono text-xs text-muted">
                            {{ $payment->mpesa_transaction_id ?? '—' }}
                        </td>
                        <td class="px-5 py-3.5 text-right font-bold text-primary">
                            KES {{ number_format($payment->amount, 2) }}
                        </td>
                        <td class="px-5 py-3.5">
                            @if($payment->invoice->receipt)
                            <a href="{{ route('tenant.receipts.download', $payment->invoice->receipt) }}"
                               class="text-xs text-brand-600 hover:text-brand-500 font-medium flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                </svg>
                                PDF
                            </a>
                            @else
                            <span class="text-xs text-muted">—</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-border">{{ $payments->links() }}</div>
        @endif
    </div>

</div>
@endsection

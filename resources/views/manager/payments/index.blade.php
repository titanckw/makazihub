@extends('layouts.app')

@section('title', 'Payments')
@section('page-title', 'Payments')
@section('page-subtitle', 'All recorded rent payments')

@section('sidebar-nav')
    @include('manager.partials.sidebar')
@endsection

@section('content')
<div class="space-y-6">

    <div class="bg-white rounded-2xl border border-border overflow-hidden">
        @if($payments->isEmpty())
            <div class="py-16 text-center">
                <svg class="w-12 h-12 text-muted mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                </svg>
                <p class="text-muted text-sm">No payments recorded yet.</p>
            </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-surface border-b border-border">
                        <th class="text-left px-6 py-3 text-xs font-semibold text-secondary uppercase tracking-wider">Tenant</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-secondary uppercase tracking-wider">Invoice</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-secondary uppercase tracking-wider">Amount</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-secondary uppercase tracking-wider">Method</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-secondary uppercase tracking-wider">Reference</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-secondary uppercase tracking-wider">Date</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-secondary uppercase tracking-wider">Status</th>
                        <th class="text-right px-6 py-3 text-xs font-semibold text-secondary uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @foreach($payments as $payment)
                    <tr class="hover:bg-surface/60 transition-colors">
                        <td class="px-6 py-4">
                            <p class="font-semibold text-primary">{{ $payment->tenant->user->name ?? '—' }}</p>
                            <p class="text-xs text-muted">{{ $payment->invoice->unit->unit_number ?? '—' }}</p>
                        </td>
                        <td class="px-6 py-4 font-mono text-xs text-secondary">
                            {{ $payment->invoice->invoice_number ?? '—' }}
                        </td>
                        <td class="px-6 py-4 font-bold text-primary">
                            KES {{ number_format($payment->amount, 2) }}
                        </td>
                        <td class="px-6 py-4 text-secondary">{{ $payment->method_label }}</td>
                        <td class="px-6 py-4 font-mono text-xs text-secondary">
                            {{ $payment->mpesa_receipt ?? $payment->reference ?? '—' }}
                        </td>
                        <td class="px-6 py-4 text-xs text-secondary">
                            {{ $payment->paid_at ? \Carbon\Carbon::parse($payment->paid_at)->format('d M Y') : '—' }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $payment->status_badge }}">
                                {{ ucfirst($payment->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <a href="{{ route('manager.payments.show', $payment) }}"
                               class="text-xs bg-navy-500 hover:bg-navy-400 text-white px-3 py-1.5 rounded-lg font-medium transition-colors float-right">
                                View
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($payments->hasPages())
        <div class="px-6 py-4 border-t border-border">{{ $payments->links() }}</div>
        @endif
        @endif
    </div>

</div>
@endsection

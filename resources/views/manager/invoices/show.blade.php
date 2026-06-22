@extends('layouts.app')

@section('title', $invoice->invoice_number)
@section('page-title', $invoice->invoice_number)
@section('page-subtitle', 'Invoice details & payment history')

@section('sidebar-nav')
    @include('manager.partials.sidebar')
@endsection

@section('content')
    <div class="space-y-6" x-data>

        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3">
                <a href="{{ route('manager.invoices.index') }}"
                    class="inline-flex items-center gap-2 text-sm text-secondary hover:text-primary transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Back
                </a>
                <span
                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $invoice->status_badge }}">
                    {{ $invoice->status_label }}
                </span>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('manager.invoices.pdf', $invoice) }}"
                    class="text-sm border border-border text-secondary hover:bg-surface px-4 py-2 rounded-xl font-medium transition-colors inline-flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Download PDF
                </a>
                @if($invoice->status !== 'paid')

                    <form method="POST" action="{{ route('manager.notifications.send-invoice', $invoice) }}">
                        @csrf
                        <button type="submit"
                            class="flex items-center gap-2 px-4 py-2 bg-brand-600 hover:bg-brand-500 text-white text-sm font-semibold rounded-xl transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                            </svg>
                            Send to Tenant
                        </button>
                    </form>

                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Invoice Info --}}
            <div class="lg:col-span-1 space-y-4">
                <div class="bg-white rounded-2xl border border-border p-6 space-y-4">
                    <h2 class="text-xs font-bold text-secondary uppercase tracking-wider">Invoice Details</h2>
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between gap-4">
                            <span class="text-muted shrink-0">Tenant</span>
                            <a href="{{ route('manager.tenants.show', $invoice->tenant) }}"
                                class="font-medium text-brand-600 hover:underline text-right">
                                {{ $invoice->tenant->user->name ?? '—' }}
                            </a>
                        </div>
                        <div class="flex justify-between gap-4">
                            <span class="text-muted">Unit</span>
                            <span class="font-medium text-primary">{{ $invoice->unit->unit_number ?? '—' }}</span>
                        </div>
                        <div class="flex justify-between gap-4">
                            <span class="text-muted">Property</span>
                            <span class="font-medium text-primary">{{ $invoice->unit->property->name ?? '—' }}</span>
                        </div>
                        <div class="border-t border-border pt-3"></div>
                        <div class="flex justify-between">
                            <span class="text-muted">Period</span>
                            <span class="font-medium text-primary text-right text-xs">
                                {{ $invoice->period_start->format('d M') }} – {{ $invoice->period_end->format('d M Y') }}
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-muted">Due Date</span>
                            <span
                                class="font-medium {{ $invoice->is_overdue && $invoice->status !== 'paid' ? 'text-danger' : 'text-primary' }}">
                                {{ $invoice->due_date->format('d M Y') }}
                            </span>
                        </div>
                        <div class="border-t border-border pt-3"></div>
                        <div class="flex justify-between">
                            <span class="text-muted">Invoice Amount</span>
                            <span class="font-bold text-primary">KES {{ number_format($invoice->amount, 2) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-muted">Amount Paid</span>
                            <span class="font-bold text-success">KES {{ number_format($invoice->amount_paid, 2) }}</span>
                        </div>
                        @if($invoice->status === 'partial' && $invoice->expected_completion_date)
                            <div class="flex justify-between">
                                <span class="text-muted">Expected completion</span>
                                <span
                                    class="font-bold text-primary">{{ $invoice->expected_completion_date->format('d M Y') }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between border-t border-border pt-3">
                            <span class="font-semibold text-primary">Balance Due</span>
                            <span class="font-bold text-lg {{ $invoice->balance > 0 ? 'text-danger' : 'text-success' }}">
                                KES {{ number_format($invoice->balance, 2) }}
                            </span>
                        </div>
                    </div>
                </div>

                @if($invoice->receipt)
                    <div class="bg-success-bg border border-success/20 rounded-2xl p-4 flex items-start gap-3">
                        <svg class="w-5 h-5 text-success mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div>
                            <p class="text-sm font-semibold text-success">Receipt Issued</p>
                            <p class="text-xs text-success/80 mt-0.5">{{ $invoice->receipt->receipt_number }}</p>
                            <a href="{{ route('manager.receipts.download', $invoice->receipt) }}"
                                class="text-xs text-success font-medium hover:underline mt-1 inline-block">
                                Download Receipt →
                            </a>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Payment History --}}
            <div class="lg:col-span-2">
                <div class="bg-white rounded-2xl border border-border overflow-hidden">
                    <div class="px-6 py-4 border-b border-border flex items-center justify-between">
                        <h2 class="text-sm font-bold text-primary">Payment History</h2>
                        <span class="text-xs text-muted">{{ $invoice->payments->count() }} payment(s)</span>
                    </div>
                    @forelse($invoice->payments as $payment)
                        <div class="px-6 py-4 border-b border-border last:border-0">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <p class="font-bold text-primary">KES {{ number_format($payment->amount, 2) }}</p>
                                    <p class="text-xs text-secondary mt-0.5">
                                        {{ $payment->method_label }}
                                        @if($payment->mpesa_receipt) · <span
                                        class="font-mono">{{ $payment->mpesa_receipt }}</span> @endif
                                        @if($payment->reference && !$payment->mpesa_receipt) · {{ $payment->reference }} @endif
                                    </p>
                                    <p class="text-xs text-muted mt-0.5">
                                        {{ $payment->paid_at ? \Carbon\Carbon::parse($payment->paid_at)->format('d M Y, h:i A') : '—' }}
                                        @if($payment->recorder) · Recorded by {{ $payment->recorder->name }} @endif
                                    </p>
                                    @if($payment->notes)
                                        <p class="text-xs text-muted italic mt-0.5">{{ $payment->notes }}</p>
                                    @endif
                                </div>
                                <div class="flex flex-col items-end gap-2 shrink-0">
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold {{ $payment->status_badge }}">
                                        {{ ucfirst($payment->status) }}
                                    </span>
                                    @if($payment->status === 'confirmed')
                                        <form action="{{ route('manager.payments.reverse', $payment) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                onclick="return confirm('Are you sure you want to reverse this payment?')"
                                                class="text-xs text-danger hover:underline">
                                                Reverse
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="py-12 text-center text-muted text-sm">
                            No payments recorded yet.
                        </div>
                    @endforelse
                </div>
            </div>

        </div>
    </div>

@endsection
{{-- resources/views/tenant/invoices/show.blade.php --}}
@extends('layouts.tenant')

@section('title', 'Invoice ' . $invoice->invoice_number)

@section('content')
    <div class="max-w-2xl mx-auto space-y-6">

        <div class="flex items-center gap-4">
            <a href="{{ route('tenant.invoices.index') }}" class="text-muted hover:text-primary transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <div>
                <h1 class="text-xl font-bold text-primary">{{ $invoice->invoice_number }}</h1>
                <p class="text-sm text-muted">{{ $invoice->period_start->format('M d') }} –
                    {{ $invoice->period_end->format('M d, Y') }}</p>
            </div>
            <span class="ml-auto px-3 py-1 rounded-full text-sm font-semibold {{ $invoice->status_badge }}">
                {{ $invoice->status_label }}
            </span>
        </div>

        {{-- Invoice detail card --}}
        <div class="bg-card rounded-2xl shadow-sm border border-border overflow-hidden">
            <div class="bg-navy-600 px-6 py-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-white/60 text-xs uppercase tracking-widest">Total Amount</p>
                        <p class="text-3xl font-bold text-white mt-1">KES {{ number_format($invoice->amount, 2) }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-white/60 text-xs">Due Date</p>
                        <p class="text-white font-semibold mt-1 {{ $invoice->is_overdue ? 'text-red-300' : '' }}">
                            {{ $invoice->due_date->format('d M Y') }}
                        </p>
                        @if($invoice->is_overdue)
                            <p class="text-red-300 text-xs mt-0.5">{{ (int) $invoice->due_date->diffInDays(now()) }} days
                                overdue</p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="p-6 space-y-3">
                <div class="flex justify-between text-sm py-2 border-b border-border">
                    <span class="text-muted">Property</span>
                    <span class="font-medium text-primary">{{ $invoice->property->name }}</span>
                </div>
                <div class="flex justify-between text-sm py-2 border-b border-border">
                    <span class="text-muted">Unit</span>
                    <span class="font-medium text-primary">{{ $invoice->unit->unit_number }}</span>
                </div>
                <div class="flex justify-between text-sm py-2 border-b border-border">
                    <span class="text-muted">Invoice Number</span>
                    <span class="font-medium text-primary font-mono">{{ $invoice->invoice_number }}</span>
                </div>
                <div class="flex justify-between text-sm py-2 border-b border-border">
                    <span class="text-muted">Rental Period</span>
                    <span class="font-medium text-primary">{{ $invoice->period_start->format('M d') }} –
                        {{ $invoice->period_end->format('M d, Y') }}</span>
                </div>
                <div class="flex justify-between text-sm py-2 border-b border-border">
                    <span class="text-muted">Amount</span>
                    <span class="font-bold text-primary">KES {{ number_format($invoice->amount, 2) }}</span>
                </div>
                @if($invoice->amount_paid > 0)
                    <div class="flex justify-between text-sm py-2 border-b border-border">
                        <span class="text-muted">Amount Paid</span>
                        <span class="font-bold text-success">KES {{ number_format($invoice->amount_paid, 2) }}</span>
                    </div>
                @endif
                @if($invoice->balance > 0)
                    <div class="flex justify-between text-sm py-2 border-b border-border">
                        <span class="text-muted">Balance Due</span>
                        <span class="font-bold text-danger">KES {{ number_format($invoice->balance, 2) }}</span>
                    </div>
                @endif
                @if($invoice->notes)
                    <div class="flex justify-between text-sm py-2">
                        <span class="text-muted">Notes</span>
                        <span class="text-primary text-right max-w-xs">{{ $invoice->notes }}</span>
                    </div>
                @endif
            </div>
        </div>

        {{-- Payment history --}}
        @if($invoice->payments->isNotEmpty())
            <div class="bg-card rounded-2xl shadow-sm border border-border overflow-hidden">
                <div class="p-5 border-b border-border">
                    <h2 class="font-semibold text-primary">Payment History</h2>
                </div>
                <div class="divide-y divide-border">
                    @foreach($invoice->payments as $payment)
                        <div class="flex items-center justify-between px-5 py-3.5">
                            <div>
                                <p class="text-sm font-medium text-primary">KES {{ number_format($payment->amount, 2) }}</p>
                                <p class="text-xs text-muted mt-0.5">
                                    {{ strtoupper($payment->payment_method) }}
                                    @if($payment->mpesa_transaction_id)
                                        · <span class="font-mono">{{ $payment->mpesa_transaction_id }}</span>
                                    @endif
                                </p>
                            </div>
                            <p class="text-xs text-muted">
                                {{ \Carbon\Carbon::parse($payment->paid_at ?? $payment->created_at)->format('d M Y, H:i') }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Actions --}}
    {{-- Actions --}}
    <div class="space-y-4">

        @if($invoice->status !== 'paid')

            {{-- M-Pesa STK Push --}}
            <div class="bg-card rounded-2xl shadow-sm border border-border p-5">
                <h2 class="font-semibold text-primary mb-1">Pay via M-Pesa STK Push</h2>
                <p class="text-xs text-muted mb-4">We'll send a payment prompt directly to your phone. Just enter your PIN to
                    confirm.</p>
                <form method="POST" action="{{ route('tenant.payments.stk-push', $invoice) }}">
                    @csrf
                    <div class="flex gap-3">
                        <div class="flex-1">
                            <input type="text" name="phone" value="{{ auth()->user()->phone }}" placeholder="e.g. 0712345678"
                                class="w-full px-4 py-2.5 rounded-xl border border-border text-sm text-primary focus:outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500">
                        </div>
                        <div class="w-36">
                            <input type="number" name="amount" value="{{ $invoice->balance }}" placeholder="Amount"
                                class="w-full px-4 py-2.5 rounded-xl border border-border text-sm text-primary focus:outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500">
                        </div>
                        <button type="submit"
                            class="flex items-center gap-2 px-4 py-2.5 bg-brand-600 hover:bg-brand-500 text-white text-sm font-semibold rounded-xl transition-colors whitespace-nowrap">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                            Send STK Push
                        </button>
                    </div>
                </form>
            </div>

            {{-- Manual / Paybill instructions --}}
            <div class="bg-card rounded-2xl shadow-sm border border-border p-5">
                <h2 class="font-semibold text-primary mb-3">Or Pay via Paybill</h2>
                <div class="space-y-2 text-sm text-secondary">
                    <div class="flex items-start gap-3">
                        <span
                            class="w-6 h-6 rounded-full bg-navy-500 text-white text-xs font-bold flex items-center justify-center shrink-0 mt-0.5">1</span>
                        <p>Go to <strong>M-Pesa → Lipa na M-Pesa → Pay Bill</strong></p>
                    </div>
                    <div class="flex items-start gap-3">
                        <span
                            class="w-6 h-6 rounded-full bg-navy-500 text-white text-xs font-bold flex items-center justify-center shrink-0 mt-0.5">2</span>
                        <p>Business No: <strong class="text-primary font-mono">{{ config('mpesa.paybill', '—') }}</strong></p>
                    </div>
                    <div class="flex items-start gap-3">
                        <span
                            class="w-6 h-6 rounded-full bg-navy-500 text-white text-xs font-bold flex items-center justify-center shrink-0 mt-0.5">3</span>
                        <p>Account No: <strong class="text-primary font-mono">{{ $invoice->unit->unit_number }}</strong></p>
                    </div>
                    <div class="flex items-start gap-3">
                        <span
                            class="w-6 h-6 rounded-full bg-navy-500 text-white text-xs font-bold flex items-center justify-center shrink-0 mt-0.5">4</span>
                        <p>Amount: <strong class="text-primary">KES {{ number_format($invoice->balance, 2) }}</strong></p>
                    </div>
                </div>
            </div>

        @endif

        {{-- PDF downloads --}}
        <div class="flex gap-3">
            <a href="{{ route('tenant.invoices.download', $invoice) }}"
                class="flex-1 flex items-center justify-center gap-2 py-3 bg-navy-500 hover:bg-navy-400 text-white text-sm font-semibold rounded-xl transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                Download Invoice
            </a>
            @if($invoice->receipt)
                <a href="{{ route('tenant.receipts.download', $invoice->receipt) }}"
                    class="flex-1 flex items-center justify-center gap-2 py-3 bg-brand-600 hover:bg-brand-500 text-white text-sm font-semibold rounded-xl transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                    Download Receipt
                </a>
            @endif
        </div>

    </div>
@endsection
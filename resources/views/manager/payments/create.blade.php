@extends('layouts.app')

@section('title', 'Record Payment')
@section('page-title', 'Record Payment')
@section('page-subtitle', $invoice->invoice_number . ' · KES ' . number_format($invoice->balance, 2) . ' outstanding')

@section('sidebar-nav')
    @include('manager.partials.sidebar')
@endsection

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    <a href="{{ route('manager.invoices.show', $invoice) }}" class="inline-flex items-center gap-2 text-sm text-secondary hover:text-primary transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Back to Invoice
    </a>

    {{-- Invoice Summary --}}
    <div class="bg-white rounded-2xl border border-border p-5">
        <div class="grid grid-cols-3 gap-4 text-center">
            <div>
                <p class="text-xs text-muted uppercase tracking-wider font-semibold mb-1">Invoice Total</p>
                <p class="text-xl font-bold text-primary">KES {{ number_format($invoice->amount, 2) }}</p>
            </div>
            <div>
                <p class="text-xs text-muted uppercase tracking-wider font-semibold mb-1">Paid So Far</p>
                <p class="text-xl font-bold text-success">KES {{ number_format($invoice->amount_paid, 2) }}</p>
            </div>
            <div>
                <p class="text-xs text-muted uppercase tracking-wider font-semibold mb-1">Balance Due</p>
                <p class="text-xl font-bold text-danger">KES {{ number_format($invoice->balance, 2) }}</p>
            </div>
        </div>
    </div>

    <form action="{{ route('manager.payments.store') }}" method="POST" class="space-y-6" x-data="{ method: 'mpesa' }">
        @csrf
        <input type="hidden" name="invoice_id" value="{{ $invoice->id }}">

        <div class="bg-white rounded-2xl border border-border p-6 space-y-5">
            <h2 class="text-sm font-bold text-primary uppercase tracking-wider">Payment Details</h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-secondary mb-1.5">Amount (KES) <span class="text-danger">*</span></label>
                    <input type="number" name="amount" value="{{ old('amount', $invoice->balance) }}"
                        step="0.01" min="1" max="{{ $invoice->balance }}" required
                        class="w-full text-sm border border-border rounded-xl px-3 py-2.5 bg-surface focus:outline-none focus:ring-2 focus:ring-brand-400">
                    @error('amount') <p class="text-xs text-danger mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-secondary mb-1.5">Payment Date <span class="text-danger">*</span></label>
                    <input type="date" name="paid_at" value="{{ old('paid_at', now()->format('Y-m-d')) }}" required
                        class="w-full text-sm border border-border rounded-xl px-3 py-2.5 bg-surface focus:outline-none focus:ring-2 focus:ring-brand-400">
                    @error('paid_at') <p class="text-xs text-danger mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-secondary mb-2">Payment Method <span class="text-danger">*</span></label>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                    @foreach(['mpesa' => 'M-Pesa', 'cash' => 'Cash', 'bank_transfer' => 'Bank Transfer', 'cheque' => 'Cheque'] as $val => $label)
                    <label class="cursor-pointer">
                        <input type="radio" name="payment_method" value="{{ $val }}" x-model="method"
                            {{ old('payment_method', 'mpesa') === $val ? 'checked' : '' }} class="sr-only peer">
                        <div class="text-center text-sm font-medium py-2.5 px-3 rounded-xl border-2 border-border
                                    peer-checked:border-brand-600 peer-checked:bg-brand-100 peer-checked:text-brand-600
                                    hover:border-brand-400 transition-colors">
                            {{ $label }}
                        </div>
                    </label>
                    @endforeach
                </div>
            </div>

            {{-- M-Pesa fields --}}
            <div x-show="method === 'mpesa'" class="space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-secondary mb-1.5">M-Pesa Receipt No.</label>
                        <input type="text" name="mpesa_receipt" value="{{ old('mpesa_receipt') }}"
                            placeholder="e.g. QKE12ABC45" maxlength="20"
                            class="w-full text-sm border border-border rounded-xl px-3 py-2.5 bg-surface focus:outline-none focus:ring-2 focus:ring-brand-400 font-mono uppercase">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-secondary mb-1.5">Phone Number</label>
                        <input type="text" name="phone_number" value="{{ old('phone_number', $invoice->tenant->user->phone ?? '') }}"
                            placeholder="07XX XXX XXX"
                            class="w-full text-sm border border-border rounded-xl px-3 py-2.5 bg-surface focus:outline-none focus:ring-2 focus:ring-brand-400">
                    </div>
                </div>
            </div>

            {{-- Reference for other methods --}}
            <div x-show="method !== 'mpesa'">
                <label class="block text-sm font-medium text-secondary mb-1.5">Reference / Cheque No.</label>
                <input type="text" name="reference" value="{{ old('reference') }}"
                    placeholder="Transaction reference or cheque number"
                    class="w-full text-sm border border-border rounded-xl px-3 py-2.5 bg-surface focus:outline-none focus:ring-2 focus:ring-brand-400">
            </div>

            <div>
                <label class="block text-sm font-medium text-secondary mb-1.5">Notes (optional)</label>
                <textarea name="notes" rows="2"
                    class="w-full text-sm border border-border rounded-xl px-3 py-2.5 bg-surface focus:outline-none focus:ring-2 focus:ring-brand-400 resize-none">{{ old('notes') }}</textarea>
            </div>
        </div>

        {{-- Receipt info --}}
        <div class="flex items-start gap-3 p-4 rounded-xl bg-success-bg border border-success/20">
            <svg class="w-5 h-5 text-success mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p class="text-sm text-success">A receipt will be automatically generated and made available for download once the payment is recorded.</p>
        </div>

        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('manager.invoices.show', $invoice) }}" class="text-sm border border-border text-secondary hover:bg-surface px-5 py-2.5 rounded-xl transition-colors font-medium">Cancel</a>
            <button type="submit" class="text-sm bg-brand-600 hover:bg-brand-500 text-white px-6 py-2.5 rounded-xl font-semibold transition-colors">
                Record Payment
            </button>
        </div>
    </form>
</div>
@endsection

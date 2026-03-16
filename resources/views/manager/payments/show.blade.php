{{-- resources/views/manager/payments/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Payment Details')
@section('page-title', 'Payment Details')
@section('page-subtitle', 'KES ' . number_format($payment->amount, 2) . ' · ' . $payment->method_label)

@section('sidebar-nav')
    @include('manager.partials.sidebar')
@endsection

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    <a href="{{ route('manager.payments.index') }}" class="inline-flex items-center gap-2 text-sm text-secondary hover:text-primary transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Back to Payments
    </a>

    <div class="bg-white rounded-2xl border border-border p-6 space-y-5">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-3xl font-bold text-primary">KES {{ number_format($payment->amount, 2) }}</p>
                <p class="text-sm text-muted mt-1">{{ $payment->method_label }}
                    @if($payment->mpesa_receipt) · <span class="font-mono">{{ $payment->mpesa_receipt }}</span> @endif
                </p>
            </div>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $payment->status_badge }}">
                {{ ucfirst($payment->status) }}
            </span>
        </div>

        <hr class="border-border">

        <div class="grid grid-cols-2 gap-4 text-sm">
            <div>
                <p class="text-xs text-muted uppercase tracking-wider mb-1">Tenant</p>
                <p class="font-semibold text-primary">{{ $payment->tenant->user->name ?? '—' }}</p>
            </div>
            <div>
                <p class="text-xs text-muted uppercase tracking-wider mb-1">Invoice</p>
                <a href="{{ route('manager.invoices.show', $payment->invoice) }}" class="font-semibold text-brand-600 hover:underline font-mono">
                    {{ $payment->invoice->invoice_number ?? '—' }}
                </a>
            </div>
            <div>
                <p class="text-xs text-muted uppercase tracking-wider mb-1">Payment Date</p>
                <p class="font-medium text-primary">{{ $payment->paid_at ? \Carbon\Carbon::parse($payment->paid_at)->format('d M Y, h:i A') : '—' }}</p>
            </div>
            <div>
                <p class="text-xs text-muted uppercase tracking-wider mb-1">Recorded By</p>
                <p class="font-medium text-primary">{{ $payment->recorder->name ?? 'System' }}</p>
            </div>
            @if($payment->phone_number)
            <div>
                <p class="text-xs text-muted uppercase tracking-wider mb-1">Phone</p>
                <p class="font-medium text-primary">{{ $payment->phone_number }}</p>
            </div>
            @endif
            @if($payment->notes)
            <div class="col-span-2">
                <p class="text-xs text-muted uppercase tracking-wider mb-1">Notes</p>
                <p class="text-secondary">{{ $payment->notes }}</p>
            </div>
            @endif
        </div>

        @if($payment->receipt)
        <hr class="border-border">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-semibold text-success">Receipt: {{ $payment->receipt->receipt_number }}</p>
                <p class="text-xs text-muted">Issued {{ $payment->receipt->issued_at?->format('d M Y') }}</p>
            </div>
            <a href="{{ route('manager.receipts.download', $payment->receipt) }}"
               class="text-sm bg-brand-600 hover:bg-brand-500 text-white px-4 py-2 rounded-xl font-semibold transition-colors">
                Download Receipt
            </a>
        </div>
        @endif
    </div>

    @if($payment->status === 'confirmed')
    <div class="flex justify-end">
        <form action="{{ route('manager.payments.reverse', $payment) }}" method="POST">
            @csrf
            @method('PATCH')
            <button type="submit"
                onclick="return confirm('Reverse this payment? The invoice balance will be restored.')"
                class="text-sm border border-danger text-danger hover:bg-danger-bg px-4 py-2 rounded-xl font-medium transition-colors">
                Reverse Payment
            </button>
        </form>
    </div>
    @endif

</div>
@endsection

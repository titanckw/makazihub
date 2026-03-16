{{-- resources/views/tenant/invoices/index.blade.php --}}
@extends('layouts.tenant')

@section('title', 'My Invoices')

@section('content')
<div class="space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-primary">My Invoices</h1>
            <p class="text-sm text-muted mt-0.5">View and download all your rent invoices</p>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-card rounded-2xl shadow-sm border border-border p-4">
        <form method="GET" class="flex flex-wrap gap-3">
            <select name="status" class="px-3 py-2 rounded-xl border border-border text-sm bg-surface text-secondary focus:outline-none focus:border-brand-500">
                <option value="">All Statuses</option>
                <option value="unpaid"  {{ request('status') === 'unpaid'  ? 'selected' : '' }}>Unpaid</option>
                <option value="partial" {{ request('status') === 'partial' ? 'selected' : '' }}>Partial</option>
                <option value="paid"    {{ request('status') === 'paid'    ? 'selected' : '' }}>Paid</option>
                <option value="overdue" {{ request('status') === 'overdue' ? 'selected' : '' }}>Overdue</option>
            </select>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Search invoice number..."
                   class="px-3 py-2 rounded-xl border border-border text-sm bg-surface text-primary focus:outline-none focus:border-brand-500 w-48">
            <button type="submit" class="px-4 py-2 bg-navy-500 hover:bg-navy-400 text-white text-sm font-semibold rounded-xl">Filter</button>
            <a href="{{ route('tenant.invoices.index') }}" class="px-4 py-2 text-sm font-medium text-secondary hover:text-primary rounded-xl">Clear</a>
        </form>
    </div>

    {{-- Invoice Grid --}}
    @if($invoices->isEmpty())
    <div class="bg-card rounded-2xl shadow-sm border border-border p-12 text-center text-muted">
        <svg class="w-12 h-12 mx-auto mb-3 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
        </svg>
        <p class="font-medium">No invoices found</p>
    </div>
    @else
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @foreach($invoices as $invoice)
        <div class="bg-card rounded-2xl shadow-sm border border-border overflow-hidden hover:shadow-md transition-shadow">
            {{-- Color bar by status --}}
            <div class="h-1.5
                @if($invoice->status === 'paid')    bg-success
                @elseif($invoice->status === 'overdue') bg-danger
                @elseif($invoice->status === 'partial') bg-info
                @else bg-warning @endif"></div>
            <div class="p-5">
                <div class="flex items-start justify-between mb-3">
                    <div>
                        <p class="font-semibold text-primary text-sm">{{ $invoice->invoice_number }}</p>
                        <p class="text-xs text-muted mt-0.5">{{ $invoice->period_start->format('M Y') }}</p>
                    </div>
                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $invoice->status_badge }}">
                        {{ $invoice->status_label }}
                    </span>
                </div>
                <div class="space-y-1.5 mb-4">
                    <div class="flex justify-between text-sm">
                        <span class="text-muted">Amount</span>
                        <span class="font-bold text-primary">KES {{ number_format($invoice->amount, 2) }}</span>
                    </div>
                    @if($invoice->amount_paid > 0)
                    <div class="flex justify-between text-sm">
                        <span class="text-muted">Paid</span>
                        <span class="font-medium text-success">KES {{ number_format($invoice->amount_paid, 2) }}</span>
                    </div>
                    @endif
                    @if($invoice->balance > 0)
                    <div class="flex justify-between text-sm">
                        <span class="text-muted">Balance</span>
                        <span class="font-medium text-danger">KES {{ number_format($invoice->balance, 2) }}</span>
                    </div>
                    @endif
                    <div class="flex justify-between text-sm">
                        <span class="text-muted">Due Date</span>
                        <span class="font-medium {{ $invoice->is_overdue ? 'text-danger' : 'text-primary' }}">
                            {{ $invoice->due_date->format('d M Y') }}
                        </span>
                    </div>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('tenant.invoices.show', $invoice) }}"
                       class="flex-1 text-center py-2 bg-navy-500 hover:bg-navy-400 text-white text-xs font-semibold rounded-xl transition-colors">
                        View
                    </a>
                    <a href="{{ route('tenant.invoices.download', $invoice) }}"
                       class="flex-1 text-center py-2 bg-brand-600 hover:bg-brand-500 text-white text-xs font-semibold rounded-xl transition-colors">
                        Download PDF
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    <div class="mt-2">{{ $invoices->links() }}</div>
    @endif

</div>
@endsection

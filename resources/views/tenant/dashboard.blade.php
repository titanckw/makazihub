@extends('layouts.app')

@section('title', 'My Dashboard')
@section('page-title', 'My Dashboard')
@section('page-subtitle', 'Your tenancy overview')

@section('sidebar-nav')
    @include('manager.partials.sidebar')
@endsection`

@section('content')
    <div class="space-y-6">

        {{-- Active Lease Banner --}}
        @if($activeLease)
            <div class="bg-navy-600 rounded-2xl p-6 relative overflow-hidden">
                <div class="absolute -right-8 -bottom-8 w-48 h-48 bg-brand-600 rounded-full opacity-15 blur-2xl"></div>
                <div class="relative z-10 grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <p class="text-white/50 text-xs mb-1 uppercase tracking-wider">Property</p>
                        <p class="text-white font-600">{{ $activeLease->unit->property->name }}</p>
                        <p class="text-white/50 text-sm">{{ $activeLease->unit->property->address }}</p>
                    </div>
                    <div>
                        <p class="text-white/50 text-xs mb-1 uppercase tracking-wider">Unit</p>
                        <p class="text-white font-600">{{ $activeLease->unit->unit_number }}</p>
                        <p class="text-white/50 text-sm">
                            {{ ucfirst(str_replace('br', ' Bedroom', $activeLease->unit->unit_type)) }}</p>
                    </div>
                    <div>
                        <p class="text-white/50 text-xs mb-1 uppercase tracking-wider">Monthly Rent</p>
                        <p class="font-display text-brand-400 text-2xl font-700">KES
                            {{ number_format($activeLease->rent_amount, 0) }}</p>
                        <p class="text-white/50 text-sm">Due on
                            {{ $activeLease->payment_day }}{{ in_array($activeLease->payment_day, [1, 21, 31]) ? 'st' : (in_array($activeLease->payment_day, [2, 22]) ? 'nd' : (in_array($activeLease->payment_day, [3, 23]) ? 'rd' : 'th')) }}
                            of each month</p>
                    </div>
                </div>
            </div>
        @endif

        {{-- KPI Cards --}}
        <div class="grid grid-cols-2 xl:grid-cols-4 gap-4">
            <x-stat-card label="Outstanding Balance" :value="'KES ' . number_format($stats['current_balance'], 0)"
                color="{{ $stats['current_balance'] > 0 ? 'red' : 'emerald' }}" :icon="'<svg class=\'w-5 h-5\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z\'/></svg>'" />
            <x-stat-card label="Total Paid" :value="'KES ' . number_format($stats['total_paid'], 0)" color="emerald"
                :icon="'<svg class=\'w-5 h-5\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z\'/></svg>'" />
            <x-stat-card label="Paid Invoices" :value="$stats['invoices_paid']" color="emerald" :icon="'<svg class=\'w-5 h-5\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M5 13l4 4L19 7\'/></svg>'" />
            <x-stat-card label="Overdue Invoices" :value="$stats['invoices_overdue']"
                color="{{ $stats['invoices_overdue'] > 0 ? 'red' : 'emerald' }}" :icon="'<svg class=\'w-5 h-5\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z\'/></svg>'" />
        </div>

        {{-- Recent Invoices + Payments --}}
        <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">

            {{-- Recent Invoices --}}
            <div class="bg-card rounded-2xl border border-border shadow-sm">
                <div class="flex items-center justify-between px-6 py-4 border-b border-border">
                    <h3 class="font-display font-700 text-primary">Recent Invoices</h3>
                    <a href="{{ route('tenant.invoices.index') }}"
                        class="text-xs text-brand-600 hover:text-brand-500 font-600">View all</a>
                </div>
                <div class="divide-y divide-border">
                    @forelse($recentInvoices as $invoice)
                        <div class="flex items-center justify-between px-6 py-3">
                            <div>
                                <p class="text-sm font-600 text-primary">{{ $invoice->invoice_number }}</p>
                                <p class="text-xs text-muted">{{ $invoice->billing_period }} · Due
                                    {{ $invoice->due_date->format('d M Y') }}</p>
                            </div>
                            <div class="flex items-center gap-3">
                                <x-badge :status="$invoice->status" />
                                <span class="text-sm font-700 text-primary">KES
                                    {{ number_format($invoice->total_amount, 0) }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="px-6 py-8 text-center text-muted text-sm">No invoices yet.</div>
                    @endforelse
                </div>
            </div>

            {{-- Payment History --}}
            <div class="bg-card rounded-2xl border border-border shadow-sm">
                <div class="flex items-center justify-between px-6 py-4 border-b border-border">
                    <h3 class="font-display font-700 text-primary">Payment History</h3>
                    <a href="{{ route('tenant.payments.index') }}"
                        class="text-xs text-brand-600 hover:text-brand-500 font-600">View all</a>
                </div>
                <div class="divide-y divide-border">
                    @forelse($recentPayments as $payment)
                        <div class="flex items-center justify-between px-6 py-3">
                            <div>
                                <p class="text-sm font-600 text-primary">{{ ucfirst($payment->payment_method) }}</p>
                                <p class="text-xs text-muted">{{ $payment->payment_date->format('d M Y') }}
                                    @if($payment->mpesa_receipt_number)
                                        · {{ $payment->mpesa_receipt_number }}
                                    @endif
                                </p>
                            </div>
                            <span class="text-sm font-700 text-success">KES {{ number_format($payment->amount, 0) }}</span>
                        </div>
                    @empty
                        <div class="px-6 py-8 text-center text-muted text-sm">No payments recorded yet.</div>
                    @endforelse
                </div>
            </div>

        </div>
    </div>
@endsection
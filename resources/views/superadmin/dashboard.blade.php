@extends('layouts.app')

@section('title', 'SuperAdmin Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'System-wide overview')

@section('sidebar-nav')
    @include('manager.partials.sidebar')
@endsection

@section('content')
<div class="space-y-6">

    {{-- KPI Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        <x-stat-card
            label="Total Managers"
            :value="number_format($stats['total_managers'])"
            color="navy"
            :icon="'<svg class=\'w-5 h-5\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z\'/></svg>'"
        />
        <x-stat-card
            label="Total Tenants"
            :value="number_format($stats['total_tenants'])"
            color="blue"
            :icon="'<svg class=\'w-5 h-5\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z\'/></svg>'"
        />
        <x-stat-card
            label="Total Properties"
            :value="number_format($stats['total_properties'])"
            color="emerald"
            :icon="'<svg class=\'w-5 h-5\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4\'/></svg>'"
        />
        <x-stat-card
            label="Total Revenue"
            :value="'KES ' . number_format($stats['total_revenue'], 2)"
            color="emerald"
            :icon="'<svg class=\'w-5 h-5\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z\'/></svg>'"
        />
    </div>

    {{-- Invoice Status Row --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <x-stat-card label="Paid Invoices" :value="number_format($stats['invoices_paid'])" color="emerald"
            :icon="'<svg class=\'w-5 h-5\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z\'/></svg>'"
        />
        <x-stat-card label="Pending Invoices" :value="number_format($stats['invoices_pending'])" color="amber"
            :icon="'<svg class=\'w-5 h-5\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z\'/></svg>'"
        />
        <x-stat-card label="Overdue Invoices" :value="number_format($stats['invoices_overdue'])" color="red"
            :icon="'<svg class=\'w-5 h-5\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z\'/></svg>'"
        />
    </div>

    {{-- Tables Row --}}
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">

        {{-- Recent Payments --}}
        <div class="bg-card rounded-2xl border border-border shadow-sm">
            <div class="flex items-center justify-between px-6 py-4 border-b border-border">
                <h3 class="font-display font-700 text-primary">Recent Payments</h3>
                <span class="text-xs text-muted">Last 10</span>
            </div>
            <div class="divide-y divide-border">
                @forelse($recentPayments as $payment)
                <div class="flex items-center justify-between px-6 py-3">
                    <div>
                        <p class="text-sm font-600 text-primary">{{ $payment->tenant->user->name }}</p>
                        <p class="text-xs text-muted">{{ $payment->payment_date->format('d M Y') }} · {{ ucfirst($payment->payment_method) }}</p>
                    </div>
                    <span class="font-700 text-success text-sm">KES {{ number_format($payment->amount, 2) }}</span>
                </div>
                @empty
                <div class="px-6 py-8 text-center text-muted text-sm">No payments recorded yet.</div>
                @endforelse
            </div>
        </div>

        {{-- Overdue Invoices --}}
        <div class="bg-card rounded-2xl border border-border shadow-sm">
            <div class="flex items-center justify-between px-6 py-4 border-b border-border">
                <h3 class="font-display font-700 text-primary">Overdue Invoices</h3>
                <span class="text-xs bg-danger-bg text-danger px-2 py-1 rounded-lg font-600">{{ $stats['invoices_overdue'] }} overdue</span>
            </div>
            <div class="divide-y divide-border">
                @forelse($overdueInvoices as $invoice)
                <div class="flex items-center justify-between px-6 py-3">
                    <div>
                        <p class="text-sm font-600 text-primary">{{ $invoice->tenant->user->name }}</p>
                        <p class="text-xs text-muted">{{ $invoice->unit->property->name }} · {{ $invoice->unit->unit_number }}</p>
                    </div>
                    <div class="text-right">
                        <p class="font-700 text-danger text-sm">KES {{ number_format($invoice->balance, 2) }}</p>
                        <p class="text-xs text-muted">Due {{ $invoice->due_date->format('d M') }}</p>
                    </div>
                </div>
                @empty
                <div class="px-6 py-8 text-center text-muted text-sm">🎉 No overdue invoices!</div>
                @endforelse
            </div>
        </div>

    </div>
</div>
@endsection

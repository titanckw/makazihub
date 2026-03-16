{{-- resources/views/tenant/dashboard/index.blade.php --}}
@extends('layouts.tenant')

@section('title', 'My Dashboard')

@section('content')
<div class="space-y-6">

    {{-- Welcome banner --}}
    <div class="bg-navy-600 rounded-2xl p-6 text-white">
        <div class="flex items-start justify-between gap-4">
            <div>
                <p class="text-white/60 text-sm">Welcome back,</p>
                <h1 class="text-2xl font-bold mt-0.5">{{ auth()->user()->name }}</h1>
                @if($tenant->activeLease)
                <p class="text-white/70 text-sm mt-2 flex items-center gap-2">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    {{ $tenant->activeLease->unit->unit_number }} — {{ $tenant->activeLease->unit->property->name }}
                </p>
                @endif
            </div>
            <div class="w-12 h-12 bg-brand-600 rounded-xl flex items-center justify-center text-white font-bold text-lg shrink-0">
                {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
            </div>
        </div>
    </div>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-card rounded-2xl p-5 border-t-4 border-brand-600 shadow-sm">
            <p class="text-sm text-muted">Total Paid</p>
            <p class="text-2xl font-bold text-primary mt-1">KES {{ number_format($totalPaid, 0) }}</p>
            <p class="text-xs text-muted mt-1">Lifetime payments</p>
        </div>
        <div class="bg-card rounded-2xl p-5 border-t-4 border-warning shadow-sm">
            <p class="text-sm text-muted">Amount Owing</p>
            <p class="text-2xl font-bold text-warning mt-1">KES {{ number_format($totalOwing, 0) }}</p>
            <p class="text-xs text-muted mt-1">Outstanding balance</p>
        </div>
        <div class="bg-card rounded-2xl p-5 border-t-4 border-danger shadow-sm">
            <p class="text-sm text-muted">Overdue</p>
            <p class="text-2xl font-bold text-danger mt-1">{{ $overdueCount }}</p>
            <p class="text-xs text-muted mt-1">Invoices overdue</p>
        </div>
        <div class="bg-card rounded-2xl p-5 border-t-4 border-navy-500 shadow-sm">
            <p class="text-sm text-muted">Total Invoices</p>
            <p class="text-2xl font-bold text-primary mt-1">{{ $totalInvoices }}</p>
            <p class="text-xs text-muted mt-1">All time</p>
        </div>
    </div>

    <div class="grid lg:grid-cols-2 gap-6">

        {{-- Active Lease Card --}}
        @if($tenant->activeLease)
        <div class="bg-card rounded-2xl shadow-sm border border-border p-5">
            <div class="flex items-center justify-between mb-4">
                <h2 class="font-semibold text-primary">Active Lease</h2>
                <a href="{{ route('tenant.lease.show') }}"
                   class="text-xs text-brand-600 hover:text-brand-500 font-medium">View Details →</a>
            </div>
            @php $lease = $tenant->activeLease; @endphp
            <div class="space-y-3">
                <div class="flex justify-between text-sm">
                    <span class="text-muted">Property</span>
                    <span class="font-medium text-primary">{{ $lease->unit->property->name }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-muted">Unit</span>
                    <span class="font-medium text-primary">{{ $lease->unit->unit_number }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-muted">Monthly Rent</span>
                    <span class="font-bold text-brand-600">KES {{ number_format($lease->monthly_rent, 2) }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-muted">Start Date</span>
                    <span class="font-medium text-primary">{{ $lease->start_date->format('d M Y') }}</span>
                </div>
                @if($lease->end_date)
                <div class="flex justify-between text-sm">
                    <span class="text-muted">End Date</span>
                    <span class="font-medium text-primary">{{ $lease->end_date->format('d M Y') }}</span>
                </div>
                @php $daysLeft = (int) now()->diffInDays($lease->end_date, false); @endphp
                @if($daysLeft <= 30 && $daysLeft >= 0)
                <div class="mt-3 p-3 bg-warning-bg rounded-xl text-warning text-xs font-medium">
                    ⚠️ Your lease expires in {{ $daysLeft }} day(s). Contact your manager to renew.
                </div>
                @endif
                @endif
                <div class="flex justify-between text-sm">
                    <span class="text-muted">Status</span>
                    <span class="px-2.5 py-0.5 bg-success-bg text-success text-xs font-semibold rounded-full">Active</span>
                </div>
            </div>
        </div>
        @else
        <div class="bg-card rounded-2xl shadow-sm border border-border p-5 flex items-center justify-center text-muted">
            <p class="text-sm">No active lease found.</p>
        </div>
        @endif

        {{-- Recent Invoices --}}
        <div class="bg-card rounded-2xl shadow-sm border border-border overflow-hidden">
            <div class="p-5 border-b border-border flex items-center justify-between">
                <h2 class="font-semibold text-primary">Recent Invoices</h2>
                <a href="{{ route('tenant.invoices.index') }}"
                   class="text-xs text-brand-600 hover:text-brand-500 font-medium">View All →</a>
            </div>
            @if($recentInvoices->isEmpty())
            <div class="p-8 text-center text-muted text-sm">No invoices yet.</div>
            @else
            <div class="divide-y divide-border">
                @foreach($recentInvoices as $invoice)
                <a href="{{ route('tenant.invoices.show', $invoice) }}"
                   class="flex items-center justify-between px-5 py-3.5 hover:bg-surface/60 transition-colors">
                    <div>
                        <p class="text-sm font-medium text-primary">{{ $invoice->invoice_number }}</p>
                        <p class="text-xs text-muted mt-0.5">Due {{ $invoice->due_date->format('d M Y') }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-semibold text-primary">KES {{ number_format($invoice->amount, 2) }}</p>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $invoice->status_badge }}">
                            {{ $invoice->status_label }}
                        </span>
                    </div>
                </a>
                @endforeach
            </div>
            @endif
        </div>

    </div>

    {{-- Recent Notifications --}}
    @if($recentNotifications->isNotEmpty())
    <div class="bg-card rounded-2xl shadow-sm border border-border overflow-hidden">
        <div class="p-5 border-b border-border">
            <h2 class="font-semibold text-primary">Recent Notifications</h2>
        </div>
        <div class="divide-y divide-border">
            @foreach($recentNotifications as $notif)
            <div class="flex items-start gap-4 px-5 py-3.5">
                <div class="w-8 h-8 rounded-full flex items-center justify-center shrink-0
                    {{ $notif->channel === 'sms' ? 'bg-info-bg' : 'bg-navy-100' }}">
                    @if($notif->channel === 'sms')
                    <svg class="w-4 h-4 text-info" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                    @else
                    <svg class="w-4 h-4 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    @endif
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-primary">{{ $notif->type_label }}</p>
                    <p class="text-xs text-muted mt-0.5 truncate">{{ $notif->message }}</p>
                </div>
                <p class="text-xs text-muted shrink-0">{{ $notif->created_at->diffForHumans() }}</p>
            </div>
            @endforeach
        </div>
    </div>
    @endif

</div>
@endsection

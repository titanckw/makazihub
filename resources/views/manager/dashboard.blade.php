@extends('layouts.app')

@section('title', 'Manager Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Your properties at a glance')

@section('sidebar-nav')
    @include('manager.partials.sidebar')
@endsection

@section('content')
    <div class="space-y-6">

        {{-- Welcome Banner --}}
        <div class="bg-navy-600 rounded-2xl p-6 flex items-center justify-between overflow-hidden relative">
            <div class="absolute -right-8 -top-8 w-40 h-40 bg-brand-600 rounded-full opacity-20 blur-2xl"></div>
            <div class="relative z-10">
                <p class="text-white/60 text-sm mb-1">Good
                    {{ now()->hour < 12 ? 'morning' : (now()->hour < 17 ? 'afternoon' : 'evening') }},
                </p>
                <h2 class="font-display text-white text-2xl font-700">{{ auth()->user()->first_name }} 👋</h2>
                <p class="text-white/50 text-sm mt-1">{{ now()->format('l, d F Y') }}</p>
            </div>
            <div class="relative z-10 text-right hidden sm:block">
                <p class="text-white/50 text-xs mb-1">This Month's Revenue</p>
                <p class="font-display text-brand-400 text-3xl font-700">KES
                    {{ number_format($stats['monthly_revenue'], 0) }}
                </p>
            </div>
        </div>

        {{-- KPI Cards --}}
        <div class="grid grid-cols-2 xl:grid-cols-4 gap-4">
            <x-stat-card label="Total Units" :value="number_format($stats['total_units'])" color="navy" :icon="'<svg class=\'w-5 h-5\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4\'/></svg>'" />
            <x-stat-card label="Total Tenants" :value="number_format($stats['total_tenants'])" color="brand" :icon="'<svg class=\'w-5 h-5\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M17 20h5v-2a3 3 0 00-5.856-1.487M15 10a3 3 0 11-6 0 3 3 0 016 0zM15 20H9m6 0h4v-2a4 4 0 00-8 0v2zM1 20h5v-2a4 4 0 015.773-3.694M13 10a4 4 0 11-8 0 4 4 0 018 0z\'/></svg>'" />
            <x-stat-card label="Occupied" :value="number_format($stats['occupied_units'])" color="emerald" :icon="'<svg class=\'w-5 h-5\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z\'/></svg>'" />
            <x-stat-card label="Vacant" :value="number_format($stats['vacant_units'])" color="blue" :icon="'<svg class=\'w-5 h-5\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z\'/></svg>'" />
        </div>

        {{-- Invoice Status --}}
        <div class="grid grid-cols-2 xl:grid-cols-4 gap-4">
            <x-stat-card label="Paid" :value="$stats['invoices_paid']" color="emerald" :icon="'<svg class=\'w-5 h-5\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M5 13l4 4L19 7\'/></svg>'" />
            <x-stat-card label="Partial" :value="$stats['invoices_partial']" color="blue" :icon="'<svg class=\'w-5 h-5\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M12 8v4l3 3\'/><circle cx=\'12\' cy=\'12\' r=\'9\' stroke=\'currentColor\' stroke-width=\'2\'/></svg>'" />
            <x-stat-card label="Unpaid" :value="$stats['invoices_unpaid']" color="amber" :icon="'<svg class=\'w-5 h-5\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z\'/></svg>'" />
            <x-stat-card label="Overdue" :value="$stats['invoices_overdue']" color="red" :icon="'<svg class=\'w-5 h-5\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z\'/></svg>'" />
        </div>

        {{-- Properties + Overdue + Recent Payments --}}
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

            {{-- Properties --}}
            <div class="bg-card rounded-2xl border border-border shadow-sm">
                <div class="flex items-center justify-between px-6 py-4 border-b border-border">
                    <h3 class="font-display font-700 text-primary">My Properties</h3>
                    <a href="{{ route('manager.properties.create') }}"
                        class="text-xs bg-brand-600 hover:bg-brand-500 text-white px-3 py-1.5 rounded-lg font-600 transition-colors">+
                        Add</a>
                </div>
                <div class="divide-y divide-border">
                    @forelse($properties as $property)
                        <div class="px-6 py-3 flex items-center justify-between">
                            <div>
                                <p class="text-sm font-600 text-primary">{{ $property->name }}</p>
                                <p class="text-xs text-muted">{{ $property->city }} · {{ $property->units_count }} units</p>
                            </div>
                            <div class="text-right">
                                <span class="text-xs font-600 text-success">{{ $property->occupied_count }} occupied</span>
                            </div>
                        </div>
                    @empty
                        <div class="px-6 py-8 text-center text-muted text-sm">No properties yet.</div>
                    @endforelse
                </div>
            </div>

            {{-- Maintenance Requests --}}
            <div class="bg-card rounded-2xl border border-border shadow-sm">
                <div class="flex items-center justify-between px-6 py-4 border-b border-border">
                    <div>
                        <h3 class="font-display font-700 text-primary">Maintenance</h3>
                        <p class="text-xs text-muted">Total requests: <span class="font-semibold text-primary">{{ number_format($stats['maintenance_requests_total']) }}</span></p>
                    </div>
                    <a href="{{ route('manager.maintenance.index') }}"
                        class="text-xs text-brand-600 hover:text-brand-500 font-600">View all</a>
                </div>
                <div class="divide-y divide-border">
                    @forelse($maintenanceRequests as $request)
                        <a href="{{ route('manager.maintenance.show', $request) }}"
                            class="px-6 py-3 flex items-center justify-between hover:bg-muted/30 transition-colors">
                            <div>
                                <p class="text-sm font-600 text-primary">{{ $request->tenant->user->name ?? 'Unknown' }}</p>
                                <p class="text-xs text-muted">{{ $request->repair_type }} · {{ $request->unit->unit_number ?? 'N/A' }}</p>
                            </div>
                            <span class="text-xs font-600 px-2 py-1 rounded {{ $request->status_badge }}">{{ $request->status_label }}</span>
                        </a>
                    @empty
                        <div class="px-6 py-8 text-center text-muted text-sm">No maintenance requests.</div>
                    @endforelse
                </div>
            </div>

            {{-- Recent Payments --}}
            <div class="bg-card rounded-2xl border border-border shadow-sm">
                <div class="flex items-center justify-between px-6 py-4 border-b border-border">
                    <h3 class="font-display font-700 text-primary">Recent Payments</h3>
                    <a href="{{ route('manager.payments.index') }}"
                        class="text-xs text-brand-600 hover:text-brand-500 font-600">View all</a>
                </div>
                <div class="divide-y divide-border">
                    @forelse($recentPayments as $payment)
                        <div class="px-6 py-3 flex items-center justify-between">
                            <div>
                                <p class="text-sm font-600 text-primary">{{ $payment->tenant->user->name }}</p>
                                <p class="text-xs text-muted">{{ $payment->payment_date->format('d M Y') }}</p>
                            </div>
                            <p class="text-sm font-700 text-success">KES {{ number_format($payment->amount, 0) }}</p>
                        </div>
                    @empty
                        <div class="px-6 py-8 text-center text-muted text-sm">No payments yet.</div>
                    @endforelse
                </div>
            </div>

        </div>

        {{-- Overdue Invoices --}}
        <div class="bg-card rounded-2xl border border-border shadow-sm">
            <div class="flex items-center justify-between px-6 py-4 border-b border-border">
                <h3 class="font-display font-700 text-primary">Overdue</h3>
                @if($stats['invoices_overdue'] > 0)
                    <span
                        class="text-xs bg-danger-bg text-danger px-2 py-1 rounded-lg font-600">{{ $stats['invoices_overdue'] }}</span>
                @endif
            </div>
            <div class="divide-y divide-border">
                @forelse($overdueInvoices as $invoice)
                    <div class="px-6 py-3 flex items-center justify-between">
                        <div>
                            <p class="text-sm font-600 text-primary">{{ $invoice->tenant->user->name ?? 'Unknown' }}</p>
                            <p class="text-xs text-muted">{{ $invoice->unit?->unit_number ?? 'N/A' }} · Due
                                {{ $invoice->due_date?->format('d M') ?? 'N/A' }}
                            </p>
                        </div>
                        <p class="text-sm font-700 text-danger">KES
                            {{ number_format(is_array($invoice->balance) ? 0 : ($invoice->balance ?? 0), 0) }}</p>
                    </div>
                @empty
                    <div class="px-6 py-8 text-center text-muted text-sm">🎉 All clear!</div>
                @endforelse
            </div>
        </div>

        {{-- Staff Management Summary --}}
        <div class="bg-card rounded-2xl border border-border shadow-sm">
            <div class="flex items-center justify-between px-6 py-4 border-b border-border">
                <h3 class="font-display font-700 text-primary">Staff Management</h3>
                <a href="{{ route('manager.staff.index') }}" class="text-xs text-brand-600 hover:text-brand-500 font-600">Manage staff</a>
            </div>

            <div class="grid grid-cols-2 lg:grid-cols-5 gap-4 px-6 py-5">
                <a href="{{ route('manager.staff.index') }}" class="block">
                    <p class="text-xs text-muted">Active Staff</p>
                    <p class="text-2xl font-700 text-primary mt-1">{{ $staffStats['total_staff'] }}</p>
                </a>
                <a href="{{ route('manager.attendance.index') }}" class="block">
                    <p class="text-xs text-muted">Present Today</p>
                    <p class="text-2xl font-700 text-success mt-1">{{ $staffStats['present_today'] }}</p>
                </a>
                <a href="{{ route('manager.leave.index') }}" class="block">
                    <p class="text-xs text-muted">Pending Leave</p>
                    <p class="text-2xl font-700 {{ $staffStats['pending_leave'] > 0 ? 'text-warning' : 'text-primary' }} mt-1">{{ $staffStats['pending_leave'] }}</p>
                </a>
                <a href="{{ route('manager.documents.index') }}" class="block">
                    <p class="text-xs text-muted">Documents Stored</p>
                    <p class="text-2xl font-700 text-primary mt-1">{{ $staffStats['total_documents'] }}</p>
                </a>
                <a href="{{ route('manager.chat.index') }}" class="block">
                    <p class="text-xs text-muted">Unread Messages</p>
                    <p class="text-2xl font-700 {{ $staffStats['unread_messages'] > 0 ? 'text-danger' : 'text-primary' }} mt-1">{{ $staffStats['unread_messages'] }}</p>
                </a>
            </div>

            @if($pendingLeaveRequests->isNotEmpty())
                <div class="border-t border-border divide-y divide-border">
                    <p class="px-6 pt-4 pb-1 text-xs font-semibold text-secondary uppercase tracking-wider">Pending Leave Requests</p>
                    @foreach($pendingLeaveRequests as $leave)
                        <a href="{{ route('manager.leave.index') }}" class="px-6 py-3 flex items-center justify-between hover:bg-muted/30 transition-colors">
                            <div>
                                <p class="text-sm font-600 text-primary">{{ $leave->staff->user->name }}</p>
                                <p class="text-xs text-muted">{{ $leave->type_label }} · {{ $leave->start_date->format('M d') }} – {{ $leave->end_date->format('M d, Y') }}</p>
                            </div>
                            <span class="text-xs font-600 px-2 py-1 rounded {{ $leave->status_badge }}">{{ ucfirst($leave->status) }}</span>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@endsection
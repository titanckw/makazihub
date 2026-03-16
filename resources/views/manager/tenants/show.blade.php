@extends('layouts.app')

@section('title', $tenant->user->name)
@section('page-title', $tenant->user->name)
@section('page-subtitle', 'Tenant profile & lease history')

@section('sidebar-nav')
    @include('manager.partials.sidebar')
@endsection

@section('content')
<div class="space-y-6">

    {{-- Header Actions --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <a href="{{ route('manager.tenants.index') }}" class="inline-flex items-center gap-2 text-sm text-secondary hover:text-primary transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Back to Tenants
        </a>
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $tenant->status_badge }}">
                {{ ucfirst($tenant->status) }}
            </span>
            <a href="{{ route('manager.leases.create', ['tenant_id' => $tenant->id]) }}"
               class="text-sm bg-brand-600 hover:bg-brand-500 text-white px-4 py-2 rounded-xl font-semibold transition-colors">
                + New Lease
            </a>
            <a href="{{ route('manager.tenants.edit', $tenant) }}"
               class="text-sm bg-navy-500 hover:bg-navy-400 text-white px-4 py-2 rounded-xl font-medium transition-colors">
                Edit
            </a>
        </div>
    </div>

    {{-- KPI Row --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white rounded-2xl border-t-4 border-brand-600 border-x border-b border-border p-5">
            <p class="text-xs font-semibold text-muted uppercase tracking-wider mb-1">Total Paid</p>
            <p class="text-2xl font-bold text-primary">KES {{ number_format($totalPaid, 2) }}</p>
        </div>
        <div class="bg-white rounded-2xl border-t-4 border-danger border-x border-b border-border p-5">
            <p class="text-xs font-semibold text-muted uppercase tracking-wider mb-1">Outstanding</p>
            <p class="text-2xl font-bold text-primary">KES {{ number_format($totalOverdue, 2) }}</p>
        </div>
        <div class="bg-white rounded-2xl border-t-4 border-navy-500 border-x border-b border-border p-5">
            <p class="text-xs font-semibold text-muted uppercase tracking-wider mb-1">Active Leases</p>
            <p class="text-2xl font-bold text-primary">{{ $activeLeases->count() }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Details --}}
        <div class="lg:col-span-1 space-y-4">
            <div class="bg-white rounded-2xl border border-border p-6 space-y-4">
                <h2 class="text-xs font-bold text-secondary uppercase tracking-wider">Contact Details</h2>
                <div class="space-y-3">
                    <div>
                        <p class="text-xs text-muted">Email</p>
                        <p class="text-sm text-primary font-medium">{{ $tenant->user->email }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-muted">Phone</p>
                        <p class="text-sm text-primary font-medium">{{ $tenant->user->phone ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-muted">ID / Passport</p>
                        <p class="text-sm text-primary font-mono font-medium">{{ $tenant->id_number }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-muted">Occupation</p>
                        <p class="text-sm text-primary">{{ $tenant->occupation ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-muted">Employer</p>
                        <p class="text-sm text-primary">{{ $tenant->employer ?? '—' }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-border p-6 space-y-4">
                <h2 class="text-xs font-bold text-secondary uppercase tracking-wider">Emergency Contact</h2>
                <div class="space-y-3">
                    <div>
                        <p class="text-xs text-muted">Name</p>
                        <p class="text-sm text-primary">{{ $tenant->emergency_contact_name ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-muted">Phone</p>
                        <p class="text-sm text-primary">{{ $tenant->emergency_contact_phone ?? '—' }}</p>
                    </div>
                </div>
            </div>

            @if($tenant->notes)
            <div class="bg-white rounded-2xl border border-border p-6">
                <h2 class="text-xs font-bold text-secondary uppercase tracking-wider mb-3">Notes</h2>
                <p class="text-sm text-secondary">{{ $tenant->notes }}</p>
            </div>
            @endif
        </div>

        {{-- Leases --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl border border-border overflow-hidden">
                <div class="px-6 py-4 border-b border-border flex items-center justify-between">
                    <h2 class="text-sm font-bold text-primary">Lease History</h2>
                    <span class="text-xs text-muted">{{ $tenant->leases->count() }} total</span>
                </div>
                @forelse($tenant->leases as $lease)
                <div class="px-6 py-4 border-b border-border last:border-0 hover:bg-surface/60 transition-colors">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="font-semibold text-primary text-sm">
                                {{ $lease->unit->unit_number ?? '—' }}
                                <span class="text-muted font-normal">@ {{ $lease->unit->property->name ?? '—' }}</span>
                            </p>
                            <p class="text-xs text-muted mt-0.5">
                                {{ $lease->start_date->format('d M Y') }} → {{ $lease->end_date->format('d M Y') }}
                            </p>
                            <p class="text-xs text-secondary mt-1">
                                Rent: <strong>KES {{ number_format($lease->rent_amount, 2) }}/mo</strong>
                            </p>
                        </div>
                        <div class="flex flex-col items-end gap-2 shrink-0">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $lease->status_badge }}">
                                {{ $lease->status_label }}
                            </span>
                            <a href="{{ route('manager.leases.show', $lease) }}" class="text-xs text-brand-600 hover:underline font-medium">
                                View Lease →
                            </a>
                        </div>
                    </div>
                </div>
                @empty
                <div class="py-12 text-center text-muted text-sm">
                    No leases yet.
                    <a href="{{ route('manager.leases.create', ['tenant_id' => $tenant->id]) }}" class="text-brand-600 font-medium hover:underline">Create one</a>.
                </div>
                @endforelse
            </div>
        </div>

    </div>
</div>
@endsection

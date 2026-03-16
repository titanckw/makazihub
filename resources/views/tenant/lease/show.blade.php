{{-- resources/views/tenant/lease/show.blade.php --}}
@extends('layouts.tenant')

@section('title', 'My Lease')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    <div>
        <h1 class="text-2xl font-bold text-primary">My Lease</h1>
        <p class="text-sm text-muted mt-0.5">Your current tenancy details</p>
    </div>

    @if(!$lease)
    <div class="bg-card rounded-2xl shadow-sm border border-border p-12 text-center text-muted">
        <p class="font-medium">No active lease found.</p>
        <p class="text-sm mt-1">Contact your property manager if you believe this is an error.</p>
    </div>
    @else

    {{-- Expiry warning --}}
    @if($daysRemaining !== null && $daysRemaining <= 30 && $daysRemaining >= 0)
    <div class="flex items-start gap-3 p-4 bg-warning-bg border border-warning/20 rounded-xl text-warning text-sm font-medium">
        <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
        </svg>
        Your lease expires in <strong class="mx-1">{{ $daysRemaining }} day(s)</strong> on {{ $lease->end_date->format('d M Y') }}. Please contact your property manager to discuss renewal.
    </div>
    @elseif($daysRemaining !== null && $daysRemaining < 0)
    <div class="flex items-start gap-3 p-4 bg-danger-bg border border-danger/20 rounded-xl text-danger text-sm font-medium">
        <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
        </svg>
        Your lease expired on {{ $lease->end_date->format('d M Y') }}. Please contact your property manager immediately.
    </div>
    @endif

    {{-- Property & Unit --}}
    <div class="bg-card rounded-2xl shadow-sm border border-border overflow-hidden">
        <div class="bg-navy-600 p-5">
            <p class="text-white/60 text-xs uppercase tracking-widest mb-1">Your Home</p>
            <h2 class="text-white text-xl font-bold">{{ $lease->unit->unit_number }}</h2>
            <p class="text-white/70 text-sm mt-0.5">{{ $lease->unit->property->name }}</p>
            @if($lease->unit->property->address)
            <p class="text-white/50 text-xs mt-1">{{ $lease->unit->property->address }}</p>
            @endif
        </div>
        <div class="p-5 space-y-3">
            <div class="flex justify-between text-sm py-2 border-b border-border">
                <span class="text-muted">Unit Type</span>
                <span class="font-medium text-primary capitalize">{{ $lease->unit->unit_type ?? '—' }}</span>
            </div>
            <div class="flex justify-between text-sm py-2 border-b border-border">
                <span class="text-muted">Floor</span>
                <span class="font-medium text-primary">{{ $lease->unit->floor ?? '—' }}</span>
            </div>
            <div class="flex justify-between text-sm py-2">
                <span class="text-muted">Bedrooms</span>
                <span class="font-medium text-primary">{{ $lease->unit->bedrooms ?? '—' }}</span>
            </div>
        </div>
    </div>

    {{-- Lease Terms --}}
    <div class="bg-card rounded-2xl shadow-sm border border-border p-5">
        <h2 class="font-semibold text-primary mb-4">Lease Terms</h2>
        <div class="space-y-3">
            <div class="flex justify-between text-sm py-2 border-b border-border">
                <span class="text-muted">Status</span>
                <span class="px-2.5 py-0.5 bg-success-bg text-success text-xs font-semibold rounded-full">Active</span>
            </div>
            <div class="flex justify-between text-sm py-2 border-b border-border">
                <span class="text-muted">Start Date</span>
                <span class="font-medium text-primary">{{ $lease->start_date->format('d M Y') }}</span>
            </div>
            <div class="flex justify-between text-sm py-2 border-b border-border">
                <span class="text-muted">End Date</span>
                <span class="font-medium text-primary">
                    {{ $lease->end_date ? $lease->end_date->format('d M Y') : 'Month-to-month' }}
                </span>
            </div>
            @if($daysRemaining !== null && $daysRemaining >= 0)
            <div class="flex justify-between text-sm py-2 border-b border-border">
                <span class="text-muted">Days Remaining</span>
                <span class="font-bold {{ $daysRemaining <= 30 ? 'text-warning' : 'text-primary' }}">
                    {{ $daysRemaining }} days
                </span>
            </div>
            @endif
            <div class="flex justify-between text-sm py-2 border-b border-border">
                <span class="text-muted">Monthly Rent</span>
                <span class="font-bold text-brand-600 text-base">KES {{ number_format($lease->monthly_rent, 2) }}</span>
            </div>
            @if($lease->deposit_amount)
            <div class="flex justify-between text-sm py-2 border-b border-border">
                <span class="text-muted">Security Deposit</span>
                <span class="font-medium text-primary">KES {{ number_format($lease->deposit_amount, 2) }}</span>
            </div>
            @endif
            @if($lease->payment_due_day)
            <div class="flex justify-between text-sm py-2">
                <span class="text-muted">Rent Due Day</span>
                <span class="font-medium text-primary">{{ $lease->payment_due_day }}{{ match((int)$lease->payment_due_day) { 1=>'st',2=>'nd',3=>'rd',default=>'th' } }} of each month</span>
            </div>
            @endif
        </div>
    </div>

    {{-- Property Manager Contact --}}
    @if($lease->unit->property->manager)
    <div class="bg-card rounded-2xl shadow-sm border border-border p-5">
        <h2 class="font-semibold text-primary mb-4">Property Manager</h2>
        <div class="flex items-center gap-4">
            <div class="w-10 h-10 rounded-full bg-navy-500 flex items-center justify-center text-white font-bold shrink-0">
                {{ strtoupper(substr($lease->unit->property->manager->name, 0, 2)) }}
            </div>
            <div>
                <p class="font-semibold text-primary">{{ $lease->unit->property->manager->name }}</p>
                <p class="text-sm text-muted">{{ $lease->unit->property->manager->email }}</p>
                @if($lease->unit->property->manager->phone)
                <p class="text-sm text-muted">{{ $lease->unit->property->manager->phone }}</p>
                @endif
            </div>
        </div>
    </div>
    @endif

    @endif

</div>
@endsection

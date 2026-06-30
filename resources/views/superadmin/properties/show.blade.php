@extends('layouts.app')

@section('title', $property->name)
@section('page-title', $property->name)
@section('page-subtitle', $property->city . ', ' . $property->county)

@section('sidebar-nav')
    @include('superadmin.partials.sidebar')
@endsection

@section('content')
<div class="space-y-6">

    <a href="{{ route('superadmin.properties.index') }}" class="inline-flex items-center gap-1.5 text-sm text-secondary hover:text-primary">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
        Back to properties
    </a>

    {{-- Summary Card --}}
    <div class="bg-white rounded-2xl border border-border p-6">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <p class="font-display font-700 text-primary text-lg">{{ $property->name }}</p>
                <p class="text-sm text-muted mt-0.5">{{ $property->address }}, {{ $property->city }}, {{ $property->county }}</p>
                <p class="text-xs text-muted mt-1">Managed by {{ optional($property->manager)->name ?? '—' }}</p>
            </div>
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $property->is_active ? 'bg-success-bg text-success' : 'bg-danger-bg text-danger' }}">
                {{ $property->is_active ? 'Active' : 'Inactive' }}
            </span>
        </div>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl border border-border p-4">
            <p class="text-xs text-muted">Total Units</p>
            <p class="text-xl font-700 text-primary mt-1">{{ $stats['total'] }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-border p-4">
            <p class="text-xs text-muted">Occupied</p>
            <p class="text-xl font-700 text-primary mt-1">{{ $stats['occupied'] }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-border p-4">
            <p class="text-xs text-muted">Vacant</p>
            <p class="text-xl font-700 text-primary mt-1">{{ $stats['vacant'] }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-border p-4">
            <p class="text-xs text-muted">Monthly Income</p>
            <p class="text-xl font-700 text-primary mt-1">KSh {{ number_format($stats['monthly_income'], 0) }}</p>
        </div>
    </div>

    {{-- Units Table --}}
    <div class="bg-white rounded-2xl border border-border overflow-hidden">
        @if($units->isEmpty())
            <div class="py-16 text-center">
                <p class="text-muted text-sm">No units recorded for this property.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-surface border-b border-border">
                            <th class="text-left px-6 py-3 text-xs font-semibold text-secondary uppercase tracking-wider">Unit</th>
                            <th class="text-left px-6 py-3 text-xs font-semibold text-secondary uppercase tracking-wider">Type</th>
                            <th class="text-left px-6 py-3 text-xs font-semibold text-secondary uppercase tracking-wider">Rent</th>
                            <th class="text-left px-6 py-3 text-xs font-semibold text-secondary uppercase tracking-wider">Status</th>
                            <th class="text-left px-6 py-3 text-xs font-semibold text-secondary uppercase tracking-wider">Tenant</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        @foreach($units as $unit)
                        <tr class="hover:bg-surface/60 transition-colors">
                            <td class="px-6 py-4 font-semibold text-primary">{{ $unit->unit_number }}</td>
                            <td class="px-6 py-4 text-secondary">{{ ucfirst($unit->unit_type) }}</td>
                            <td class="px-6 py-4 text-secondary">KSh {{ number_format($unit->rent_amount, 0) }}</td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold
                                    {{ $unit->status === 'occupied' ? 'bg-success-bg text-success' : ($unit->status === 'maintenance' ? 'bg-warning-bg text-warning' : 'bg-surface text-secondary') }}">
                                    {{ ucfirst($unit->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-secondary text-xs">
                                {{ optional(optional(optional($unit->activeLease)->tenant)->user)->name ?? '—' }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

</div>
@endsection

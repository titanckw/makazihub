@extends('layouts.app')

@section('title', 'Leases')
@section('page-title', 'Leases')
@section('page-subtitle', 'All rental agreements across your properties')

@section('sidebar-nav')
    @include('manager.partials.sidebar')
@endsection

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <p class="text-sm text-muted">{{ $leases->total() }} lease{{ $leases->total() !== 1 ? 's' : '' }} found</p>
        <a href="{{ route('manager.leases.create') }}"
           class="inline-flex items-center gap-2 bg-brand-600 hover:bg-brand-500 text-white text-sm font-semibold px-4 py-2.5 rounded-xl transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            New Lease
        </a>
    </div>

    {{-- Expiry Warning --}}
    @if($expiringCount > 0)
    <div class="flex items-start gap-3 p-4 rounded-xl bg-warning-bg border border-warning/20">
        <svg class="w-5 h-5 text-warning mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
        </svg>
        <p class="text-sm text-warning font-medium">
            <strong>{{ $expiringCount }} lease{{ $expiringCount > 1 ? 's' : '' }}</strong> expiring within 30 days — review and renew if needed.
        </p>
    </div>
    @endif

    {{-- Filters --}}
    <div class="bg-white rounded-2xl border border-border p-4">
        <form method="GET" class="flex flex-wrap gap-3 items-end">
            <div class="min-w-[160px]">
                <label class="block text-xs font-semibold text-secondary mb-1.5">Property</label>
                <select name="property_id" class="w-full text-sm border border-border rounded-xl px-3 py-2 bg-surface focus:outline-none focus:ring-2 focus:ring-brand-400">
                    <option value="">All Properties</option>
                    @foreach($properties as $property)
                        <option value="{{ $property->id }}" {{ request('property_id') == $property->id ? 'selected' : '' }}>
                            {{ $property->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="min-w-[140px]">
                <label class="block text-xs font-semibold text-secondary mb-1.5">Status</label>
                <select name="status" class="w-full text-sm border border-border rounded-xl px-3 py-2 bg-surface focus:outline-none focus:ring-2 focus:ring-brand-400">
                    <option value="">All</option>
                    <option value="active"     {{ request('status') === 'active'     ? 'selected' : '' }}>Active</option>
                    <option value="expired"    {{ request('status') === 'expired'    ? 'selected' : '' }}>Expired</option>
                    <option value="terminated" {{ request('status') === 'terminated' ? 'selected' : '' }}>Terminated</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="text-sm bg-navy-500 hover:bg-navy-400 text-white px-4 py-2 rounded-xl font-medium transition-colors">Filter</button>
                @if(request()->hasAny(['property_id','status']))
                    <a href="{{ route('manager.leases.index') }}" class="text-sm border border-border text-secondary hover:bg-surface px-4 py-2 rounded-xl transition-colors">Clear</a>
                @endif
            </div>
        </form>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-2xl border border-border overflow-hidden">
        @if($leases->isEmpty())
            <div class="py-16 text-center">
                <svg class="w-12 h-12 text-muted mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <p class="text-muted text-sm">No leases found. <a href="{{ route('manager.leases.create') }}" class="text-brand-600 font-semibold hover:underline">Create the first lease</a>.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-surface border-b border-border">
                            <th class="text-left px-6 py-3 text-xs font-semibold text-secondary uppercase tracking-wider">Tenant</th>
                            <th class="text-left px-6 py-3 text-xs font-semibold text-secondary uppercase tracking-wider">Unit</th>
                            <th class="text-left px-6 py-3 text-xs font-semibold text-secondary uppercase tracking-wider">Period</th>
                            <th class="text-left px-6 py-3 text-xs font-semibold text-secondary uppercase tracking-wider">Rent/mo</th>
                            <th class="text-left px-6 py-3 text-xs font-semibold text-secondary uppercase tracking-wider">Status</th>
                            <th class="text-right px-6 py-3 text-xs font-semibold text-secondary uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        @foreach($leases as $lease)
                        <tr class="hover:bg-surface/60 transition-colors {{ $lease->is_expiring_soon && $lease->status === 'active' ? 'border-l-4 border-l-warning' : '' }}">
                            <td class="px-6 py-4">
                                <p class="font-semibold text-primary">{{ $lease->tenant->user->name ?? '—' }}</p>
                                <p class="text-xs text-muted">{{ $lease->tenant->user->email ?? '' }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <p class="font-medium text-primary">{{ $lease->unit->unit_number ?? '—' }}</p>
                                <p class="text-xs text-muted">{{ $lease->unit->property->name ?? '—' }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-secondary text-xs">{{ $lease->start_date->format('d M Y') }}</p>
                                <p class="text-secondary text-xs">→ {{ $lease->end_date->format('d M Y') }}</p>
                                @if($lease->is_expiring_soon && $lease->status === 'active')
                                    <span class="text-xs text-warning font-semibold">⚠ {{ $lease->days_remaining }}d left</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 font-bold text-primary">
                                KES {{ number_format($lease->rent_amount, 0) }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $lease->status_badge }}">
                                    {{ $lease->status_label }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-end">
                                    <a href="{{ route('manager.leases.show', $lease) }}"
                                       class="text-xs bg-navy-500 hover:bg-navy-400 text-white px-3 py-1.5 rounded-lg font-medium transition-colors">
                                        View
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($leases->hasPages())
            <div class="px-6 py-4 border-t border-border">
                {{ $leases->links() }}
            </div>
            @endif
        @endif
    </div>

</div>
@endsection

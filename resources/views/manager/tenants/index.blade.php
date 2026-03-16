@extends('layouts.app')

@section('title', 'Tenants')
@section('page-title', 'Tenants')
@section('page-subtitle', 'Manage all tenants across your properties')

@section('sidebar-nav')
    @include('manager.partials.sidebar')
@endsection

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <p class="text-sm text-muted">{{ $tenants->total() }} tenant{{ $tenants->total() !== 1 ? 's' : '' }} found</p>
        </div>
        <a href="{{ route('manager.tenants.create') }}"
           class="inline-flex items-center gap-2 bg-brand-600 hover:bg-brand-500 text-white text-sm font-semibold px-4 py-2.5 rounded-xl transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Add Tenant
        </a>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-2xl border border-border p-4">
        <form method="GET" class="flex flex-wrap gap-3 items-end">
            <div class="flex-1 min-w-[180px]">
                <label class="block text-xs font-semibold text-secondary mb-1.5">Search</label>
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Name, email or ID number…"
                    class="w-full text-sm border border-border rounded-xl px-3 py-2 bg-surface focus:outline-none focus:ring-2 focus:ring-brand-400">
            </div>
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
                    <option value="">All Statuses</option>
                    <option value="active"      {{ request('status') === 'active'      ? 'selected' : '' }}>Active</option>
                    <option value="inactive"    {{ request('status') === 'inactive'    ? 'selected' : '' }}>Inactive</option>
                    <option value="blacklisted" {{ request('status') === 'blacklisted' ? 'selected' : '' }}>Blacklisted</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="text-sm bg-navy-500 hover:bg-navy-400 text-white px-4 py-2 rounded-xl font-medium transition-colors">Filter</button>
                @if(request()->hasAny(['search','property_id','status']))
                    <a href="{{ route('manager.tenants.index') }}" class="text-sm border border-border text-secondary hover:bg-surface px-4 py-2 rounded-xl transition-colors">Clear</a>
                @endif
            </div>
        </form>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-2xl border border-border overflow-hidden">
        @if($tenants->isEmpty())
            <div class="py-16 text-center">
                <svg class="w-12 h-12 text-muted mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <p class="text-muted text-sm">No tenants found.
                    <a href="{{ route('manager.tenants.create') }}" class="text-brand-600 font-semibold hover:underline">Add your first tenant</a>.
                </p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-surface border-b border-border">
                            <th class="text-left px-6 py-3 text-xs font-semibold text-secondary uppercase tracking-wider">Tenant</th>
                            <th class="text-left px-6 py-3 text-xs font-semibold text-secondary uppercase tracking-wider">ID Number</th>
                            <th class="text-left px-6 py-3 text-xs font-semibold text-secondary uppercase tracking-wider">Current Unit</th>
                            <th class="text-left px-6 py-3 text-xs font-semibold text-secondary uppercase tracking-wider">Status</th>
                            <th class="text-left px-6 py-3 text-xs font-semibold text-secondary uppercase tracking-wider">Joined</th>
                            <th class="text-right px-6 py-3 text-xs font-semibold text-secondary uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        @foreach($tenants as $tenant)
                        <tr class="hover:bg-surface/60 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full bg-navy-500 flex items-center justify-center shrink-0">
                                        <span class="text-sm font-bold text-white">
                                            {{ strtoupper(substr($tenant->user->name, 0, 1)) }}
                                        </span>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-primary">{{ $tenant->user->name }}</p>
                                        <p class="text-xs text-muted">{{ $tenant->user->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-secondary font-mono text-xs">{{ $tenant->id_number }}</td>
                            <td class="px-6 py-4">
                                @if($tenant->unit)
                                    <p class="font-medium text-primary">{{ $tenant->unit->unit_number }}</p>
                                    <p class="text-xs text-muted">{{ $tenant->unit->property->name ?? '—' }}</p>
                                @else
                                    <span class="text-muted text-xs italic">No unit assigned</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $tenant->status_badge }}">
                                    {{ ucfirst($tenant->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-secondary text-xs">{{ $tenant->created_at->format('d M Y') }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('manager.tenants.show', $tenant) }}"
                                       class="text-xs bg-navy-500 hover:bg-navy-400 text-white px-3 py-1.5 rounded-lg font-medium transition-colors">
                                        View
                                    </a>
                                    <a href="{{ route('manager.leases.create', ['tenant_id' => $tenant->id]) }}"
                                       class="text-xs bg-brand-600 hover:bg-brand-500 text-white px-3 py-1.5 rounded-lg font-medium transition-colors">
                                        + Lease
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($tenants->hasPages())
            <div class="px-6 py-4 border-t border-border">
                {{ $tenants->links() }}
            </div>
            @endif
        @endif
    </div>

</div>
@endsection

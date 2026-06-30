@extends('layouts.app')

@section('title', 'Properties')
@section('page-title', 'Properties')
@section('page-subtitle', 'All properties across the platform')

@section('sidebar-nav')
    @include('superadmin.partials.sidebar')
@endsection

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <p class="text-sm text-muted">{{ $properties->total() }} propert{{ $properties->total() === 1 ? 'y' : 'ies' }} found</p>

    {{-- Filters --}}
    <div class="bg-white rounded-2xl border border-border p-4">
        <form method="GET" class="flex flex-wrap gap-3 items-end">
            <div class="flex-1 min-w-[180px]">
                <label class="block text-xs font-semibold text-secondary mb-1.5">Search</label>
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Name, city or county…"
                    class="w-full text-sm border border-border rounded-xl px-3 py-2 bg-surface focus:outline-none focus:ring-2 focus:ring-brand-400">
            </div>
            <div class="min-w-[160px]">
                <label class="block text-xs font-semibold text-secondary mb-1.5">Manager</label>
                <select name="manager_id" class="w-full text-sm border border-border rounded-xl px-3 py-2 bg-surface focus:outline-none focus:ring-2 focus:ring-brand-400">
                    <option value="">All Managers</option>
                    @foreach($managers as $manager)
                        <option value="{{ $manager->id }}" {{ request('manager_id') == $manager->id ? 'selected' : '' }}>{{ $manager->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="min-w-[160px]">
                <label class="block text-xs font-semibold text-secondary mb-1.5">Type</label>
                <select name="type" class="w-full text-sm border border-border rounded-xl px-3 py-2 bg-surface focus:outline-none focus:ring-2 focus:ring-brand-400">
                    <option value="">All Types</option>
                    @foreach(['apartment', 'maisonette', 'commercial', 'bedsitter', 'townhouse'] as $type)
                        <option value="{{ $type }}" {{ request('type') === $type ? 'selected' : '' }}>{{ ucfirst($type) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="text-sm bg-navy-500 hover:bg-navy-400 text-white px-4 py-2 rounded-xl font-medium transition-colors">Filter</button>
                @if(request()->hasAny(['search', 'manager_id', 'type']))
                    <a href="{{ route('superadmin.properties.index') }}" class="text-sm border border-border text-secondary hover:bg-surface px-4 py-2 rounded-xl transition-colors">Clear</a>
                @endif
            </div>
        </form>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-2xl border border-border overflow-hidden">
        @if($properties->isEmpty())
            <div class="py-16 text-center">
                <p class="text-muted text-sm">No properties found.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-surface border-b border-border">
                            <th class="text-left px-6 py-3 text-xs font-semibold text-secondary uppercase tracking-wider">Property</th>
                            <th class="text-left px-6 py-3 text-xs font-semibold text-secondary uppercase tracking-wider">Manager</th>
                            <th class="text-left px-6 py-3 text-xs font-semibold text-secondary uppercase tracking-wider">Type</th>
                            <th class="text-left px-6 py-3 text-xs font-semibold text-secondary uppercase tracking-wider">Units</th>
                            <th class="text-left px-6 py-3 text-xs font-semibold text-secondary uppercase tracking-wider">Occupancy</th>
                            <th class="text-left px-6 py-3 text-xs font-semibold text-secondary uppercase tracking-wider">Status</th>
                            <th class="text-right px-6 py-3 text-xs font-semibold text-secondary uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        @foreach($properties as $property)
                        <tr class="hover:bg-surface/60 transition-colors">
                            <td class="px-6 py-4">
                                <p class="font-semibold text-primary">{{ $property->name }}</p>
                                <p class="text-xs text-muted">{{ $property->city }}, {{ $property->county }}</p>
                            </td>
                            <td class="px-6 py-4 text-secondary">{{ optional($property->manager)->name ?? '—' }}</td>
                            <td class="px-6 py-4 text-secondary">{{ ucfirst($property->property_type) }}</td>
                            <td class="px-6 py-4 text-secondary">{{ $property->units_count }}</td>
                            <td class="px-6 py-4 text-secondary text-xs">
                                {{ $property->occupied_count }} occupied / {{ $property->vacant_count }} vacant
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $property->is_active ? 'bg-success-bg text-success' : 'bg-danger-bg text-danger' }}">
                                    {{ $property->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('superadmin.properties.show', $property) }}"
                                   class="text-xs bg-navy-500 hover:bg-navy-400 text-white px-3 py-1.5 rounded-lg font-medium transition-colors">View</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($properties->hasPages())
            <div class="px-6 py-4 border-t border-border">{{ $properties->links() }}</div>
            @endif
        @endif
    </div>

</div>
@endsection

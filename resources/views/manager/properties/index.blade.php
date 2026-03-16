@extends('layouts.app')

@section('title', 'Properties')
@section('page-title', 'Properties')
@section('page-subtitle', 'Manage your properties')

@section('sidebar-nav')
    @include('manager.partials.sidebar')
@endsection

@section('content')
<div class="space-y-6">

    {{-- Header Actions --}}
    <div class="flex items-center justify-between">
        <p class="text-secondary text-sm">{{ $properties->total() }} {{ Str::plural('property', $properties->total()) }} total</p>
        <a href="{{ route('manager.properties.create') }}"
           class="bg-brand-600 hover:bg-brand-500 text-white text-sm font-600 px-4 py-2.5 rounded-xl transition-colors flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Add Property
        </a>
    </div>

    {{-- Properties Grid --}}
    @forelse($properties as $property)
    <div class="bg-card rounded-2xl border border-border shadow-sm overflow-hidden">

        {{-- Property Header --}}
        <div class="flex items-start justify-between p-6 border-b border-border">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 bg-navy-100 rounded-xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6 text-navy-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-display font-700 text-primary text-lg">{{ $property->name }}</h3>
                    <p class="text-secondary text-sm">{{ $property->address }}, {{ $property->city }}, {{ $property->county }}</p>
                    <span class="inline-block mt-1 text-xs bg-navy-100 text-navy-500 px-2.5 py-1 rounded-lg font-500 capitalize">
                        {{ ucfirst($property->property_type) }}
                    </span>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('manager.properties.edit', $property) }}"
                   class="bg-navy-500 hover:bg-navy-400 text-white text-xs font-600 px-3 py-2 rounded-lg transition-colors">
                    Edit
                </a>
                <a href="{{ route('manager.properties.show', $property) }}"
                   class="bg-brand-600 hover:bg-brand-500 text-white text-xs font-600 px-3 py-2 rounded-lg transition-colors">
                    Manage Units
                </a>
            </div>
        </div>

        {{-- Unit Stats --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 divide-x divide-border">
            <div class="px-6 py-4 text-center">
                <p class="font-display font-700 text-primary text-2xl">{{ $property->units_count }}</p>
                <p class="text-muted text-xs mt-0.5">Total Units</p>
            </div>
            <div class="px-6 py-4 text-center">
                <p class="font-display font-700 text-success text-2xl">{{ $property->occupied_count }}</p>
                <p class="text-muted text-xs mt-0.5">Occupied</p>
            </div>
            <div class="px-6 py-4 text-center">
                <p class="font-display font-700 text-info text-2xl">{{ $property->vacant_count }}</p>
                <p class="text-muted text-xs mt-0.5">Vacant</p>
            </div>
            <div class="px-6 py-4 text-center">
                <p class="font-display font-700 text-warning text-2xl">{{ $property->maintenance_count }}</p>
                <p class="text-muted text-xs mt-0.5">Maintenance</p>
            </div>
        </div>

        {{-- Occupancy Bar --}}
        @if($property->units_count > 0)
        <div class="px-6 py-3 bg-surface border-t border-border">
            <div class="flex items-center justify-between text-xs text-muted mb-1.5">
                <span>Occupancy Rate</span>
                <span class="font-600 text-primary">{{ round(($property->occupied_count / $property->units_count) * 100) }}%</span>
            </div>
            <div class="w-full bg-border rounded-full h-2">
                <div class="bg-brand-600 h-2 rounded-full transition-all"
                     style="width: {{ ($property->occupied_count / $property->units_count) * 100 }}%">
                </div>
            </div>
        </div>
        @endif

    </div>
    @empty
    <div class="bg-card rounded-2xl border border-border p-16 text-center">
        <div class="w-16 h-16 bg-navy-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-navy-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
        </div>
        <h3 class="font-display font-700 text-primary text-xl mb-2">No properties yet</h3>
        <p class="text-secondary text-sm mb-6">Get started by adding your first property.</p>
        <a href="{{ route('manager.properties.create') }}"
           class="inline-flex items-center gap-2 bg-brand-600 hover:bg-brand-500 text-white font-600 px-5 py-2.5 rounded-xl transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Add Your First Property
        </a>
    </div>
    @endforelse

    {{-- Pagination --}}
    {{ $properties->links() }}

</div>
@endsection

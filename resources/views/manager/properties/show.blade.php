@extends('layouts.app')

@section('title', $property->name)
@section('page-title', $property->name)
@section('page-subtitle', $property->address . ', ' . $property->city)

@section('sidebar-nav')
    @include('manager.partials.sidebar')
@endsection

@section('content')
<div class="space-y-6">

    {{-- Stats Row --}}
    <div class="grid grid-cols-2 xl:grid-cols-5 gap-4">
        <x-stat-card label="Total Units" :value="$stats['total']" color="navy"
            :icon="'<svg class=\'w-5 h-5\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16\'/></svg>'"
        />
        <x-stat-card label="Occupied" :value="$stats['occupied']" color="emerald"
            :icon="'<svg class=\'w-5 h-5\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z\'/></svg>'"
        />
        <x-stat-card label="Vacant" :value="$stats['vacant']" color="blue"
            :icon="'<svg class=\'w-5 h-5\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z\'/></svg>'"
        />
        <x-stat-card label="Maintenance" :value="$stats['maintenance']" color="amber"
            :icon="'<svg class=\'w-5 h-5\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0\'/></svg>'"
        />
        <x-stat-card label="Monthly Income" :value="'KES ' . number_format($stats['monthly_income'], 0)" color="emerald"
            :icon="'<svg class=\'w-5 h-5\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z\'/></svg>'"
        />
    </div>

    {{-- Units Table --}}
    <div class="bg-card rounded-2xl border border-border shadow-sm">
        <div class="flex items-center justify-between px-6 py-4 border-b border-border">
            <h3 class="font-display font-700 text-primary">Units</h3>
            <a href="{{ route('manager.properties.units.create', $property) }}"
               class="bg-brand-600 hover:bg-brand-500 text-white text-sm font-600 px-4 py-2 rounded-xl transition-colors flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Add Unit
            </a>
        </div>

        @if($units->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-border">
                        <th class="text-left text-xs font-600 text-muted uppercase tracking-wider px-6 py-3">Unit</th>
                        <th class="text-left text-xs font-600 text-muted uppercase tracking-wider px-6 py-3">Type</th>
                        <th class="text-left text-xs font-600 text-muted uppercase tracking-wider px-6 py-3">Tenant</th>
                        <th class="text-left text-xs font-600 text-muted uppercase tracking-wider px-6 py-3">Rent (KES)</th>
                        <th class="text-left text-xs font-600 text-muted uppercase tracking-wider px-6 py-3">Status</th>
                        <th class="text-left text-xs font-600 text-muted uppercase tracking-wider px-6 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @foreach($units as $unit)
                    <tr class="hover:bg-surface transition-colors">
                        <td class="px-6 py-4">
                            <span class="font-700 text-primary">{{ $unit->unit_number }}</span>
                            @if($unit->floor !== null)
                            <span class="text-xs text-muted ml-1">Floor {{ $unit->floor }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-secondary text-sm capitalize">
                            {{ str_replace('br', ' Bedroom', $unit->unit_type) }}
                        </td>
                        <td class="px-6 py-4 text-sm">
                            @if($unit->activeLease)
                                <span class="text-primary font-500">{{ $unit->activeLease->tenant->user->name }}</span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 font-600 text-primary text-sm">
                            {{ number_format($unit->rent_amount, 0) }}
                        </td>
                        <td class="px-6 py-4">
                            <x-badge :status="$unit->status" />
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('manager.properties.units.edit', [$property, $unit]) }}"
                                   class="text-xs bg-navy-500 hover:bg-navy-400 text-white font-600 px-3 py-1.5 rounded-lg transition-colors">
                                    Edit
                                </a>
                                @if($unit->status === 'vacant')
                                <form method="POST" action="{{ route('manager.properties.units.destroy', [$property, $unit]) }}"
                                    onsubmit="return confirm('Delete unit {{ $unit->unit_number }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                        class="text-xs text-danger hover:bg-danger-bg font-600 px-3 py-1.5 rounded-lg transition-colors">
                                        Delete
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="p-12 text-center">
            <p class="text-muted text-sm mb-4">No units added yet.</p>
            <a href="{{ route('manager.properties.units.create', $property) }}"
               class="inline-flex items-center gap-2 bg-brand-600 hover:bg-brand-500 text-white font-600 px-5 py-2.5 rounded-xl transition-colors text-sm">
                Add First Unit
            </a>
        </div>
        @endif
    </div>

    {{-- Back link --}}
    <div>
        <a href="{{ route('manager.properties.index') }}" class="text-secondary hover:text-primary text-sm flex items-center gap-2 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Back to Properties
        </a>
    </div>

</div>
@endsection

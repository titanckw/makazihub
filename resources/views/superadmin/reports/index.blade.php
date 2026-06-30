@extends('layouts.app')

@section('title', 'Reports')
@section('page-title', 'Reports')
@section('page-subtitle', 'Platform-wide revenue and occupancy overview')

@section('sidebar-nav')
    @include('superadmin.partials.sidebar')
@endsection

@section('content')
<div class="space-y-6">

    {{-- Export --}}
    <div class="flex justify-end">
        <a href="{{ route('superadmin.reports.export') }}"
           class="inline-flex items-center gap-2 bg-navy-500 hover:bg-navy-400 text-white text-sm font-semibold px-4 py-2.5 rounded-xl transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5 5-5M12 15V3" />
            </svg>
            Export CSV
        </a>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl border border-border p-4">
            <p class="text-xs text-muted">Total Revenue</p>
            <p class="text-xl font-700 text-primary mt-1">KSh {{ number_format($stats['total_revenue'], 0) }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-border p-4">
            <p class="text-xs text-muted">This Month</p>
            <p class="text-xl font-700 text-primary mt-1">KSh {{ number_format($stats['monthly_revenue'], 0) }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-border p-4">
            <p class="text-xs text-muted">Occupancy Rate</p>
            <p class="text-xl font-700 text-primary mt-1">{{ $occupancyRate }}%</p>
        </div>
        <div class="bg-white rounded-2xl border border-border p-4">
            <p class="text-xs text-muted">Overdue Invoices</p>
            <p class="text-xl font-700 text-primary mt-1">{{ $stats['invoices_overdue'] }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Revenue by month --}}
        <div class="bg-white rounded-2xl border border-border p-6">
            <p class="font-display font-700 text-primary mb-4">Revenue (last 6 months)</p>
            @if($revenueByMonth->isEmpty())
                <p class="text-sm text-muted">No confirmed payments recorded yet.</p>
            @else
                <div class="space-y-3">
                    @foreach($revenueByMonth as $row)
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-secondary">{{ \Carbon\Carbon::createFromFormat('Y-m', $row->month)->format('M Y') }}</span>
                            <span class="font-semibold text-primary">KSh {{ number_format($row->total, 0) }}</span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Top properties --}}
        <div class="bg-white rounded-2xl border border-border p-6">
            <p class="font-display font-700 text-primary mb-4">Largest Properties</p>
            @if($topProperties->isEmpty())
                <p class="text-sm text-muted">No properties recorded yet.</p>
            @else
                <div class="space-y-3">
                    @foreach($topProperties as $property)
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-secondary">{{ $property->name }}</span>
                            <span class="font-semibold text-primary">{{ $property->occupied_count }}/{{ $property->units_count }} occupied</span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

</div>
@endsection

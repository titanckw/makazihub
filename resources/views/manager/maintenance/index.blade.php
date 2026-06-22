@extends('layouts.app')

@section('title', 'Maintenance Requests')
@section('page-title', 'Maintenance Requests')
@section('page-subtitle', 'Track all maintenance and repair requests')

@section('sidebar-nav')
    @include('manager.partials.sidebar')
@endsection

@section('content')
    <div class="space-y-6">
        {{-- Stats Cards --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-card rounded-xl border border-border shadow-sm p-4">
                <p class="text-xs text-muted font-medium mb-1">Under Review</p>
                <p class="text-2xl font-bold text-blue-600">{{ $stats['under_review'] }}</p>
            </div>
            <div class="bg-card rounded-xl border border-border shadow-sm p-4">
                <p class="text-xs text-muted font-medium mb-1">Pending Repairs</p>
                <p class="text-2xl font-bold text-amber-600">{{ $stats['pending_repairs'] }}</p>
            </div>
            <div class="bg-card rounded-xl border border-border shadow-sm p-4">
                <p class="text-xs text-muted font-medium mb-1">Repair Review</p>
                <p class="text-2xl font-bold text-purple-600">{{ $stats['repair_review'] }}</p>
            </div>
            <div class="bg-card rounded-xl border border-border shadow-sm p-4">
                <p class="text-xs text-muted font-medium mb-1">Completed</p>
                <p class="text-2xl font-bold text-emerald-600">{{ $stats['completed'] }}</p>
            </div>
        </div>

        {{-- Maintenance Requests Table --}}
        <div class="bg-card rounded-2xl border border-border shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-border">
                <h3 class="font-display font-700 text-primary">All Maintenance Requests</h3>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-border bg-muted/30">
                            <th class="px-6 py-3 text-left text-xs font-semibold text-muted uppercase tracking-wider">Tenant</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-muted uppercase tracking-wider">Unit</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-muted uppercase tracking-wider">Repair Type</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-muted uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-muted uppercase tracking-wider">Priority</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-muted uppercase tracking-wider">Date Requested</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-muted uppercase tracking-wider">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        @forelse($maintenanceRequests as $request)
                            <tr class="hover:bg-muted/20 transition-colors">
                                <td class="px-6 py-4">
                                    <p class="text-sm font-medium text-primary">{{ $request->tenant->user->name ?? 'Unknown' }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-sm text-muted">{{ $request->unit->unit_number ?? 'N/A' }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-sm text-muted">{{ ucfirst(str_replace('_', ' ', $request->repair_type)) }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-xs font-semibold px-3 py-1.5 rounded-full {{ $request->status_badge }}">
                                        {{ $request->status_label }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-xs font-semibold px-3 py-1.5 rounded-full 
                                        @if($request->priority === 'urgent') bg-red-100 text-red-800
                                        @elseif($request->priority === 'high') bg-orange-100 text-orange-800
                                        @elseif($request->priority === 'medium') bg-amber-100 text-amber-800
                                        @else bg-green-100 text-green-800
                                        @endif">
                                        {{ ucfirst($request->priority) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-sm text-muted">{{ $request->requested_date->format('d M Y') }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    <a href="{{ route('manager.maintenance.show', $request) }}"
                                        class="text-sm font-medium text-brand-600 hover:text-brand-500 transition-colors">
                                        View
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-muted">
                                    No maintenance requests found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($maintenanceRequests->hasPages())
                <div class="px-6 py-4 border-t border-border">
                    {{ $maintenanceRequests->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection

@extends('layouts.app')

@section('title', 'Maintenance Requests')
@section('page-title', 'Maintenance & Repairs')
@section('page-subtitle', 'Submit and track maintenance requests')

@section('sidebar-nav')
    @include('tenant.partials.sidebar')
@endsection

@section('content')
    <div class="space-y-6">
        {{-- Submit New Request Button --}}
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-lg font-semibold text-primary">Your Maintenance Requests</h2>
                <p class="text-sm text-muted mt-1">View all your submitted maintenance and repair requests</p>
            </div>
            <a href="{{ route('tenant.maintenance.create') }}" class="bg-brand-600 hover:bg-brand-500 text-white font-semibold px-4 py-2.5 rounded-lg transition-colors flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Submit New Request
            </a>
        </div>

        {{-- Maintenance Requests --}}
        @forelse($maintenanceRequests as $request)
            <div class="bg-card rounded-2xl border border-border shadow-sm p-6 hover:shadow-md transition-shadow">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <h3 class="text-lg font-bold text-primary">{{ ucfirst(str_replace('_', ' ', $request->repair_type)) }}</h3>
                        <p class="text-sm text-muted mt-1">Request #{{ $request->id }} • Unit {{ $request->unit->unit_number ?? 'N/A' }}</p>
                    </div>
                    <span class="text-sm font-semibold px-3 py-1.5 rounded-full {{ $request->status_badge }}">
                        {{ $request->status_label }}
                    </span>
                </div>

                <p class="text-muted mb-4 leading-relaxed">{{ Str::limit($request->description, 150) }}</p>

                <div class="flex items-center justify-between pt-4 border-t border-border">
                    <div class="flex items-center gap-6 text-sm">
                        <div>
                            <p class="text-muted">Requested</p>
                            <p class="font-semibold text-primary">{{ $request->requested_date->format('d M Y') }}</p>
                        </div>
                        <div>
                            <p class="text-muted">Priority</p>
                            <p class="font-semibold 
                                @if($request->priority === 'urgent') text-red-600
                                @elseif($request->priority === 'high') text-orange-600
                                @elseif($request->priority === 'medium') text-amber-600
                                @else text-green-600
                                @endif">
                                {{ ucfirst($request->priority) }}
                            </p>
                        </div>
                        @if($request->completed_date)
                            <div>
                                <p class="text-muted">Completed</p>
                                <p class="font-semibold text-primary">{{ $request->completed_date->format('d M Y') }}</p>
                            </div>
                        @endif
                    </div>
                    <a href="{{ route('tenant.maintenance.show', $request) }}" class="text-brand-600 hover:text-brand-500 font-semibold">
                        View Details →
                    </a>
                </div>
            </div>
        @empty
            <div class="bg-card rounded-2xl border border-border shadow-sm p-12 text-center">
                <svg class="w-16 h-16 text-muted/30 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                <h3 class="text-lg font-semibold text-primary mb-2">No Maintenance Requests Yet</h3>
                <p class="text-muted mb-6">You haven't submitted any maintenance requests. Click the button above to submit your first request.</p>
                <a href="{{ route('tenant.maintenance.create') }}" class="inline-block bg-brand-600 hover:bg-brand-500 text-white font-semibold px-6 py-2.5 rounded-lg transition-colors">
                    Submit Request
                </a>
            </div>
        @endforelse

        @if($maintenanceRequests->hasPages())
            <div class="mt-6">
                {{ $maintenanceRequests->links() }}
            </div>
        @endif
    </div>
@endsection

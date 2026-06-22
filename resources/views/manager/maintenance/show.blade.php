@extends('layouts.app')

@section('title', 'Maintenance Request - ' . ($maintenance->tenant->user->name ?? 'Unknown'))
@section('page-title', 'Maintenance Request')
@section('page-subtitle', 'View and update maintenance request')

@section('sidebar-nav')
    @include('manager.partials.sidebar')
@endsection

@section('content')
    <div class="space-y-6">
        {{-- Breadcrumb --}}
        <div class="flex items-center gap-2 text-sm">
            <a href="{{ route('manager.maintenance.index') }}" class="text-brand-600 hover:text-brand-500">Maintenance</a>
            <span class="text-muted">/</span>
            <span class="text-muted">Request #{{ $maintenance->id }}</span>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Main Content --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Request Details --}}
                <div class="bg-card rounded-2xl border border-border shadow-sm p-6">
                    <div class="flex items-start justify-between mb-6">
                        <div>
                            <h2 class="text-2xl font-bold text-primary">{{ $maintenance->tenant->user->name ?? 'Unknown Tenant' }}</h2>
                            <p class="text-muted mt-1">Unit {{ $maintenance->unit->unit_number ?? 'N/A' }} • Property: {{ $maintenance->property->name ?? 'N/A' }}</p>
                        </div>
                        <span class="text-lg font-semibold px-4 py-2 rounded-lg {{ $maintenance->status_badge }}">
                            {{ $maintenance->status_label }}
                        </span>
                    </div>

                    <div class="grid grid-cols-2 gap-6 mb-6">
                        <div>
                            <p class="text-sm text-muted font-medium mb-2">Repair Type</p>
                            <p class="text-lg font-semibold text-primary">{{ ucfirst(str_replace('_', ' ', $maintenance->repair_type)) }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-muted font-medium mb-2">Priority</p>
                            <span class="text-lg font-semibold px-3 py-1.5 rounded-full inline-block
                                @if($maintenance->priority === 'urgent') bg-red-100 text-red-800
                                @elseif($maintenance->priority === 'high') bg-orange-100 text-orange-800
                                @elseif($maintenance->priority === 'medium') bg-amber-100 text-amber-800
                                @else bg-green-100 text-green-800
                                @endif">
                                {{ ucfirst($maintenance->priority) }}
                            </span>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-6 mb-6">
                        <div>
                            <p class="text-sm text-muted font-medium mb-2">Requested Date</p>
                            <p class="text-base text-primary">{{ $maintenance->requested_date->format('d M Y') }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-muted font-medium mb-2">Completed Date</p>
                            <p class="text-base text-primary">{{ $maintenance->completed_date?->format('d M Y') ?? '—' }}</p>
                        </div>
                    </div>

                    <div>
                        <p class="text-sm text-muted font-medium mb-2">Description</p>
                        <p class="text-base text-primary leading-relaxed">{{ $maintenance->description }}</p>
                    </div>
                </div>

                {{-- Status Timeline --}}
                <div class="bg-card rounded-2xl border border-border shadow-sm p-6">
                    <h3 class="font-bold text-primary mb-6">Status Timeline</h3>
                    <div class="space-y-4">
                        @php
                            $statuses = [
                                ['key' => 'under_review', 'label' => 'Under Review', 'color' => 'blue'],
                                ['key' => 'pending_repairs', 'label' => 'Pending Repairs', 'color' => 'amber'],
                                ['key' => 'repair_review', 'label' => 'Repair Review', 'color' => 'purple'],
                                ['key' => 'completed', 'label' => 'Completed', 'color' => 'emerald'],
                            ];
                        @endphp

                        @foreach($statuses as $statusItem)
                            <div class="flex items-start gap-4">
                                <div class="flex flex-col items-center">
                                    <div class="w-3 h-3 rounded-full mt-2 
                                        @if($statusItem['key'] === $maintenance->status) 
                                            bg-{{ $statusItem['color'] }}-600
                                        @elseif(
                                            ($statusItem['key'] === 'pending_repairs' && in_array($maintenance->status, ['pending_repairs', 'repair_review', 'completed'])) ||
                                            ($statusItem['key'] === 'repair_review' && in_array($maintenance->status, ['repair_review', 'completed'])) ||
                                            ($statusItem['key'] === 'completed' && $maintenance->status === 'completed')
                                        )
                                            bg-emerald-600
                                        @else
                                            bg-gray-300
                                        @endif">
                                    </div>
                                    @if(!$loop->last)
                                        <div class="w-0.5 h-12 bg-gray-300 my-2"></div>
                                    @endif
                                </div>
                                <div class="pt-1">
                                    <p class="font-semibold text-primary">{{ $statusItem['label'] }}</p>
                                    @if($statusItem['key'] === $maintenance->status)
                                        <p class="text-sm text-muted">Current status</p>
                                    @elseif(
                                        ($statusItem['key'] === 'pending_repairs' && in_array($maintenance->status, ['pending_repairs', 'repair_review', 'completed'])) ||
                                        ($statusItem['key'] === 'repair_review' && in_array($maintenance->status, ['repair_review', 'completed'])) ||
                                        ($statusItem['key'] === 'completed' && $maintenance->status === 'completed')
                                    )
                                        <p class="text-sm text-emerald-600">Completed</p>
                                    @else
                                        <p class="text-sm text-muted">Pending</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Notes --}}
                @if($maintenance->notes)
                    <div class="bg-card rounded-2xl border border-border shadow-sm p-6">
                        <h3 class="font-bold text-primary mb-3">Notes</h3>
                        <p class="text-base text-muted leading-relaxed">{{ $maintenance->notes }}</p>
                    </div>
                @endif
            </div>

            {{-- Sidebar Actions --}}
            <div class="space-y-6">
                {{-- Update Status Form --}}
                <div class="bg-card rounded-2xl border border-border shadow-sm p-6">
                    <h3 class="font-bold text-primary mb-4">Update Status</h3>
                    <form action="{{ route('manager.maintenance.update-status', $maintenance) }}" method="POST" class="space-y-4">
                        @csrf
                        @method('PATCH')

                        <div>
                            <label class="block text-sm font-medium text-primary mb-2">New Status</label>
                            <select name="status" class="w-full rounded-lg border border-border bg-input text-primary px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-brand-600">
                                <option value="under_review" @selected($maintenance->status === 'under_review')>Under Review (🔵 Blue)</option>
                                <option value="pending_repairs" @selected($maintenance->status === 'pending_repairs')>Pending Repairs (🟡 Amber)</option>
                                <option value="repair_review" @selected($maintenance->status === 'repair_review')>Repair Review (🟣 Purple)</option>
                                <option value="completed" @selected($maintenance->status === 'completed')>Completed (🟢 Green)</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-primary mb-2">Notes (Optional)</label>
                            <textarea name="notes" rows="4" class="w-full rounded-lg border border-border bg-input text-primary px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-brand-600" placeholder="Add any notes about this maintenance request...">{{ $maintenance->notes }}</textarea>
                        </div>

                        <button type="submit" class="w-full bg-brand-600 hover:bg-brand-500 text-white font-semibold py-2.5 rounded-lg transition-colors">
                            Update Status
                        </button>
                    </form>
                </div>

                {{-- Request Info --}}
                <div class="bg-card rounded-2xl border border-border shadow-sm p-6">
                    <h3 class="font-bold text-primary mb-4">Request Information</h3>
                    <div class="space-y-3">
                        <div>
                            <p class="text-xs text-muted font-medium mb-1">Request ID</p>
                            <p class="text-sm font-mono text-primary">#{{ $maintenance->id }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-muted font-medium mb-1">Tenant Phone</p>
                            <p class="text-sm text-primary">{{ $maintenance->tenant->user->phone ?? 'Not provided' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-muted font-medium mb-1">Property Address</p>
                            <p class="text-sm text-primary">{{ $maintenance->property->location ?? 'Not provided' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

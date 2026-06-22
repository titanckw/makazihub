@extends('layouts.app')

@section('title', 'Maintenance Request #' . $maintenance->id)
@section('page-title', 'Maintenance Request')
@section('page-subtitle', 'View your maintenance request status')

@section('sidebar-nav')
    @include('tenant.partials.sidebar')
@endsection

@section('content')
    <div class="space-y-6">
        {{-- Breadcrumb --}}
        <div class="flex items-center gap-2 text-sm">
            <a href="{{ route('tenant.maintenance.index') }}" class="text-brand-600 hover:text-brand-500">Maintenance</a>
            <span class="text-muted">/</span>
            <span class="text-muted">Request #{{ $maintenance->id }}</span>
        </div>

        {{-- Request Header --}}
        <div class="bg-card rounded-2xl border border-border shadow-sm p-6">
            <div class="flex items-start justify-between mb-6">
                <div>
                    <h2 class="text-3xl font-bold text-primary">{{ ucfirst(str_replace('_', ' ', $maintenance->repair_type)) }}</h2>
                    <p class="text-muted mt-2">Unit {{ $maintenance->unit->unit_number ?? 'N/A' }} • Property: {{ $maintenance->property->name ?? 'N/A' }}</p>
                </div>
                <span class="text-xl font-semibold px-4 py-2 rounded-lg {{ $maintenance->status_badge }}">
                    {{ $maintenance->status_label }}
                </span>
            </div>

            <p class="text-primary leading-relaxed mb-6">{{ $maintenance->description }}</p>

            <div class="grid grid-cols-3 gap-4 pt-6 border-t border-border">
                <div>
                    <p class="text-sm text-muted font-medium mb-1">Request Date</p>
                    <p class="font-semibold text-primary">{{ $maintenance->requested_date->format('d M Y') }}</p>
                </div>
                <div>
                    <p class="text-sm text-muted font-medium mb-1">Priority</p>
                    <p class="font-semibold 
                        @if($maintenance->priority === 'urgent') text-red-600
                        @elseif($maintenance->priority === 'high') text-orange-600
                        @elseif($maintenance->priority === 'medium') text-amber-600
                        @else text-green-600
                        @endif">
                        {{ ucfirst($maintenance->priority) }}
                    </p>
                </div>
                <div>
                    <p class="text-sm text-muted font-medium mb-1">Completed Date</p>
                    <p class="font-semibold text-primary">{{ $maintenance->completed_date?->format('d M Y') ?? '—' }}</p>
                </div>
            </div>
        </div>

        {{-- Status Progress --}}
        <div class="bg-card rounded-2xl border border-border shadow-sm p-8">
            <h3 class="text-xl font-bold text-primary mb-8">Request Status Timeline</h3>
            
            <div class="space-y-6">
                @php
                    $statuses = [
                        ['key' => 'under_review', 'label' => 'Under Review', 'description' => 'Your request is being reviewed', 'color' => 'blue'],
                        ['key' => 'pending_repairs', 'label' => 'Pending Repairs', 'description' => 'Repairs are being scheduled', 'color' => 'amber'],
                        ['key' => 'repair_review', 'label' => 'Repair Review', 'description' => 'Repairs are in progress', 'color' => 'purple'],
                        ['key' => 'completed', 'label' => 'Completed', 'description' => 'Repairs have been completed', 'color' => 'emerald'],
                    ];
                @endphp

                @foreach($statuses as $statusItem)
                    @php
                        $isActive = $statusItem['key'] === $maintenance->status;
                        $isCompleted = 
                            ($statusItem['key'] === 'pending_repairs' && in_array($maintenance->status, ['pending_repairs', 'repair_review', 'completed'])) ||
                            ($statusItem['key'] === 'repair_review' && in_array($maintenance->status, ['repair_review', 'completed'])) ||
                            ($statusItem['key'] === 'completed' && $maintenance->status === 'completed');
                    @endphp

                    <div class="flex gap-6">
                        {{-- Timeline Indicator --}}
                        <div class="flex flex-col items-center">
                            <div class="w-6 h-6 rounded-full flex items-center justify-center font-bold text-white
                                @if($isActive)
                                    bg-{{ $statusItem['color'] }}-600 ring-4 ring-{{ $statusItem['color'] }}-100
                                @elseif($isCompleted)
                                    bg-emerald-600
                                @else
                                    bg-gray-300
                                @endif">
                                @if($isCompleted)
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                @else
                                    {{ $loop->iteration }}
                                @endif
                            </div>
                            @if(!$loop->last)
                                <div class="w-1 h-12 mt-2 {{ $isCompleted || $isActive ? 'bg-emerald-600' : 'bg-gray-300' }}"></div>
                            @endif
                        </div>

                        {{-- Content --}}
                        <div class="pb-4 flex-1 {{ !$loop->last ? 'border-b border-gray-200' : '' }}">
                            <div class="flex items-start justify-between">
                                <div>
                                    <h4 class="text-lg font-bold text-primary">{{ $statusItem['label'] }}</h4>
                                    <p class="text-muted mt-1">{{ $statusItem['description'] }}</p>
                                </div>
                                @if($isActive)
                                    <span class="inline-block bg-{{ $statusItem['color'] }}-100 text-{{ $statusItem['color'] }}-800 text-xs font-bold px-3 py-1 rounded-full">Current</span>
                                @elseif($isCompleted)
                                    <span class="inline-block bg-emerald-100 text-emerald-800 text-xs font-bold px-3 py-1 rounded-full">✓ Complete</span>
                                @else
                                    <span class="inline-block bg-gray-100 text-gray-800 text-xs font-bold px-3 py-1 rounded-full">Pending</span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Help Info --}}
            <div class="mt-8 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                <div class="flex gap-3">
                    <svg class="w-5 h-5 text-blue-600 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                    <div>
                        <p class="font-semibold text-blue-900 text-sm">Understanding the Status</p>
                        <ul class="text-sm text-blue-800 mt-2 space-y-1">
                            <li><span class="font-bold text-blue-600">🔵 Under Review</span> - Management is reviewing your request</li>
                            <li><span class="font-bold text-amber-600">🟡 Pending Repairs</span> - Repairs are being scheduled and arranged</li>
                            <li><span class="font-bold text-purple-600">🟣 Repair Review</span> - Work is in progress</li>
                            <li><span class="font-bold text-emerald-600">🟢 Completed</span> - Your issue has been resolved</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        {{-- Notes --}}
        @if($maintenance->notes)
            <div class="bg-amber-50 border border-amber-200 rounded-2xl p-6">
                <h3 class="font-bold text-amber-900 mb-3">Notes from Management</h3>
                <p class="text-amber-900 leading-relaxed">{{ $maintenance->notes }}</p>
            </div>
        @endif

        {{-- Back Button --}}
        <div>
            <a href="{{ route('tenant.maintenance.index') }}" class="inline-block text-brand-600 hover:text-brand-500 font-semibold">
                ← Back to Maintenance Requests
            </a>
        </div>
    </div>
@endsection

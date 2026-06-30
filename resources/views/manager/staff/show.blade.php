@extends('layouts.app')

@section('title', $staff->user->name)
@section('page-title', $staff->user->name)
@section('page-subtitle', ucfirst($staff->role) . ' · ' . $staff->employment_type_label)

@section('sidebar-nav')
    @include('manager.partials.sidebar')
@endsection

@section('content')
<div class="space-y-6">

    {{-- Header actions --}}
    <div class="flex items-center justify-between">
        <a href="{{ route('manager.staff.index') }}"
            class="inline-flex items-center gap-1.5 text-sm text-secondary hover:text-primary transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to Staff
        </a>
        <div class="flex items-center gap-2">
            <a href="{{ route('manager.staff.edit', $staff) }}"
                class="inline-flex items-center gap-2 bg-navy-500 hover:bg-navy-400 text-white text-sm font-semibold px-4 py-2 rounded-xl transition-colors">
                Edit
            </a>
            <form method="POST" action="{{ route('manager.staff.destroy', $staff) }}"
                  onsubmit="return confirm('Remove this staff member and their login account? This cannot be undone.')">
                @csrf @method('DELETE')
                <button type="submit"
                    class="inline-flex items-center gap-2 bg-danger hover:opacity-80 text-white text-sm font-semibold px-4 py-2 rounded-xl transition-colors">
                    Remove
                </button>
            </form>
        </div>
    </div>

    {{-- Profile card --}}
    <div class="bg-white rounded-2xl border border-border p-6">
        <div class="flex items-start gap-5">
            <div class="w-14 h-14 rounded-full bg-navy-500 flex items-center justify-center shrink-0">
                <span class="text-xl font-bold text-white">
                    {{ strtoupper(substr($staff->user->name, 0, 1)) }}
                </span>
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex flex-wrap items-center gap-3 mb-1">
                    <h2 class="text-lg font-bold text-primary">{{ $staff->user->name }}</h2>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $staff->status_badge }}">
                        {{ ucfirst($staff->status) }}
                    </span>
                </div>
                <p class="text-sm text-secondary">{{ $staff->user->email }}</p>
                <p class="text-sm text-secondary">{{ $staff->user->phone ?? '—' }}</p>
            </div>
        </div>
    </div>

    {{-- Details grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="bg-white rounded-2xl border border-border p-5 space-y-4">
            <h3 class="text-xs font-semibold text-secondary uppercase tracking-wider">Employment</h3>
            <dl class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <dt class="text-muted">Role</dt>
                    <dd class="font-medium text-primary">{{ ucfirst($staff->role) }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-muted">Department</dt>
                    <dd class="font-medium text-primary">{{ $staff->department ?? '—' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-muted">Employment Type</dt>
                    <dd class="font-medium text-primary">{{ $staff->employment_type_label }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-muted">Start Date</dt>
                    <dd class="font-medium text-primary">
                        {{ $staff->start_date ? $staff->start_date->format('d M Y') : '—' }}
                    </dd>
                </div>
            </dl>
        </div>

        <div class="bg-white rounded-2xl border border-border p-5 space-y-4">
            <h3 class="text-xs font-semibold text-secondary uppercase tracking-wider">Identity</h3>
            <dl class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <dt class="text-muted">ID Number</dt>
                    <dd class="font-mono text-primary">{{ $staff->id_number ?? '—' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-muted">Added</dt>
                    <dd class="text-primary">{{ $staff->created_at->format('d M Y') }}</dd>
                </div>
            </dl>
        </div>
    </div>

    {{-- Notes --}}
    @if($staff->notes)
    <div class="bg-white rounded-2xl border border-border p-5">
        <h3 class="text-xs font-semibold text-secondary uppercase tracking-wider mb-3">Notes</h3>
        <p class="text-sm text-secondary whitespace-pre-line">{{ $staff->notes }}</p>
    </div>
    @endif

    {{-- Quick links --}}
    <div class="bg-white rounded-2xl border border-border p-5">
        <h3 class="text-xs font-semibold text-secondary uppercase tracking-wider mb-3">More</h3>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('manager.leave.index') }}" class="px-4 py-2 bg-surface text-primary text-sm font-medium rounded-xl hover:bg-navy-100 transition-colors">
                View Leave Requests
            </a>
            <a href="{{ route('manager.documents.index', ['staff_id' => $staff->id]) }}" class="px-4 py-2 bg-surface text-primary text-sm font-medium rounded-xl hover:bg-navy-100 transition-colors">
                View Documents
            </a>
            <a href="{{ route('manager.chat.index', ['with' => $staff->user_id]) }}" class="px-4 py-2 bg-surface text-primary text-sm font-medium rounded-xl hover:bg-navy-100 transition-colors">
                Message {{ $staff->user->first_name }}
            </a>
        </div>
    </div>

</div>
@endsection

@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Welcome, ' . auth()->user()->first_name)
@section('page-subtitle', ucfirst($staff->role) . ' · ' . $staff->employment_type_label)

@section('sidebar-nav')
    @include('staff.partials.sidebar')
@endsection

@section('content')
<div class="space-y-6">

    {{-- Profile card --}}
    <div class="bg-white rounded-2xl border border-border p-6">
        <div class="flex items-center gap-5">
            <img src="{{ auth()->user()->avatar_url }}" class="w-14 h-14 rounded-full object-cover shrink-0 border border-border" alt="Avatar">
            <div class="flex-1">
                <h2 class="text-lg font-bold text-primary">{{ auth()->user()->name }}</h2>
                <p class="text-sm text-secondary">{{ auth()->user()->email }}</p>
                <div class="flex items-center gap-2 mt-1.5">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $staff->status_badge }}">
                        {{ ucfirst($staff->status) }}
                    </span>
                    <span class="text-xs text-muted">{{ $staff->employment_type_label }}</span>
                </div>
            </div>
            <a href="{{ route('staff.profile.edit') }}"
                class="shrink-0 text-sm bg-surface hover:bg-navy-100 text-primary font-semibold px-4 py-2 rounded-xl transition-colors">
                Edit Profile
            </a>
        </div>
    </div>

    {{-- Details --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="bg-white rounded-2xl border border-border p-5">
            <h3 class="text-xs font-semibold text-secondary uppercase tracking-wider mb-4">My Details</h3>
            <dl class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <dt class="text-muted">Role</dt>
                    <dd class="font-medium text-primary">{{ ucfirst($staff->role) }}</dd>
                </div>
                @if($staff->department)
                <div class="flex justify-between">
                    <dt class="text-muted">Department</dt>
                    <dd class="font-medium text-primary">{{ $staff->department }}</dd>
                </div>
                @endif
                @if($staff->start_date)
                <div class="flex justify-between">
                    <dt class="text-muted">Start Date</dt>
                    <dd class="font-medium text-primary">{{ $staff->start_date->format('d M Y') }}</dd>
                </div>
                @endif
                <div class="flex justify-between">
                    <dt class="text-muted">Phone</dt>
                    <dd class="font-medium text-primary">{{ auth()->user()->phone ?? '—' }}</dd>
                </div>
            </dl>
        </div>

        <div class="bg-white rounded-2xl border border-border p-5">
            <h3 class="text-xs font-semibold text-secondary uppercase tracking-wider mb-4">Manager</h3>
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-full bg-brand-600 flex items-center justify-center shrink-0">
                    <span class="text-sm font-bold text-white">
                        {{ strtoupper(substr($staff->manager->name, 0, 1)) }}
                    </span>
                </div>
                <div>
                    <p class="font-semibold text-primary text-sm">{{ $staff->manager->name }}</p>
                    <p class="text-xs text-muted">{{ $staff->manager->email }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Quick stats: attendance & leave --}}
    @php
        $todayLog = \App\Models\AttendanceLog::where('staff_id', $staff->id)->whereDate('date', now()->toDateString())->first();
        $leaveBalance = $staff->currentLeaveBalance();
        $pendingLeave = $staff->leaveRequests()->where('status', 'pending')->count();
    @endphp
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <a href="{{ route('staff.attendance.index') }}" class="bg-white rounded-2xl border border-border p-5 hover:border-brand-600/40 transition-colors">
            <p class="text-xs font-semibold text-secondary uppercase tracking-wider">Today's Status</p>
            <p class="text-lg font-bold text-primary mt-1">
                {{ $todayLog && $todayLog->clock_in ? ($todayLog->clock_out ? 'Clocked Out' : 'Clocked In') : 'Not Clocked In' }}
            </p>
        </a>
        <a href="{{ route('staff.leave.index') }}" class="bg-white rounded-2xl border border-border p-5 hover:border-brand-600/40 transition-colors">
            <p class="text-xs font-semibold text-secondary uppercase tracking-wider">Leave Days Remaining</p>
            <p class="text-lg font-bold text-success mt-1">{{ $leaveBalance->remaining_days }} / {{ $leaveBalance->allocated_days }}</p>
        </a>
        <a href="{{ route('staff.chat.index') }}" class="bg-white rounded-2xl border border-border p-5 hover:border-brand-600/40 transition-colors">
            <p class="text-xs font-semibold text-secondary uppercase tracking-wider">Messages</p>
            <p class="text-lg font-bold text-primary mt-1">Chat with {{ $staff->manager->first_name }}</p>
        </a>
    </div>

    {{-- Quick link to change password --}}
    <div class="bg-white rounded-2xl border border-border p-5 flex items-center justify-between">
        <div>
            <p class="text-sm font-semibold text-primary">Update your password</p>
            <p class="text-xs text-muted mt-0.5">Your account was created with a default password. Change it in Settings.</p>
        </div>
        <a href="{{ route('settings.index') }}"
            class="shrink-0 text-sm bg-brand-600 hover:bg-brand-500 text-white font-semibold px-4 py-2 rounded-xl transition-colors">
            Settings
        </a>
    </div>

</div>
@endsection

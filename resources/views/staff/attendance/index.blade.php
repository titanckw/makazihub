@extends('layouts.app')

@section('title', 'Attendance')
@section('page-title', 'Attendance')
@section('page-subtitle', 'Clock in and out, and review your attendance history')

@section('sidebar-nav')
    @include('staff.partials.sidebar')
@endsection

@section('content')
<div class="space-y-6" x-data="{ lat: null, lng: null }" x-init="navigator.geolocation && navigator.geolocation.getCurrentPosition(p => { lat = p.coords.latitude; lng = p.coords.longitude; })">

    @if(session('success'))
        <div class="bg-success-bg text-success text-sm font-medium px-4 py-3 rounded-xl">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="bg-danger-bg text-danger text-sm font-medium px-4 py-3 rounded-xl">{{ session('error') }}</div>
    @endif

    {{-- Clock card --}}
    <div class="bg-white rounded-2xl border border-border p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <p class="text-xs font-semibold text-secondary uppercase tracking-wider mb-1">Today · {{ now()->format('l, M d Y') }}</p>
                <p class="text-2xl font-bold text-primary">
                    @if($today && $today->clock_in)
                        Clocked in at {{ $today->clock_in->format('H:i') }}
                        @if($today->clock_out)
                            — out at {{ $today->clock_out->format('H:i') }}
                        @endif
                    @else
                        Not clocked in yet
                    @endif
                </p>
                @if($today)
                    <span class="inline-flex mt-2 items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $today->status_badge }}">
                        {{ ucfirst(str_replace('_', ' ', $today->status)) }}
                    </span>
                @endif
            </div>
            <div class="flex gap-3">
                <form method="POST" action="{{ route('staff.attendance.clock-in') }}">
                    @csrf
                    <input type="hidden" name="lat" :value="lat">
                    <input type="hidden" name="lng" :value="lng">
                    <button type="submit" {{ $today && $today->clock_in ? 'disabled' : '' }}
                        class="px-5 py-2.5 bg-brand-600 text-white text-sm font-semibold rounded-xl hover:bg-brand-500 disabled:opacity-40 disabled:cursor-not-allowed transition-colors">
                        Clock In
                    </button>
                </form>
                <form method="POST" action="{{ route('staff.attendance.clock-out') }}">
                    @csrf
                    <input type="hidden" name="lat" :value="lat">
                    <input type="hidden" name="lng" :value="lng">
                    <button type="submit" {{ !$today || !$today->clock_in || $today->clock_out ? 'disabled' : '' }}
                        class="px-5 py-2.5 bg-navy-600 text-white text-sm font-semibold rounded-xl hover:bg-navy-500 disabled:opacity-40 disabled:cursor-not-allowed transition-colors">
                        Clock Out
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- History --}}
    <div class="bg-white rounded-2xl border border-border overflow-hidden">
        <div class="px-6 py-4 border-b border-border">
            <h3 class="font-semibold text-primary">Attendance History</h3>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-surface text-secondary text-xs uppercase tracking-wider">
                <tr>
                    <th class="px-6 py-3 text-left">Date</th>
                    <th class="px-6 py-3 text-left">Clock In</th>
                    <th class="px-6 py-3 text-left">Clock Out</th>
                    <th class="px-6 py-3 text-left">Hours</th>
                    <th class="px-6 py-3 text-left">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border">
                @forelse($logs as $log)
                    <tr>
                        <td class="px-6 py-3 text-primary">{{ $log->date->format('M d, Y') }}</td>
                        <td class="px-6 py-3 text-secondary">{{ $log->clock_in?->format('H:i') ?? '—' }}</td>
                        <td class="px-6 py-3 text-secondary">{{ $log->clock_out?->format('H:i') ?? '—' }}</td>
                        <td class="px-6 py-3 text-secondary">{{ $log->hours_worked ?? '—' }}</td>
                        <td class="px-6 py-3">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $log->status_badge }}">
                                {{ ucfirst(str_replace('_', ' ', $log->status)) }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-6 py-8 text-center text-muted">No attendance records yet.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-6 py-4">{{ $logs->links() }}</div>
    </div>
</div>
@endsection

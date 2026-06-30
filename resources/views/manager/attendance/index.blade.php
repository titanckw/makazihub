@extends('layouts.app')

@section('title', 'Attendance')
@section('page-title', 'Staff Attendance')
@section('page-subtitle', 'Daily clock-in / clock-out logs for your team')

@section('sidebar-nav')
    @include('manager.partials.sidebar')
@endsection

@section('content')
<div class="space-y-6">

    {{-- Summary --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl border border-border p-5">
            <p class="text-xs font-semibold text-secondary uppercase tracking-wider">Total Staff</p>
            <p class="text-2xl font-bold text-primary mt-1">{{ $summary['total'] }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-border p-5">
            <p class="text-xs font-semibold text-secondary uppercase tracking-wider">Present</p>
            <p class="text-2xl font-bold text-success mt-1">{{ $summary['present'] }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-border p-5">
            <p class="text-xs font-semibold text-secondary uppercase tracking-wider">Late</p>
            <p class="text-2xl font-bold text-warning mt-1">{{ $summary['late'] }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-border p-5">
            <p class="text-xs font-semibold text-secondary uppercase tracking-wider">Absent</p>
            <p class="text-2xl font-bold text-danger mt-1">{{ max(0, $summary['absent']) }}</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-border overflow-hidden">
        <div class="px-6 py-4 border-b border-border flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <h3 class="font-semibold text-primary">Logs for {{ \Carbon\Carbon::parse($date)->format('l, M d Y') }}</h3>
            <form method="GET" class="flex items-center gap-2">
                <input type="date" name="date" value="{{ $date }}" onchange="this.form.submit()"
                    class="px-3 py-2 border border-border rounded-xl text-sm text-primary">
            </form>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-surface text-secondary text-xs uppercase tracking-wider">
                <tr>
                    <th class="px-6 py-3 text-left">Staff</th>
                    <th class="px-6 py-3 text-left">Role</th>
                    <th class="px-6 py-3 text-left">Clock In</th>
                    <th class="px-6 py-3 text-left">Clock Out</th>
                    <th class="px-6 py-3 text-left">Hours</th>
                    <th class="px-6 py-3 text-left">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border">
                @forelse($logs as $log)
                    <tr>
                        <td class="px-6 py-3 text-primary font-medium">{{ $log->staff->user->name }}</td>
                        <td class="px-6 py-3 text-secondary">{{ ucfirst($log->staff->role) }}</td>
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
                    <tr><td colspan="6" class="px-6 py-8 text-center text-muted">No clock-ins recorded for this date.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

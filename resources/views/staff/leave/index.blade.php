@extends('layouts.app')

@section('title', 'Leave')
@section('page-title', 'Leave Management')
@section('page-subtitle', 'Request time off and track your balance')

@section('sidebar-nav')
    @include('staff.partials.sidebar')
@endsection

@section('content')
<div class="space-y-6" x-data="{ showForm: false }">

    @if(session('success'))
        <div class="bg-success-bg text-success text-sm font-medium px-4 py-3 rounded-xl">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="bg-danger-bg text-danger text-sm font-medium px-4 py-3 rounded-xl">{{ session('error') }}</div>
    @endif

    {{-- Balance --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white rounded-2xl border border-border p-5">
            <p class="text-xs font-semibold text-secondary uppercase tracking-wider">Allocated Days ({{ $balance->year }})</p>
            <p class="text-2xl font-bold text-primary mt-1">{{ $balance->allocated_days }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-border p-5">
            <p class="text-xs font-semibold text-secondary uppercase tracking-wider">Used</p>
            <p class="text-2xl font-bold text-warning mt-1">{{ $balance->used_days }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-border p-5">
            <p class="text-xs font-semibold text-secondary uppercase tracking-wider">Remaining</p>
            <p class="text-2xl font-bold text-success mt-1">{{ $balance->remaining_days }}</p>
        </div>
    </div>

    <div class="flex justify-end">
        <button @click="showForm = !showForm" class="px-5 py-2.5 bg-brand-600 text-white text-sm font-semibold rounded-xl hover:bg-brand-500 transition-colors">
            + Request Leave
        </button>
    </div>

    {{-- Request form --}}
    <div x-show="showForm" x-transition class="bg-white rounded-2xl border border-border p-6">
        <form method="POST" action="{{ route('staff.leave.store') }}" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            @csrf
            <div>
                <label class="text-xs font-semibold text-secondary uppercase tracking-wider">Leave Type</label>
                <select name="type" class="mt-1 w-full px-3 py-2 border border-border rounded-xl text-sm" required>
                    <option value="annual">Annual Leave</option>
                    <option value="sick">Sick Leave</option>
                    <option value="unpaid">Unpaid Leave</option>
                    <option value="compassionate">Compassionate Leave</option>
                </select>
            </div>
            <div></div>
            <div>
                <label class="text-xs font-semibold text-secondary uppercase tracking-wider">Start Date</label>
                <input type="date" name="start_date" class="mt-1 w-full px-3 py-2 border border-border rounded-xl text-sm" required>
            </div>
            <div>
                <label class="text-xs font-semibold text-secondary uppercase tracking-wider">End Date</label>
                <input type="date" name="end_date" class="mt-1 w-full px-3 py-2 border border-border rounded-xl text-sm" required>
            </div>
            <div class="sm:col-span-2">
                <label class="text-xs font-semibold text-secondary uppercase tracking-wider">Reason</label>
                <textarea name="reason" rows="3" class="mt-1 w-full px-3 py-2 border border-border rounded-xl text-sm"></textarea>
            </div>
            <div class="sm:col-span-2 flex justify-end">
                <button type="submit" class="px-5 py-2.5 bg-brand-600 text-white text-sm font-semibold rounded-xl hover:bg-brand-500 transition-colors">
                    Submit Request
                </button>
            </div>
        </form>
    </div>

    {{-- History --}}
    <div class="bg-white rounded-2xl border border-border overflow-hidden">
        <div class="px-6 py-4 border-b border-border">
            <h3 class="font-semibold text-primary">My Leave Requests</h3>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-surface text-secondary text-xs uppercase tracking-wider">
                <tr>
                    <th class="px-6 py-3 text-left">Type</th>
                    <th class="px-6 py-3 text-left">Dates</th>
                    <th class="px-6 py-3 text-left">Days</th>
                    <th class="px-6 py-3 text-left">Status</th>
                    <th class="px-6 py-3 text-left">Manager Comment</th>
                    <th class="px-6 py-3 text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border">
                @forelse($requests as $leave)
                    <tr>
                        <td class="px-6 py-3 text-primary">{{ $leave->type_label }}</td>
                        <td class="px-6 py-3 text-secondary">{{ $leave->start_date->format('M d') }} – {{ $leave->end_date->format('M d, Y') }}</td>
                        <td class="px-6 py-3 text-secondary">{{ $leave->days }}</td>
                        <td class="px-6 py-3">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $leave->status_badge }}">
                                {{ ucfirst($leave->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-3 text-secondary">{{ $leave->manager_comment ?? '—' }}</td>
                        <td class="px-6 py-3 text-right">
                            @if($leave->status === 'pending')
                                <form method="POST" action="{{ route('staff.leave.cancel', $leave) }}" onsubmit="return confirm('Cancel this request?')">
                                    @csrf
                                    <button class="text-danger text-xs font-semibold hover:underline">Cancel</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-6 py-8 text-center text-muted">No leave requests yet.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-6 py-4">{{ $requests->links() }}</div>
    </div>
</div>
@endsection

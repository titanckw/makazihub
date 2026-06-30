@extends('layouts.app')

@section('title', 'Leave Requests')
@section('page-title', 'Leave Management')
@section('page-subtitle', 'Review staff leave requests and balances')

@section('sidebar-nav')
    @include('manager.partials.sidebar')
@endsection

@section('content')
<div class="space-y-6">

    @if(session('success'))
        <div class="bg-success-bg text-success text-sm font-medium px-4 py-3 rounded-xl">{{ session('success') }}</div>
    @endif

    {{-- Balances overview --}}
    <div class="bg-white rounded-2xl border border-border overflow-hidden">
        <div class="px-6 py-4 border-b border-border flex items-center justify-between">
            <h3 class="font-semibold text-primary">Team Leave Balances ({{ now()->year }})</h3>
            @if($pendingCount > 0)
                <span class="px-2.5 py-0.5 bg-warning-bg text-warning text-xs font-semibold rounded-full">{{ $pendingCount }} pending</span>
            @endif
        </div>
        <table class="w-full text-sm">
            <thead class="bg-surface text-secondary text-xs uppercase tracking-wider">
                <tr>
                    <th class="px-6 py-3 text-left">Staff</th>
                    <th class="px-6 py-3 text-left">Allocated</th>
                    <th class="px-6 py-3 text-left">Used</th>
                    <th class="px-6 py-3 text-left">Remaining</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border">
                @forelse($balances as $staff)
                    @php $bal = $staff->leaveBalances->first(); @endphp
                    <tr>
                        <td class="px-6 py-3 text-primary font-medium">{{ $staff->user->name }}</td>
                        <td class="px-6 py-3 text-secondary">{{ $bal->allocated_days ?? 21 }}</td>
                        <td class="px-6 py-3 text-secondary">{{ $bal->used_days ?? 0 }}</td>
                        <td class="px-6 py-3 text-success font-medium">{{ $bal ? $bal->remaining_days : 21 }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-6 py-8 text-center text-muted">No staff yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Requests --}}
    <div class="bg-white rounded-2xl border border-border overflow-hidden">
        <div class="px-6 py-4 border-b border-border flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <h3 class="font-semibold text-primary">Leave Requests</h3>
            <form method="GET" class="flex gap-2">
                <select name="status" onchange="this.form.submit()" class="px-3 py-2 border border-border rounded-xl text-sm">
                    <option value="">All Statuses</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </form>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-surface text-secondary text-xs uppercase tracking-wider">
                <tr>
                    <th class="px-6 py-3 text-left">Staff</th>
                    <th class="px-6 py-3 text-left">Type</th>
                    <th class="px-6 py-3 text-left">Dates</th>
                    <th class="px-6 py-3 text-left">Days</th>
                    <th class="px-6 py-3 text-left">Reason</th>
                    <th class="px-6 py-3 text-left">Status</th>
                    <th class="px-6 py-3 text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border">
                @forelse($requests as $leave)
                    <tr x-data="{ open: false }">
                        <td class="px-6 py-3 text-primary font-medium">{{ $leave->staff->user->name }}</td>
                        <td class="px-6 py-3 text-secondary">{{ $leave->type_label }}</td>
                        <td class="px-6 py-3 text-secondary">{{ $leave->start_date->format('M d') }} – {{ $leave->end_date->format('M d, Y') }}</td>
                        <td class="px-6 py-3 text-secondary">{{ $leave->days }}</td>
                        <td class="px-6 py-3 text-secondary max-w-[200px] truncate">{{ $leave->reason ?? '—' }}</td>
                        <td class="px-6 py-3">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $leave->status_badge }}">
                                {{ ucfirst($leave->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-3 text-right">
                            @if($leave->status === 'pending')
                                <button @click="open = !open" class="text-brand-600 text-xs font-semibold hover:underline">Review</button>
                            @endif
                        </td>
                    </tr>
                    @if($leave->status === 'pending')
                    <tr x-show="open" x-transition>
                        <td colspan="7" class="px-6 py-4 bg-surface/50">
                            <form method="POST" action="{{ route('manager.leave.review', $leave) }}" class="flex flex-col sm:flex-row gap-3 items-start sm:items-center">
                                @csrf
                                @method('PATCH')
                                <textarea name="comment" placeholder="Optional comment..." rows="1"
                                    class="flex-1 px-3 py-2 border border-border rounded-xl text-sm w-full"></textarea>
                                <div class="flex gap-2 shrink-0">
                                    <button type="submit" name="decision" value="approved"
                                        class="px-4 py-2 bg-success text-white text-xs font-semibold rounded-xl hover:opacity-90">Approve</button>
                                    <button type="submit" name="decision" value="rejected"
                                        class="px-4 py-2 bg-danger text-white text-xs font-semibold rounded-xl hover:opacity-90">Reject</button>
                                </div>
                            </form>
                        </td>
                    </tr>
                    @endif
                @empty
                    <tr><td colspan="7" class="px-6 py-8 text-center text-muted">No leave requests found.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-6 py-4">{{ $requests->links() }}</div>
    </div>
</div>
@endsection

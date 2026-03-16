@extends('layouts.app')

@section('title', 'Lease Details')
@section('page-title', 'Lease Details')
@section('page-subtitle', ($lease->unit->unit_number ?? '—') . ' · ' . ($lease->unit->property->name ?? '—'))

@section('sidebar-nav')
    @include('manager.partials.sidebar')
@endsection

@section('content')
<div class="space-y-6" x-data>

    {{-- Header Actions --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('manager.leases.index') }}" class="inline-flex items-center gap-2 text-sm text-secondary hover:text-primary transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to Leases
            </a>
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $lease->status_badge }}">
                {{ $lease->status_label }}
            </span>
            @if($isExpiringSoon && $lease->status === 'active')
                <span class="text-xs text-warning font-semibold">⚠ Expires in {{ $daysLeft }} days</span>
            @endif
        </div>

        @if($lease->status === 'active')
        <div class="flex gap-2">
            <button @click="$dispatch('open-renew')"
                class="text-sm bg-brand-600 hover:bg-brand-500 text-white px-4 py-2 rounded-xl font-semibold transition-colors">
                Renew
            </button>
            <a href="{{ route('manager.leases.edit', $lease) }}"
               class="text-sm bg-navy-500 hover:bg-navy-400 text-white px-4 py-2 rounded-xl font-medium transition-colors">
                Edit
            </a>
            <button @click="$dispatch('open-terminate')"
                class="text-sm border border-danger text-danger hover:bg-danger-bg px-4 py-2 rounded-xl font-medium transition-colors">
                Terminate
            </button>
        </div>
        @endif
    </div>

    {{-- KPIs --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white rounded-2xl border-t-4 border-brand-600 border-x border-b border-border p-5">
            <p class="text-xs font-semibold text-muted uppercase tracking-wider mb-1">Total Collected</p>
            <p class="text-2xl font-bold text-primary">KES {{ number_format($totalPaid, 2) }}</p>
        </div>
        <div class="bg-white rounded-2xl border-t-4 border-danger border-x border-b border-border p-5">
            <p class="text-xs font-semibold text-muted uppercase tracking-wider mb-1">Outstanding</p>
            <p class="text-2xl font-bold text-primary">KES {{ number_format($totalOutstanding, 2) }}</p>
        </div>
        <div class="bg-white rounded-2xl border-t-4 border-navy-500 border-x border-b border-border p-5">
            <p class="text-xs font-semibold text-muted uppercase tracking-wider mb-1">Total Invoiced</p>
            <p class="text-2xl font-bold text-primary">KES {{ number_format($totalExpected, 2) }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Lease Info --}}
        <div class="lg:col-span-1 space-y-4">
            <div class="bg-white rounded-2xl border border-border p-6 space-y-4">
                <h2 class="text-xs font-bold text-secondary uppercase tracking-wider">Lease Details</h2>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between gap-4">
                        <span class="text-muted shrink-0">Tenant</span>
                        <a href="{{ route('manager.tenants.show', $lease->tenant) }}" class="font-medium text-brand-600 hover:underline text-right">
                            {{ $lease->tenant->user->name ?? '—' }}
                        </a>
                    </div>
                    <div class="flex justify-between gap-4">
                        <span class="text-muted shrink-0">Unit</span>
                        <span class="font-medium text-primary text-right">{{ $lease->unit->unit_number ?? '—' }}</span>
                    </div>
                    <div class="flex justify-between gap-4">
                        <span class="text-muted shrink-0">Property</span>
                        <span class="font-medium text-primary text-right">{{ $lease->unit->property->name ?? '—' }}</span>
                    </div>
                    <div class="border-t border-border pt-3"></div>
                    <div class="flex justify-between">
                        <span class="text-muted">Start</span>
                        <span class="font-medium text-primary">{{ $lease->start_date->format('d M Y') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-muted">End</span>
                        <span class="font-medium text-primary">{{ $lease->end_date->format('d M Y') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-muted">Monthly Rent</span>
                        <span class="font-bold text-primary">KES {{ number_format($lease->rent_amount, 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-muted">Deposit</span>
                        <span class="font-medium text-primary">KES {{ number_format($lease->deposit_amount, 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-muted">Due Day</span>
                        <span class="font-medium text-primary">{{ $lease->payment_day }}th of month</span>
                    </div>
                </div>
            </div>

            @if($lease->termination_reason)
            <div class="bg-danger-bg border border-danger/20 rounded-2xl p-4">
                <p class="text-xs font-bold text-danger uppercase mb-1">Termination Reason</p>
                <p class="text-sm text-danger">{{ $lease->termination_reason }}</p>
                @if($lease->terminated_at)
                    <p class="text-xs text-danger/70 mt-1">{{ $lease->terminated_at->format('d M Y') }}</p>
                @endif
            </div>
            @endif

            @if($lease->notes)
            <div class="bg-white rounded-2xl border border-border p-4">
                <p class="text-xs font-bold text-muted uppercase mb-2">Notes</p>
                <p class="text-sm text-secondary">{{ $lease->notes }}</p>
            </div>
            @endif
        </div>

        {{-- Invoice History --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl border border-border overflow-hidden">
                <div class="px-6 py-4 border-b border-border flex items-center justify-between">
                    <h2 class="text-sm font-bold text-primary">Invoice History</h2>
                    <span class="text-xs text-muted">{{ $lease->invoices->count() }} invoices</span>
                </div>
                @forelse($lease->invoices->sortByDesc('due_date') as $invoice)
                <div class="px-6 py-4 border-b border-border last:border-0 hover:bg-surface/60 transition-colors">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="font-semibold text-primary text-sm">KES {{ number_format($invoice->amount, 2) }}</p>
                            <p class="text-xs text-muted">Due: {{ \Carbon\Carbon::parse($invoice->due_date)->format('d M Y') }}</p>
                        </div>
                        @php
                            $badge = match($invoice->status) {
                                'paid'    => 'bg-success-bg text-success',
                                'partial' => 'bg-info-bg text-info',
                                'overdue' => 'bg-danger-bg text-danger',
                                default   => 'bg-warning-bg text-warning',
                            };
                        @endphp
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $badge }}">
                            {{ ucfirst($invoice->status) }}
                        </span>
                    </div>
                </div>
                @empty
                <div class="py-12 text-center text-muted text-sm">
                    No invoices yet — coming in Module 6.
                </div>
                @endforelse
            </div>
        </div>

    </div>
</div>

{{-- TERMINATE MODAL --}}
<div x-data="{ open: false }" @open-terminate.window="open = true" x-show="open" x-cloak
     class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50"
     @keydown.escape.window="open = false">
    <div class="bg-white rounded-2xl border border-border w-full max-w-md shadow-2xl" @click.stop>
        <div class="p-6 border-b border-border">
            <h3 class="font-bold text-primary">Terminate Lease</h3>
            <p class="text-sm text-muted mt-1">This will free up the unit and mark the lease as terminated.</p>
        </div>
        <form action="{{ route('manager.leases.terminate', $lease) }}" method="POST" class="p-6 space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-secondary mb-1.5">Termination Date <span class="text-danger">*</span></label>
                <input type="date" name="termination_date" value="{{ now()->format('Y-m-d') }}" required
                    class="w-full text-sm border border-border rounded-xl px-3 py-2.5 bg-surface focus:outline-none focus:ring-2 focus:ring-brand-400">
            </div>
            <div>
                <label class="block text-sm font-medium text-secondary mb-1.5">Reason <span class="text-danger">*</span></label>
                <textarea name="termination_reason" rows="3" required
                    placeholder="e.g. Tenant vacated, non-payment, mutual agreement…"
                    class="w-full text-sm border border-border rounded-xl px-3 py-2.5 bg-surface focus:outline-none focus:ring-2 focus:ring-brand-400 resize-none"></textarea>
            </div>
            <div class="flex justify-end gap-3 pt-2">
                <button type="button" @click="open = false"
                    class="text-sm border border-border text-secondary hover:bg-surface px-4 py-2 rounded-xl transition-colors">Cancel</button>
                <button type="submit"
                    class="text-sm bg-danger hover:bg-red-700 text-white px-5 py-2 rounded-xl font-semibold transition-colors">Terminate</button>
            </div>
        </form>
    </div>
</div>

{{-- RENEW MODAL --}}
<div x-data="{ open: false }" @open-renew.window="open = true" x-show="open" x-cloak
     class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50"
     @keydown.escape.window="open = false">
    <div class="bg-white rounded-2xl border border-border w-full max-w-md shadow-2xl" @click.stop>
        <div class="p-6 border-b border-border">
            <h3 class="font-bold text-primary">Renew Lease</h3>
            <p class="text-sm text-muted mt-1">Current lease ends <strong>{{ $lease->end_date->format('d M Y') }}</strong>. A new lease starts from that date.</p>
        </div>
        <form action="{{ route('manager.leases.renew', $lease) }}" method="POST" class="p-6 space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-secondary mb-1.5">New End Date <span class="text-danger">*</span></label>
                <input type="date" name="new_end_date" value="{{ $lease->end_date->addYear()->format('Y-m-d') }}" required
                    class="w-full text-sm border border-border rounded-xl px-3 py-2.5 bg-surface focus:outline-none focus:ring-2 focus:ring-brand-400">
            </div>
            <div>
                <label class="block text-sm font-medium text-secondary mb-1.5">New Monthly Rent (KES) <span class="text-danger">*</span></label>
                <input type="number" name="rent_amount" value="{{ $lease->rent_amount }}" step="0.01" min="0" required
                    class="w-full text-sm border border-border rounded-xl px-3 py-2.5 bg-surface focus:outline-none focus:ring-2 focus:ring-brand-400">
            </div>
            <div class="flex justify-end gap-3 pt-2">
                <button type="button" @click="open = false"
                    class="text-sm border border-border text-secondary hover:bg-surface px-4 py-2 rounded-xl transition-colors">Cancel</button>
                <button type="submit"
                    class="text-sm bg-brand-600 hover:bg-brand-500 text-white px-5 py-2 rounded-xl font-semibold transition-colors">Renew Lease</button>
            </div>
        </form>
    </div>
</div>

@endsection

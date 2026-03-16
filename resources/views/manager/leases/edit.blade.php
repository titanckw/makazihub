@extends('layouts.app')

@section('title', 'Edit Lease')
@section('page-title', 'Edit Lease')
@section('page-subtitle', ($lease->tenant->user->name ?? '—') . ' · ' . ($lease->unit->unit_number ?? '—'))

@section('sidebar-nav')
    @include('manager.partials.sidebar')
@endsection

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    <a href="{{ route('manager.leases.show', $lease) }}" class="inline-flex items-center gap-2 text-sm text-secondary hover:text-primary transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Back to Lease
    </a>

    {{-- Info notice --}}
    <div class="flex items-start gap-3 p-4 rounded-xl bg-info-bg border border-info/20">
        <svg class="w-5 h-5 text-info mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <p class="text-sm text-info">Tenant and unit cannot be changed on an active lease. To reassign, terminate this lease and create a new one.</p>
    </div>

    <form action="{{ route('manager.leases.update', $lease) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        {{-- Read-only --}}
        <div class="bg-white rounded-2xl border border-border p-6 space-y-4">
            <h2 class="text-sm font-bold text-primary uppercase tracking-wider">Assignment (read-only)</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-secondary mb-1.5">Tenant</label>
                    <input type="text" value="{{ $lease->tenant->user->name ?? '—' }}" disabled
                        class="w-full text-sm border border-border rounded-xl px-3 py-2.5 bg-surface/50 text-muted cursor-not-allowed">
                </div>
                <div>
                    <label class="block text-sm font-medium text-secondary mb-1.5">Unit</label>
                    <input type="text" value="{{ ($lease->unit->unit_number ?? '—') . ' — ' . ($lease->unit->property->name ?? '') }}" disabled
                        class="w-full text-sm border border-border rounded-xl px-3 py-2.5 bg-surface/50 text-muted cursor-not-allowed">
                </div>
            </div>
        </div>

        {{-- Editable --}}
        <div class="bg-white rounded-2xl border border-border p-6 space-y-5">
            <h2 class="text-sm font-bold text-primary uppercase tracking-wider">Lease Terms</h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-secondary mb-1.5">Start Date (read-only)</label>
                    <input type="date" value="{{ $lease->start_date->format('Y-m-d') }}" disabled
                        class="w-full text-sm border border-border rounded-xl px-3 py-2.5 bg-surface/50 text-muted cursor-not-allowed">
                </div>
                <div>
                    <label class="block text-sm font-medium text-secondary mb-1.5">End Date <span class="text-danger">*</span></label>
                    <input type="date" name="end_date" value="{{ old('end_date', $lease->end_date->format('Y-m-d')) }}" required
                        class="w-full text-sm border border-border rounded-xl px-3 py-2.5 bg-surface focus:outline-none focus:ring-2 focus:ring-brand-400">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-secondary mb-1.5">Monthly Rent (KES) <span class="text-danger">*</span></label>
                    <input type="number" name="rent_amount" value="{{ old('rent_amount', $lease->rent_amount) }}" step="0.01" min="0" required
                        class="w-full text-sm border border-border rounded-xl px-3 py-2.5 bg-surface focus:outline-none focus:ring-2 focus:ring-brand-400">
                </div>
                <div>
                    <label class="block text-sm font-medium text-secondary mb-1.5">Security Deposit (KES) <span class="text-danger">*</span></label>
                    <input type="number" name="deposit_amount" value="{{ old('deposit_amount', $lease->deposit_amount) }}" step="0.01" min="0" required
                        class="w-full text-sm border border-border rounded-xl px-3 py-2.5 bg-surface focus:outline-none focus:ring-2 focus:ring-brand-400">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-secondary mb-1.5">Rent Due Day <span class="text-danger">*</span></label>
                <input type="number" name="payment_day" value="{{ old('payment_day', $lease->payment_day) }}" min="1" max="28" required
                    class="w-full sm:w-32 text-sm border border-border rounded-xl px-3 py-2.5 bg-surface focus:outline-none focus:ring-2 focus:ring-brand-400">
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-border p-6">
            <label class="block text-sm font-medium text-secondary mb-1.5">Notes</label>
            <textarea name="notes" rows="3"
                class="w-full text-sm border border-border rounded-xl px-3 py-2.5 bg-surface focus:outline-none focus:ring-2 focus:ring-brand-400 resize-none">{{ old('notes', $lease->notes) }}</textarea>
        </div>

        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('manager.leases.show', $lease) }}" class="text-sm border border-border text-secondary hover:bg-surface px-5 py-2.5 rounded-xl transition-colors font-medium">Cancel</a>
            <button type="submit" class="text-sm bg-brand-600 hover:bg-brand-500 text-white px-6 py-2.5 rounded-xl font-semibold transition-colors">Save Changes</button>
        </div>
    </form>
</div>
@endsection

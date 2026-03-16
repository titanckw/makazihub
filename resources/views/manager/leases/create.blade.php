@extends('layouts.app')

@section('title', 'New Lease')
@section('page-title', 'New Lease')
@section('page-subtitle', 'Assign a tenant to a unit and set lease terms')

@section('sidebar-nav')
    @include('manager.partials.sidebar')
@endsection

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    <a href="{{ route('manager.leases.index') }}" class="inline-flex items-center gap-2 text-sm text-secondary hover:text-primary transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Back to Leases
    </a>

    <form action="{{ route('manager.leases.store') }}" method="POST" class="space-y-6">
        @csrf

        {{-- Assignment --}}
        <div class="bg-white rounded-2xl border border-border p-6 space-y-5">
            <h2 class="text-sm font-bold text-primary uppercase tracking-wider">Assignment</h2>

            <div>
                <label class="block text-sm font-medium text-secondary mb-1.5">Tenant <span class="text-danger">*</span></label>
                <select name="tenant_id" required
                    class="w-full text-sm border border-border rounded-xl px-3 py-2.5 bg-surface focus:outline-none focus:ring-2 focus:ring-brand-400 @error('tenant_id') border-danger @enderror">
                    <option value="">— Select Tenant —</option>
                    @foreach($tenants as $tenant)
                        <option value="{{ $tenant->id }}"
                            {{ (old('tenant_id', $selectedTenant?->id) == $tenant->id) ? 'selected' : '' }}>
                            {{ $tenant->user->name }} ({{ $tenant->id_number }})
                        </option>
                    @endforeach
                </select>
                @error('tenant_id') <p class="text-xs text-danger mt-1">{{ $message }}</p> @enderror
                @if($tenants->isEmpty())
                    <p class="text-xs text-warning mt-1">No active tenants. <a href="{{ route('manager.tenants.create') }}" class="underline font-medium">Create a tenant first</a>.</p>
                @endif
            </div>

            <div>
                <label class="block text-sm font-medium text-secondary mb-1.5">Unit <span class="text-danger">*</span></label>
                <select name="unit_id" required
                    class="w-full text-sm border border-border rounded-xl px-3 py-2.5 bg-surface focus:outline-none focus:ring-2 focus:ring-brand-400 @error('unit_id') border-danger @enderror">
                    <option value="">— Select Vacant Unit —</option>
                    @foreach($units as $unit)
                        <option value="{{ $unit->id }}" {{ old('unit_id') == $unit->id ? 'selected' : '' }}>
                            {{ $unit->unit_number }} — {{ $unit->property->name }}
                            @if($unit->rent_amount) (KES {{ number_format($unit->rent_amount, 0) }}/mo) @endif
                        </option>
                    @endforeach
                </select>
                @error('unit_id') <p class="text-xs text-danger mt-1">{{ $message }}</p> @enderror
                @if($units->isEmpty())
                    <p class="text-xs text-warning mt-1">No vacant units available.</p>
                @endif
            </div>
        </div>

        {{-- Lease Terms --}}
        <div class="bg-white rounded-2xl border border-border p-6 space-y-5">
            <h2 class="text-sm font-bold text-primary uppercase tracking-wider">Lease Terms</h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-secondary mb-1.5">Start Date <span class="text-danger">*</span></label>
                    <input type="date" name="start_date" value="{{ old('start_date', now()->format('Y-m-d')) }}" required
                        class="w-full text-sm border border-border rounded-xl px-3 py-2.5 bg-surface focus:outline-none focus:ring-2 focus:ring-brand-400">
                    @error('start_date') <p class="text-xs text-danger mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-secondary mb-1.5">End Date <span class="text-danger">*</span></label>
                    <input type="date" name="end_date" value="{{ old('end_date', now()->addYear()->format('Y-m-d')) }}" required
                        class="w-full text-sm border border-border rounded-xl px-3 py-2.5 bg-surface focus:outline-none focus:ring-2 focus:ring-brand-400">
                    @error('end_date') <p class="text-xs text-danger mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-secondary mb-1.5">Monthly Rent (KES) <span class="text-danger">*</span></label>
                    <input type="number" name="rent_amount" value="{{ old('rent_amount') }}" step="0.01" min="0" required
                        class="w-full text-sm border border-border rounded-xl px-3 py-2.5 bg-surface focus:outline-none focus:ring-2 focus:ring-brand-400">
                    @error('rent_amount') <p class="text-xs text-danger mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-secondary mb-1.5">Security Deposit (KES) <span class="text-danger">*</span></label>
                    <input type="number" name="deposit_amount" value="{{ old('deposit_amount') }}" step="0.01" min="0" required
                        class="w-full text-sm border border-border rounded-xl px-3 py-2.5 bg-surface focus:outline-none focus:ring-2 focus:ring-brand-400">
                    @error('deposit_amount') <p class="text-xs text-danger mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-secondary mb-1.5">
                    Rent Due Day <span class="text-danger">*</span>
                    <span class="font-normal text-muted">(day of month, 1–28)</span>
                </label>
                <input type="number" name="payment_day" value="{{ old('payment_day', 1) }}" min="1" max="28" required
                    class="w-full sm:w-32 text-sm border border-border rounded-xl px-3 py-2.5 bg-surface focus:outline-none focus:ring-2 focus:ring-brand-400">
                @error('payment_day') <p class="text-xs text-danger mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        {{-- Notes --}}
        <div class="bg-white rounded-2xl border border-border p-6">
            <label class="block text-sm font-medium text-secondary mb-1.5">Notes (optional)</label>
            <textarea name="notes" rows="3"
                class="w-full text-sm border border-border rounded-xl px-3 py-2.5 bg-surface focus:outline-none focus:ring-2 focus:ring-brand-400 resize-none">{{ old('notes') }}</textarea>
        </div>

        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('manager.leases.index') }}" class="text-sm border border-border text-secondary hover:bg-surface px-5 py-2.5 rounded-xl transition-colors font-medium">Cancel</a>
            <button type="submit" class="text-sm bg-brand-600 hover:bg-brand-500 text-white px-6 py-2.5 rounded-xl font-semibold transition-colors">
                Create Lease
            </button>
        </div>
    </form>
</div>
@endsection

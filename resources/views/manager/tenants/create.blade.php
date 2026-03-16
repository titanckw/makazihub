@extends('layouts.app')

@section('title', 'Add Tenant')
@section('page-title', 'Add Tenant')
@section('page-subtitle', 'Create a new tenant profile and login account')

@section('sidebar-nav')
    @include('manager.partials.sidebar')
@endsection

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    {{-- Back --}}
    <a href="{{ route('manager.tenants.index') }}" class="inline-flex items-center gap-2 text-sm text-secondary hover:text-primary transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Back to Tenants
    </a>

    <form action="{{ route('manager.tenants.store') }}" method="POST" class="space-y-6">
        @csrf

        {{-- Personal Information --}}
        <div class="bg-white rounded-2xl border border-border p-6 space-y-5">
            <h2 class="text-sm font-bold text-primary uppercase tracking-wider">Personal Information</h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-secondary mb-1.5">Full Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                        class="w-full text-sm border border-border rounded-xl px-3 py-2.5 bg-surface focus:outline-none focus:ring-2 focus:ring-brand-400 @error('name') border-danger @enderror">
                    @error('name') <p class="text-xs text-danger mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-secondary mb-1.5">ID / Passport Number <span class="text-danger">*</span></label>
                    <input type="text" name="id_number" value="{{ old('id_number') }}" required
                        class="w-full text-sm border border-border rounded-xl px-3 py-2.5 bg-surface focus:outline-none focus:ring-2 focus:ring-brand-400 @error('id_number') border-danger @enderror">
                    @error('id_number') <p class="text-xs text-danger mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-secondary mb-1.5">Email Address <span class="text-danger">*</span></label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                        class="w-full text-sm border border-border rounded-xl px-3 py-2.5 bg-surface focus:outline-none focus:ring-2 focus:ring-brand-400 @error('email') border-danger @enderror">
                    @error('email') <p class="text-xs text-danger mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-secondary mb-1.5">Phone Number <span class="text-danger">*</span></label>
                    <input type="text" name="phone" value="{{ old('phone') }}" placeholder="07XX XXX XXX" required
                        class="w-full text-sm border border-border rounded-xl px-3 py-2.5 bg-surface focus:outline-none focus:ring-2 focus:ring-brand-400 @error('phone') border-danger @enderror">
                    @error('phone') <p class="text-xs text-danger mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-secondary mb-1.5">Occupation</label>
                    <input type="text" name="occupation" value="{{ old('occupation') }}"
                        class="w-full text-sm border border-border rounded-xl px-3 py-2.5 bg-surface focus:outline-none focus:ring-2 focus:ring-brand-400">
                </div>
                <div>
                    <label class="block text-sm font-medium text-secondary mb-1.5">Employer / Business</label>
                    <input type="text" name="employer" value="{{ old('employer') }}"
                        class="w-full text-sm border border-border rounded-xl px-3 py-2.5 bg-surface focus:outline-none focus:ring-2 focus:ring-brand-400">
                </div>
            </div>
        </div>

        {{-- Emergency Contact --}}
        <div class="bg-white rounded-2xl border border-border p-6 space-y-5">
            <h2 class="text-sm font-bold text-primary uppercase tracking-wider">Emergency Contact</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-secondary mb-1.5">Contact Name</label>
                    <input type="text" name="emergency_contact_name" value="{{ old('emergency_contact_name') }}"
                        class="w-full text-sm border border-border rounded-xl px-3 py-2.5 bg-surface focus:outline-none focus:ring-2 focus:ring-brand-400">
                </div>
                <div>
                    <label class="block text-sm font-medium text-secondary mb-1.5">Contact Phone</label>
                    <input type="text" name="emergency_contact_phone" value="{{ old('emergency_contact_phone') }}"
                        class="w-full text-sm border border-border rounded-xl px-3 py-2.5 bg-surface focus:outline-none focus:ring-2 focus:ring-brand-400">
                </div>
            </div>
        </div>

        {{-- Notes --}}
        <div class="bg-white rounded-2xl border border-border p-6">
            <label class="block text-sm font-medium text-secondary mb-1.5">Notes (optional)</label>
            <textarea name="notes" rows="3" placeholder="Any additional notes about this tenant…"
                class="w-full text-sm border border-border rounded-xl px-3 py-2.5 bg-surface focus:outline-none focus:ring-2 focus:ring-brand-400 resize-none">{{ old('notes') }}</textarea>
        </div>

        {{-- Info Banner --}}
        <div class="flex items-start gap-3 p-4 rounded-xl bg-info-bg border border-info/20">
            <svg class="w-5 h-5 text-info mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p class="text-sm text-info">A login account will be created for this tenant with the default password <strong>Tenant@1234</strong>. Advise them to change it on first login.</p>
        </div>

        {{-- Submit --}}
        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('manager.tenants.index') }}" class="text-sm border border-border text-secondary hover:bg-surface px-5 py-2.5 rounded-xl transition-colors font-medium">
                Cancel
            </a>
            <button type="submit" class="text-sm bg-brand-600 hover:bg-brand-500 text-white px-6 py-2.5 rounded-xl font-semibold transition-colors">
                Create Tenant
            </button>
        </div>

    </form>
</div>
@endsection

@extends('layouts.app')

@section('title', 'Add Staff Member')
@section('page-title', 'Add Staff Member')
@section('page-subtitle', 'Create a new staff account')

@section('sidebar-nav')
    @include('manager.partials.sidebar')
@endsection

@section('content')
<div class="max-w-2xl">
    <form method="POST" action="{{ route('manager.staff.store') }}" class="space-y-6">
        @csrf

        {{-- Personal Details --}}
        <div class="bg-white rounded-2xl border border-border p-6 space-y-5">
            <h2 class="text-sm font-semibold text-primary">Personal Details</h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-secondary mb-1.5">Full Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                        class="w-full text-sm border border-border rounded-xl px-3 py-2 bg-surface focus:outline-none focus:ring-2 focus:ring-brand-400
                               @error('name') border-danger @enderror">
                    @error('name')<p class="text-danger text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-secondary mb-1.5">Phone <span class="text-danger">*</span></label>
                    <input type="text" name="phone" value="{{ old('phone') }}" required
                        class="w-full text-sm border border-border rounded-xl px-3 py-2 bg-surface focus:outline-none focus:ring-2 focus:ring-brand-400
                               @error('phone') border-danger @enderror">
                    @error('phone')<p class="text-danger text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-secondary mb-1.5">Email Address <span class="text-danger">*</span></label>
                <input type="email" name="email" value="{{ old('email') }}" required
                    class="w-full text-sm border border-border rounded-xl px-3 py-2 bg-surface focus:outline-none focus:ring-2 focus:ring-brand-400
                           @error('email') border-danger @enderror">
                @error('email')<p class="text-danger text-xs mt-1">{{ $message }}</p>@enderror
                <p class="text-xs text-muted mt-1">This will be their login email. Default password: <span class="font-mono font-semibold">Staff@1234</span></p>
            </div>

            <div>
                <label class="block text-xs font-semibold text-secondary mb-1.5">ID Number</label>
                <input type="text" name="id_number" value="{{ old('id_number') }}"
                    class="w-full text-sm border border-border rounded-xl px-3 py-2 bg-surface focus:outline-none focus:ring-2 focus:ring-brand-400
                           @error('id_number') border-danger @enderror">
                @error('id_number')<p class="text-danger text-xs mt-1">{{ $message }}</p>@enderror
            </div>
        </div>

        {{-- Employment Details --}}
        <div class="bg-white rounded-2xl border border-border p-6 space-y-5">
            <h2 class="text-sm font-semibold text-primary">Employment Details</h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-secondary mb-1.5">Role / Job Title <span class="text-danger">*</span></label>
                    <input type="text" name="role" value="{{ old('role') }}" required
                        placeholder="e.g. Caretaker, Security, Cleaner"
                        class="w-full text-sm border border-border rounded-xl px-3 py-2 bg-surface focus:outline-none focus:ring-2 focus:ring-brand-400
                               @error('role') border-danger @enderror">
                    @error('role')<p class="text-danger text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-secondary mb-1.5">Department</label>
                    <input type="text" name="department" value="{{ old('department') }}"
                        placeholder="e.g. Maintenance, Admin"
                        class="w-full text-sm border border-border rounded-xl px-3 py-2 bg-surface focus:outline-none focus:ring-2 focus:ring-brand-400
                               @error('department') border-danger @enderror">
                    @error('department')<p class="text-danger text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-secondary mb-1.5">Employment Type <span class="text-danger">*</span></label>
                    <select name="employment_type" required
                        class="w-full text-sm border border-border rounded-xl px-3 py-2 bg-surface focus:outline-none focus:ring-2 focus:ring-brand-400
                               @error('employment_type') border-danger @enderror">
                        <option value="full_time"  {{ old('employment_type') === 'full_time'  ? 'selected' : '' }}>Full Time</option>
                        <option value="part_time"  {{ old('employment_type') === 'part_time'  ? 'selected' : '' }}>Part Time</option>
                        <option value="contract"   {{ old('employment_type') === 'contract'   ? 'selected' : '' }}>Contract</option>
                    </select>
                    @error('employment_type')<p class="text-danger text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-secondary mb-1.5">Start Date</label>
                    <input type="date" name="start_date" value="{{ old('start_date') }}"
                        class="w-full text-sm border border-border rounded-xl px-3 py-2 bg-surface focus:outline-none focus:ring-2 focus:ring-brand-400
                               @error('start_date') border-danger @enderror">
                    @error('start_date')<p class="text-danger text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-secondary mb-1.5">Notes</label>
                <textarea name="notes" rows="3"
                    class="w-full text-sm border border-border rounded-xl px-3 py-2 bg-surface focus:outline-none focus:ring-2 focus:ring-brand-400
                           @error('notes') border-danger @enderror">{{ old('notes') }}</textarea>
                @error('notes')<p class="text-danger text-xs mt-1">{{ $message }}</p>@enderror
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex items-center gap-3">
            <button type="submit"
                class="bg-brand-600 hover:bg-brand-500 text-white text-sm font-semibold px-5 py-2.5 rounded-xl transition-colors">
                Add Staff Member
            </button>
            <a href="{{ route('manager.staff.index') }}"
                class="text-sm border border-border text-secondary hover:bg-surface px-5 py-2.5 rounded-xl transition-colors">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection

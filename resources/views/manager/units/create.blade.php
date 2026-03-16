@extends('layouts.app')

@section('title', 'Add Unit')
@section('page-title', 'Add Unit')
@section('page-subtitle', $property->name)

@section('sidebar-nav')
    @include('manager.partials.sidebar')
@endsection

@section('content')
<div class="max-w-2xl">
    <div class="bg-card rounded-2xl border border-border shadow-sm">

        <div class="px-6 py-5 border-b border-border">
            <h3 class="font-display font-700 text-primary">New Unit</h3>
            <p class="text-muted text-sm mt-0.5">Adding unit to <span class="font-600 text-secondary">{{ $property->name }}</span></p>
        </div>

        <form method="POST" action="{{ route('manager.properties.units.store', $property) }}" class="p-6 space-y-5">
            @csrf

            <div class="grid grid-cols-2 gap-4">
                {{-- Unit Number --}}
                <div>
                    <label class="block text-sm font-600 text-primary mb-1.5">Unit Number <span class="text-danger">*</span></label>
                    <input type="text" name="unit_number" value="{{ old('unit_number') }}" required
                        placeholder="e.g. A1, Unit 3B"
                        class="w-full px-4 py-2.5 rounded-xl border @error('unit_number') border-danger @else border-border @enderror bg-white text-primary placeholder-muted focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all">
                    @error('unit_number') <p class="text-danger text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Floor --}}
                <div>
                    <label class="block text-sm font-600 text-primary mb-1.5">Floor <span class="text-muted font-400">(optional)</span></label>
                    <input type="number" name="floor" value="{{ old('floor') }}" min="0" max="100"
                        placeholder="e.g. 2"
                        class="w-full px-4 py-2.5 rounded-xl border border-border bg-white text-primary placeholder-muted focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all">
                </div>
            </div>

            {{-- Unit Type --}}
            <div>
                <label class="block text-sm font-600 text-primary mb-1.5">Unit Type <span class="text-danger">*</span></label>
                <select name="unit_type" required
                    class="w-full px-4 py-2.5 rounded-xl border @error('unit_type') border-danger @else border-border @enderror bg-white text-primary focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all">
                    <option value="">Select type...</option>
                    @foreach(['studio' => 'Studio', 'bedsitter' => 'Bedsitter', '1br' => '1 Bedroom', '2br' => '2 Bedroom', '3br' => '3 Bedroom', '4br' => '4 Bedroom', 'penthouse' => 'Penthouse', 'commercial' => 'Commercial'] as $value => $label)
                    <option value="{{ $value }}" {{ old('unit_type') == $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @error('unit_type') <p class="text-danger text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                {{-- Rent Amount --}}
                <div>
                    <label class="block text-sm font-600 text-primary mb-1.5">Monthly Rent (KES) <span class="text-danger">*</span></label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-muted text-sm font-600">KES</span>
                        <input type="number" name="rent_amount" value="{{ old('rent_amount') }}" required min="0" step="0.01"
                            placeholder="0.00"
                            class="w-full pl-14 pr-4 py-2.5 rounded-xl border @error('rent_amount') border-danger @else border-border @enderror bg-white text-primary focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all">
                    </div>
                    @error('rent_amount') <p class="text-danger text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Deposit --}}
                <div>
                    <label class="block text-sm font-600 text-primary mb-1.5">Deposit Amount (KES) <span class="text-danger">*</span></label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-muted text-sm font-600">KES</span>
                        <input type="number" name="deposit_amount" value="{{ old('deposit_amount') }}" required min="0" step="0.01"
                            placeholder="0.00"
                            class="w-full pl-14 pr-4 py-2.5 rounded-xl border @error('deposit_amount') border-danger @else border-border @enderror bg-white text-primary focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all">
                    </div>
                    @error('deposit_amount') <p class="text-danger text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Description --}}
            <div>
                <label class="block text-sm font-600 text-primary mb-1.5">Description <span class="text-muted font-400">(optional)</span></label>
                <textarea name="description" rows="2"
                    placeholder="Any notes about this unit..."
                    class="w-full px-4 py-2.5 rounded-xl border border-border bg-white text-primary placeholder-muted focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all resize-none">{{ old('description') }}</textarea>
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit"
                    class="bg-brand-600 hover:bg-brand-500 text-white font-600 px-6 py-2.5 rounded-xl transition-colors">
                    Add Unit
                </button>
                <a href="{{ route('manager.properties.show', $property) }}"
                   class="text-secondary hover:text-primary font-500 px-4 py-2.5 rounded-xl hover:bg-surface transition-all">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

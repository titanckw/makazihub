@extends('layouts.app')

@section('title', 'Add Property')
@section('page-title', 'Add Property')
@section('page-subtitle', 'Register a new property')

@section('sidebar-nav')
    @include('manager.partials.sidebar')
@endsection

@section('content')
<div class="max-w-2xl">
    <div class="bg-card rounded-2xl border border-border shadow-sm">

        <div class="px-6 py-5 border-b border-border">
            <h3 class="font-display font-700 text-primary">Property Details</h3>
            <p class="text-muted text-sm mt-0.5">Fill in the details for the new property.</p>
        </div>

        <form method="POST" action="{{ route('manager.properties.store') }}" class="p-6 space-y-5">
            @csrf

            {{-- Name --}}
            <div>
                <label class="block text-sm font-600 text-primary mb-1.5">Property Name <span class="text-danger">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" required
                    placeholder="e.g. Sunset Apartments"
                    class="w-full px-4 py-2.5 rounded-xl border @error('name') border-danger @else border-border @enderror bg-white text-primary placeholder-muted focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all">
                @error('name') <p class="text-danger text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Property Type --}}
            <div>
                <label class="block text-sm font-600 text-primary mb-1.5">Property Type <span class="text-danger">*</span></label>
                <select name="property_type" required
                    class="w-full px-4 py-2.5 rounded-xl border @error('property_type') border-danger @else border-border @enderror bg-white text-primary focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all">
                    <option value="">Select type...</option>
                    @foreach(['apartment' => 'Apartment', 'maisonette' => 'Maisonette', 'townhouse' => 'Townhouse', 'bedsitter' => 'Bedsitter Block', 'commercial' => 'Commercial'] as $value => $label)
                    <option value="{{ $value }}" {{ old('property_type') == $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @error('property_type') <p class="text-danger text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Address --}}
            <div>
                <label class="block text-sm font-600 text-primary mb-1.5">Address <span class="text-danger">*</span></label>
                <textarea name="address" rows="2" required
                    placeholder="e.g. 123 Ngong Road, Karen"
                    class="w-full px-4 py-2.5 rounded-xl border @error('address') border-danger @else border-border @enderror bg-white text-primary placeholder-muted focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all resize-none">{{ old('address') }}</textarea>
                @error('address') <p class="text-danger text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- City + County --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-600 text-primary mb-1.5">City <span class="text-danger">*</span></label>
                    <input type="text" name="city" value="{{ old('city') }}" required
                        placeholder="e.g. Nairobi"
                        class="w-full px-4 py-2.5 rounded-xl border @error('city') border-danger @else border-border @enderror bg-white text-primary placeholder-muted focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all">
                    @error('city') <p class="text-danger text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-600 text-primary mb-1.5">County <span class="text-danger">*</span></label>
                    <select name="county" required
                        class="w-full px-4 py-2.5 rounded-xl border @error('county') border-danger @else border-border @enderror bg-white text-primary focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all">
                        <option value="">Select county...</option>
                        @foreach(['Nairobi', 'Mombasa', 'Kisumu', 'Nakuru', 'Eldoret', 'Thika', 'Meru', 'Nyeri', 'Kiambu', 'Machakos', 'Kajiado', 'Murang\'a', 'Kirinyaga', 'Nyandarua', 'Laikipia', 'Other'] as $county)
                        <option value="{{ $county }}" {{ old('county') == $county ? 'selected' : '' }}>{{ $county }}</option>
                        @endforeach
                    </select>
                    @error('county') <p class="text-danger text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Description --}}
            <div>
                <label class="block text-sm font-600 text-primary mb-1.5">Description <span class="text-muted font-400">(optional)</span></label>
                <textarea name="description" rows="3"
                    placeholder="Any additional notes about this property..."
                    class="w-full px-4 py-2.5 rounded-xl border border-border bg-white text-primary placeholder-muted focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all resize-none">{{ old('description') }}</textarea>
            </div>

            {{-- Tax Classification (Kenya) --}}
            <div class="border-t border-border pt-5">
                <h4 class="text-sm font-700 text-primary mb-1">Tax Classification</h4>
                <p class="text-xs text-muted mb-3">Used to compute Kenyan rental income tax (RRI / Commercial / Non-Resident WHT) on invoices for this property.</p>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-600 text-primary mb-1.5">Landlord Residency <span class="text-danger">*</span></label>
                        <select name="landlord_tax_status" required
                            class="w-full px-4 py-2.5 rounded-xl border @error('landlord_tax_status') border-danger @else border-border @enderror bg-white text-primary focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all">
                            <option value="resident" {{ old('landlord_tax_status', 'resident') == 'resident' ? 'selected' : '' }}>Resident</option>
                            <option value="non_resident" {{ old('landlord_tax_status') == 'non_resident' ? 'selected' : '' }}>Non-Resident</option>
                        </select>
                        @error('landlord_tax_status') <p class="text-danger text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-600 text-primary mb-1.5">Landlord KRA PIN</label>
                        <input type="text" name="landlord_pin" value="{{ old('landlord_pin') }}"
                            placeholder="e.g. A001234567Z"
                            class="w-full px-4 py-2.5 rounded-xl border border-border bg-white text-primary placeholder-muted focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all">
                    </div>
                </div>

                <label class="flex items-center gap-2 mt-3 text-sm text-primary">
                    <input type="checkbox" name="is_vat_registered" value="1" {{ old('is_vat_registered') ? 'checked' : '' }}
                        class="rounded border-border text-brand-600 focus:ring-brand-500">
                    Landlord is VAT-registered for this property (adds 16% VAT to commercial rent invoices)
                </label>
            </div>

            {{-- Actions --}}
            <div class="flex items-center gap-3 pt-2">
                <button type="submit"
                    class="bg-brand-600 hover:bg-brand-500 text-white font-600 px-6 py-2.5 rounded-xl transition-colors">
                    Save Property
                </button>
                <a href="{{ route('manager.properties.index') }}"
                   class="text-secondary hover:text-primary font-500 px-4 py-2.5 rounded-xl hover:bg-surface transition-all">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

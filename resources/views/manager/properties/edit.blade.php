@extends('layouts.app')

@section('title', 'Edit Property')
@section('page-title', 'Edit Property')
@section('page-subtitle', $property->name)

@section('sidebar-nav')
    @include('manager.partials.sidebar')
@endsection

@section('content')
<div class="max-w-2xl">
    <div class="bg-card rounded-2xl border border-border shadow-sm">

        <div class="px-6 py-5 border-b border-border">
            <h3 class="font-display font-700 text-primary">Edit Property Details</h3>
            <p class="text-muted text-sm mt-0.5">Update the information for {{ $property->name }}.</p>
        </div>

        <form method="POST" action="{{ route('manager.properties.update', $property) }}" class="p-6 space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-600 text-primary mb-1.5">Property Name <span class="text-danger">*</span></label>
                <input type="text" name="name" value="{{ old('name', $property->name) }}" required
                    class="w-full px-4 py-2.5 rounded-xl border @error('name') border-danger @else border-border @enderror bg-white text-primary focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all">
                @error('name') <p class="text-danger text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-600 text-primary mb-1.5">Property Type <span class="text-danger">*</span></label>
                <select name="property_type" required
                    class="w-full px-4 py-2.5 rounded-xl border border-border bg-white text-primary focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all">
                    @foreach(['apartment' => 'Apartment', 'maisonette' => 'Maisonette', 'townhouse' => 'Townhouse', 'bedsitter' => 'Bedsitter Block', 'commercial' => 'Commercial'] as $value => $label)
                    <option value="{{ $value }}" {{ old('property_type', $property->property_type) == $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-600 text-primary mb-1.5">Address <span class="text-danger">*</span></label>
                <textarea name="address" rows="2" required
                    class="w-full px-4 py-2.5 rounded-xl border border-border bg-white text-primary focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all resize-none">{{ old('address', $property->address) }}</textarea>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-600 text-primary mb-1.5">City <span class="text-danger">*</span></label>
                    <input type="text" name="city" value="{{ old('city', $property->city) }}" required
                        class="w-full px-4 py-2.5 rounded-xl border border-border bg-white text-primary focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all">
                </div>
                <div>
                    <label class="block text-sm font-600 text-primary mb-1.5">County <span class="text-danger">*</span></label>
                    <select name="county" required
                        class="w-full px-4 py-2.5 rounded-xl border border-border bg-white text-primary focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all">
                        @foreach(['Nairobi', 'Mombasa', 'Kisumu', 'Nakuru', 'Eldoret', 'Thika', 'Meru', 'Nyeri', 'Kiambu', 'Machakos', 'Kajiado', 'Murang\'a', 'Kirinyaga', 'Nyandarua', 'Laikipia', 'Other'] as $county)
                        <option value="{{ $county }}" {{ old('county', $property->county) == $county ? 'selected' : '' }}>{{ $county }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-600 text-primary mb-1.5">Description</label>
                <textarea name="description" rows="3"
                    class="w-full px-4 py-2.5 rounded-xl border border-border bg-white text-primary focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all resize-none">{{ old('description', $property->description) }}</textarea>
            </div>

            {{-- Active Status --}}
            <div class="flex items-center gap-3 p-4 bg-surface rounded-xl border border-border">
                <input type="checkbox" name="is_active" id="is_active" value="1"
                    {{ old('is_active', $property->is_active) ? 'checked' : '' }}
                    class="w-4 h-4 rounded border-border text-brand-600 focus:ring-brand-500">
                <div>
                    <label for="is_active" class="text-sm font-600 text-primary">Active Property</label>
                    <p class="text-xs text-muted">Inactive properties are hidden from reports.</p>
                </div>
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit"
                    class="bg-brand-600 hover:bg-brand-500 text-white font-600 px-6 py-2.5 rounded-xl transition-colors">
                    Save Changes
                </button>
                <a href="{{ route('manager.properties.show', $property) }}"
                   class="text-secondary hover:text-primary font-500 px-4 py-2.5 rounded-xl hover:bg-surface transition-all">
                    Cancel
                </a>

                {{-- Delete --}}
                <form method="POST" action="{{ route('manager.properties.destroy', $property) }}" class="ml-auto"
                    onsubmit="return confirm('Are you sure you want to delete {{ $property->name }}? This cannot be undone.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="text-danger hover:bg-danger-bg font-500 px-4 py-2.5 rounded-xl transition-all text-sm">
                        Delete Property
                    </button>
                </form>
            </div>
        </form>
    </div>
</div>
@endsection

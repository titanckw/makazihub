@extends('layouts.app')

@section('title', 'Edit – ' . $marketplace->name)
@section('page-title', 'Edit Provider')
@section('page-subtitle', $marketplace->name)

@section('sidebar-nav')
    @include('manager.partials.sidebar')
@endsection

@section('content')
<div class="max-w-2xl space-y-6">

    <a href="{{ route('manager.marketplace.index') }}" class="inline-flex items-center gap-1.5 text-sm text-muted hover:text-primary transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Back to Marketplace
    </a>

    @if($errors->any())
    <div class="bg-red-50 border border-red-200 text-red-800 rounded-xl px-4 py-3 text-sm">
        <ul class="list-disc list-inside space-y-1">
            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
    @endif

    <form method="POST" action="{{ route('manager.marketplace.update', $marketplace) }}" enctype="multipart/form-data"
          class="bg-card rounded-2xl border border-border shadow-sm p-6 space-y-5">
        @csrf @method('PUT')

        <div class="grid sm:grid-cols-2 gap-4">
            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-primary mb-1">Provider Name <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name', $marketplace->name) }}" required
                       class="w-full rounded-xl border border-border bg-white px-3 py-2.5 text-sm text-primary focus:outline-none focus:ring-2 focus:ring-brand-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-primary mb-1">Category <span class="text-red-500">*</span></label>
                <select name="category" required
                        class="w-full rounded-xl border border-border bg-white px-3 py-2.5 text-sm text-primary focus:outline-none focus:ring-2 focus:ring-brand-500">
                    @foreach($categories as $key => $meta)
                        <option value="{{ $key }}" {{ old('category', $marketplace->category) === $key ? 'selected' : '' }}>
                            {{ $meta['icon'] }} {{ $meta['label'] }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-primary mb-1">Phone <span class="text-red-500">*</span></label>
                <input type="text" name="phone" value="{{ old('phone', $marketplace->phone) }}" required
                       class="w-full rounded-xl border border-border bg-white px-3 py-2.5 text-sm text-primary focus:outline-none focus:ring-2 focus:ring-brand-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-primary mb-1">WhatsApp Number</label>
                <input type="text" name="whatsapp" value="{{ old('whatsapp', $marketplace->whatsapp) }}"
                       class="w-full rounded-xl border border-border bg-white px-3 py-2.5 text-sm text-primary focus:outline-none focus:ring-2 focus:ring-brand-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-primary mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email', $marketplace->email) }}"
                       class="w-full rounded-xl border border-border bg-white px-3 py-2.5 text-sm text-primary focus:outline-none focus:ring-2 focus:ring-brand-500">
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-primary mb-1">Description <span class="text-red-500">*</span></label>
            <textarea name="description" rows="3" required
                      class="w-full rounded-xl border border-border bg-white px-3 py-2.5 text-sm text-primary focus:outline-none focus:ring-2 focus:ring-brand-500 resize-none">{{ old('description', $marketplace->description) }}</textarea>
        </div>

        <div class="grid sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-primary mb-1">Price Label</label>
                <input type="text" name="price_label" value="{{ old('price_label', $marketplace->price_label) }}"
                       class="w-full rounded-xl border border-border bg-white px-3 py-2.5 text-sm text-primary focus:outline-none focus:ring-2 focus:ring-brand-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-primary mb-1">Base Price (KES)</label>
                <input type="number" name="base_price" value="{{ old('base_price', $marketplace->base_price) }}" step="0.01" min="0"
                       class="w-full rounded-xl border border-border bg-white px-3 py-2.5 text-sm text-primary focus:outline-none focus:ring-2 focus:ring-brand-500">
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-primary mb-1">Logo / Photo</label>
            @if($marketplace->logo)
                <div class="mb-2">
                    <img src="{{ asset('storage/' . $marketplace->logo) }}" class="h-16 w-16 rounded-xl object-cover">
                    <p class="text-xs text-muted mt-1">Upload a new file to replace the existing logo.</p>
                </div>
            @endif
            <input type="file" name="logo" accept="image/*"
                   class="w-full text-sm text-muted file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-brand-50 file:text-brand-700 hover:file:bg-brand-100">
        </div>

        <div class="grid sm:grid-cols-3 gap-4">
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $marketplace->is_active) ? 'checked' : '' }}
                       class="w-4 h-4 accent-brand-600">
                <span class="text-sm font-medium text-primary">Active</span>
            </label>
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="hidden" name="is_featured" value="0">
                <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $marketplace->is_featured) ? 'checked' : '' }}
                       class="w-4 h-4 accent-brand-600">
                <span class="text-sm font-medium text-primary">Featured</span>
            </label>
            <div>
                <label class="block text-sm font-medium text-primary mb-1">Sort Order</label>
                <input type="number" name="sort_order" value="{{ old('sort_order', $marketplace->sort_order) }}" min="0"
                       class="w-full rounded-xl border border-border bg-white px-3 py-2.5 text-sm text-primary focus:outline-none focus:ring-2 focus:ring-brand-500">
            </div>
        </div>

        <div class="pt-2">
            <button type="submit"
                    class="bg-brand-600 hover:bg-brand-500 text-white font-semibold px-6 py-3 rounded-xl transition-colors text-sm">
                Save Changes
            </button>
        </div>
    </form>
</div>
@endsection

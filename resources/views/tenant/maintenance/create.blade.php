@extends('layouts.app')

@section('title', 'Submit Maintenance Request')
@section('page-title', 'Submit Maintenance Request')
@section('page-subtitle', 'Report any maintenance or repair issues')

@section('sidebar-nav')
    @include('tenant.partials.sidebar')
@endsection

@section('content')
    <div class="max-w-2xl">
        {{-- Form Card --}}
        <div class="bg-card rounded-2xl border border-border shadow-sm p-8">
            <form action="{{ route('tenant.maintenance.store') }}" method="POST" class="space-y-6">
                @csrf

                {{-- Repair Type --}}
                <div>
                    <label class="block text-sm font-semibold text-primary mb-3">Select Repair Type</label>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        @foreach($repairTypes as $key => $label)
                            <label class="flex items-center gap-3 p-4 border-2 border-border rounded-lg cursor-pointer hover:border-brand-600 transition-colors
                                @error('repair_type') border-red-500 @enderror">
                                <input type="radio" name="repair_type" value="{{ $key }}" class="w-5 h-5 text-brand-600" 
                                    @if(old('repair_type') === $key) checked @endif required>
                                <span class="text-sm font-medium text-primary">{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('repair_type')
                        <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Description --}}
                <div>
                    <label class="block text-sm font-semibold text-primary mb-2">Detailed Description</label>
                    <p class="text-xs text-muted mb-3">Describe the issue in detail. The more information you provide, the better our maintenance team can help.</p>
                    <textarea name="description" rows="5" class="w-full rounded-lg border border-border bg-input text-primary px-4 py-3 focus:outline-none focus:ring-2 focus:ring-brand-600 @error('description') border-red-500 @enderror" 
                        placeholder="Please describe the maintenance issue..." required>{{ old('description') }}</textarea>
                    @error('description')
                        <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Priority --}}
                <div>
                    <label class="block text-sm font-semibold text-primary mb-3">Priority Level</label>
                    <div class="space-y-2">
                        <label class="flex items-center gap-3 p-3 border-2 border-green-200 bg-green-50 rounded-lg cursor-pointer hover:border-green-300 transition-colors">
                            <input type="radio" name="priority" value="low" class="w-5 h-5 text-green-600" 
                                @if(old('priority') === 'low' || !old('priority')) checked @endif>
                            <div>
                                <span class="font-medium text-green-900">Low Priority</span>
                                <p class="text-xs text-green-700">Can wait a few weeks (🟢 Green)</p>
                            </div>
                        </label>

                        <label class="flex items-center gap-3 p-3 border-2 border-amber-200 bg-amber-50 rounded-lg cursor-pointer hover:border-amber-300 transition-colors">
                            <input type="radio" name="priority" value="medium" class="w-5 h-5 text-amber-600"
                                @if(old('priority') === 'medium') checked @endif>
                            <div>
                                <span class="font-medium text-amber-900">Medium Priority</span>
                                <p class="text-xs text-amber-700">Needs attention in 1-2 weeks (🟡 Amber)</p>
                            </div>
                        </label>

                        <label class="flex items-center gap-3 p-3 border-2 border-orange-200 bg-orange-50 rounded-lg cursor-pointer hover:border-orange-300 transition-colors">
                            <input type="radio" name="priority" value="high" class="w-5 h-5 text-orange-600"
                                @if(old('priority') === 'high') checked @endif>
                            <div>
                                <span class="font-medium text-orange-900">High Priority</span>
                                <p class="text-xs text-orange-700">Needs urgent attention (🟠 Orange)</p>
                            </div>
                        </label>

                        <label class="flex items-center gap-3 p-3 border-2 border-red-200 bg-red-50 rounded-lg cursor-pointer hover:border-red-300 transition-colors">
                            <input type="radio" name="priority" value="urgent" class="w-5 h-5 text-red-600"
                                @if(old('priority') === 'urgent') checked @endif>
                            <div>
                                <span class="font-medium text-red-900">Urgent</span>
                                <p class="text-xs text-red-700">Critical issue - immediate action needed (🔴 Red)</p>
                            </div>
                        </label>
                    </div>
                    @error('priority')
                        <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Info Box --}}
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex gap-3">
                        <svg class="w-5 h-5 text-blue-600 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                        <div>
                            <p class="font-semibold text-blue-900 text-sm">What happens next?</p>
                            <ul class="text-sm text-blue-800 mt-2 space-y-1">
                                <li>✓ Your request will be reviewed by management</li>
                                <li>✓ Repairs will be scheduled based on priority</li>
                                <li>✓ You'll be notified when work begins</li>
                                <li>✓ Track progress in the status timeline</li>
                            </ul>
                        </div>
                    </div>
                </div>

                {{-- Form Actions --}}
                <div class="flex gap-3 pt-6 border-t border-border">
                    <button type="submit" class="flex-1 bg-brand-600 hover:bg-brand-500 text-white font-semibold py-3 rounded-lg transition-colors">
                        Submit Request
                    </button>
                    <a href="{{ route('tenant.maintenance.index') }}" class="flex-1 bg-muted/10 hover:bg-muted/20 text-primary font-semibold py-3 rounded-lg transition-colors text-center">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection

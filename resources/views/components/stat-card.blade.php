@props([
    'label',
    'value',
    'icon',
    'color' => 'emerald', // emerald | red | amber | blue | navy
    'prefix' => '',
    'suffix' => '',
])

@php
$colors = [
    'emerald' => ['border' => 'border-t-brand-600', 'icon_bg' => 'bg-brand-100', 'icon_text' => 'text-brand-600'],
    'red'     => ['border' => 'border-t-danger',    'icon_bg' => 'bg-danger-bg',  'icon_text' => 'text-danger'],
    'amber'   => ['border' => 'border-t-warning',   'icon_bg' => 'bg-warning-bg', 'icon_text' => 'text-warning'],
    'blue'    => ['border' => 'border-t-info',      'icon_bg' => 'bg-info-bg',    'icon_text' => 'text-info'],
    'navy'    => ['border' => 'border-t-navy-500',  'icon_bg' => 'bg-navy-100',   'icon_text' => 'text-navy-500'],
];
$c = $colors[$color] ?? $colors['emerald'];
@endphp

<div class="bg-card rounded-2xl border border-border border-t-4 {{ $c['border'] }} p-5 flex items-start justify-between shadow-sm hover:shadow-md transition-shadow">
    <div>
        <p class="text-muted text-sm font-500 mb-1">{{ $label }}</p>
        <p class="font-display text-primary text-2xl font-700">
            {{ $prefix }}{{ $value }}{{ $suffix }}
        </p>
    </div>
    <div class="w-11 h-11 rounded-xl {{ $c['icon_bg'] }} {{ $c['icon_text'] }} flex items-center justify-center flex-shrink-0">
        {!! $icon !!}
    </div>
</div>

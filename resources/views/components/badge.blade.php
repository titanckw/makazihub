@props(['status'])

@php
$styles = [
    'paid'        => 'bg-success-bg text-success',
    'partial'     => 'bg-info-bg text-info',
    'unpaid'      => 'bg-warning-bg text-warning',
    'overdue'     => 'bg-danger-bg text-danger',
    'active'      => 'bg-success-bg text-success',
    'expired'     => 'bg-warning-bg text-warning',
    'terminated'  => 'bg-danger-bg text-danger',
    'vacant'      => 'bg-info-bg text-info',
    'occupied'    => 'bg-success-bg text-success',
    'maintenance' => 'bg-warning-bg text-warning',
    'confirmed'   => 'bg-success-bg text-success',
    'pending'     => 'bg-warning-bg text-warning',
    'failed'      => 'bg-danger-bg text-danger',
    'reversed'    => 'bg-danger-bg text-danger',
];

$labels = [
    'paid'        => '✓ Paid',
    'partial'     => '⟳ Partial',
    'unpaid'      => '○ Unpaid',
    'overdue'     => '✕ Overdue',
    'active'      => '● Active',
    'expired'     => '○ Expired',
    'terminated'  => '✕ Terminated',
    'vacant'      => '○ Vacant',
    'occupied'    => '● Occupied',
    'maintenance' => '⚙ Maintenance',
    'confirmed'   => '✓ Confirmed',
    'pending'     => '⟳ Pending',
    'failed'      => '✕ Failed',
    'reversed'    => '↩ Reversed',
];

$style = $styles[$status] ?? 'bg-gray-100 text-gray-600';
$label = $labels[$status] ?? ucfirst($status);
@endphp

<span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-600 {{ $style }}">
    {{ $label }}
</span>

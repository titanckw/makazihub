@extends('layouts.app')

@section('title', 'Invoices')
@section('page-title', 'Invoices')
@section('page-subtitle', 'Color-coded rent invoice dashboard')

@section('sidebar-nav')
    @include('manager.partials.sidebar')
@endsection

@section('content')
<div class="space-y-6">

    {{-- KPI Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <a href="{{ route('manager.invoices.index', ['status' => 'paid']) }}"
           class="bg-white rounded-2xl border-t-4 border-success border-x border-b p-5 hover:shadow-md transition-shadow">
            <p class="text-xs font-semibold text-muted uppercase tracking-wider mb-1">Paid</p>
            <p class="text-3xl font-bold text-primary">{{ $summary['paid'] }}</p>
            <p class="text-xs text-success font-medium mt-1">KES {{ number_format($summary['total_collected_month'], 0) }} this month</p>
        </a>
        <a href="{{ route('manager.invoices.index', ['status' => 'unpaid']) }}"
           class="bg-white rounded-2xl border-t-4 border-warning border-x border-b p-5 hover:shadow-md transition-shadow">
            <p class="text-xs font-semibold text-muted uppercase tracking-wider mb-1">Unpaid</p>
            <p class="text-3xl font-bold text-primary">{{ $summary['unpaid'] }}</p>
        </a>
        <a href="{{ route('manager.invoices.index', ['status' => 'partial']) }}"
           class="bg-white rounded-2xl border-t-4 border-info border-x border-b p-5 hover:shadow-md transition-shadow">
            <p class="text-xs font-semibold text-muted uppercase tracking-wider mb-1">Partial</p>
            <p class="text-3xl font-bold text-primary">{{ $summary['partial'] }}</p>
        </a>
        <a href="{{ route('manager.invoices.index', ['status' => 'overdue']) }}"
           class="bg-white rounded-2xl border-t-4 border-danger border-x border-b p-5 hover:shadow-md transition-shadow">
            <p class="text-xs font-semibold text-muted uppercase tracking-wider mb-1">Overdue</p>
            <p class="text-3xl font-bold text-primary">{{ $summary['overdue'] }}</p>
            <p class="text-xs text-danger font-medium mt-1">KES {{ number_format($summary['total_outstanding'], 0) }} outstanding</p>
        </a>
    </div>

    {{-- Generate Invoice Form --}}
    <div class="bg-white rounded-2xl border border-border p-5" x-data="{ open: false }">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-sm font-bold text-primary">Generate Invoice</h3>
                <p class="text-xs text-muted mt-0.5">Manually generate a rent invoice for a specific lease and month</p>
            </div>
            <button @click="open = !open"
                class="text-sm bg-brand-600 hover:bg-brand-500 text-white px-4 py-2 rounded-xl font-semibold transition-colors">
                + Generate
            </button>
        </div>

        <div x-show="open" x-cloak class="mt-5 pt-5 border-t border-border">
            <form action="{{ route('manager.invoices.generate') }}" method="POST" class="flex flex-wrap gap-4 items-end">
                @csrf
                <div class="flex-1 min-w-50">
                    <label class="block text-xs font-semibold text-secondary mb-1.5">Lease</label>
                    <select name="lease_id" required class="w-full text-sm border border-border rounded-xl px-3 py-2.5 bg-surface focus:outline-none focus:ring-2 focus:ring-brand-400">
                        <option value="">— Select Active Lease —</option>
                        @foreach(\App\Models\Lease::with(['tenant.user','unit.property'])->where('status','active')->whereHas('unit.property', fn($q) => $q->where('manager_id', auth()->id()))->get() as $lease)
                            <option value="{{ $lease->id }}">
                                {{ $lease->tenant->user->name ?? '—' }} · {{ $lease->unit->unit_number ?? '—' }} ({{ $lease->unit->property->name ?? '' }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="min-w-40">
                    <label class="block text-xs font-semibold text-secondary mb-1.5">Month</label>
                    <input type="month" name="month" value="{{ now()->format('Y-m') }}" required
                        class="w-full text-sm border border-border rounded-xl px-3 py-2.5 bg-surface focus:outline-none focus:ring-2 focus:ring-brand-400">
                </div>
                <button type="submit" class="text-sm bg-navy-500 hover:bg-navy-400 text-white px-5 py-2.5 rounded-xl font-semibold transition-colors">
                    Generate
                </button>
            </form>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-2xl border border-border p-4">
        <form method="GET" class="flex flex-wrap gap-3 items-end">
            <div class="min-w-35">
                <label class="block text-xs font-semibold text-secondary mb-1.5">Status</label>
                <select name="status" class="w-full text-sm border border-border rounded-xl px-3 py-2 bg-surface focus:outline-none focus:ring-2 focus:ring-brand-400">
                    <option value="">All</option>
                    <option value="paid"    {{ request('status') === 'paid'    ? 'selected' : '' }}>Paid</option>
                    <option value="unpaid"  {{ request('status') === 'unpaid'  ? 'selected' : '' }}>Unpaid</option>
                    <option value="partial" {{ request('status') === 'partial' ? 'selected' : '' }}>Partial</option>
                    <option value="overdue" {{ request('status') === 'overdue' ? 'selected' : '' }}>Overdue</option>
                </select>
            </div>
            <div class="min-w-40">
                <label class="block text-xs font-semibold text-secondary mb-1.5">Property</label>
                <select name="property_id" class="w-full text-sm border border-border rounded-xl px-3 py-2 bg-surface focus:outline-none focus:ring-2 focus:ring-brand-400">
                    <option value="">All Properties</option>
                    @foreach($properties as $p)
                        <option value="{{ $p->id }}" {{ request('property_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="min-w-37.5">
                <label class="block text-xs font-semibold text-secondary mb-1.5">Month</label>
                <input type="month" name="month" value="{{ request('month') }}"
                    class="w-full text-sm border border-border rounded-xl px-3 py-2 bg-surface focus:outline-none focus:ring-2 focus:ring-brand-400">
            </div>
            <div class="flex gap-2">
                <button type="submit" class="text-sm bg-navy-500 hover:bg-navy-400 text-white px-4 py-2 rounded-xl font-medium transition-colors">Filter</button>
                @if(request()->hasAny(['status','property_id','month']))
                    <a href="{{ route('manager.invoices.index') }}" class="text-sm border border-border text-secondary hover:bg-surface px-4 py-2 rounded-xl transition-colors">Clear</a>
                @endif
            </div>
        </form>
    </div>

    {{-- Invoice Table --}}
    <div class="bg-white rounded-2xl border border-border overflow-hidden">
        @if($invoices->isEmpty())
            <div class="py-16 text-center">
                <svg class="w-12 h-12 text-muted mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <p class="text-muted text-sm">No invoices found. Generate one above.</p>
            </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-surface border-b border-border">
                        <th class="text-left px-6 py-3 text-xs font-semibold text-secondary uppercase tracking-wider">Invoice #</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-secondary uppercase tracking-wider">Tenant / Unit</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-secondary uppercase tracking-wider">Period</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-secondary uppercase tracking-wider">Amount</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-secondary uppercase tracking-wider">Balance</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-secondary uppercase tracking-wider">Due</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-secondary uppercase tracking-wider">Status</th>
                        <th class="text-right px-6 py-3 text-xs font-semibold text-secondary uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @foreach($invoices as $invoice)
                    <tr class="hover:bg-surface/60 transition-colors">
                        <td class="px-6 py-4 font-mono text-xs text-secondary">{{ $invoice->invoice_number }}</td>
                        <td class="px-6 py-4">
                            <p class="font-semibold text-primary">{{ $invoice->tenant->user->name ?? '—' }}</p>
                            <p class="text-xs text-muted">{{ $invoice->unit->unit_number ?? '—' }} · {{ $invoice->unit->property->name ?? '—' }}</p>
                        </td>
                        <td class="px-6 py-4 text-xs text-secondary">
                            {{ $invoice->period_start->format('d M') }} – {{ $invoice->period_end->format('d M Y') }}
                        </td>
                        <td class="px-6 py-4 font-semibold text-primary">KES {{ number_format($invoice->amount, 0) }}</td>
                        <td class="px-6 py-4 font-semibold {{ $invoice->balance > 0 ? 'text-danger' : 'text-success' }}">
                            KES {{ number_format($invoice->balance, 0) }}
                        </td>
                        <td class="px-6 py-4 text-xs text-secondary">
                            {{ $invoice->due_date->format('d M Y') }}
                            @if($invoice->is_overdue && $invoice->status !== 'paid')
                                <br><span class="text-danger font-semibold">Overdue</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $invoice->status_badge }}">
                                {{ $invoice->status_label }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('manager.invoices.show', $invoice) }}"
                                   class="text-xs bg-navy-500 hover:bg-navy-400 text-white px-3 py-1.5 rounded-lg font-medium transition-colors">
                                    View
                                </a>
                                @if($invoice->status !== 'paid')
                                <a href="{{ route('manager.payments.create', ['invoice_id' => $invoice->id]) }}"
                                   class="text-xs bg-brand-600 hover:bg-brand-500 text-white px-3 py-1.5 rounded-lg font-medium transition-colors">
                                    + Pay
                                </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($invoices->hasPages())
        <div class="px-6 py-4 border-t border-border">{{ $invoices->links() }}</div>
        @endif
        @endif
    </div>

</div>
@endsection

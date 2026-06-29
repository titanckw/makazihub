{{-- resources/views/tenant/partials/sidebar.blade.php --}}

<p class="px-3 mb-2 text-xs font-semibold text-white/40 uppercase tracking-widest">Overview</p>

<a href="{{ route('tenant.dashboard') }}"
    class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors
          {{ request()->routeIs('tenant.dashboard') ? 'bg-brand-600 text-white' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
    </svg>
    Dashboard
</a>

<p class="px-3 pt-5 mb-2 text-xs font-semibold text-white/40 uppercase tracking-widest">My Tenancy</p>

<a href="{{ route('tenant.lease.show') }}"
    class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors
          {{ request()->routeIs('tenant.lease.*') ? 'bg-brand-600 text-white' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
    </svg>
    My Lease
</a>

<p class="px-3 pt-5 mb-2 text-xs font-semibold text-white/40 uppercase tracking-widest">Services</p>

<a href="{{ route('tenant.marketplace.index') }}"
    class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors
          {{ request()->routeIs('tenant.marketplace.*') ? 'bg-brand-600 text-white' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
    </svg>
    Marketplace
</a>

<a href="{{ route('tenant.maintenance.index') }}"
    class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors
          {{ request()->routeIs('tenant.maintenance.*') ? 'bg-brand-600 text-white' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
    </svg>
    Maintenance
</a>

<p class="px-3 pt-5 mb-2 text-xs font-semibold text-white/40 uppercase tracking-widest">Financials</p>

<a href="{{ route('tenant.invoices.index') }}"
    class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors
          {{ request()->routeIs('tenant.invoices.*') ? 'bg-brand-600 text-white' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
    </svg>
    Invoices
    @php
        $tenantModel = \App\Models\Tenant::where('user_id', auth()->id())->first();
        $overdueInvoices = $tenantModel ? \App\Models\Invoice::where('tenant_id', $tenantModel->id)->where('status', 'overdue')->count() : 0;
    @endphp
    @if($overdueInvoices > 0)
        <span
            class="ml-auto bg-danger text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full">{{ $overdueInvoices }}</span>
    @endif
</a>

<a href="{{ route('tenant.payments.index') }}"
    class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors
          {{ request()->routeIs('tenant.payments.*') ? 'bg-brand-600 text-white' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
    </svg>
    Payment History
</a>

<p class="px-3 pt-5 mb-2 text-xs font-semibold text-white/40 uppercase tracking-widest">Account</p>

<a href="{{ route('settings.index') }}"
    class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors
          {{ request()->routeIs('settings.*') ? 'bg-brand-600 text-white' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
    </svg>
    Settings
</a>
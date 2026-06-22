{{-- resources/views/layouts/tenant.blade.php --}}
<!DOCTYPE html>
<html lang="en" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Tenant Portal') — MakaziHub</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="h-full bg-surface font-sans" x-data="{ sidebarOpen: false }">

    <div class="flex h-full">

        {{-- Mobile overlay --}}
        <div x-show="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 bg-black/50 z-20 lg:hidden"></div>

        {{-- Sidebar --}}
        <aside class="fixed inset-y-0 left-0 z-30 w-64 bg-navy-600 flex flex-col transition-transform duration-200
                  lg:translate-x-0 lg:static lg:z-auto" :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">

            {{-- Logo --}}
            <div class="flex items-center gap-3 px-5 py-5 border-b border-white/10">
                <div class="w-8 h-8 bg-brand-600 rounded-lg flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                            d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                        <polyline stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                            points="9 22 9 12 15 12 15 22" />
                    </svg>
                </div>
                <div>
                    <div class="text-white font-bold text-sm leading-tight">MakaziHub</div>
                    <div class="text-white/40 text-[10px] uppercase tracking-widest">Tenant Portal</div>
                </div>
            </div>

            {{-- Nav --}}
            <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
                @include('tenant.partials.sidebar')
            </nav>

            {{-- User info --}}
            <div class="px-4 py-4 border-t border-white/10">
                <div class="flex items-center gap-3">
                    <div
                        class="w-8 h-8 rounded-full bg-brand-600 flex items-center justify-center text-white text-xs font-bold shrink-0">
                        {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-white text-xs font-semibold truncate">{{ auth()->user()->name }}</p>
                        <p class="text-white/40 text-[10px] truncate">Tenant</p>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-white/40 hover:text-white transition-colors" title="Logout">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        {{-- Main --}}
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">

            {{-- Top navbar --}}
            <header class="bg-card border-b border-border px-4 lg:px-6 py-3 flex items-center gap-4 shrink-0">
                <button @click="sidebarOpen = true" class="lg:hidden text-secondary hover:text-primary">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
                <div class="flex-1">
                    <h1 class="text-sm font-semibold text-primary">@yield('title', 'Dashboard')</h1>
                </div>
                {{-- Overdue alert badge --}}
                @php
                    $tenantModel = \App\Models\Tenant::where('user_id', auth()->id())->first();
                    $overdueCount = $tenantModel ? \App\Models\Invoice::where('tenant_id', $tenantModel->id)->where('status', 'overdue')->count() : 0;
                @endphp
                @if($overdueCount > 0)
                    <a href="{{ route('tenant.invoices.index', ['status' => 'overdue']) }}"
                        class="flex items-center gap-1.5 px-3 py-1.5 bg-danger-bg text-danger text-xs font-semibold rounded-full animate-pulse">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
                        </svg>
                        {{ $overdueCount }} Overdue
                    </a>
                @endif

                {{-- Bell icon — links to correct notifications page per role --}}
                {{-- Bell Dropdown --}}
                @php
                    $bellNotifs = \App\Models\NotificationLog::where('user_id', auth()->id())
                        ->latest()
                        ->take(6)
                        ->get();

                    $bellCount = \App\Models\NotificationLog::where('user_id', auth()->id())
                        ->whereNull('read_at')
                        ->count();

                    // Tenant layout always links the bell to the tenant notifications page.
                    // Route::has() avoids a hard crash if that named route isn't defined yet.
                    $bellRoute = \Illuminate\Support\Facades\Route::has('tenant.notifications.index')
                        ? route('tenant.notifications.index')
                        : '#';
                @endphp

                <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                    <button @click="open = !open"
                        class="relative text-secondary hover:text-primary transition-colors p-1">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        @if($bellCount > 0)
                            <span
                                class="absolute -top-1 -right-1 w-4 h-4 bg-danger text-white text-[9px] font-bold rounded-full flex items-center justify-center">
                                {{ $bellCount }}
                            </span>
                        @endif
                    </button>

                    {{-- Dropdown panel --}}
                    <div x-show="open" x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-100"
                        x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                        class="absolute right-0 mt-2 w-80 bg-card border border-border rounded-2xl shadow-xl z-50 overflow-hidden"
                        style="display: none;">

                        <div class="px-4 py-3 border-b border-border flex items-center justify-between">
                            <p class="font-semibold text-primary text-sm">Notifications</p>
                            @if($bellCount > 0)
                                <span class="px-2 py-0.5 bg-warning-bg text-warning text-xs font-semibold rounded-full">
                                    {{ $bellCount }} unread
                                </span>
                            @endif
                        </div>

                        @if($bellNotifs->isEmpty())
                            <div class="px-4 py-8 text-center text-muted text-sm">
                                <svg class="w-8 h-8 mx-auto mb-2 opacity-30" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5" />
                                </svg>
                                No notifications yet
                            </div>
                        @else
                            <div class="divide-y divide-border max-h-72 overflow-y-auto">
                                @foreach($bellNotifs as $notif)
                                    <a href="{{ $bellRoute }}?notification_id={{ $notif->id }}"
                                        class="flex items-start gap-3 px-4 py-3 hover:bg-surface/60 transition-colors group">
                                        {{-- Channel icon --}}
                                        <div
                                            class="w-8 h-8 rounded-full flex items-center justify-center shrink-0 mt-0.5
                                                                            {{ $notif->status === 'failed' ? 'bg-danger-bg' : ($notif->channel === 'sms' ? 'bg-info-bg' : 'bg-navy-100') }}">
                                            @if($notif->status === 'failed')
                                                <svg class="w-3.5 h-3.5 text-danger" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            @elseif($notif->channel === 'sms')
                                                <svg class="w-3.5 h-3.5 text-info" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                                </svg>
                                            @else
                                                <svg class="w-3.5 h-3.5 text-secondary" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                                </svg>
                                            @endif
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-xs font-semibold text-primary">
                                                {{ ucfirst(str_replace('_', ' ', $notif->type)) }}
                                                <span
                                                    class="ml-1 px-1.5 py-0.5 rounded-full text-[10px] font-semibold
                                                                                    {{ $notif->status === 'failed' ? 'bg-danger-bg text-danger' : 'bg-success-bg text-success' }}">
                                                    {{ ucfirst($notif->status) }}
                                                </span>
                                                @if(!$notif->read_at)
                                                    <span
                                                        class="ml-1 px-1.5 py-0.5 rounded-full text-[10px] font-semibold bg-warning-bg text-warning">
                                                        Unread
                                                    </span>
                                                @endif
                                            </p>
                                            <p class="text-xs text-muted mt-0.5 truncate">{{ $notif->recipient }}</p>
                                            <p class="text-[10px] text-muted mt-0.5">{{ $notif->created_at->diffForHumans() }}
                                            </p>
                                        </div>
                                    </a>
                                @endforeach
                            </div>

                            <div class="px-4 py-3 border-t border-border">
                                <a href="{{ $bellRoute }}"
                                    class="block text-center text-xs font-semibold text-brand-600 hover:text-brand-500 transition-colors">
                                    View all notifications →
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </header>

            {{-- Page content --}}
            <main class="flex-1 overflow-y-auto p-4 lg:p-6">
                @yield('content')
            </main>

        </div>
    </div>

</body>

</html>
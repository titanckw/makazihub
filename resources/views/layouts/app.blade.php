<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'MakaziHub') — MakaziHub</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Syne:wght@600;700;800&display=swap"
        rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>

<body class="bg-surface font-sans text-primary antialiased" x-data="{ sidebarOpen: false }">

    <div class="flex h-screen overflow-hidden">

        {{-- ===================== SIDEBAR ===================== --}}
        {{-- Mobile overlay --}}
        <div x-show="sidebarOpen" x-transition:enter="transition-opacity ease-linear duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0" @click="sidebarOpen = false"
            class="fixed inset-0 z-20 bg-black/50 lg:hidden"></div>

        {{-- Sidebar --}}
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
            class="fixed lg:static inset-y-0 left-0 z-30 w-64 bg-navy-600 flex flex-col transition-transform duration-300 ease-in-out">
            {{-- Logo --}}
            <div class="flex items-center gap-3 px-6 py-5 border-b border-white/10">
                <div class="w-9 h-9 bg-brand-600 rounded-xl flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                </div>
                <div>
                    <span class="font-display text-white text-lg font-700 leading-none">MakaziHub</span>
                    <p class="text-white/40 text-xs mt-0.5">{{ auth()->user()->role_label }}</p>
                </div>
            </div>

            {{-- Navigation --}}
            <nav class="flex-1 px-4 py-6 overflow-y-auto space-y-1">
                @yield('sidebar-nav')
            </nav>

            {{-- User profile at bottom --}}
            <div class="px-4 py-4 border-t border-white/10">
                <div class="flex items-center gap-3 px-2 py-2 rounded-xl hover:bg-white/5 transition-colors">
                    <div class="w-9 h-9 bg-brand-600 rounded-full flex items-center justify-center shrink-0">
                        <span
                            class="text-white text-sm font-600">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-white text-sm font-500 truncate">{{ auth()->user()->first_name }}</p>
                        <p class="text-white/40 text-xs truncate">{{ auth()->user()->email }}</p>
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

        {{-- ===================== MAIN CONTENT ===================== --}}
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">

            {{-- Top Navbar --}}
            <header class="bg-white border-b border-border px-6 py-4 flex items-center justify-between shrink-0">
                <div class="flex items-center gap-4">
                    {{-- Mobile menu toggle --}}
                    <button @click="sidebarOpen = !sidebarOpen"
                        class="lg:hidden text-secondary hover:text-primary transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>

                    <div>
                        <h1 class="font-display text-primary font-700 text-xl leading-none">@yield('page-title')</h1>
                        @hasSection('page-subtitle')
                            <p class="text-muted text-sm mt-0.5">@yield('page-subtitle')</p>
                        @endif
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    {{-- Notifications bell --}}
                    @php
                        $bellNotifs = \App\Models\NotificationLog::where('user_id', auth()->id())
                            ->latest()
                            ->take(6)
                            ->get();

                        $bellCount = \App\Models\NotificationLog::where('user_id', auth()->id())
                            ->whereNull('read_at')
                            ->count();

                        $bellRoute = match (true) {
                            auth()->user()->hasRole('superadmin') => route('superadmin.notifications.log'),
                            auth()->user()->hasRole('manager') => route('manager.notifications.log'),
                            default => route('tenant.notifications.index'),
                        };
                    @endphp

                    <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                        <button @click="open = !open"
                            class="relative p-2 text-secondary hover:text-primary hover:bg-surface rounded-xl transition-all">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                            @if($bellCount > 0)
                                <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-danger rounded-full"></span>
                            @endif
                        </button>

                        {{-- Dropdown --}}
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
                                                        <span class="ml-1 px-1.5 py-0.5 rounded-full text-[10px] font-semibold bg-warning-bg text-warning">
                                                            Unread
                                                        </span>
                                                    @endif
                                                </p>
                                                <p class="text-xs text-muted mt-0.5 truncate">{{ $notif->recipient }}</p>
                                                <p class="text-[10px] text-muted mt-0.5">
                                                    {{ $notif->created_at->diffForHumans() }}</p>
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

                    {{-- Date --}}
                    <div class="hidden md:block text-sm text-muted">
                        {{ now()->format('D, d M Y') }}
                    </div>
                </div>
            </header>

            {{-- Flash messages --}}
            @if (session('success'))
                <div
                    class="mx-6 mt-4 bg-success-bg border border-success/20 text-success rounded-xl px-4 py-3 text-sm flex items-center gap-2">
                    <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                            clip-rule="evenodd" />
                    </svg>
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div
                    class="mx-6 mt-4 bg-danger-bg border border-danger/20 text-danger rounded-xl px-4 py-3 text-sm flex items-center gap-2">
                    <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                            clip-rule="evenodd" />
                    </svg>
                    {{ session('error') }}
                </div>
            @endif

            {{-- Page Content --}}
            <main class="flex-1 overflow-y-auto p-6">
                @yield('content')
            </main>
        </div>
    </div>

    @stack('scripts')
</body>

</html>
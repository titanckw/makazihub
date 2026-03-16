<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MakaziHub — Login</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Syne:wght@600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-surface min-h-screen flex">

    {{-- Left Panel - Branding --}}
    <div class="hidden lg:flex w-1/2 bg-navy-600 flex-col justify-between p-12 relative overflow-hidden">

        {{-- Background pattern --}}
        <div class="absolute inset-0 opacity-5">
            <svg width="100%" height="100%" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <pattern id="grid" width="40" height="40" patternUnits="userSpaceOnUse">
                        <path d="M 40 0 L 0 0 0 40" fill="none" stroke="white" stroke-width="1"/>
                    </pattern>
                </defs>
                <rect width="100%" height="100%" fill="url(#grid)" />
            </svg>
        </div>

        {{-- Emerald accent orb --}}
        <div class="absolute -bottom-32 -left-32 w-96 h-96 bg-brand-600 rounded-full opacity-20 blur-3xl"></div>
        <div class="absolute top-20 -right-20 w-64 h-64 bg-brand-500 rounded-full opacity-10 blur-2xl"></div>

        {{-- Logo --}}
        <div class="relative z-10">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-brand-600 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                </div>
                <span class="font-display text-white text-2xl font-700">MakaziHub</span>
            </div>
        </div>

        {{-- Middle content --}}
        <div class="relative z-10">
            <h1 class="font-display text-white text-5xl font-800 leading-tight mb-6">
                Smart Property<br>Management<br>
                <span class="text-brand-400">Made Simple.</span>
            </h1>
            <p class="text-navy-100 text-lg opacity-70 leading-relaxed max-w-sm">
                Manage properties, track rent payments, and keep tenants happy — all in one place.
            </p>

            {{-- Feature pills --}}
            <div class="flex flex-wrap gap-3 mt-8">
                @foreach(['M-Pesa Payments', 'Auto-Invoicing', 'PDF Receipts', 'SMS Alerts'] as $feature)
                <span class="bg-white/10 text-white/80 text-sm px-4 py-2 rounded-full border border-white/10">
                    {{ $feature }}
                </span>
                @endforeach
            </div>
        </div>

        {{-- Bottom stats --}}
        <div class="relative z-10 grid grid-cols-3 gap-6 pt-8 border-t border-white/10">
            @foreach([['Properties', 'Managed'], ['Payments', 'Processed'], ['Tenants', 'Served']] as $stat)
            <div>
                <div class="text-brand-400 font-display text-2xl font-700">—</div>
                <div class="text-white/60 text-sm">{{ $stat[0] }}<br>{{ $stat[1] }}</div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Right Panel - Login Form --}}
    <div class="flex-1 flex items-center justify-center p-8">
        <div class="w-full max-w-md">

            {{-- Mobile logo --}}
            <div class="flex items-center gap-3 mb-10 lg:hidden">
                <div class="w-9 h-9 bg-brand-600 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                </div>
                <span class="font-display text-navy-600 text-xl font-700">MakaziHub</span>
            </div>

            <h2 class="font-display text-primary text-3xl font-700 mb-2">Welcome back</h2>
            <p class="text-secondary mb-8">Sign in to your account to continue</p>

            {{-- Session errors --}}
            @if ($errors->any())
            <div class="bg-danger-bg border border-danger/20 text-danger rounded-xl p-4 mb-6 flex items-start gap-3">
                <svg class="w-5 h-5 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                <div>
                    @foreach ($errors->all() as $error)
                        <p class="text-sm">{{ $error }}</p>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Success message --}}
            @if (session('success'))
            <div class="bg-success-bg border border-success/20 text-success rounded-xl p-4 mb-6 text-sm">
                {{ session('success') }}
            </div>
            @endif

            {{-- Login Form --}}
            <form method="POST" action="{{ route('login.post') }}" class="space-y-5">
                @csrf

                <div>
                    <label class="block text-sm font-600 text-primary mb-2">Email Address</label>
                    <input
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        autofocus
                        placeholder="you@example.com"
                        class="w-full px-4 py-3 rounded-xl border border-border bg-white text-primary placeholder-muted focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition-all"
                    >
                </div>

                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label class="block text-sm font-600 text-primary">Password</label>
                    </div>
                    <input
                        type="password"
                        name="password"
                        required
                        placeholder="••••••••"
                        class="w-full px-4 py-3 rounded-xl border border-border bg-white text-primary placeholder-muted focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition-all"
                    >
                </div>

                <div class="flex items-center gap-3">
                    <input type="checkbox" name="remember" id="remember" class="w-4 h-4 rounded border-border text-brand-600 focus:ring-brand-500">
                    <label for="remember" class="text-sm text-secondary">Keep me signed in</label>
                </div>

                <button
                    type="submit"
                    class="w-full bg-brand-600 hover:bg-brand-500 text-white font-600 py-3 rounded-xl transition-all duration-200 flex items-center justify-center gap-2 group"
                >
                    Sign In
                    <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                </button>
            </form>

            <p class="text-center text-muted text-sm mt-8">
                &copy; {{ date('Y') }} MakaziHub. All rights reserved.
            </p>
        </div>
    </div>

</body>
</html>

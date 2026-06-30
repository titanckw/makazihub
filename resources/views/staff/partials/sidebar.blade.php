{{-- resources/views/staff/partials/sidebar.blade.php --}}

<p class="px-3 mb-2 text-xs font-semibold text-white/40 uppercase tracking-widest">Overview</p>

<a href="{{ route('staff.dashboard') }}"
    class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors
          {{ request()->routeIs('staff.dashboard') ? 'bg-brand-600 text-white' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
    </svg>
    Dashboard
</a>

<p class="px-3 pt-5 mb-2 text-xs font-semibold text-white/40 uppercase tracking-widest">Work</p>

<a href="{{ route('staff.attendance.index') }}"
    class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors
          {{ request()->routeIs('staff.attendance.*') ? 'bg-brand-600 text-white' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
    </svg>
    Attendance
</a>

<a href="{{ route('staff.leave.index') }}"
    class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors
          {{ request()->routeIs('staff.leave.*') ? 'bg-brand-600 text-white' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
    </svg>
    Leave
</a>

<a href="{{ route('staff.chat.index') }}"
    class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors
          {{ request()->routeIs('staff.chat.*') ? 'bg-brand-600 text-white' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
    </svg>
    Messages
</a>

<p class="px-3 pt-5 mb-2 text-xs font-semibold text-white/40 uppercase tracking-widest">Account</p>

<a href="{{ route('staff.profile.edit') }}"
    class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors
          {{ request()->routeIs('staff.profile.*') ? 'bg-brand-600 text-white' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
    </svg>
    My Profile
</a>

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

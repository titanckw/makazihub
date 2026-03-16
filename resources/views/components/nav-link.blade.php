@props(['href', 'active' => false, 'icon'])

<a
    href="{{ $href }}"
    class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-500 transition-all duration-150
        {{ $active
            ? 'bg-brand-600 text-white'
            : 'text-white/60 hover:text-white hover:bg-white/8' }}"
>
    <span class="flex-shrink-0 w-5 h-5">
        {!! $icon !!}
    </span>
    <span>{{ $slot }}</span>

    @if($active)
    <span class="ml-auto w-1.5 h-1.5 rounded-full bg-brand-400"></span>
    @endif
</a>

@props([
    'href',
    'active' => false,
])

<a href="{{ $href }}"
    {{ $attributes->merge([
        'class' => 'group flex w-full items-center gap-3 rounded-2xl border px-4 py-3 text-sm font-medium transition duration-150 ease-out text-decoration-none ' . ($active ? 'border-white/10 bg-white/[0.06] text-white shadow-[0_12px_30px_rgba(15,23,42,0.18)]' : 'border-transparent text-slate-300 hover:border-white/5 hover:bg-white/[0.04] hover:text-white'),
    ])->merge([':class' => "sidebarCollapsed ? 'lg:justify-center lg:px-2' : ''"]) }}>
    {{ $icon ?? '' }}
    <span x-show="!sidebarCollapsed" x-transition class="truncate">{{ $slot }}</span>

    @if ($active)
        <span x-show="!sidebarCollapsed" class="ml-auto h-2 w-2 rounded-full bg-green-300"></span>
    @endif
</a>

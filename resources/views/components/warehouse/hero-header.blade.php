@props([
    'title',
    'eyebrow' => null,
    'description' => null,
    'backHref' => null,
    'backLabel' => 'Back',
])

<div {{ $attributes->class(['relative overflow-hidden rounded-2xl border border-slate-200 bg-[radial-gradient(circle_at_top_left,_rgba(239,68,68,0.15),_transparent_34%),linear-gradient(135deg,_#ffffff_0%,_#fff1f2_100%)] px-4 py-4 text-slate-900 shadow-sm sm:rounded-3xl sm:px-8 sm:py-6']) }}>
    <div class="absolute -right-16 -top-20 h-56 w-56 rounded-full bg-red-500/10 blur-2xl"></div>
    <div class="relative flex flex-col justify-between gap-3 sm:flex-row sm:items-center sm:gap-5">
        <div>
            @if ($eyebrow)
                <div class="mb-1 flex items-center gap-2 text-[10px] font-bold uppercase tracking-widest text-red-600 sm:mb-2 sm:text-xs"><span class="h-1.5 w-1.5 rounded-full bg-red-500 sm:h-2 sm:w-2"></span>{{ $eyebrow }}</div>
            @endif
            <h1 class="m-0 text-xl font-extrabold tracking-tight text-slate-900 sm:text-3xl">{{ $title }}</h1>
            @if ($description)<p class="mb-0 mt-1 text-xs text-slate-600 sm:mt-2 sm:text-sm">{{ $description }}</p>@endif
        </div>
        @if ($backHref)
            <x-warehouse.button :href="$backHref" variant="secondary" class="h-9 w-full px-3 text-xs sm:h-11 sm:w-auto sm:px-4 sm:text-sm">{{ $backLabel }}</x-warehouse.button>
        @endif
        @isset($actions){{ $actions }}@endisset
    </div>
</div>

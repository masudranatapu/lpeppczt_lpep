@props([
    'action',
    'method' => 'GET',
    'wrapperClass' => 'border-b border-slate-200 bg-slate-50 px-4 py-4 sm:px-6',
    'gridClass' => 'grid gap-4 lg:grid-cols-[1fr_180px_160px_auto] lg:items-end',
    'actionsClass' => 'flex flex-wrap gap-3',
])

<div {{ $attributes->class([$wrapperClass]) }}>
    <form method="{{ $method }}" action="{{ $action }}" class="{{ $gridClass }}">
        {{ $slot }}

        @if (isset($actions))
            <div class="{{ $actionsClass }}">
                {{ $actions }}
            </div>
        @endif
    </form>
</div>

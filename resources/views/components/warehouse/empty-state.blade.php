@props([
    'title',
    'description' => null,
    'maxWidth' => 'max-w-sm',
])

<div {{ $attributes->class(['mx-auto', $maxWidth]) }}>
    <p class="text-base font-semibold text-slate-900">
        {{ $title }}
    </p>

    @if ($description)
        <p class="mt-2 text-sm text-slate-600">
            {{ $description }}
        </p>
    @endif
</div>

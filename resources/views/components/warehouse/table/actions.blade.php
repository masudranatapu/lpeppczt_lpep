@props([
    'toggleAction',
    'deleteAction',
    'editHref' => null,
    'checked' => false,
    'toggleMethod' => 'PATCH',
    'deleteMethod' => 'DELETE',
    'activeTitle' => 'Set active',
    'inactiveTitle' => 'Set inactive',
    'confirmText' => 'Delete this record?',
    'editTitle' => 'Edit',
    'deleteTitle' => 'Delete',
])

@php
    $toggleTitle = $checked ? $inactiveTitle : $activeTitle;
    $toggleAriaLabel = $toggleTitle;
@endphp

<div class="flex items-center gap-2">
    <form method="POST" action="{{ $toggleAction }}" class="inline-flex">
        @csrf
        @method($toggleMethod)
        <x-warehouse.button
            type="submit"
            variant="secondary"
            size="sm"
            :title="$toggleTitle"
            aria-label="{{ $toggleAriaLabel }}"
            icon="power"
            class="{{ $checked ? 'border-emerald-200 text-emerald-700 hover:border-emerald-300 hover:bg-emerald-50' : '' }}"
        />
    </form>

    @if ($editHref)
        <x-warehouse.button
            href="{{ $editHref }}"
            variant="outline"
            size="icon"
            title="{{ $editTitle }}"
            aria-label="{{ $editTitle }}"
            icon="square-pen"
            icon-class="h-5 w-5"
        >
            <span class="sr-only">{{ $editTitle }}</span>
        </x-warehouse.button>
    @endif

    <form
        method="POST"
        action="{{ $deleteAction }}"
        onsubmit="return confirm(@js($confirmText));"
        class="inline-flex"
    >
        @csrf
        @method($deleteMethod)
        <x-warehouse.button
            type="submit"
            variant="danger"
            size="icon"
            title="{{ $deleteTitle }}"
            aria-label="{{ $deleteTitle }}"
            icon="trash-2"
            icon-class="h-5 w-5"
        >
            <span class="sr-only">{{ $deleteTitle }}</span>
        </x-warehouse.button>
    </form>
</div>

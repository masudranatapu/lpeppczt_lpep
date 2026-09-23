@props([
    'type' => 'submit',
    'icon' => null,
    'iconClass' => 'h-4 w-4',
    'loadingText' => 'Loading...',
    'disabled' => false,
])

<button
    type="{{ $type }}"
    @if ($disabled) disabled @endif
    data-loading-submit
    {{ $attributes }}
>
    <span data-submit-default class="inline-flex items-center gap-2">
        @if ($icon)
            <x-warehouse.icon :name="$icon" :size-class="$iconClass" />
        @endif
        {{ $slot }}
    </span>
    <span data-submit-loading class="inline-flex items-center gap-2" style="display: none;">
        <svg class="{{ $iconClass }} animate-spin shrink-0" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <circle class="opacity-30" cx="12" cy="12" r="9" stroke="currentColor" stroke-width="3"></circle>
            <path class="opacity-90" fill="currentColor" d="M12 3a9 9 0 0 1 9 9h-3a6 6 0 0 0-6-6V3Z"></path>
        </svg>
        {{ $loadingText }}
    </span>
</button>

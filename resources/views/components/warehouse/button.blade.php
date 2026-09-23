@props([
    'variant' => 'primary',
    'size' => 'md',
    'type' => 'button',
    'href' => null,
    'disabled' => false,
    'icon' => null,
    'iconPosition' => 'left',
    'iconClass' => 'h-4 w-4',
])

@php
    $baseClasses = 'inline-flex items-center justify-center gap-2 rounded-lg border font-semibold tracking-tight transition focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50';

    $variantClasses = [
        'primary' => 'border-transparent bg-red-600 text-white shadow-sm hover:bg-red-500 focus:ring-red-500',
        'success' => 'border-transparent bg-emerald-600 text-white shadow-sm hover:bg-emerald-500 focus:ring-emerald-500',
        'secondary' => 'border-slate-300 bg-white text-slate-700 shadow-sm hover:border-slate-400 hover:bg-slate-50 hover:text-slate-900 focus:ring-slate-300',
        'outline' => 'border-red-200 bg-white text-red-700 shadow-sm hover:border-red-300 hover:bg-red-50 focus:ring-red-200',
        'danger' => 'border-transparent bg-red-600 text-white shadow-sm hover:bg-red-500 focus:ring-red-500',
        'ghost' => 'border-transparent bg-transparent text-slate-700 hover:bg-slate-100 focus:ring-slate-300',
    ];

    $sizeClasses = [
        'sm' => 'h-9 px-3 text-xs',
        'md' => 'h-11 px-4 text-sm',
        'lg' => 'h-12 px-5 text-base',
        'icon' => 'h-10 w-10 p-0',
    ];

    $classes = trim(sprintf(
        '%s %s %s',
        $baseClasses,
        $variantClasses[$variant] ?? $variantClasses['primary'],
        $sizeClasses[$size] ?? $sizeClasses['md'],
    ));
@endphp

@if ($href)
    <a
        href="{{ $disabled ? '#' : $href }}"
        @if ($disabled) aria-disabled="true" tabindex="-1" @endif
        {{ $attributes->class([
            $classes,
            $disabled ? 'pointer-events-none' : '',
        ]) }}
    >
        @if ($icon && $iconPosition === 'left')
            <x-warehouse.icon :name="$icon" :size-class="$iconClass" />
        @endif
        {{ $slot }}
        @if ($icon && $iconPosition === 'right')
            <x-warehouse.icon :name="$icon" :size-class="$iconClass" />
        @endif
    </a>
@else
    <button
        type="{{ $type }}"
        @if ($disabled) disabled @endif
        {{ $attributes->class([$classes]) }}
    >
        @if ($icon && $iconPosition === 'left')
            <x-warehouse.icon :name="$icon" :size-class="$iconClass" />
        @endif
        {{ $slot }}
        @if ($icon && $iconPosition === 'right')
            <x-warehouse.icon :name="$icon" :size-class="$iconClass" />
        @endif
    </button>
@endif

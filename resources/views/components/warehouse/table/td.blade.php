@props([
    'align' => 'left',
    'nowrap' => true,
    'paddingClass' => 'px-4 py-3 sm:px-6',
])

@php
    $alignClasses = [
        'left' => 'text-left',
        'center' => 'text-center',
        'right' => 'text-right',
    ];

    $classes = trim(sprintf(
        '%s %s %s',
        $paddingClass . ' text-sm text-slate-700',
        $alignClasses[$align] ?? $alignClasses['left'],
        $nowrap ? 'whitespace-nowrap' : '',
    ));
@endphp

<td {{ $attributes->class([$classes]) }}>
    {{ $slot }}
</td>

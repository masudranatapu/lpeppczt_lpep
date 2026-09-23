@props([
    'name',
    'sizeClass' => 'h-4 w-4',
])

@switch($name)
    @case('x')
        <svg class="{{ $sizeClass }} shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" d="M6 6l12 12M18 6 6 18" /></svg>
        @break
    @case('wallet-cards')
        <svg class="{{ $sizeClass }} shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="3.5" y="5" width="17" height="14" rx="2" /><path stroke-linecap="round" d="M3.5 9h17M7.5 14h3" /><circle cx="16.5" cy="14" r="1.2" /></svg>
        @break
    @case('plus')
        <svg class="{{ $sizeClass }} shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" d="M12 5v14M5 12h14" /></svg>
        @break
    @case('eye')
        <svg class="{{ $sizeClass }} shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M2.75 12s3.4-6 9.25-6 9.25 6 9.25 6-3.4 6-9.25 6-9.25-6-9.25-6Z" /><circle cx="12" cy="12" r="2.75" /></svg>
        @break
    @case('funnel')
        <svg class="{{ $sizeClass }} shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M4 5.75h16l-6.25 7.1v5.4l-3.5-1.8v-3.6L4 5.75Z" /></svg>
        @break
    @case('arrow-left')
        <svg class="{{ $sizeClass }} shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m10 6-6 6 6 6M4 12h16" /></svg>
        @break
    @case('square-pen')
        <svg class="{{ $sizeClass }} shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 5.5H6.75A2.25 2.25 0 0 0 4.5 7.75v9.5a2.25 2.25 0 0 0 2.25 2.25h9.5a2.25 2.25 0 0 0 2.25-2.25V10.5M12 15l.5-3 6.25-6.25a1.77 1.77 0 0 0-2.5-2.5L10 9.5l-3 .5" /></svg>
        @break
    @case('trash-2')
        <svg class="{{ $sizeClass }} shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M4 7h16M9 3h6l1 4H8l1-4Zm-2 4 1 14h8l1-14M10 11v6m4-6v6" /></svg>
        @break
    @case('rotate-ccw')
        <svg class="{{ $sizeClass }} shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v6h6M5.5 15.5A7.5 7.5 0 1 0 6 7" /></svg>
        @break
    @case('save')
        <svg class="{{ $sizeClass }} shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M5 3.75h12l2 2v14.5H5V3.75Zm3 0v6h8v-6M8 20.25v-7h8v7" /></svg>
        @break
    @case('bar-chart-3')
        <svg class="{{ $sizeClass }} shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 19.25V10.5h4v8.75h-4Zm5.75 0V4.75h4v14.5h-4Zm5.75 0v-6.5h4v6.5h-4Z" /></svg>
        @break
    @case('boxes')
        <svg class="{{ $sizeClass }} shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M4.75 7.5 12 3.75 19.25 7.5 12 11.25 4.75 7.5ZM4.75 11.75 12 15.5l7.25-3.75M4.75 16 12 19.75 19.25 16" /></svg>
        @break
    @case('power')
        <svg class="{{ $sizeClass }} shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" d="M12 3v9" /><path stroke-linecap="round" d="M6.35 6.35a8 8 0 1 0 11.3 0" /></svg>
        @break
    @case('log-out')
        <svg class="{{ $sizeClass }} shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M14 8V5H5v14h9v-3m-3-4h10m0 0-3-3m3 3-3 3" /></svg>
        @break
    @case('printer')
        <svg class="{{ $sizeClass }} shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M7 8V4.75h10V8M6.5 17.25H5A1.75 1.75 0 0 1 3.25 15.5V10.75A1.75 1.75 0 0 1 5 9h14a1.75 1.75 0 0 1 1.75 1.75v4.75A1.75 1.75 0 0 1 19 17.25h-1.5M8 14h8M8 18.25h8v-5.5H8v5.5Z" /></svg>
        @break
    @case('file-down')
        <svg class="{{ $sizeClass }} shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M14 3.75H7.5A1.75 1.75 0 0 0 5.75 5.5v13A1.75 1.75 0 0 0 7.5 20.25h9A1.75 1.75 0 0 0 18.25 18.5V8L14 3.75Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M14 3.75V8h4.25" /><path stroke-linecap="round" stroke-linejoin="round" d="M12 11v5m0 0-2.25-2.25M12 16l2.25-2.25" /></svg>
        @break
    @case('file-text')
        <svg class="{{ $sizeClass }} shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M14 3.75H7.5A1.75 1.75 0 0 0 5.75 5.5v13a1.75 1.75 0 0 0 1.75 1.75h9a1.75 1.75 0 0 0 1.75-1.75V8L14 3.75Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M14 3.75V8h4.25" /><path stroke-linecap="round" stroke-linejoin="round" d="M8.75 12h6.5M8.75 15h6.5" /></svg>
        @break
    @default
        <svg class="{{ $sizeClass }} shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="12" cy="12" r="8" /><path stroke-linecap="round" d="M9 12h6" /></svg>
@endswitch

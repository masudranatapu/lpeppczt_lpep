@php
    $toast = session('toast');
    $status = session('status');

    if (is_array($toast)) {
        $message = trim((string) ($toast['message'] ?? ''));
        $variant = (string) ($toast['variant'] ?? 'success');
    } elseif (is_string($status) && $status !== '') {
        $mapped = toast_from_status($status);
        $message = trim((string) ($mapped['message'] ?? ''));
        $variant = (string) ($mapped['variant'] ?? 'success');
    } else {
        $message = '';
        $variant = 'success';
    }

    $styles = [
        'success' => [
            'container' => 'border-emerald-200 bg-emerald-50 text-emerald-950 shadow-[0_16px_45px_rgba(16,185,129,0.18)]',
            'accent' => 'bg-emerald-500',
            'iconBg' => 'bg-emerald-100 text-emerald-700',
            'title' => 'Success',
        ],
        'warning' => [
            'container' => 'border-amber-200 bg-amber-50 text-amber-950 shadow-[0_16px_45px_rgba(245,158,11,0.16)]',
            'accent' => 'bg-amber-500',
            'iconBg' => 'bg-amber-100 text-amber-700',
            'title' => 'Notice',
        ],
        'danger' => [
            'container' => 'border-rose-200 bg-rose-50 text-rose-950 shadow-[0_16px_45px_rgba(244,63,94,0.18)]',
            'accent' => 'bg-rose-500',
            'iconBg' => 'bg-rose-100 text-rose-700',
            'title' => 'Error',
        ],
        'info' => [
            'container' => 'border-slate-200 bg-slate-50 text-slate-950 shadow-[0_16px_45px_rgba(15,23,42,0.16)]',
            'accent' => 'bg-slate-500',
            'iconBg' => 'bg-slate-100 text-slate-700',
            'title' => 'Info',
        ],
    ];

    $style = $styles[$variant] ?? $styles['success'];
@endphp

@if ($message !== '')
    <div
        x-data="{ open: true }"
        x-init="setTimeout(() => open = false, 5000)"
        x-show="open"
        x-transition:enter="transform ease-out duration-300 transition"
        x-transition:enter-start="translate-y-2 opacity-0 scale-95"
        x-transition:enter-end="translate-y-0 opacity-100 scale-100"
        x-transition:leave="transform ease-in duration-200 transition"
        x-transition:leave-start="translate-y-0 opacity-100 scale-100"
        x-transition:leave-end="translate-y-2 opacity-0 scale-95"
        class="pointer-events-none fixed right-4 top-4 z-[70] w-[calc(100vw-2rem)] max-w-sm sm:right-6 sm:top-6"
        role="status"
        aria-live="polite"
    >
        <div class="pointer-events-auto overflow-hidden rounded-3xl border backdrop-blur-xl {{ $style['container'] }}">
            <div class="h-1 w-full {{ $style['accent'] }}"></div>

            <div class="flex items-start gap-4 p-4 sm:p-5">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl {{ $style['iconBg'] }}">
                    @if ($variant === 'warning')
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.29 3.86l-7.1 12.28A2 2 0 0 0 4.92 19h14.16a2 2 0 0 0 1.73-2.86l-7.1-12.28a2 2 0 0 0-3.46 0Z" />
                        </svg>
                    @elseif ($variant === 'danger')
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.29 3.86l-7.1 12.28A2 2 0 0 0 4.92 19h14.16a2 2 0 0 0 1.73-2.86l-7.1-12.28a2 2 0 0 0-3.46 0Z" />
                        </svg>
                    @else
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20 6L9 17l-5-5" />
                        </svg>
                    @endif
                </div>

                <div class="min-w-0 flex-1">
                    <p class="text-[10px] font-semibold uppercase tracking-[0.3em] opacity-70">
                        {{ $style['title'] }}
                    </p>
                    <p class="mt-1 text-sm font-medium leading-6">
                        {{ $message }}
                    </p>
                </div>

                <button
                    type="button"
                    @click="open = false"
                    class="rounded-full p-1.5 opacity-70 transition hover:bg-black/5 hover:opacity-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-transparent"
                    aria-label="Dismiss notification"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>
@endif

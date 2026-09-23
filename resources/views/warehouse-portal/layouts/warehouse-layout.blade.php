<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }}</title>

    <script>
        window.tailwind = window.tailwind || {};
        window.tailwind.config = {
            corePlugins: {
                preflight: false,
            },
        };
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
    {{-- <link href="{{ asset('css/app.css') }}" rel="stylesheet"> --}}

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
</head>
<body class="m-0 bg-gray-100 font-sans antialiased text-gray-900">
    <div x-data="{ sidebarOpen: false, sidebarCollapsed: localStorage.getItem('warehouse-sidebar-collapsed') === 'true', userMenuOpen: false, toggleSidebar() { this.sidebarCollapsed = !this.sidebarCollapsed; localStorage.setItem('warehouse-sidebar-collapsed', this.sidebarCollapsed); } }" class="min-h-screen bg-gray-100 transition-[padding] duration-300 lg:pl-64" :class="sidebarCollapsed ? 'lg:pl-20' : 'lg:pl-64'">
        @include('warehouse-portal.layouts.includes.sidebar')

        <div
            x-cloak
            x-show="sidebarOpen"
            x-transition.opacity
            class="fixed inset-0 z-40 bg-gray-600/75 lg:hidden"
            @click="sidebarOpen = false">
        </div>

        @include('warehouse-portal.layouts.includes.topbar')

        <main class="pb-10 pt-4">
            <div class="px-4 sm:px-6 lg:px-8">
                @if (session('message'))
                    <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
                        {{ session('message') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-800">
                        {{ $errors->first() }}
                    </div>
                @endif

                {{ $slot }}
            </div>
        </main>
    </div>

    <script src="{{ asset('js/app.js') }}"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        document.addEventListener('submit', function (event) {
            const form = event.target;

            if (!(form instanceof HTMLFormElement)) {
                return;
            }

            const submitButtons = form.querySelectorAll('[data-loading-submit]');

            submitButtons.forEach(function (button) {
                if (button.disabled || button.dataset.loading === 'true') {
                    return;
                }

                const defaultLabel = button.querySelector('[data-submit-default]');
                const loadingLabel = button.querySelector('[data-submit-loading]');

                button.dataset.loading = 'true';
                button.disabled = true;
                button.setAttribute('aria-busy', 'true');

                if (defaultLabel && loadingLabel) {
                    defaultLabel.style.display = 'none';
                    loadingLabel.style.display = 'inline-flex';
                }
            });
        }, true);
    </script>

    @isset($script)
        {{ $script }}
    @endisset
</body>
</html>

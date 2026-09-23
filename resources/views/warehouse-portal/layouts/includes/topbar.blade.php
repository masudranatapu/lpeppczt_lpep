<nav class="sticky top-0 z-30 border-b border-gray-100 bg-white/90 backdrop-blur">
    <div class="px-4 sm:px-6 lg:px-8">
        <div class="relative flex h-14 items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <button
                    @click="sidebarOpen = !sidebarOpen"
                    class="inline-flex items-center justify-center rounded-md p-2 text-gray-400 transition hover:bg-gray-100 hover:text-gray-500 focus:bg-gray-100 focus:text-gray-500 focus:outline-none lg:hidden"
                    type="button"
                    aria-label="Toggle sidebar">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>

            @if ($isSalesman)
                <a href="{{ route('warehouse.sales.create') }}" class="absolute left-1/2 top-1/2 z-20 inline-flex -translate-x-1/2 -translate-y-1/2 items-center gap-2 rounded-xl border border-emerald-200 bg-emerald-50 px-3 py-2 text-xs font-extrabold uppercase tracking-wide text-emerald-700 shadow-sm transition hover:border-emerald-300 hover:bg-emerald-100 hover:text-emerald-800 focus:outline-none focus:ring-2 focus:ring-emerald-200 lg:hidden" aria-label="Open POS">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6.5h16l-1.25 11.75H5.25L4 6.5ZM8 6.5l1-3h6l1 3M9 11.25h6M9.5 15h5" /></svg>
                    <span>POS</span>
                </a>
            @endif

            <div class="absolute left-1/2 top-1/2 z-10 hidden -translate-x-1/2 -translate-y-1/2 items-center sm:flex">
                <div class="relative overflow-hidden rounded-full p-[1px] shadow-sm">
                    <div class="absolute inset-0 animate-spin bg-[conic-gradient(from_180deg,_#16a34a,_#0ea5e9,_#f59e0b,_#16a34a)]"></div>
                    <div class="relative z-10 inline-flex items-center gap-2 rounded-full bg-slate-50 px-5 py-2 text-sm font-semibold text-slate-700">
                        <span class="inline-flex h-5 w-5 items-center justify-center rounded-full bg-white text-xs font-extrabold leading-none text-emerald-700 shadow-sm ring-1 ring-emerald-100">
                            {{ $isSalesman ? 'S' : 'W' }}
                        </span>
                        <span>{{ $isSalesman ? 'LSP Portal' : 'Area Office Portal' }}</span>
                        <span class="h-1.5 w-1.5 rounded-full bg-slate-300"></span>
                        <span>{{ $warehouseName }}</span>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-2">
                @if ($isSalesman)
                    <a href="{{ route('warehouse.sales.create') }}" class="hidden items-center gap-2 rounded-xl border border-emerald-200 bg-emerald-50 px-3 py-2 text-xs font-extrabold uppercase tracking-wide text-emerald-700 shadow-sm transition hover:border-emerald-300 hover:bg-emerald-100 hover:text-emerald-800 focus:outline-none focus:ring-2 focus:ring-emerald-200 lg:inline-flex" aria-label="Open POS">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6.5h16l-1.25 11.75H5.25L4 6.5ZM8 6.5l1-3h6l1 3M9 11.25h6M9.5 15h5" /></svg>
                        <span>POS</span>
                    </a>
                @endif

                <div class="relative" @click.outside="userMenuOpen = false">
                <button
                    @click="userMenuOpen = !userMenuOpen"
                    class="inline-flex h-9 w-9 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-600 shadow-sm transition hover:border-emerald-200 hover:text-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-200"
                    aria-label="User menu"
                    type="button">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M20 21a8 8 0 1 0-16 0" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z" />
                    </svg>
                </button>

                <div
                    x-cloak
                    x-show="userMenuOpen"
                    x-transition.origin.top.right
                    class="absolute right-0 z-50 mt-2 w-56 rounded-md bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5"
                    @click="userMenuOpen = false">
                    <div class="border-b border-gray-100 px-4 py-3">
                        <p class="m-0 text-xs font-semibold uppercase text-gray-400">{{ $isSalesman ? 'Signed in as LSP' : 'Signed in as' }}</p>
                        <p class="mb-0 mt-1 text-sm font-semibold text-gray-900">{{ $warehouseName }}</p>
                        @if ($warehouseEmail)
                            <p class="mb-0 mt-1 truncate text-xs font-medium text-emerald-700">{{ $warehouseEmail }}</p>
                        @endif
                    </div>
                    <form action="{{ route('warehouse.logout') }}" method="POST" class="m-0">
                        @csrf
                        <button class="flex w-full items-center gap-2 px-4 py-2 text-left text-sm leading-5 text-gray-700 transition hover:bg-gray-100 focus:bg-gray-100 focus:outline-none" type="submit">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M14 8V5H5v14h9v-3m-3-4h10m0 0-3-3m3 3-3 3" /></svg>
                            Log Out
                        </button>
                    </form>
                </div>
                </div>
            </div>
        </div>
    </div>
</nav>

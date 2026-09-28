<aside
    class="fixed inset-y-0 left-0 z-50 w-64 -translate-x-full transform border-r border-gray-700 bg-gray-800 shadow-lg transition-[width,transform] duration-300 ease-in-out lg:translate-x-0"
    :class="(sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0') + (sidebarCollapsed ? ' lg:w-20' : ' lg:w-64')"
    @click.stop
>
    <div class="flex items-center justify-between p-4" :class="sidebarCollapsed ? 'lg:justify-center' : ''">
        <a href="{{ route('warehouse.dashboard') }}" class="flex items-center gap-3 text-decoration-none">
            <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-500/15 text-emerald-300 ring-1 ring-emerald-400/20">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 9.75 12 4l8.25 5.75v8.5A1.75 1.75 0 0 1 18.5 20h-13a1.75 1.75 0 0 1-1.75-1.75v-8.5Z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 20v-6.25h8V20M8.5 10.25h7" />
                </svg>
            </span>
            <span x-show="!sidebarCollapsed" x-transition>
                <span class="block text-base font-semibold leading-5 text-white">{{ $isSalesman ? 'LSP' : 'Area Office' }}</span>
                <span class="block text-xs font-medium text-slate-400">Portal</span>
            </span>
        </a>

        <button @click="toggleSidebar()" class="group absolute -right-3 top-6 hidden h-7 w-7 items-center justify-center rounded-full border border-slate-600 bg-slate-800 text-slate-300 shadow-lg shadow-slate-950/30 ring-4 ring-gray-800/80 transition duration-200 hover:scale-110 hover:border-emerald-400 hover:bg-emerald-500 hover:text-white focus:outline-none focus:ring-2 focus:ring-emerald-400 lg:inline-flex" type="button" :aria-label="sidebarCollapsed ? 'Expand sidebar' : 'Collapse sidebar'" :title="sidebarCollapsed ? 'Expand sidebar' : 'Collapse sidebar'">
            <svg x-show="!sidebarCollapsed" x-transition class="h-4 w-4 transition-transform duration-200 group-hover:-translate-x-0.5" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="m14 6-6 6 6 6" /></svg>
            <svg x-show="sidebarCollapsed" x-transition class="h-4 w-4 transition-transform duration-200 group-hover:translate-x-0.5" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="m10 6 6 6-6 6" /></svg>
        </button>

        <button
            @click="sidebarOpen = false"
            class="rounded-md p-2 text-gray-300 transition hover:bg-gray-700 hover:text-white focus:bg-gray-700 focus:text-white focus:outline-none lg:hidden"
            type="button"
            aria-label="Close sidebar"
        >
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    <nav class="px-3" :class="sidebarCollapsed ? 'lg:px-2' : ''">
        <div class="space-y-1">
            @if (Auth::guard('warehouse')->check())
                <x-warehouse.sidebar-item :href="route('warehouse.dashboard')" :active="request()->routeIs('warehouse.dashboard')" x-on:click="sidebarOpen = false">
                    <x-slot name="icon">
                        <svg class="h-5 w-5 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 4.75h6.25V11H4V4.75ZM13.75 4.75H20V11h-6.25V4.75ZM4 13h6.25v6.25H4V13ZM13.75 13H20v6.25h-6.25V13Z" />
                        </svg>
                    </x-slot>
                    Dashboard
                </x-warehouse.sidebar-item>

                <div x-data="{ manageStockOpen: @js(request()->routeIs('warehouse.stock', 'warehouse.stock.received')) }" class="pt-1">
                    <button type="button" @click="manageStockOpen = !manageStockOpen" class="flex w-full items-center gap-3 rounded-2xl border border-transparent px-4 py-3 text-left text-sm font-medium text-slate-300 transition hover:border-white/5 hover:bg-white/[0.04] hover:text-white" :class="sidebarCollapsed ? 'lg:justify-center lg:px-2' : ''">
                        <svg class="h-5 w-5 text-sky-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M4.75 7.5 12 3.75 19.25 7.5 12 11.25 4.75 7.5 12 11.25M4.75 11.75 12 15.5l7.25-3.75M4.75 16 12 19.75 19.25 16" /></svg>
                        <span x-show="!sidebarCollapsed" class="truncate">Manage Stock</span>
                        <svg x-show="!sidebarCollapsed" class="ml-auto h-4 w-4 transition-transform" :class="manageStockOpen ? 'rotate-180 text-sky-300' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6" /></svg>
                    </button>
                    <div x-cloak x-show="manageStockOpen && !sidebarCollapsed" x-collapse class="ml-9 border-l border-slate-700/80 py-1">
                        <a href="{{ route('warehouse.stock') }}" class="block px-4 py-2 text-sm transition {{ request()->routeIs('warehouse.stock') ? 'text-sky-300' : 'text-slate-400 hover:text-white' }}">Current Stock</a>
                        <a href="{{ route('warehouse.stock.received') }}" class="block px-4 py-2 text-sm transition {{ request()->routeIs('warehouse.stock.received') ? 'text-sky-300' : 'text-slate-400 hover:text-white' }}">Received List</a>
                    </div>
                </div>

                <div x-data="{ manageLspOpen: @js(request()->routeIs('warehouse.salesmen.*', 'warehouse.salesman-assignments.*', 'warehouse.salesman-returns.*')) }" class="pt-1">
                    <button type="button" @click="manageLspOpen = !manageLspOpen" class="flex w-full items-center gap-3 rounded-2xl border border-transparent px-4 py-3 text-left text-sm font-medium text-slate-300 transition hover:border-white/5 hover:bg-white/[0.04] hover:text-white" :class="sidebarCollapsed ? 'lg:justify-center lg:px-2' : ''"><svg class="h-5 w-5 text-cyan-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M16 11.5a3.5 3.5 0 1 0-7 0 3.5 3.5 0 0 0 7 0ZM5 20a7 7 0 0 1 14 0M18.5 7.5h2.75M19.875 6.125v2.75" /></svg><span x-show="!sidebarCollapsed" class="truncate">Manage LSP</span><svg x-show="!sidebarCollapsed" class="ml-auto h-4 w-4 transition-transform" :class="manageLspOpen ? 'rotate-180 text-cyan-300' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6" /></svg></button>
                    <div x-cloak x-show="manageLspOpen && !sidebarCollapsed" x-collapse class="ml-9 border-l border-slate-700/80 py-1"><a href="{{ route('warehouse.salesmen.index') }}" class="block px-4 py-2 text-sm transition {{ request()->routeIs('warehouse.salesmen.*') ? 'text-cyan-300' : 'text-slate-400 hover:text-white' }}">LSP List</a><a href="{{ route('warehouse.salesman-assignments.index') }}" class="block px-4 py-2 text-sm transition {{ request()->routeIs('warehouse.salesman-assignments.*') ? 'text-cyan-300' : 'text-slate-400 hover:text-white' }}">Assign Stock</a><a href="{{ route('warehouse.salesman-returns.index') }}" class="block px-4 py-2 text-sm transition {{ request()->routeIs('warehouse.salesman-returns.*') ? 'text-cyan-300' : 'text-slate-400 hover:text-white' }}">Return Product</a></div>
                </div>

                <div class="pt-1"><a href="{{ route('warehouse.beneficiaries.index') }}" class="flex w-full items-center gap-3 rounded-2xl border border-transparent px-4 py-3 text-sm font-medium transition hover:border-white/5 hover:bg-white/[0.04] hover:text-white {{ request()->routeIs('warehouse.beneficiaries.*') ? 'text-emerald-300' : 'text-slate-300' }}" :class="sidebarCollapsed ? 'lg:justify-center lg:px-2' : ''"><svg class="h-5 w-5 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7"><path stroke-linecap="round" stroke-linejoin="round" d="M16 11.5a4 4 0 1 0-8 0 4 4 0 0 0 8 0ZM4.75 20a7.25 7.25 0 0 1 14.5 0M19 8.25v5.5m2.75-2.75h-5.5" /></svg><span x-show="!sidebarCollapsed">Beneficiary</span></a></div>
            @endif

            @if (Auth::guard('warehouse')->check())
            <div x-data="{ salesManagementOpen: @js(request()->routeIs('warehouse.sales.*', 'warehouse.due-amount')) }" class="pt-1"><button type="button" @click="salesManagementOpen = !salesManagementOpen" class="flex w-full items-center gap-3 rounded-2xl border border-transparent px-4 py-3 text-left text-sm font-medium text-slate-300 transition hover:border-white/5 hover:bg-white/[0.04] hover:text-white" :class="sidebarCollapsed ? 'lg:justify-center lg:px-2' : ''"><svg class="h-5 w-5 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 5.25h12v15l-2-1.25-2 1.25-2-1.25-2 1.25-2-1.25-2 1.25v-15ZM8.75 9h6.5M8.75 12.25h6.5" /></svg><span x-show="!sidebarCollapsed" class="truncate">Sales Management</span><svg x-show="!sidebarCollapsed" class="ml-auto h-4 w-4 transition-transform" :class="salesManagementOpen ? 'rotate-180 text-emerald-300' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6" /></svg></button><div x-cloak x-show="salesManagementOpen && !sidebarCollapsed" x-collapse class="ml-9 border-l border-slate-700/80 py-1"><a href="{{ route('warehouse.sales.index') }}" class="block px-4 py-2 text-sm transition {{ request()->routeIs('warehouse.sales.show', 'warehouse.sales.invoice') || (request()->routeIs('warehouse.sales.index') && request('payment_status') !== 'due') ? 'text-emerald-300' : 'text-slate-400 hover:text-white' }}">Sales</a><a href="{{ route('warehouse.due-amount') }}" class="block px-4 py-2 text-sm transition {{ request()->routeIs('warehouse.due-amount') || (request()->routeIs('warehouse.sales.index') && request('payment_status') === 'due') ? 'text-emerald-300' : 'text-slate-400 hover:text-white' }}">Due Amount</a></div></div>
            @endif

            @if (Auth::guard('warehouse')->check())
                <div x-data="{ dailyVisitMenuOpen: @js(request()->routeIs('warehouse.daily-visits')) }" class="pt-1">
                    <button type="button" @click="dailyVisitMenuOpen = !dailyVisitMenuOpen" class="flex w-full items-center gap-3 rounded-2xl border border-transparent px-4 py-3 text-left text-sm font-medium text-slate-300 transition hover:border-white/5 hover:bg-white/[0.04] hover:text-white" :class="sidebarCollapsed ? 'lg:justify-center lg:px-2' : ''">
                        <svg class="h-5 w-5 text-violet-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7"><path stroke-linecap="round" stroke-linejoin="round" d="M6.5 4.75h11A1.75 1.75 0 0 1 19.25 6.5v13H4.75v-13A1.75 1.75 0 0 1 6.5 4.75ZM8 3.75v3M16 3.75v3M7.5 10h9M7.5 14h5" /></svg>
                        <span x-show="!sidebarCollapsed" class="truncate">Daily Visits</span>
                        <svg x-show="!sidebarCollapsed" class="ml-auto h-4 w-4 transition-transform" :class="dailyVisitMenuOpen ? 'rotate-180 text-violet-300' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6" /></svg>
                    </button>
                    <div x-cloak x-show="dailyVisitMenuOpen && !sidebarCollapsed" x-collapse class="ml-9 border-l border-slate-700/80 py-1">
                        <a href="{{ route('warehouse.daily-visits') }}" class="block px-4 py-2 text-sm transition {{ request()->routeIs('warehouse.daily-visits') ? 'text-violet-300' : 'text-slate-400 hover:text-white' }}">Visit List</a>
                        <a href="{{ route('warehouse.daily-visits.area-report') }}" class="block px-4 py-2 text-sm transition {{ request()->routeIs('warehouse.daily-visits.area-report') ? 'text-violet-300' : 'text-slate-400 hover:text-white' }}">Report</a>
                    </div>
                </div>

                <div x-data="{ reportsMenuOpen: @js(request()->routeIs('warehouse.reports.*')) }" class="pt-1">
                    <button type="button" @click="reportsMenuOpen = !reportsMenuOpen" class="flex w-full items-center gap-3 rounded-2xl border border-transparent px-4 py-3 text-left text-sm font-medium text-slate-300 transition hover:border-white/5 hover:bg-white/[0.04] hover:text-white" :class="sidebarCollapsed ? 'lg:justify-center lg:px-2' : ''">
                        <svg class="h-5 w-5 text-violet-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 19.25V10.5h4v8.75h-4Zm5.75 0V4.75h4v14.5h-4Zm5.75 0v-6.5h4v6.5h-4Z" /></svg>
                        <span x-show="!sidebarCollapsed" class="truncate">Reports</span>
                        <svg x-show="!sidebarCollapsed" class="ml-auto h-4 w-4 transition-transform" :class="reportsMenuOpen ? 'rotate-180 text-violet-300' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6" /></svg>
                    </button>
                    <div x-cloak x-show="reportsMenuOpen && !sidebarCollapsed" x-collapse class="ml-9 border-l border-slate-700/80 py-1">
                        <a href="{{ route('warehouse.reports.index') }}" class="block px-4 py-2 text-sm transition {{ request()->routeIs('warehouse.reports.index', 'warehouse.reports.print', 'warehouse.reports.pdf', 'warehouse.reports.excel') ? 'text-violet-300' : 'text-slate-400 hover:text-white' }}">Sales Report</a>
                        <a href="{{ route('warehouse.reports.area-manager-daily') }}" class="block px-4 py-2 text-sm transition {{ request()->routeIs('warehouse.reports.area-manager-daily*') ? 'text-violet-300' : 'text-slate-400 hover:text-white' }}">Area Manager Daily Report</a>
                    </div>
                </div>
            @endif

            @if (Auth::guard('warehouse_salesman')->check())
                <x-warehouse.sidebar-item :href="route('warehouse.dashboard')" :active="request()->routeIs('warehouse.dashboard')" x-on:click="sidebarOpen = false">
                    <x-slot name="icon"><svg class="h-5 w-5 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4.75h6.25V11H4V4.75ZM13.75 4.75H20V11h-6.25V4.75ZM4 13h6.25v6.25H4V13ZM13.75 13H20v6.25h-6.25V13Z" /></svg></x-slot>
                    Dashboard
                </x-warehouse.sidebar-item>

                <x-warehouse.sidebar-item :href="route('warehouse.beneficiaries.index')" :active="request()->routeIs('warehouse.beneficiaries.*')" x-on:click="sidebarOpen = false">
                    <x-slot name="icon"><svg class="h-5 w-5 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7"><path stroke-linecap="round" stroke-linejoin="round" d="M16 11.5a4 4 0 1 0-8 0 4 4 0 0 0 8 0ZM4.75 20a7.25 7.25 0 0 1 14.5 0M19 8.25v5.5m2.75-2.75h-5.5" /></svg></x-slot>
                    Beneficiaries
                </x-warehouse.sidebar-item>

                <div x-data="{ appMenuOpen: @js(request()->routeIs('warehouse.wallet', 'warehouse.farms', 'warehouse.cattle', 'warehouse.calves')), reportMenuOpen: false }" class="pt-1">
                    <button type="button" @click="appMenuOpen = !appMenuOpen" class="flex w-full items-center gap-3 rounded-2xl border border-transparent px-4 py-3 text-left text-sm font-medium text-slate-300 transition hover:border-white/5 hover:bg-white/[0.04] hover:text-white" :class="sidebarCollapsed ? 'lg:justify-center lg:px-2' : ''">
                        <svg class="h-5 w-5 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6"><rect x="6.5" y="3.75" width="11" height="16.5" rx="1.75" /><path stroke-linecap="round" d="M10 17h4" /></svg>
                        <span x-show="!sidebarCollapsed" class="truncate">App</span>
                        <svg x-show="!sidebarCollapsed" class="ml-auto h-4 w-4 transition-transform" :class="appMenuOpen ? 'rotate-180 text-emerald-300' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6" /></svg>
                    </button>

                    <div x-cloak x-show="appMenuOpen && !sidebarCollapsed" x-collapse class="ml-9 border-l border-slate-700/80 py-1">
                        <div x-data="{ walletMenuOpen: @js(request()->routeIs('warehouse.wallet')) }">
                            <button type="button" @click="walletMenuOpen = !walletMenuOpen" class="flex w-full items-center px-4 py-2 text-left text-sm text-slate-300 transition hover:text-emerald-200">
                                <span>Wallet</span><svg class="ml-auto h-3.5 w-3.5 transition-transform" :class="walletMenuOpen ? 'rotate-180 text-emerald-300' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6" /></svg>
                            </button>
                            <div x-cloak x-show="walletMenuOpen" x-collapse class="ml-4 border-l border-slate-700/80 py-1">
                                <a href="{{ route('warehouse.wallet') }}" class="block px-4 py-2 text-sm transition {{ request()->routeIs('warehouse.wallet') ? 'text-emerald-300' : 'text-slate-400 hover:text-white' }}">Customer</a>
                            </div>
                        </div>
                        <a href="{{ route('warehouse.farms') }}" class="block px-4 py-2 text-sm transition {{ request()->routeIs('warehouse.farms') ? 'text-emerald-300' : 'text-slate-400 hover:text-white' }}">Farm</a>
                        <a href="{{ route('warehouse.cattle') }}" class="block px-4 py-2 text-sm transition {{ request()->routeIs('warehouse.cattle') ? 'text-emerald-300' : 'text-slate-400 hover:text-white' }}">Cattle</a>
                        <a href="{{ route('warehouse.calves') }}" class="block px-4 py-2 text-sm transition {{ request()->routeIs('warehouse.calves') ? 'text-emerald-300' : 'text-slate-400 hover:text-white' }}">Calf</a>

                        <button type="button" @click="reportMenuOpen = !reportMenuOpen" class="flex w-full items-center px-4 py-2 text-sm text-emerald-300 transition hover:text-emerald-200">
                            Report
                            <svg class="ml-auto h-3.5 w-3.5 transition-transform" :class="reportMenuOpen ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6" /></svg>
                        </button>
                        <div x-cloak x-show="reportMenuOpen" x-collapse class="pb-1">
                            @foreach(['Farm', 'Milk Production', 'Vaccine', 'Disease', 'Impregnation', 'Pregnancy', 'Abortion', 'Food Consumption', 'Weight Info'] as $report)
                                <span class="block cursor-not-allowed px-6 py-2 text-sm text-slate-500" title="Area Office report coming soon">{{ $report }}</span>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div x-data="{ manageStockOpen: @js(request()->routeIs('warehouse.stock', 'warehouse.stock.lsp-received')) }" class="pt-1">
                    <button type="button" @click="manageStockOpen = !manageStockOpen" class="flex w-full items-center gap-3 rounded-2xl border border-transparent px-4 py-3 text-left text-sm font-medium text-slate-300 transition hover:border-white/5 hover:bg-white/[0.04] hover:text-white" :class="sidebarCollapsed ? 'lg:justify-center lg:px-2' : ''">
                        <svg class="h-5 w-5 text-sky-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.75 7.5 12 3.75 19.25 7.5 12 11.25 4.75 7.5 12 11.25M4.75 11.75 12 15.5l7.25-3.75M4.75 16 12 19.75 19.25 16" /></svg>
                        <span x-show="!sidebarCollapsed" class="truncate">Manage Stock</span>
                        <svg x-show="!sidebarCollapsed" class="ml-auto h-4 w-4 transition-transform" :class="manageStockOpen ? 'rotate-180 text-sky-300' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6" /></svg>
                    </button>
                    <div x-cloak x-show="manageStockOpen && !sidebarCollapsed" x-collapse class="ml-9 border-l border-slate-700/80 py-1">
                        <a href="{{ route('warehouse.stock') }}" class="block px-4 py-2 text-sm transition {{ request()->routeIs('warehouse.stock') ? 'text-sky-300' : 'text-slate-400 hover:text-white' }}">Current Stock</a>
                        <a href="{{ route('warehouse.stock.lsp-received') }}" class="block px-4 py-2 text-sm transition {{ request()->routeIs('warehouse.stock.lsp-received') ? 'text-sky-300' : 'text-slate-400 hover:text-white' }}">Received List</a>
                    </div>
                </div>

                <div x-data="{ manageSalesOpen: @js(request()->routeIs('warehouse.sales.index', 'warehouse.due-amount', 'warehouse.sales.create', 'warehouse.sales.show', 'warehouse.sales.invoice')) }" class="pt-1">
                    <button type="button" @click="manageSalesOpen = !manageSalesOpen" class="flex w-full items-center gap-3 rounded-2xl border border-transparent px-4 py-3 text-left text-sm font-medium text-slate-300 transition hover:border-white/5 hover:bg-white/[0.04] hover:text-white" :class="sidebarCollapsed ? 'lg:justify-center lg:px-2' : ''">
                        <svg class="h-5 w-5 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6"><path stroke-linecap="round" stroke-linejoin="round" d="M6 4.75h12v14.5l-2-1.25-2 1.25-2-1.25-2 1.25-2-1.25-2 1.25V4.75ZM8.75 9h6.5M8.75 12.25h6.5" /></svg>
                        <span x-show="!sidebarCollapsed" class="truncate">Manage Sales</span>
                        <svg x-show="!sidebarCollapsed" class="ml-auto h-4 w-4 transition-transform" :class="manageSalesOpen ? 'rotate-180 text-emerald-300' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6" /></svg>
                    </button>
                    <div x-cloak x-show="manageSalesOpen && !sidebarCollapsed" x-collapse class="ml-9 border-l border-slate-700/80 py-1">
                        <a href="{{ route('warehouse.sales.index') }}" class="block px-4 py-2 text-sm transition {{ request()->routeIs('warehouse.sales.index') && request('payment_status') !== 'due' ? 'text-emerald-300' : 'text-slate-400 hover:text-white' }}">Sales</a>
                        <a href="{{ route('warehouse.due-amount') }}" class="block px-4 py-2 text-sm transition {{ request()->routeIs('warehouse.due-amount') ? 'text-emerald-300' : 'text-slate-400 hover:text-white' }}">Due Sales</a>
                        <a href="{{ route('warehouse.sales.create') }}" class="block px-4 py-2 text-sm transition {{ request()->routeIs('warehouse.sales.create') ? 'text-emerald-300' : 'text-slate-400 hover:text-white' }}">POS</a>
                    </div>
                </div>

                <div x-data="{ dailyVisitMenuOpen: @js(request()->routeIs('warehouse.daily-visits*')) }" class="pt-1">
                    <button type="button" @click="dailyVisitMenuOpen = !dailyVisitMenuOpen" class="flex w-full items-center gap-3 rounded-2xl border border-transparent px-4 py-3 text-left text-sm font-medium text-slate-300 transition hover:border-white/5 hover:bg-white/[0.04] hover:text-white" :class="sidebarCollapsed ? 'lg:justify-center lg:px-2' : ''">
                        <svg class="h-5 w-5 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7"><path stroke-linecap="round" stroke-linejoin="round" d="M6.5 4.75h11A1.75 1.75 0 0 1 19.25 6.5v13H4.75v-13A1.75 1.75 0 0 1 6.5 4.75ZM8 3.75v3M16 3.75v3M7.5 10h9M7.5 14h5" /></svg>
                        <span x-show="!sidebarCollapsed" class="truncate">Daily Visit</span>
                        <svg x-show="!sidebarCollapsed" class="ml-auto h-4 w-4 transition-transform" :class="dailyVisitMenuOpen ? 'rotate-180 text-emerald-300' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6" /></svg>
                    </button>
                    <div x-cloak x-show="dailyVisitMenuOpen && !sidebarCollapsed" x-collapse class="ml-9 border-l border-slate-700/80 py-1">
                        <a href="{{ route('warehouse.daily-visits') }}" class="block px-4 py-2 text-sm transition {{ request()->routeIs('warehouse.daily-visits') ? 'text-emerald-300' : 'text-slate-400 hover:text-white' }}">Visit List</a>
                        <a href="{{ route('warehouse.daily-visits.create') }}" class="block px-4 py-2 text-sm transition {{ request()->routeIs('warehouse.daily-visits.create') ? 'text-emerald-300' : 'text-slate-400 hover:text-white' }}">Manual Entry</a>
                        <a href="{{ route('warehouse.qr.scan') }}" class="block px-4 py-2 text-sm transition {{ request()->routeIs('warehouse.qr.scan') ? 'text-emerald-300' : 'text-slate-400 hover:text-white' }}">QR Code Scan</a>
                        <a href="{{ route('warehouse.daily-visits.report') }}" class="block px-4 py-2 text-sm transition {{ request()->routeIs('warehouse.daily-visits.report') ? 'text-emerald-300' : 'text-slate-400 hover:text-white' }}">Report</a>
                    </div>
                </div>
            @endif

            @if (Auth::guard('warehouse')->check() || Auth::guard('warehouse_salesman')->check())
                <div class="pt-1">
                    <a href="{{ route('warehouse.renewable-energy.index') }}" class="flex w-full items-center gap-3 rounded-2xl border border-transparent px-4 py-3 text-sm font-medium transition hover:border-white/5 hover:bg-white/[0.04] hover:text-white {{ request()->routeIs('warehouse.renewable-energy.*') ? 'text-emerald-300' : 'text-slate-300' }}" :class="sidebarCollapsed ? 'lg:justify-center lg:px-2' : ''">
                        <svg class="h-5 w-5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3.75v2.5m0 11.5v2.5M4.75 12h-2.5m19.5 0h-2.5m-1.86-5.14-1.77 1.77m-9.24 9.24-1.77 1.77m0-12.78 1.77 1.77m9.24 9.24 1.77 1.77M16.25 12a4.25 4.25 0 1 1-8.5 0 4.25 4.25 0 0 1 8.5 0Z" /></svg>
                        <span x-show="!sidebarCollapsed">Renewable Energy</span>
                    </a>
                </div>
            @endif

        </div>
    </nav>


</aside>

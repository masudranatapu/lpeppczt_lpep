<x-warehouse-layout :title="$isSalesman ? 'Sales Dashboard' : 'Area Office Dashboard'">
    @php
        $totalCompanyStock = $stock->sum(fn ($product) => (float) ($product->company_stock_qty ?? 0));
        $totalWarehouseStock = $stock->sum(fn ($product) => (float) ($product->warehouse_available_qty ?? 0));
        $totalSalesmanStock = $stock->sum(fn ($product) => (float) ($product->salesman_stock_qty ?? 0));
        $inStockProducts = $stock->filter(fn ($product) => (float) ($product->warehouse_available_qty ?? 0) > 0)->values();
        $availableProducts = $inStockProducts->count();
    @endphp

    <div class="space-y-6">
        <section class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
            <x-warehouse.page-header
                :title="$isSalesman ? 'Sales Dashboard' : 'Area Office Dashboard'"
                description="{{ $isSalesman ? 'Your sales today and current product stock at ' : 'A clear overview of today\'s sales and current stock at ' }}{{ $warehouse->name }}."
            >
                <x-slot name="actions">
                    <div class="flex flex-wrap gap-2">
                        @unless ($isSalesman)
                            <x-warehouse.button href="{{ route('warehouse.stock') }}" variant="secondary" icon="eye" class="rounded-xl px-5 py-3">
                                View Stock
                            </x-warehouse.button>
                        @endunless
                        @if ($isSalesman)
                            <x-warehouse.button href="{{ route('warehouse.sales.create') }}" variant="primary" icon="plus" class="rounded-xl px-5 py-3">
                                New Sale
                            </x-warehouse.button>
                        @endif
                    </div>
                </x-slot>
            </x-warehouse.page-header>

            <div class="grid gap-4 bg-slate-50 px-4 py-5 sm:grid-cols-2 sm:px-6 xl:grid-cols-3">
                <div class="relative overflow-hidden rounded-2xl border border-emerald-200 bg-white p-5 shadow-sm">
                    <div class="absolute -right-6 -top-6 h-24 w-24 rounded-full bg-emerald-100"></div>
                    <div class="relative">
                        <div class="flex items-center justify-between gap-3">
                            <p class="m-0 text-sm font-semibold text-slate-500">{{ $isSalesman ? 'Your Sales Today' : 'Today Sales' }}</p>
                            <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600 ring-1 ring-emerald-100">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 19.5h15M6.5 16V9.5m5.5 6.5V4.5m5.5 11.5v-4" />
                                </svg>
                            </span>
                        </div>
                        <p class="mb-0 mt-5 text-3xl font-bold tracking-tight text-slate-900">{{ number_format($todaySales, 2) }}</p>
                        <p class="mb-0 mt-1 text-xs font-medium text-emerald-700">{{ $isSalesman ? 'Your BDT sales today' : 'BDT sold today' }}</p>
                    </div>
                </div>

                <div class="relative overflow-hidden rounded-2xl border border-blue-200 bg-white p-5 shadow-sm"><div class="relative"><p class="m-0 text-sm font-semibold text-slate-500">Collected / Paid Today</p><p class="mb-0 mt-5 text-3xl font-bold tracking-tight text-slate-900">{{ number_format($todayCollectedAmount, 2) }}</p><p class="mb-0 mt-1 text-xs font-medium text-blue-700">BDT received today</p></div></div>
                <div class="relative overflow-hidden rounded-2xl border border-amber-200 bg-white p-5 shadow-sm"><div class="relative"><p class="m-0 text-sm font-semibold text-slate-500">Due Today</p><p class="mb-0 mt-5 text-3xl font-bold tracking-tight text-slate-900">{{ number_format($todayDueAmount, 2) }}</p><p class="mb-0 mt-1 text-xs font-medium text-amber-700">BDT outstanding from today’s sales</p></div></div>

                <div class="relative overflow-hidden rounded-2xl border border-sky-200 bg-white p-5 shadow-sm">
                    <div class="absolute -right-6 -top-6 h-24 w-24 rounded-full bg-sky-100"></div>
                    <div class="relative">
                        <div class="flex items-center justify-between gap-3">
                            <p class="m-0 text-sm font-semibold text-slate-500">{{ $isSalesman ? 'Stock' : 'Area Office Stock' }}</p>
                            <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-sky-50 text-sky-600 ring-1 ring-sky-100">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m4 7.5 8-4 8 4-8 4-8-4Zm0 4.5 8 4 8-4M4 16.5l8 4 8-4" />
                                </svg>
                            </span>
                        </div>
                        <p class="mb-0 mt-5 text-3xl font-bold tracking-tight text-slate-900">{{ number_format($totalWarehouseStock, 0) }}</p>
                        <p class="mb-0 mt-1 text-xs font-medium text-sky-700">Across {{ $availableProducts }} products</p>
                    </div>
                </div>

                @unless ($isSalesman)
                    <div class="relative overflow-hidden rounded-2xl border border-violet-200 bg-white p-5 shadow-sm">
                        <div class="absolute -right-6 -top-6 h-24 w-24 rounded-full bg-violet-100"></div>
                        <div class="relative">
                            <div class="flex items-center justify-between gap-3">
                                <p class="m-0 text-sm font-semibold text-slate-500">LSP Stock</p>
                                <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-violet-50 text-violet-600 ring-1 ring-violet-100">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v12m0 0 4-4m-4 4-4-4M5 19.5h14" />
                                    </svg>
                                </span>
                            </div>
                            <p class="mb-0 mt-5 text-3xl font-bold tracking-tight text-slate-900">{{ number_format($totalSalesmanStock, 0) }}</p>
                            <p class="mb-0 mt-1 text-xs font-medium text-violet-700">Allocated to salesmen</p>
                        </div>
                    </div>
                @endunless

            </div>
        </section>

        <section class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_300px]">
            <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
                <div class="flex flex-col gap-2 border-b border-slate-200 px-4 py-3 sm:flex-row sm:items-center sm:justify-between sm:px-5">
                    <div>
                        <h2 class="m-0 text-lg font-bold tracking-tight text-slate-900">Current Stock</h2>
                        <p class="mb-0 mt-0.5 text-sm text-slate-500">
                            {{ $isSalesman ? 'Received' : 'Purchased' }}, sold, and {{ $isSalesman ? 'stock' : 'warehouse stock' }} quantities{{ $isSalesman ? ' for your account.' : '.' }}
                        </p>
                    </div>
                    @unless ($isSalesman)
                        <x-warehouse.button href="{{ route('warehouse.stock') }}" variant="secondary" size="sm" class="rounded-lg" icon="eye">
                            Full Stock Report
                        </x-warehouse.button>
                    @endunless
                </div>

                <x-warehouse.table min-width="min-w-0 table-fixed" wrapper-class="w-full" >
                    <x-slot name="header">
                        <tr>
                            <x-warehouse.table.th padding-class="px-2 py-2 text-xs">SL</x-warehouse.table.th>
                            <x-warehouse.table.th padding-class="px-2 py-2 text-xs" :nowrap="false">Product</x-warehouse.table.th>
                            <x-warehouse.table.th padding-class="px-2 py-2 text-xs" :nowrap="false">Unit</x-warehouse.table.th>
                            <x-warehouse.table.th padding-class="px-2 py-2 text-xs" :nowrap="false">{{ $isSalesman ? 'Received Qty' : 'Purchase' }}</x-warehouse.table.th>
                            <x-warehouse.table.th padding-class="px-2 py-2 text-xs" :nowrap="false">{{ $isSalesman ? 'Sold Qty' : 'Sold' }}</x-warehouse.table.th>
                            <x-warehouse.table.th padding-class="px-2 py-2 text-xs" :nowrap="false">{{ $isSalesman ? 'Stock Qty' : 'Area Stock' }}</x-warehouse.table.th>
                            @unless ($isSalesman)
                                <x-warehouse.table.th padding-class="px-2 py-2 text-xs" :nowrap="false">LSP Stock</x-warehouse.table.th>
                            @endunless
                        </tr>
                    </x-slot>

                    @forelse($inStockProducts as $product)
                        @php
                            $purchased = (float) ($product->warehouse_purchase_qty ?? 0) + (float) ($product->warehouse_transfer_qty ?? 0);
                            $sold = (float) ($product->warehouse_sale_qty ?? 0);
                            $warehouseStock = (float) ($product->warehouse_available_qty ?? 0);
                            $salesmanStock = (float) ($product->salesman_stock_qty ?? 0);
                        @endphp
                        <tr class="hover:bg-slate-50">
                            <x-warehouse.table.td padding-class="px-2 py-1.5 text-xs" class="font-medium text-slate-900">{{ $loop->iteration }}</x-warehouse.table.td>
                            <x-warehouse.table.td padding-class="px-2 py-1.5 text-xs" :nowrap="false" class="max-w-[150px] truncate font-semibold text-slate-900">{{ $product->product_name }}</x-warehouse.table.td>
                            <x-warehouse.table.td padding-class="px-2 py-1.5 text-xs" :nowrap="false">{{ $product->unit?->short_name ?: $product->unit?->actual_name ?: '-' }}</x-warehouse.table.td>
                            <x-warehouse.table.td padding-class="px-2 py-1.5 text-xs" :nowrap="false">{{ number_format($purchased, 0) }}</x-warehouse.table.td>
                            <x-warehouse.table.td padding-class="px-2 py-1.5 text-xs" :nowrap="false">{{ number_format($sold, 0) }}</x-warehouse.table.td>
                            <x-warehouse.table.td padding-class="px-2 py-1.5 text-xs" :nowrap="false">
                                <span class="inline-flex min-w-[72px] justify-center rounded-full border px-3 py-1 text-sm font-bold {{ $warehouseStock > 0 ? 'border-emerald-200 bg-emerald-50 text-emerald-700' : 'border-red-200 bg-red-50 text-red-700' }}">
                                    {{ number_format($warehouseStock, 0) }}
                                </span>
                            </x-warehouse.table.td>
                            @unless ($isSalesman)
                                <x-warehouse.table.td padding-class="px-2 py-1.5 text-xs" :nowrap="false">{{ number_format($salesmanStock, 0) }}</x-warehouse.table.td>
                            @endunless
                        </tr>
                    @empty
                        <tr>
                            <x-warehouse.table.td colspan="{{ $isSalesman ? 6 : 7 }}" :nowrap="false" class="py-12 text-center">
                                <x-warehouse.empty-state title="No products in stock" description="All warehouse products are currently out of stock." />
                            </x-warehouse.table.td>
                        </tr>
                    @endforelse
                </x-warehouse.table>
            </div>

            <aside class="space-y-4">
                <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                    <p class="m-0 text-xs font-bold uppercase tracking-[0.16em] text-slate-400">Area Office Profile</p>
                    <h3 class="mb-0 mt-3 text-xl font-bold text-slate-900">{{ $warehouse->name }}</h3>
                    @if ($warehouse->code)
                        <span class="mt-3 inline-flex rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">{{ $warehouse->code }}</span>
                    @endif
                    <dl class="mb-0 mt-5 space-y-4 text-sm">
                        <div>
                            <dt class="font-medium text-slate-400">Email</dt>
                            <dd class="mb-0 mt-1 break-words font-semibold text-slate-700">{{ $warehouse->email ?: 'Not provided' }}</dd>
                        </div>
                        @if ($warehouse->phone)
                            <div>
                                <dt class="font-medium text-slate-400">Phone</dt>
                                <dd class="mb-0 mt-1 font-semibold text-slate-700">{{ $warehouse->phone }}</dd>
                            </div>
                        @endif
                    </dl>
                </div>

                <div class="rounded-3xl bg-slate-900 p-5 text-white shadow-sm">
                    <p class="m-0 text-xs font-bold uppercase tracking-[0.16em] text-slate-400">Stock Movement</p>
                    <div class="mt-5 space-y-4">
                        <div class="flex items-center justify-between gap-4">
                            <span class="text-sm text-slate-300">{{ $isSalesman ? 'Your total sold' : 'Total sold' }}</span>
                            <strong class="text-lg">{{ number_format($isSalesman ? $portalSoldQuantity : $totalCompanyStock - $totalWarehouseStock, 0) }}</strong>
                        </div>
                        <div class="h-px bg-slate-700"></div>
                        <div class="flex items-center justify-between gap-4">
                            <span class="text-sm text-slate-300">Products tracked</span>
                            <strong class="text-lg">{{ $stock->count() }}</strong>
                        </div>
                    </div>
                </div>
            </aside>
        </section>
    </div>
</x-warehouse-layout>

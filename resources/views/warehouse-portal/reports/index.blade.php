<x-warehouse-layout title="Sales Reports">
    @php
        $exportQuery = request()->only(['start_date', 'end_date', 'per_page']);
    @endphp

    <div class="space-y-5">
        <div
            x-data="{ reportFilterOpen: false }"
            x-on:keydown.escape.window="reportFilterOpen = false"
            x-effect="document.body.classList.toggle('overflow-hidden', reportFilterOpen && window.innerWidth < 1024)"
            class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm"
        >
            <x-warehouse.page-header
                title="LSP Reports"
                description="Accurate sales performance for every salesman at {{ $warehouse->name }}."
            >
                <x-slot name="actions">
                    <div class="flex flex-wrap gap-2">
                        <a href="{{ route('warehouse.reports.print', $exportQuery) }}" target="_blank" rel="noopener" class="inline-flex h-10 items-center gap-2 rounded-xl border border-slate-300 bg-white px-4 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50">
                            <svg class="h-4 w-4" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M6 7V4.75A1.75 1.75 0 0 1 7.75 3h4.5A1.75 1.75 0 0 1 14 4.75V7m-8 7h8m-9.25-6h10.5A1.75 1.75 0 0 1 17 9.75v3.5A1.75 1.75 0 0 1 15.25 15H14v1.25A1.75 1.75 0 0 1 12.25 18h-4.5A1.75 1.75 0 0 1 6 16.25V15H4.75A1.75 1.75 0 0 1 3 13.25v-3.5A1.75 1.75 0 0 1 4.75 8Z" /></svg>
                            Print
                        </a>
                        <a href="{{ route('warehouse.reports.pdf', $exportQuery) }}" class="inline-flex h-10 items-center gap-2 rounded-xl border border-red-200 bg-red-50 px-4 text-sm font-semibold text-red-700 shadow-sm transition hover:bg-red-100">
                            <svg class="h-4 w-4 shrink-0" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 2.75h6l3.5 3.5v11H5.25v-14Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 2.75v3.5h3.5M7.25 13.75h5.5M7.25 10.75h5.5" />
                            </svg>
                            PDF
                        </a>
                        <a href="{{ route('warehouse.reports.excel', $exportQuery) }}" class="inline-flex h-10 items-center gap-2 rounded-xl border border-emerald-200 bg-emerald-50 px-4 text-sm font-semibold text-emerald-700 shadow-sm transition hover:bg-emerald-100">
                            <svg class="h-4 w-4 shrink-0" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 2.75h6l3.5 3.5v11H5.25v-14Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 2.75v3.5h3.5M7.5 10l4.75 4.75m0-4.75L7.5 14.75" />
                            </svg>
                            Excel
                        </a>
                    </div>
                </x-slot>
            </x-warehouse.page-header>

            <div class="flex items-center justify-between gap-3 border-t border-slate-200 bg-slate-50 px-4 py-3 lg:hidden">
                <div class="min-w-0">
                    <p class="m-0 text-sm font-bold text-slate-900">Report Filters</p>
                    <p class="mb-0 mt-0.5 truncate text-xs text-slate-500">{{ $filters['label'] }}</p>
                </div>
                <button
                    type="button"
                    x-on:click="reportFilterOpen = true"
                    x-bind:aria-expanded="reportFilterOpen.toString()"
                    aria-controls="report-filter-panel"
                    class="inline-flex h-11 shrink-0 items-center justify-center gap-2 rounded-xl border border-red-200 bg-red-50 px-4 text-sm font-bold text-red-700 shadow-sm transition active:scale-[0.98]"
                >
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M4 5.75h16l-6.25 7.1v5.4l-3.5-1.8v-3.6L4 5.75Z" /></svg>
                    Filter
                </button>
            </div>

            <div
                x-cloak
                x-show="reportFilterOpen"
                x-transition.opacity
                x-on:click="reportFilterOpen = false"
                class="fixed inset-0 z-[60] bg-slate-950/60 backdrop-blur-[1px] lg:hidden"
                aria-hidden="true"
            ></div>

            <form
                method="GET"
                action="{{ route('warehouse.reports.index') }}"
                id="report-filter-panel"
                x-cloak
                x-bind:class="reportFilterOpen ? 'translate-x-0' : 'translate-x-full'"
                class="fixed inset-y-0 right-0 z-[70] w-[min(90vw,380px)] overflow-y-auto border-l border-slate-200 bg-slate-50 px-4 py-4 shadow-2xl transition-transform duration-300 ease-out sm:px-6 lg:static lg:z-auto lg:w-auto lg:translate-x-0 lg:overflow-visible lg:border-l-0 lg:border-t lg:border-b lg:shadow-none"
            >
                <div class="mb-4 flex items-center justify-between border-b border-slate-200 pb-4 lg:hidden">
                    <div>
                        <h2 class="m-0 text-lg font-bold text-slate-900">Filter Reports</h2>
                        <p class="mb-0 mt-1 text-xs text-slate-500">Choose a date range for the report.</p>
                    </div>
                    <div>
                        <label for="report-per-page" class="mb-2 block text-sm font-semibold text-slate-700">Per page</label>
                        <select name="per_page" id="report-per-page" class="block h-11 w-full rounded-xl border border-slate-300 bg-white px-3 text-sm text-slate-800 outline-none focus:border-red-500 focus:ring-2 focus:ring-red-500/20">
                            @foreach(['all' => 'All', '10' => '10', '20' => '20', '50' => '50', '100' => '100'] as $value => $label)
                                <option value="{{ $value }}" @selected(($filters['per_page'] ?? 'all') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="button" x-on:click="reportFilterOpen = false" class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-600 shadow-sm" aria-label="Close filters">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" d="M6 6l12 12M18 6 6 18" /></svg>
                    </button>
                </div>

                <div class="grid gap-4 lg:grid-cols-[minmax(180px,1fr)_minmax(180px,1fr)_minmax(140px,0.6fr)_auto] lg:items-end">
                    <div><label for="report-start-date" class="mb-2 block text-sm font-semibold text-slate-700">From Date</label><input type="date" name="start_date" id="report-start-date" value="{{ $filters['start_date'] }}" class="block h-11 w-full rounded-xl border border-slate-300 bg-white px-3 text-sm text-slate-800 outline-none focus:border-red-500 focus:ring-2 focus:ring-red-500/20"></div>
                    <div><label for="report-end-date" class="mb-2 block text-sm font-semibold text-slate-700">To Date</label><input type="date" name="end_date" id="report-end-date" value="{{ $filters['end_date'] }}" class="block h-11 w-full rounded-xl border border-slate-300 bg-white px-3 text-sm text-slate-800 outline-none focus:border-red-500 focus:ring-2 focus:ring-red-500/20"></div>

                    <div class="flex flex-col gap-2 sm:flex-row">
                        <x-loading-submit type="submit" class="inline-flex w-full lg:w-auto items-center justify-center gap-2 rounded-xl border border-transparent bg-red-600 px-4 py-3 text-white shadow-sm hover:bg-red-500" icon="funnel" loading-text="Filtering...">Filter</x-loading-submit>
                        @if (request()->hasAny(['start_date', 'end_date', 'per_page']))
                            <x-warehouse.button href="{{ route('warehouse.reports.index') }}" variant="secondary" icon="rotate-ccw" class="w-full lg:w-auto">Reset</x-warehouse.button>
                        @endif
                    </div>
                </div>
            </form>

        </div>

        <section class="grid gap-3 sm:grid-cols-2 xl:grid-cols-6">
            <div class="rounded-xl border border-sky-200 bg-white p-3.5 shadow-sm">
                <p class="m-0 text-xs font-bold uppercase tracking-wider text-sky-700">Total Sales</p>
                <p class="mb-0 mt-1.5 text-2xl font-extrabold leading-tight text-slate-900">{{ number_format($summary->sale_count) }}</p>
                <p class="mb-0 mt-0.5 text-xs text-slate-500">Completed transactions</p>
            </div>
            <div class="rounded-xl border border-violet-200 bg-white p-3.5 shadow-sm">
                <p class="m-0 text-xs font-bold uppercase tracking-wider text-violet-700">Quantity Sold</p>
                <p class="mb-0 mt-1.5 text-2xl font-extrabold leading-tight text-slate-900">{{ number_format($summary->total_quantity, 0) }}</p>
                <p class="mb-0 mt-0.5 text-xs text-slate-500">Across all products</p>
            </div>
            <div class="rounded-xl border border-amber-200 bg-white p-3.5 shadow-sm">
                <p class="m-0 text-xs font-bold uppercase tracking-wider text-amber-700">Active LSPs</p>
                <p class="mb-0 mt-1.5 text-2xl font-extrabold leading-tight text-slate-900">{{ $summary->active_salesmen }} / {{ $summary->registered_salesmen }}</p>
                <p class="mb-0 mt-0.5 text-xs text-slate-500">With sales in this period</p>
            </div>
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-3.5 shadow-sm">
                <p class="m-0 text-xs font-bold uppercase tracking-wider text-emerald-700">Collected Sales Amount</p>
                <p class="mb-0 mt-1.5 text-2xl font-extrabold leading-tight text-emerald-900">{{ number_format($summary->paid_amount, 2) }}</p>
                <p class="mb-0 mt-0.5 text-xs text-emerald-700">BDT paid amount</p>
            </div>
            <div class="rounded-xl border border-blue-200 bg-blue-50 p-3.5 shadow-sm">
                <p class="m-0 text-xs font-bold uppercase tracking-wider text-blue-700">Sales Amount</p>
                <p class="mb-0 mt-1.5 text-2xl font-extrabold leading-tight text-blue-900">{{ number_format($summary->total_amount, 2) }}</p>
                <p class="mb-0 mt-0.5 text-xs text-blue-700">BDT after discount</p>
            </div>
            <div class="rounded-xl border border-amber-200 bg-amber-50 p-3.5 shadow-sm">
                <p class="m-0 text-xs font-bold uppercase tracking-wider text-amber-700">Total Due</p>
                <p class="mb-0 mt-1.5 text-2xl font-extrabold leading-tight text-amber-900">{{ number_format($summary->due_amount, 2) }}</p>
                <p class="mb-0 mt-0.5 text-xs text-amber-700">BDT outstanding</p>
            </div>
        </section>

        <section class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 px-4 py-4 sm:px-6">
                <h2 class="m-0 text-lg font-bold text-slate-900">Sales Transactions</h2>
                <p class="mb-0 mt-1 text-sm text-slate-500">Invoice-level records used in the report calculations.</p>
            </div>
            <x-warehouse.table min-width="min-w-[1050px]">
                <x-slot name="header"><tr><x-warehouse.table.th>SL</x-warehouse.table.th><x-warehouse.table.th>Date</x-warehouse.table.th><x-warehouse.table.th>Invoice</x-warehouse.table.th><x-warehouse.table.th>LSP</x-warehouse.table.th><x-warehouse.table.th>Customer</x-warehouse.table.th><x-warehouse.table.th>Products</x-warehouse.table.th><x-warehouse.table.th>Qty</x-warehouse.table.th><x-warehouse.table.th>Paid / Due</x-warehouse.table.th><x-warehouse.table.th>Total</x-warehouse.table.th></tr></x-slot>
                @forelse ($sales as $sale)
                    <tr class="hover:bg-slate-50">
                        <x-warehouse.table.td>{{ ($sales instanceof \Illuminate\Pagination\AbstractPaginator ? $sales->firstItem() : 1) + $loop->index }}</x-warehouse.table.td>
                        <x-warehouse.table.td>{{ \Carbon\Carbon::parse($sale->sale_date)->format('d-m-Y') }}<div class="text-xs text-slate-400">{{ $sale->created_at?->format('h:i A') }}</div></x-warehouse.table.td>
                        <x-warehouse.table.td class="font-semibold text-slate-900">{{ $sale->invoice_no }}</x-warehouse.table.td>
                        <x-warehouse.table.td>{{ $sale->salesman?->name ?: 'Area Office Direct' }}</x-warehouse.table.td>
                        <x-warehouse.table.td>{{ $sale->customer_name ?: '-' }}<div class="text-xs text-slate-400">{{ $sale->customer_phone }}</div></x-warehouse.table.td>
                        <x-warehouse.table.td>
                            @foreach ($sale->items as $item)
                                <div class="flex min-w-[240px] justify-between gap-3 border-b border-dashed border-slate-200 py-1 last:border-0"><span>{{ $item->product?->product_name ?: 'Deleted product' }}</span><span class="text-slate-500">{{ number_format($item->quantity, 0) }}</span></div>
                            @endforeach
                        </x-warehouse.table.td>
                        <x-warehouse.table.td>{{ number_format($sale->total_quantity, 0) }}</x-warehouse.table.td>
                        <x-warehouse.table.td><div class="flex flex-col items-start gap-1"><span class="inline-flex whitespace-nowrap rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-bold text-emerald-800 ring-1 ring-inset ring-emerald-200">Paid {{ number_format((float) $sale->paid_amount, 2) }}</span><span class="inline-flex whitespace-nowrap rounded-full px-2.5 py-1 text-xs font-bold ring-1 ring-inset {{ (float) $sale->due_amount > 0 ? 'bg-amber-100 text-amber-800 ring-amber-200' : 'bg-slate-100 text-slate-600 ring-slate-200' }}">Due {{ number_format((float) $sale->due_amount, 2) }}</span></div></x-warehouse.table.td>
                        <x-warehouse.table.td class="font-bold text-slate-900">{{ number_format($sale->total_amount, 2) }}</x-warehouse.table.td>
                    </tr>
                @empty
                    <tr><x-warehouse.table.td colspan="9" :nowrap="false" class="py-10 text-center">No sales found for {{ $filters['label'] }}.</x-warehouse.table.td></tr>
                @endforelse
            </x-warehouse.table>
            <div class="flex flex-col gap-3 border-t border-slate-200 px-4 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
                <p class="m-0 text-sm text-slate-500">Showing {{ $sales instanceof \Illuminate\Pagination\AbstractPaginator ? $sales->firstItem() : ($sales->count() ? 1 : 0) }} to {{ $sales instanceof \Illuminate\Pagination\AbstractPaginator ? $sales->lastItem() : $sales->count() }} of {{ $sales instanceof \Illuminate\Pagination\AbstractPaginator ? $sales->total() : $sales->count() }} transactions</p>
                @if ($sales instanceof \Illuminate\Pagination\AbstractPaginator)
                    {{ $sales->onEachSide(1)->links('warehouse-portal.partials.pagination') }}
                @endif
            </div>
        </section>
    </div>

    <x-slot name="script">
    </x-slot>
</x-warehouse-layout>

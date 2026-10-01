<x-warehouse-layout title="{{ $isSalesman ?? false ? 'Current Stock' : 'Area Office Stock' }}">
    @php
        $totalPurchaseQty = $stock->sum(fn ($product) => (float) ($product->warehouse_purchase_qty ?? 0) + (float) ($product->warehouse_transfer_qty ?? 0));
        $totalWarehouseAvailable = $stock->sum(fn ($product) => (float) ($product->warehouse_available_qty ?? 0));
        $totalSalesmanStock = $stock->sum(fn ($product) => (float) ($product->salesman_stock_qty ?? 0));
        $exportQuery = array_filter(request()->only(['search', 'status', 'per_page', 'salesman_id']), fn ($value) => $value !== null && $value !== '');
    @endphp
    <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
        <x-warehouse.page-header
            title="{{ $isSalesman ?? false ? 'Current Stock' : 'Area Office Stock' }}"
            description="{{ $isSalesman ?? false ? 'Review received, sold, and available products for your account.' : 'Review product availability in this warehouse and compare received, sold, and available quantities.' }}"
            wrapper-class="border-b border-slate-200 bg-[radial-gradient(circle_at_top_left,_rgba(239,68,68,0.15),_transparent_34%),linear-gradient(135deg,_#ffffff_0%,_#fff1f2_100%)] px-4 py-3 sm:px-5"
            title-class="mt-0 text-lg font-bold tracking-tight text-slate-900 sm:text-xl"
            description-class="mt-0.5 text-sm text-slate-600"
        />

        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-200 bg-white px-4 py-3 sm:px-5">
            <div>
                <p class="m-0 text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Export</p>
                <p class="m-0 mt-1 text-sm text-slate-600">{{ ($isSalesman ?? false) ? 'Print or download your current stock.' : 'Print or download the current stock snapshot.' }}</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <x-warehouse.button
                    :href="route('warehouse.stock.print', $exportQuery)"
                    target="_blank"
                    rel="noopener"
                    variant="secondary"
                    icon="printer"
                    class="h-10 px-4"
                >
                    Print
                </x-warehouse.button>
                <x-warehouse.button
                    :href="route('warehouse.stock.pdf', $exportQuery)"
                    variant="outline"
                    icon="file-text"
                    class="h-10 px-4"
                >
                    PDF
                </x-warehouse.button>
                <x-warehouse.button
                    :href="route('warehouse.stock.excel', $exportQuery)"
                    variant="success"
                    icon="file-down"
                    class="h-10 px-4"
                >
                    Excel
                </x-warehouse.button>
            </div>
        </div>

        @if(!($isSalesman ?? false))
        <div class="grid gap-3 border-b border-slate-200 bg-slate-50 px-4 py-3 sm:grid-cols-2 xl:grid-cols-4 sm:px-5">
            <div class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 shadow-sm">
                <p class="m-0 text-sm font-medium text-slate-500">Products</p>
                <p class="mb-0 mt-1 text-xl font-bold tracking-tight text-slate-900">{{ number_format($summary->product_count, 0) }}</p>
            </div>

            <div class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 shadow-sm">
                <p class="m-0 text-sm font-medium text-slate-500">Area Office Received</p>
                <p class="mb-0 mt-1 text-xl font-bold tracking-tight text-slate-900">{{ number_format($totalPurchaseQty, 0) }}</p>
            </div>

            <div class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 shadow-sm">
                <p class="m-0 text-sm font-medium text-slate-500">Area Office Available</p>
                <p class="mb-0 mt-1 text-xl font-bold tracking-tight text-slate-900">{{ number_format($totalWarehouseAvailable, 0) }}</p>
            </div>

            <div class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 shadow-sm">
                <p class="m-0 text-sm font-medium text-slate-500">LSP Stock</p>
                <p class="mb-0 mt-1 text-xl font-bold tracking-tight text-slate-900">{{ number_format($totalSalesmanStock, 0) }}</p>
            </div>
        </div>
        @endif

        <form method="GET" action="{{ route('warehouse.stock') }}" class="flex flex-col gap-3 border-b border-slate-200 bg-white px-4 py-3 lg:flex-row lg:items-end lg:justify-between sm:px-5">
            <div class="relative w-full lg:max-w-md">
                <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35m2.1-5.4a7.5 7.5 0 1 1-15 0 7.5 7.5 0 0 1 15 0Z" />
                </svg>
                <input
                    type="search"
                    id="search"
                    name="search"
                    value="{{ $search }}"
                    placeholder="Search product..."
                    autocomplete="off"
                    class="block h-10 w-full rounded-xl border border-slate-300 bg-white py-2 pl-10 pr-10 text-sm text-slate-900 outline-none transition focus:border-red-500 focus:ring-2 focus:ring-red-500/20"
                >
            </div>
            <div class="flex flex-wrap items-center gap-3">
                @if(!($isSalesman ?? false))
                    <div class="w-52">
                        <x-warehouse.searchable-select
                            name="salesman_id"
                            id="stock-salesman"
                            :options="$salesmen->mapWithKeys(fn ($salesman) => [(string) $salesman->id => ['value' => (string) $salesman->id, 'label' => $salesman->name, 'description' => $salesman->email ?: 'LSP']])->all()"
                            :selected="$salesmanId"
                            placeholder="All LSPs"
                            search-placeholder="Search LSP..."
                        />
                    </div>
                @endif
                <label for="status" class="m-0 whitespace-nowrap text-sm font-medium text-slate-600">Stock status</label>
                <select id="status" name="status" class="h-10 rounded-xl border border-slate-300 bg-white px-3 text-sm text-slate-700 outline-none focus:border-red-500 focus:ring-2 focus:ring-red-500/20">
                    <option value="all" @selected($status === 'all')>All Products</option>
                    <option value="in" @selected($status === 'in')>In Stock</option>
                    <option value="out" @selected($status === 'out')>Out of Stock</option>
                </select>
                <label for="per_page" class="m-0 whitespace-nowrap text-sm font-medium text-slate-600">Per page</label>
                <select id="per_page" name="per_page" class="h-10 rounded-xl border border-slate-300 bg-white px-3 text-sm text-slate-700 outline-none focus:border-red-500 focus:ring-2 focus:ring-red-500/20">
                    @foreach ($perPageOptions as $option)
                        <option value="{{ $option }}" @selected((string) $perPage === (string) $option)>{{ $option === 'all' ? 'All' : $option }}</option>
                    @endforeach
                </select>
                <x-loading-submit type="submit" class="inline-flex h-10 items-center justify-center rounded-xl border border-transparent bg-red-600 px-4 text-sm font-semibold text-white shadow-sm hover:bg-red-500" icon="funnel" loading-text="Filtering...">Filter</x-loading-submit>
                @if ($search !== '' || $status !== 'all' || $perPage !== 'all' || (($salesmanId ?? 0) > 0))
                    <x-warehouse.button href="{{ route('warehouse.stock') }}" variant="secondary" class="h-10 px-4" icon="rotate-ccw">Reset</x-warehouse.button>
                @endif
            </div>
        </form>

        <x-warehouse.table min-width="min-w-[900px]" table-class="stock-summary-table w-full divide-y divide-slate-200" wrapper-class="max-h-[70vh] overflow-auto">
            <x-slot name="header">
                <tr class="sticky top-0 z-20 bg-emerald-700 shadow-sm">
                    <x-warehouse.table.th padding-class="px-4 py-2 sm:px-5">SL</x-warehouse.table.th>
                    <x-warehouse.table.th padding-class="px-4 py-2 sm:px-5">Product</x-warehouse.table.th>
                    <x-warehouse.table.th padding-class="px-4 py-2 sm:px-5">Selling Price</x-warehouse.table.th>
                    <x-warehouse.table.th padding-class="px-4 py-2 sm:px-5">{{ ($isSalesman ?? false) ? 'Received Qty' : 'Area Office Received' }}</x-warehouse.table.th>
                    <x-warehouse.table.th padding-class="px-4 py-2 sm:px-5">{{ ($isSalesman ?? false) ? 'Sold Qty' : 'Area Office Available' }}</x-warehouse.table.th>
                    @if(!($isSalesman ?? false))<x-warehouse.table.th padding-class="px-4 py-2 sm:px-5">LSP Sale</x-warehouse.table.th>@endif
                    <x-warehouse.table.th padding-class="px-4 py-2 sm:px-5">{{ ($isSalesman ?? false) ? 'Stock Qty' : 'LSP Stock' }}</x-warehouse.table.th>
                    <x-warehouse.table.th padding-class="px-4 py-2 sm:px-5">Total Selling Price</x-warehouse.table.th>
                    @if(!($isSalesman ?? false))<x-warehouse.table.th padding-class="px-4 py-2 sm:px-5">Action</x-warehouse.table.th>@endif
                </tr>
            </x-slot>

            @forelse($stock as $product)
                @php
                    $purchaseQty = (float) ($product->warehouse_purchase_qty ?? 0) + (float) ($product->warehouse_transfer_qty ?? 0);
                    $warehouseAvailable = (float) ($product->warehouse_available_qty ?? 0);
                    $salesmanStock = (float) ($product->salesman_stock_qty ?? 0);
                    $salesmanSale = (float) ($product->salesman_sale_qty ?? ((float) ($product->warehouse_sale_qty ?? 0) - (float) ($product->warehouse_direct_sale_qty ?? 0)));
                    $imagePath = $product->image ? json_decode($product->image) : null;
                    $imageUrl = $imagePath ? asset($imagePath) : asset('logo.jpeg');
                @endphp

                <tr class="hover:bg-slate-50">
                    <x-warehouse.table.td padding-class="px-4 py-1.5 sm:px-5" class="font-medium text-slate-900">
                        {{ $stock->firstItem() + $loop->index }}
                    </x-warehouse.table.td>
                    <x-warehouse.table.td padding-class="px-4 py-1.5 sm:px-5" class="font-semibold text-slate-900">
                        <div class="flex items-center gap-3">
                            <div class="flex h-11 w-11 shrink-0 items-center justify-center overflow-hidden rounded-xl border border-slate-200 bg-white">
                                <img
                                    src="{{ $imageUrl }}"
                                    alt="{{ $product->product_name }}"
                                    class="h-full w-full object-contain"
                                    loading="lazy"
                                    onerror="this.onerror=null;this.src='{{ asset('logo.jpeg') }}';"
                                >
                            </div>
                            <span class="min-w-0 truncate">{{ $product->product_name }}</span>
                        </div>
                    </x-warehouse.table.td>
                    <x-warehouse.table.td padding-class="px-4 py-1.5 sm:px-5">{{ number_format((float) $product->selling_price, 2) }}</x-warehouse.table.td>
                    <x-warehouse.table.td padding-class="px-4 py-1.5 sm:px-5">
                        {{ number_format($purchaseQty, 0) }}
                    </x-warehouse.table.td>
                    <x-warehouse.table.td padding-class="px-4 py-1.5 sm:px-5">
                        <span class="inline-flex items-center rounded-full border px-3 py-1 text-sm font-bold tracking-tight {{ $warehouseAvailable > 0 ? 'border-emerald-200 bg-emerald-50 text-emerald-700' : 'border-red-200 bg-red-50 text-red-700' }}">
                            {{ number_format(($isSalesman ?? false) ? $salesmanSale : $warehouseAvailable, 0) }}
                        </span>
                    </x-warehouse.table.td>
                    @if(!($isSalesman ?? false))
                    <x-warehouse.table.td padding-class="px-4 py-1.5 sm:px-5">
                        <span class="lsp-sale-pulse inline-flex items-center rounded-full border border-red-300 bg-red-50 px-3 py-1 text-sm font-bold text-red-700">
                            {{ number_format(max($salesmanSale, 0), 0) }}
                        </span>
                    </x-warehouse.table.td>@endif
                    <x-warehouse.table.td padding-class="px-4 py-1.5 sm:px-5">
                        @if($isSalesman ?? false)
                            <span class="lsp-stock-pulse inline-flex items-center rounded-full border border-emerald-300 bg-emerald-50 px-3 py-1 text-sm font-bold text-emerald-700">{{ number_format($salesmanStock, 0) }}</span>
                        @else
                            {{ number_format($salesmanStock, 0) }}
                        @endif
                    </x-warehouse.table.td>
                    @if($isSalesman ?? false)
                        <x-warehouse.table.td padding-class="px-4 py-1.5 sm:px-5">{{ number_format((float) $product->selling_price * $salesmanStock, 2) }}</x-warehouse.table.td>
                    @else
                        {{-- Same as the admin Stock Summary and the exports: selling price x total remaining (office + LSP stock). --}}
                        <x-warehouse.table.td padding-class="px-4 py-1.5 sm:px-5">{{ number_format((float) ($product->stock_sale_price ?? 0), 2) }}</x-warehouse.table.td>
                    @endif
                    @if(!($isSalesman ?? false))<x-warehouse.table.td padding-class="px-4 py-1.5 sm:px-5">
                        <a href="{{ route('warehouse.stock.lsp-distribution', $product) }}" title="View LSP stock distribution" aria-label="View LSP stock distribution" class="inline-flex h-10 w-10 items-center justify-center rounded-lg border border-sky-200 bg-sky-50 text-sky-700 shadow-sm transition hover:border-sky-300 hover:bg-sky-100 hover:text-sky-800">
                            <x-warehouse.icon name="eye" size-class="h-5 w-5" />
                        </a>
                    </x-warehouse.table.td>@endif
                </tr>
            @empty
                <tr>
                    <x-warehouse.table.td colspan="{{ ($isSalesman ?? false) ? 7 : 9 }}" :nowrap="false" class="py-12 text-center">
                        <x-warehouse.empty-state
                            title="No stock found"
                            description="Try a different product name or stock status."
                        />
                    </x-warehouse.table.td>
                </tr>
            @endforelse

            @php
                $stockRows = method_exists($stock, 'getCollection') ? $stock->getCollection() : collect($stock);
                $receivedTotal = $stockRows->sum(fn ($product) => (float) ($product->warehouse_purchase_qty ?? 0) + (float) ($product->warehouse_transfer_qty ?? 0));
                $officeAvailableTotal = $stockRows->sum(fn ($product) => (float) ($product->warehouse_available_qty ?? 0));
                $stockTotal = $stockRows->sum(fn ($product) => (float) ($product->salesman_stock_qty ?? 0));
                $soldTotal = $stockRows->sum(fn ($product) => (float) ($product->salesman_sale_qty ?? ((float) ($product->warehouse_sale_qty ?? 0) - (float) ($product->warehouse_direct_sale_qty ?? 0))));
                $sellingTotal = ($isSalesman ?? false)
                    ? $stockRows->sum(fn ($product) => (float) ($product->selling_price ?? 0) * (float) ($product->salesman_stock_qty ?? 0))
                    : $stockRows->sum(fn ($product) => (float) ($product->stock_sale_price ?? 0));
            @endphp
            <tfoot class="border-t-2 border-slate-300 bg-slate-100 font-extrabold text-slate-900">
                <tr>
                    <td colspan="2" class="px-4 py-3 text-right sm:px-5">Total</td>
                    @if($isSalesman ?? false)
                        <td class="px-4 py-3 sm:px-5">—</td>
                        <td class="px-4 py-3 sm:px-5">{{ number_format($receivedTotal, 2) }}</td>
                        <td class="px-4 py-3 sm:px-5">{{ number_format($soldTotal, 2) }}</td>
                        <td class="px-4 py-3 sm:px-5">{{ number_format($stockTotal, 2) }}</td>
                        <td class="px-4 py-3 sm:px-5">{{ number_format($sellingTotal, 2) }}</td>
                    @else
                        <td class="px-4 py-3 sm:px-5">—</td>
                        <td class="px-4 py-3 sm:px-5">{{ number_format($receivedTotal, 2) }}</td>
                        <td class="px-4 py-3 sm:px-5">{{ number_format($officeAvailableTotal, 2) }}</td>
                        <td class="px-4 py-3 sm:px-5">{{ number_format($soldTotal, 2) }}</td>
                        <td class="px-4 py-3 sm:px-5">{{ number_format($stockTotal, 2) }}</td>
                        <td class="px-4 py-3 sm:px-5">{{ number_format($sellingTotal, 2) }}</td>
                        <td class="px-4 py-3 sm:px-5"></td>
                    @endif
                </tr>
            </tfoot>

        </x-warehouse.table>

        <div class="flex flex-col gap-3 border-t border-slate-200 px-4 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-5">
            <p class="m-0 text-sm font-medium text-slate-500">
                Showing {{ $stock->firstItem() ?? 0 }} to {{ $stock->lastItem() ?? 0 }} of {{ $stock->total() }} products
            </p>
            <div>
                {{ $stock->onEachSide(1)->links('warehouse-portal.partials.pagination') }}
            </div>
        </div>
    </div>

    <style>
        .stock-summary-table thead th { position: sticky; top: 0; z-index: 30; background: #047857; color: #fff !important; }
        .lsp-sale-pulse { animation: lsp-sale-portal-pulse 1.8s ease-in-out infinite; }
        .lsp-stock-pulse { animation: lsp-stock-portal-pulse 1.8s ease-in-out infinite; }
        @keyframes lsp-sale-portal-pulse {
            0%, 100% { border-color: #fecaca; box-shadow: 0 0 0 0 rgba(220, 38, 38, 0); }
            50% { border-color: #dc2626; box-shadow: 0 0 0 3px rgba(220, 38, 38, .13); }
        }
        @keyframes lsp-stock-portal-pulse {
            0%, 100% { border-color: #a7f3d0; box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
            50% { border-color: #059669; box-shadow: 0 0 0 3px rgba(16, 185, 129, .14); }
        }
        @media (prefers-reduced-motion: reduce) { .lsp-sale-pulse, .lsp-stock-pulse { animation: none; } }
    </style>
</x-warehouse-layout>

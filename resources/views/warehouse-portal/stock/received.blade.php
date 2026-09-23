<x-warehouse-layout title="Received List">
    @php
        $isPaginated = $transfers instanceof \Illuminate\Pagination\AbstractPaginator;
        $rows = $isPaginated ? $transfers->getCollection() : $transfers;
        $firstItem = $isPaginated ? $transfers->firstItem() : 1;
    @endphp

    <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
        <x-warehouse.page-header title="Received List" description="Review stock transferred from Admin to your Area Office." />

        <form method="GET" class="border-t border-slate-200 bg-slate-50 px-4 py-4 sm:px-6">
            <div class="grid gap-4 lg:grid-cols-[minmax(220px,1fr)_160px_160px_140px_auto] lg:items-end">
                <div><x-warehouse.input-label for="search" value="Search" class="mb-2 text-slate-700" /><input type="text" id="search" name="search" value="{{ $search }}" placeholder="Invoice or product name" class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 outline-none transition focus:border-red-500 focus:ring-2 focus:ring-red-500/20"></div>
                <div><x-warehouse.input-label for="from_date" value="From" class="mb-2 text-slate-700" /><input type="date" id="from_date" name="from_date" value="{{ $fromDate }}" class="block h-11 w-full rounded-xl border border-slate-300 bg-white px-3 text-sm text-slate-800 outline-none focus:border-red-500 focus:ring-2 focus:ring-red-500/20"></div>
                <div><x-warehouse.input-label for="to_date" value="To" class="mb-2 text-slate-700" /><input type="date" id="to_date" name="to_date" value="{{ $toDate }}" class="block h-11 w-full rounded-xl border border-slate-300 bg-white px-3 text-sm text-slate-800 outline-none focus:border-red-500 focus:ring-2 focus:ring-red-500/20"></div>
                <div><x-warehouse.input-label for="per_page" value="Per page" class="mb-2 text-slate-700" /><select name="per_page" id="per_page" class="block h-11 w-full rounded-xl border border-slate-300 bg-white px-3 text-sm text-slate-800 outline-none focus:border-red-500 focus:ring-2 focus:ring-red-500/20"><option value="all" @selected($perPage === 'all')>All</option><option value="10" @selected($perPage === '10')>10</option><option value="25" @selected($perPage === '25')>25</option><option value="50" @selected($perPage === '50')>50</option><option value="100" @selected($perPage === '100')>100</option></select></div>
                <div class="flex flex-wrap gap-3"><x-loading-submit type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-xl border border-transparent bg-red-600 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-red-500 focus:outline-none focus:ring-2 focus:ring-red-500/30 lg:w-auto" icon="funnel" loading-text="Filtering...">Filter</x-loading-submit><x-warehouse.button href="{{ route('warehouse.stock.received') }}" variant="secondary" class="w-full px-5 py-3 lg:w-auto" icon="rotate-ccw">Reset</x-warehouse.button></div>
            </div>
        </form>

        <div class="grid gap-3 border-b border-slate-200 bg-white px-4 py-3 sm:grid-cols-2 sm:px-6"><div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3"><p class="m-0 text-xs font-bold uppercase tracking-wider text-slate-500">Received Transfers</p><p class="mb-0 mt-1 text-2xl font-extrabold text-slate-900">{{ number_format($isPaginated ? $transfers->total() : $rows->count()) }}</p></div><div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3"><p class="m-0 text-xs font-bold uppercase tracking-wider text-emerald-700">Total Received Quantity</p><p class="mb-0 mt-1 text-2xl font-extrabold text-emerald-900">{{ number_format((float) $filteredTotalQuantity, 2) }}</p></div></div>

        <div class="p-4 sm:p-6"><x-warehouse.table min-width="min-w-[1050px]"><x-slot name="header"><tr><x-warehouse.table.th padding-class="px-4 py-2 sm:px-5">SL</x-warehouse.table.th><x-warehouse.table.th padding-class="px-4 py-2 sm:px-5">Invoice No.</x-warehouse.table.th><x-warehouse.table.th padding-class="px-4 py-2 sm:px-5">Product Name</x-warehouse.table.th><x-warehouse.table.th padding-class="px-4 py-2 sm:px-5">Date</x-warehouse.table.th><x-warehouse.table.th padding-class="px-4 py-2 sm:px-5">Total Quantity</x-warehouse.table.th><x-warehouse.table.th padding-class="px-4 py-2 sm:px-5">Total Selling Price</x-warehouse.table.th><x-warehouse.table.th padding-class="px-4 py-2 sm:px-5">Action</x-warehouse.table.th></tr></x-slot>
            @forelse($rows as $transfer)
                <tr class="hover:bg-slate-50">
                    <x-warehouse.table.td padding-class="px-4 py-1.5 sm:px-5" class="font-medium text-slate-900">{{ $firstItem + $loop->index }}</x-warehouse.table.td>
                    <x-warehouse.table.td padding-class="px-4 py-1.5 sm:px-5" class="font-semibold text-slate-900">{{ $transfer->invoice_no }}</x-warehouse.table.td>
                    <x-warehouse.table.td padding-class="px-4 py-1.5 sm:px-5" :nowrap="false">
                        <div class="flex max-w-[420px] flex-wrap gap-1.5">
                            @forelse($transfer->items->pluck('product.product_name')->filter() as $productName)
                                <span class="inline-flex max-w-full items-center rounded-md border border-emerald-200 bg-emerald-50 px-2 py-1 text-xs font-semibold leading-4 text-emerald-900 break-words">{{ $productName }}</span>
                            @empty
                                <span class="text-slate-400">-</span>
                            @endforelse
                        </div>
                    </x-warehouse.table.td>
                    <x-warehouse.table.td padding-class="px-4 py-1.5 sm:px-5">{{ $transfer->transfer_date ? date('Y-m-d', strtotime($transfer->transfer_date)) : '-' }}</x-warehouse.table.td>
                    <x-warehouse.table.td padding-class="px-4 py-1.5 sm:px-5" class="font-semibold text-slate-900">{{ number_format($transfer->total_quantity, 2) }}</x-warehouse.table.td>
                    <x-warehouse.table.td padding-class="px-4 py-1.5 sm:px-5" class="font-semibold text-emerald-700">{{ number_format((float) $transfer->items->sum(fn ($item) => (float) $item->quantity * (float) ($item->product?->selling_price ?? 0)), 2) }} BDT</x-warehouse.table.td>
                    <x-warehouse.table.td padding-class="px-4 py-1.5 sm:px-5"><x-warehouse.button href="{{ route('warehouse.stock.received.show', $transfer) }}" variant="secondary" class="px-3 py-2 text-xs">View</x-warehouse.button></x-warehouse.table.td>
                </tr>
            @empty
                <tr><x-warehouse.table.td colspan="6" :nowrap="false" class="py-12 text-center"><x-warehouse.empty-state title="No received stock found" description="Try a different product name or date range." /></x-warehouse.table.td></tr>
            @endforelse
        </x-warehouse.table>@if($isPaginated)<div class="mt-3">{{ $transfers->onEachSide(1)->links('warehouse-portal.partials.pagination') }}</div>@endif</div>
    </div>
</x-warehouse-layout>

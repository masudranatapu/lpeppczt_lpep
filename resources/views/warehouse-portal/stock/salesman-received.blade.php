<x-warehouse-layout title="Received List">
    @php
        $isPaginated = $assignments instanceof \Illuminate\Pagination\AbstractPaginator;
        $rows = $isPaginated ? $assignments->getCollection() : $assignments;
        $firstItem = $isPaginated ? $assignments->firstItem() : 1;
    @endphp
    <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
        <x-warehouse.page-header title="Received List" description="Review products assigned to you by your Area Office." />
        <form method="GET" class="border-t border-slate-200 bg-slate-50 px-4 py-4 sm:px-6">
            <div class="grid gap-4 lg:grid-cols-[minmax(220px,1fr)_160px_160px_140px_auto] lg:items-end">
                <div><x-warehouse.input-label for="search" value="Search" class="mb-2" /><input id="search" name="search" value="{{ $search }}" placeholder="Invoice or product name" class="block h-11 w-full rounded-xl border border-slate-300 bg-white px-4 text-sm"></div>
                <div><x-warehouse.input-label for="from_date" value="From" class="mb-2" /><input id="from_date" type="date" name="from_date" value="{{ $fromDate }}" class="block h-11 w-full rounded-xl border border-slate-300 bg-white px-3 text-sm"></div>
                <div><x-warehouse.input-label for="to_date" value="To" class="mb-2" /><input id="to_date" type="date" name="to_date" value="{{ $toDate }}" class="block h-11 w-full rounded-xl border border-slate-300 bg-white px-3 text-sm"></div>
                <div><x-warehouse.input-label for="per_page" value="Per page" class="mb-2" /><select id="per_page" name="per_page" class="block h-11 w-full rounded-xl border border-slate-300 bg-white px-3 text-sm"><option value="all" {{ $perPage === 'all' ? 'selected' : '' }}>All</option>@foreach(['10','25','50','100'] as $size)<option value="{{ $size }}" {{ $perPage === $size ? 'selected' : '' }}>{{ $size }}</option>@endforeach</select></div>
                <div class="flex gap-3"><x-loading-submit type="submit" class="rounded-xl bg-emerald-600 px-5 py-3 text-sm font-semibold text-white" loading-text="Filtering...">Filter</x-loading-submit><x-warehouse.button href="{{ route('warehouse.stock.lsp-received') }}" variant="secondary" class="px-5 py-3">Reset</x-warehouse.button></div>
            </div>
        </form>
        <div class="grid gap-3 border-b border-slate-200 px-4 py-3 sm:grid-cols-2 sm:px-6"><div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3"><p class="m-0 text-xs font-bold uppercase tracking-wider text-slate-500">Received Invoices</p><p class="mb-0 mt-1 text-2xl font-extrabold text-slate-900">{{ number_format($isPaginated ? $assignments->total() : $rows->count()) }}</p></div><div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3"><p class="m-0 text-xs font-bold uppercase tracking-wider text-emerald-700">Total Received Quantity</p><p class="mb-0 mt-1 text-2xl font-extrabold text-emerald-900">{{ number_format($filteredTotalQuantity, 2) }}</p></div></div>
        <div class="p-4 sm:p-6">
            <x-warehouse.table min-width="min-w-[900px]">
                <x-slot name="header"><tr><x-warehouse.table.th>SL</x-warehouse.table.th><x-warehouse.table.th>Invoice No.</x-warehouse.table.th><x-warehouse.table.th>Product Name</x-warehouse.table.th><x-warehouse.table.th>Date</x-warehouse.table.th><x-warehouse.table.th>Total Quantity</x-warehouse.table.th><x-warehouse.table.th>Total Selling Price</x-warehouse.table.th><x-warehouse.table.th>Action</x-warehouse.table.th></tr></x-slot>
                @forelse($rows as $assignment)
                    <tr class="hover:bg-slate-50">
                        <x-warehouse.table.td class="font-medium">{{ $firstItem + $loop->index }}</x-warehouse.table.td>
                        <x-warehouse.table.td class="font-semibold">{{ $assignment->invoice_no }}</x-warehouse.table.td>
                        <x-warehouse.table.td :nowrap="false">
                            <div class="flex max-w-[420px] flex-wrap gap-1.5">
                                @forelse($assignment->items->pluck('product.product_name')->filter() as $productName)
                                    <span class="inline-flex max-w-full items-center rounded-md border border-emerald-200 bg-emerald-50 px-2 py-1 text-xs font-semibold leading-4 text-emerald-900 break-words">{{ $productName }}</span>
                                @empty
                                    <span class="text-slate-400">-</span>
                                @endforelse
                            </div>
                        </x-warehouse.table.td>
                        <x-warehouse.table.td>{{ $assignment->created_at ? date('Y-m-d H:i', strtotime($assignment->created_at)) : ($assignment->assignment_date ?: '-') }}</x-warehouse.table.td>
                        <x-warehouse.table.td class="font-semibold">{{ number_format((float) $assignment->total_quantity, 2) }}</x-warehouse.table.td>
                        <x-warehouse.table.td class="font-semibold text-emerald-700">{{ number_format((float) $assignment->items->sum(fn ($item) => (float) $item->quantity * (float) ($item->product?->selling_price ?? 0)), 2) }} BDT</x-warehouse.table.td>
                        <x-warehouse.table.td><x-warehouse.button href="{{ route('warehouse.stock.lsp-received.show', $assignment) }}" variant="secondary" class="px-3 py-2 text-xs">View</x-warehouse.button></x-warehouse.table.td>
                    </tr>
                @empty
                    <tr><x-warehouse.table.td colspan="6" :nowrap="false" class="py-12 text-center"><x-warehouse.empty-state title="No received stock found" description="No stock was assigned to you for the selected period." /></x-warehouse.table.td></tr>
                @endforelse
            </x-warehouse.table>
            @if($isPaginated)<div class="mt-3">{{ $assignments->onEachSide(1)->links('warehouse-portal.partials.pagination') }}</div>@endif
        </div>
    </div>
</x-warehouse-layout>

<x-warehouse-layout title="LSP Stock Assignments">
    @php
        $salesmanFilterOptions = $salesmen->mapWithKeys(function ($salesman) {
            return [(string) $salesman->id => [
                'value' => (string) $salesman->id,
                'label' => $salesman->name,
                'description' => $salesman->email ?: 'Area Office salesman',
            ]];
        });
        $isPaginated = $assignments instanceof \Illuminate\Pagination\AbstractPaginator;
        $rows = $isPaginated ? $assignments->getCollection() : $assignments;
        $firstItem = $isPaginated ? $assignments->firstItem() : 1;
    @endphp

    <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
        <x-warehouse.page-header
            title="LSP Stock Assignments"
            description="Search assignments, filter by date range, and review stock allocated to your sales team."
        >
            <x-slot name="actions">
                <x-warehouse.button href="{{ route('warehouse.salesman-assignments.create') }}" variant="primary" icon="plus" class="rounded-xl px-5 py-3">
                    New Assignment
                </x-warehouse.button>
            </x-slot>
        </x-warehouse.page-header>

        <form method="GET" class="border-t border-slate-200 bg-slate-50 px-4 py-4 sm:px-6">
            <div class="grid gap-4 lg:grid-cols-[minmax(220px,1fr)_160px_160px_220px_140px_auto] lg:items-end">
                <div>
                    <x-warehouse.input-label for="search" value="Search" class="mb-2 text-slate-700" />
                    <input type="text" id="search" name="search" value="{{ $search ?? '' }}" placeholder="Invoice, note, product" class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 outline-none transition focus:border-red-500 focus:ring-2 focus:ring-red-500/20">
                </div>
                <div>
                    <x-warehouse.input-label for="from_date" value="From" class="mb-2 text-slate-700" />
                    <input type="date" id="from_date" name="from_date" value="{{ $fromDate ?? '' }}" class="block h-11 w-full rounded-xl border border-slate-300 bg-white px-3 text-sm text-slate-800 outline-none focus:border-red-500 focus:ring-2 focus:ring-red-500/20">
                </div>
                <div>
                    <x-warehouse.input-label for="to_date" value="To" class="mb-2 text-slate-700" />
                    <input type="date" id="to_date" name="to_date" value="{{ $toDate ?? '' }}" class="block h-11 w-full rounded-xl border border-slate-300 bg-white px-3 text-sm text-slate-800 outline-none focus:border-red-500 focus:ring-2 focus:ring-red-500/20">
                </div>
                <div>
                    <x-warehouse.searchable-select
                        name="salesman_id"
                        id="assignment-salesman-filter"
                        label="LSP"
                        :options="$salesmanFilterOptions"
                        :selected="$salesmanId"
                        placeholder="All LSPs"
                        search-placeholder="Search salesman name or email"
                    />
                </div>
                <div>
                    <x-warehouse.input-label for="per_page" value="Per page" class="mb-2 text-slate-700" />
                    <select name="per_page" id="per_page" class="block h-11 w-full rounded-xl border border-slate-300 bg-white px-3 text-sm text-slate-800 outline-none focus:border-red-500 focus:ring-2 focus:ring-red-500/20">
                        <option value="all" @selected((string) ($perPage ?? 'all') === 'all')>All</option>
                        <option value="10" @selected((string) ($perPage ?? 'all') === '10')>10</option>
                        <option value="25" @selected((string) ($perPage ?? 'all') === '25')>25</option>
                        <option value="50" @selected((string) ($perPage ?? 'all') === '50')>50</option>
                        <option value="100" @selected((string) ($perPage ?? 'all') === '100')>100</option>
                    </select>
                </div>
                <div class="flex flex-wrap gap-3">
                    <x-warehouse.button type="submit" variant="primary" class="w-full px-5 py-3 lg:w-auto" icon="funnel">Filter</x-warehouse.button>
                    <x-warehouse.button href="{{ route('warehouse.salesman-assignments.index') }}" variant="secondary" class="w-full px-5 py-3 lg:w-auto" icon="rotate-ccw">Reset</x-warehouse.button>
                </div>
            </div>
        </form>

        <div class="grid gap-3 border-b border-slate-200 bg-white px-4 py-3 sm:grid-cols-2 sm:px-6">
            <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
                <p class="m-0 text-xs font-bold uppercase tracking-wider text-slate-500">Filtered Assignments</p>
                <p class="mb-0 mt-1 text-2xl font-extrabold text-slate-900">{{ number_format($isPaginated ? $assignments->total() : $rows->count()) }}</p>
            </div>
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3">
                <p class="m-0 text-xs font-bold uppercase tracking-wider text-emerald-700">Total Assigned Quantity</p>
                <p class="mb-0 mt-1 text-2xl font-extrabold text-emerald-900">{{ number_format((float) ($filteredTotalQuantity ?? 0), 2) }}</p>
            </div>
        </div>

        <div class="p-4 sm:p-6">
            <x-warehouse.table min-width="min-w-[980px]">
                <x-slot name="header">
                    <tr>
                        <x-warehouse.table.th padding-class="px-4 py-2 sm:px-5">SL</x-warehouse.table.th>
                        <x-warehouse.table.th padding-class="px-4 py-2 sm:px-5">Invoice</x-warehouse.table.th>
                        <x-warehouse.table.th padding-class="px-4 py-2 sm:px-5">Date</x-warehouse.table.th>
                        <x-warehouse.table.th padding-class="px-4 py-2 sm:px-5">LSP</x-warehouse.table.th>
                        <x-warehouse.table.th padding-class="px-4 py-2 sm:px-5">Products</x-warehouse.table.th>
                        <x-warehouse.table.th padding-class="px-4 py-2 sm:px-5">Quantity</x-warehouse.table.th>
                        <x-warehouse.table.th padding-class="px-4 py-2 sm:px-5">Actions</x-warehouse.table.th>
                    </tr>
                </x-slot>

                @forelse($rows as $assignment)
                    <tr class="hover:bg-slate-50">
                        <x-warehouse.table.td padding-class="px-4 py-1.5 sm:px-5" class="font-medium text-slate-900">{{ $firstItem + $loop->index }}</x-warehouse.table.td>
                        <x-warehouse.table.td padding-class="px-4 py-1.5 sm:px-5" class="font-semibold text-slate-900">{{ $assignment->invoice_no }}</x-warehouse.table.td>
                        <x-warehouse.table.td padding-class="px-4 py-1.5 sm:px-5">{{ \Carbon\Carbon::parse($assignment->assignment_date)->format('d-m-Y') }}</x-warehouse.table.td>
                        <x-warehouse.table.td padding-class="px-4 py-1.5 sm:px-5">
                            <span class="font-semibold text-slate-900">{{ $assignment->salesman?->name ?: '-' }}</span>
                            <div class="text-xs text-slate-400">{{ $assignment->salesman?->email }}</div>
                        </x-warehouse.table.td>
                        <x-warehouse.table.td padding-class="px-4 py-1.5 sm:px-5" class="font-semibold text-slate-900">{{ $assignment->items->count() }} {{ \Illuminate\Support\Str::plural('Product', $assignment->items->count()) }}</x-warehouse.table.td>
                        <x-warehouse.table.td padding-class="px-4 py-1.5 sm:px-5" class="font-semibold text-slate-900">{{ number_format((float) $assignment->total_quantity, 2) }}</x-warehouse.table.td>
                        <x-warehouse.table.td padding-class="px-4 py-1.5 sm:px-5" class="align-middle">
                            <div class="flex items-center gap-2 whitespace-nowrap leading-none">
                                <a href="{{ route('warehouse.salesman-assignments.show', $assignment) }}" title="View" aria-label="View" class="inline-flex h-10 w-10 items-center justify-center rounded-lg border border-slate-300 bg-white text-slate-700 shadow-sm transition hover:border-slate-400 hover:bg-slate-50 hover:text-slate-900"><x-warehouse.icon name="eye" size-class="h-5 w-5" /></a>
                                <a href="{{ route('warehouse.salesman-assignments.edit', $assignment) }}" title="Edit" aria-label="Edit" class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-amber-200 bg-amber-50 text-amber-700 shadow-sm transition hover:border-amber-300 hover:bg-amber-100 hover:text-amber-800">
                                    <x-warehouse.icon name="square-pen" size-class="h-5 w-5" />
                                </a>
                                <form class="delete-form" method="POST" action="{{ route('warehouse.salesman-assignments.destroy', $assignment) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" title="Delete" aria-label="Delete" class="inline-flex h-10 w-10 items-center justify-center rounded-lg border border-rose-200 bg-rose-50 text-rose-700 shadow-sm transition hover:border-rose-300 hover:bg-rose-100 hover:text-rose-800 delete-button">
                                        <x-warehouse.icon name="trash-2" size-class="h-5 w-5" />
                                    </button>
                                </form>
                            </div>
                        </x-warehouse.table.td>
                    </tr>
                @empty
                    <tr>
                        <x-warehouse.table.td colspan="7" :nowrap="false" class="py-12 text-center">
                            <x-warehouse.empty-state title="No assignments found" description="Try different filters or create the first stock assignment." />
                        </x-warehouse.table.td>
                    </tr>
                @endforelse
            </x-warehouse.table>

            @if($isPaginated)
                <div class="mt-3">
                    {{ $assignments->onEachSide(1)->links('warehouse-portal.partials.pagination') }}
                </div>
            @endif
        </div>
    </div>
</x-warehouse-layout>

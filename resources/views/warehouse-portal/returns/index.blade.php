<x-warehouse-layout title="Return Products">
    @php
        $salesmanFilterOptions = $salesmen->mapWithKeys(function ($salesman) {
            return [(string) $salesman->id => [
                'value' => (string) $salesman->id,
                'label' => $salesman->name,
                'description' => $salesman->email ?: 'Area Office salesman',
            ]];
        });
    @endphp

    <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
        <x-warehouse.page-header
            title="Return Products"
            description="Review products returned from salesmen back into warehouse stock."
        >
            <x-slot name="actions">
                <x-warehouse.button href="{{ route('warehouse.salesman-returns.create') }}" variant="primary" icon="plus" class="rounded-xl px-5 py-3">
                    New Return
                </x-warehouse.button>
            </x-slot>
        </x-warehouse.page-header>

        <x-warehouse.filter-panel
            action="{{ route('warehouse.salesman-returns.index') }}"
            grid-class="grid gap-4 lg:grid-cols-[minmax(220px,1fr)_190px_150px_130px_110px_auto] lg:items-end"
            actions-class="flex flex-wrap gap-3"
        >
            <div>
                <x-warehouse.input-label for="search" value="Search" class="mb-2 text-slate-700" />
                <input type="text" id="search" name="search" value="{{ $search }}" placeholder="Invoice, salesman, product, or date" class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 outline-none transition focus:border-red-500 focus:ring-2 focus:ring-red-500/20">
            </div>
            <div>
                <x-warehouse.searchable-select
                    name="salesman_id"
                    id="return-salesman-filter"
                    label="LSP"
                    :options="$salesmanFilterOptions"
                    :selected="$salesmanId"
                    placeholder="All LSPs"
                    search-placeholder="Search salesman name or email"
                />
            </div>
            <div>
                <x-warehouse.input-label for="month" value="Month" class="mb-2 text-slate-700" />
                <select name="month" id="month" class="block h-11 w-full rounded-xl border border-slate-300 bg-white px-3 text-sm text-slate-800 outline-none focus:border-red-500 focus:ring-2 focus:ring-red-500/20"><option value="">All Months</option>@foreach(range(1,12) as $monthNumber)<option value="{{ $monthNumber }}" {{ $month === $monthNumber ? 'selected' : '' }}>{{ \Carbon\Carbon::create(null, $monthNumber, 1)->format('F') }}</option>@endforeach</select>
            </div>
            <div>
                <x-warehouse.input-label for="year" value="Year" class="mb-2 text-slate-700" />
                <select name="year" id="year" class="block h-11 w-full rounded-xl border border-slate-300 bg-white px-3 text-sm text-slate-800 outline-none focus:border-red-500 focus:ring-2 focus:ring-red-500/20"><option value="">All Years</option>@foreach($yearOptions as $yearOption)<option value="{{ $yearOption }}" {{ $year === $yearOption ? 'selected' : '' }}>{{ $yearOption }}</option>@endforeach</select>
            </div>
            <div>
                <x-warehouse.input-label for="per_page" value="Per page" class="mb-2 text-slate-700" />
                <select name="per_page" id="per_page" class="block h-11 w-full rounded-xl border border-slate-300 bg-white px-3 text-sm text-slate-800 outline-none focus:border-red-500 focus:ring-2 focus:ring-red-500/20">@foreach($perPageOptions as $option)<option value="{{ $option }}" {{ $perPage === $option ? 'selected' : '' }}>{{ $option }}</option>@endforeach</select>
            </div>
            <x-slot name="actions">
                <x-loading-submit type="submit" class="inline-flex w-full px-5 py-3 lg:w-auto items-center justify-center gap-2 rounded-xl border border-transparent bg-red-600 text-sm font-semibold text-white shadow-sm hover:bg-red-500" icon="funnel" loading-text="Filtering...">Filter</x-loading-submit>
                @if($search !== '' || $salesmanId || $month !== now()->month || $year !== now()->year || $perPage !== 10)
                    <x-warehouse.button href="{{ route('warehouse.salesman-returns.index') }}" variant="secondary" class="w-full px-5 py-3 lg:w-auto" icon="rotate-ccw">Reset</x-warehouse.button>
                @endif
            </x-slot>
        </x-warehouse.filter-panel>

        <div class="grid gap-3 border-b border-slate-200 bg-white px-4 py-3 sm:grid-cols-2 sm:px-6">
            <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3"><p class="m-0 text-xs font-bold uppercase tracking-wider text-slate-500">Filtered Returns</p><p class="mb-0 mt-1 text-2xl font-extrabold text-slate-900">{{ number_format($returns->total()) }}</p></div>
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3"><p class="m-0 text-xs font-bold uppercase tracking-wider text-emerald-700">Returned Quantity</p><p class="mb-0 mt-1 text-2xl font-extrabold text-emerald-900">{{ number_format($filteredTotalQuantity, 2) }}</p></div>
        </div>

        <x-warehouse.table min-width="min-w-[900px]">
            <x-slot name="header"><tr><x-warehouse.table.th padding-class="px-4 py-2 sm:px-5">SL</x-warehouse.table.th><x-warehouse.table.th padding-class="px-4 py-2 sm:px-5">Invoice</x-warehouse.table.th><x-warehouse.table.th padding-class="px-4 py-2 sm:px-5">Return Date Time</x-warehouse.table.th><x-warehouse.table.th padding-class="px-4 py-2 sm:px-5">LSP</x-warehouse.table.th><x-warehouse.table.th padding-class="px-4 py-2 sm:px-5">Products</x-warehouse.table.th><x-warehouse.table.th padding-class="px-4 py-2 sm:px-5">Quantity</x-warehouse.table.th><x-warehouse.table.th padding-class="px-4 py-2 sm:px-5">Actions</x-warehouse.table.th></tr></x-slot>
            @forelse($returns as $return)
                <tr class="hover:bg-slate-50">
                    <x-warehouse.table.td padding-class="px-4 py-1.5 sm:px-5" class="font-medium text-slate-900">{{ $returns->firstItem() + $loop->index }}</x-warehouse.table.td>
                    <x-warehouse.table.td padding-class="px-4 py-1.5 sm:px-5" class="font-semibold text-slate-900">{{ $return->invoice_no }}</x-warehouse.table.td>
                    <x-warehouse.table.td padding-class="px-4 py-1.5 sm:px-5">{{ \Carbon\Carbon::parse($return->return_date_time)->format('d-m-Y h:i A') }}</x-warehouse.table.td>
                    <x-warehouse.table.td padding-class="px-4 py-1.5 sm:px-5"><span class="font-semibold text-slate-900">{{ $return->salesman?->name ?: '-' }}</span><div class="text-xs text-slate-400">{{ $return->salesman?->email }}</div></x-warehouse.table.td>
                    <x-warehouse.table.td padding-class="px-4 py-1.5 sm:px-5" class="font-semibold text-slate-900">{{ $return->items->count() }} {{ \Illuminate\Support\Str::plural('Product', $return->items->count()) }}</x-warehouse.table.td>
                    <x-warehouse.table.td padding-class="px-4 py-1.5 sm:px-5" class="font-semibold text-slate-900">{{ number_format($return->total_quantity, 2) }}</x-warehouse.table.td>
                    <x-warehouse.table.td padding-class="px-4 py-1.5 sm:px-5" class="align-middle">
                        <a href="{{ route('warehouse.salesman-returns.show', $return) }}" title="View" aria-label="View" class="inline-flex h-10 w-10 items-center justify-center rounded-lg border border-slate-300 bg-white text-slate-700 shadow-sm transition hover:border-slate-400 hover:bg-slate-50 hover:text-slate-900"><x-warehouse.icon name="eye" size-class="h-5 w-5" /></a>
                    </x-warehouse.table.td>
                </tr>
            @empty
                <tr><x-warehouse.table.td colspan="7" :nowrap="false" class="py-12 text-center"><x-warehouse.empty-state title="No returns found" description="Create a return when stock comes back from a salesman." /></x-warehouse.table.td></tr>
            @endforelse
        </x-warehouse.table>

        <div class="flex flex-col gap-3 border-t border-slate-200 px-4 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
            <p class="m-0 text-sm text-slate-600">Showing {{ $returns->firstItem() ?? 0 }} to {{ $returns->lastItem() ?? 0 }} of {{ $returns->total() }} records</p>
            <div>{{ $returns->onEachSide(1)->links('warehouse-portal.partials.pagination') }}</div>
        </div>
    </div>
</x-warehouse-layout>

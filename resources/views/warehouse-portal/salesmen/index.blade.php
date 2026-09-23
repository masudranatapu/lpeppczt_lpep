<x-warehouse-layout title="LSPs">
    @php
        $query = request()->only(['period', 'month', 'year']);
    @endphp

    <div class="space-y-5">
        <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
            <x-warehouse.page-header
                title="LSPs"
                description="LSPs assigned to {{ $warehouse->name }} with sales totals for the selected period."
            />

            <form method="GET" action="{{ route('warehouse.salesmen.index') }}" class="border-t border-b border-slate-200 bg-slate-50 px-4 py-4 sm:px-6">
                <div class="grid gap-4 lg:grid-cols-[minmax(220px,1fr)_minmax(180px,0.7fr)_minmax(140px,0.5fr)_auto] lg:items-end">
                    <div>
                        <x-warehouse.input-label for="period" value="Report Period" class="mb-2 text-slate-700" />
                        <select name="period" id="period" class="block h-11 w-full rounded-xl border border-slate-300 bg-white px-3 text-sm text-slate-800 outline-none focus:border-red-500 focus:ring-2 focus:ring-red-500/20">
                            <option value="today" {{ $filters['period'] === 'today' ? 'selected' : '' }}>Today</option>
                            <option value="this_week" {{ $filters['period'] === 'this_week' ? 'selected' : '' }}>This Week</option>
                            <option value="month" {{ $filters['period'] === 'month' ? 'selected' : '' }}>Select Month &amp; Year</option>
                        </select>
                    </div>
                    <div class="month-filter-field">
                        <x-warehouse.input-label for="month" value="Month" class="mb-2 text-slate-700" />
                        <select name="month" id="month" class="block h-11 w-full rounded-xl border border-slate-300 bg-white px-3 text-sm text-slate-800 outline-none focus:border-red-500 focus:ring-2 focus:ring-red-500/20">
                            @foreach(range(1, 12) as $monthNumber)
                                <option value="{{ $monthNumber }}" {{ $filters['month'] === $monthNumber ? 'selected' : '' }}>{{ \Carbon\Carbon::create(null, $monthNumber, 1)->format('F') }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="month-filter-field">
                        <x-warehouse.input-label for="year" value="Year" class="mb-2 text-slate-700" />
                        <select name="year" id="year" class="block h-11 w-full rounded-xl border border-slate-300 bg-white px-3 text-sm text-slate-800 outline-none focus:border-red-500 focus:ring-2 focus:ring-red-500/20">
                            @foreach($yearOptions as $year)
                                <option value="{{ $year }}" {{ $filters['year'] === $year ? 'selected' : '' }}>{{ $year }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex flex-col gap-2 sm:flex-row">
                        <x-loading-submit type="submit" class="inline-flex w-full lg:w-auto items-center justify-center gap-2 rounded-xl border border-transparent bg-red-600 px-4 py-3 text-white shadow-sm hover:bg-red-500" icon="funnel" loading-text="Applying...">Apply</x-loading-submit>
                        @if(request()->hasAny(['period', 'month', 'year']))
                            <x-warehouse.button href="{{ route('warehouse.salesmen.index') }}" variant="secondary" icon="rotate-ccw" class="w-full lg:w-auto">Reset</x-warehouse.button>
                        @endif
                    </div>
                </div>
            </form>

            <div class="border-b border-slate-200 px-4 py-3 sm:px-6">
                <p class="m-0 text-sm font-bold text-slate-800">{{ $filters['label'] }}</p>
            </div>

            <x-warehouse.table min-width="min-w-[900px]">
                <x-slot name="header">
                    <tr>
                        <x-warehouse.table.th padding-class="px-4 py-2 sm:px-5">SL</x-warehouse.table.th>
                        <x-warehouse.table.th padding-class="px-4 py-2 sm:px-5">LSP</x-warehouse.table.th>
                        <x-warehouse.table.th padding-class="px-4 py-2 sm:px-5">Status</x-warehouse.table.th>
                        <x-warehouse.table.th padding-class="px-4 py-2 sm:px-5">Sales</x-warehouse.table.th>
                        <x-warehouse.table.th padding-class="px-4 py-2 sm:px-5">Quantity</x-warehouse.table.th>
                        <x-warehouse.table.th padding-class="px-4 py-2 sm:px-5">Current Stock</x-warehouse.table.th>
                        <x-warehouse.table.th padding-class="px-4 py-2 sm:px-5">Sales Amount</x-warehouse.table.th>
                        <x-warehouse.table.th padding-class="px-4 py-2 sm:px-5">Action</x-warehouse.table.th>
                    </tr>
                </x-slot>
                @forelse($salesmen as $salesman)
                    <tr class="hover:bg-slate-50">
                        <x-warehouse.table.td padding-class="px-4 py-1.5 sm:px-5" class="font-medium text-slate-900">{{ $salesmen->firstItem() + $loop->index }}</x-warehouse.table.td>
                        <x-warehouse.table.td padding-class="px-4 py-1.5 sm:px-5" class="font-semibold text-slate-900">
                            {{ $salesman->name }}
                            <div class="text-xs font-normal text-slate-500">{{ $salesman->email ?: '-' }}</div>
                        </x-warehouse.table.td>
                        <x-warehouse.table.td padding-class="px-4 py-1.5 sm:px-5">
                            <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-bold {{ (int) $salesman->status === 1 ? 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200' : 'bg-slate-100 text-slate-600 ring-1 ring-slate-200' }}">{{ (int) $salesman->status === 1 ? 'Active' : 'Inactive' }}</span>
                        </x-warehouse.table.td>
                        <x-warehouse.table.td padding-class="px-4 py-1.5 sm:px-5">{{ number_format($salesman->sale_count) }}</x-warehouse.table.td>
                        <x-warehouse.table.td padding-class="px-4 py-1.5 sm:px-5">{{ number_format($salesman->total_quantity, 2) }}</x-warehouse.table.td>
                        <x-warehouse.table.td padding-class="px-4 py-1.5 sm:px-5" class="font-bold text-emerald-700">{{ number_format($salesman->current_stock_quantity, 2) }}</x-warehouse.table.td>
                        <x-warehouse.table.td padding-class="px-4 py-1.5 sm:px-5" class="font-bold text-slate-900">{{ number_format($salesman->total_sales_amount, 2) }}</x-warehouse.table.td>
                        <x-warehouse.table.td padding-class="px-4 py-1.5 sm:px-5">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('warehouse.salesmen.stock', $salesman) }}" title="Current Stock" aria-label="Current Stock" class="inline-flex h-10 w-10 items-center justify-center rounded-lg border border-emerald-200 bg-emerald-50 text-emerald-700 shadow-sm transition hover:border-emerald-300 hover:bg-emerald-100">
                                    <x-warehouse.icon name="boxes" size-class="h-5 w-5" />
                                </a>
                                <a href="{{ route('warehouse.salesmen.report', array_merge(['warehouse_salesman' => $salesman], $query)) }}" title="Sales Report" aria-label="Sales Report" class="inline-flex h-10 w-10 items-center justify-center rounded-lg border border-sky-200 bg-sky-50 text-sky-700 shadow-sm transition hover:border-sky-300 hover:bg-sky-100">
                                    <x-warehouse.icon name="bar-chart-3" size-class="h-5 w-5" />
                                </a>
                            </div>
                        </x-warehouse.table.td>
                    </tr>
                @empty
                    <tr><x-warehouse.table.td colspan="8" :nowrap="false" class="py-12 text-center"><x-warehouse.empty-state title="No salesmen found" description="No salesmen are assigned to this warehouse." /></x-warehouse.table.td></tr>
                @endforelse
            </x-warehouse.table>

            <div class="flex flex-col gap-3 border-t border-slate-200 px-4 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
                <p class="m-0 text-sm text-slate-600">Showing {{ $salesmen->firstItem() ?? 0 }} to {{ $salesmen->lastItem() ?? 0 }} of {{ $salesmen->total() }} records</p>
                <div>{{ $salesmen->onEachSide(1)->links('warehouse-portal.partials.pagination') }}</div>
            </div>
        </div>
    </div>

    <x-slot name="script">
        <script>
            (function () {
                const period = document.getElementById('period');
                const monthFields = document.querySelectorAll('.month-filter-field');
                function toggleMonthFields() {
                    monthFields.forEach(function (field) {
                        field.classList.toggle('hidden', period.value !== 'month');
                    });
                }
                period.addEventListener('change', toggleMonthFields);
                toggleMonthFields();
            })();
        </script>
    </x-slot>
</x-warehouse-layout>

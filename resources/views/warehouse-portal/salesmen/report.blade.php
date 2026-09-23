<x-warehouse-layout :title="'Sales Report - ' . $salesman->name">
    <div class="space-y-5">
        <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
            <x-warehouse.page-header
                :title="'Sales Report: ' . $salesman->name"
                description="{{ $warehouse->name }} &middot; {{ $filters['label'] }}"
            >
                <x-slot name="actions">
                    <x-warehouse.button href="{{ route('warehouse.salesmen.index', request()->only(['period', 'month', 'year'])) }}" variant="secondary" icon="arrow-left" class="rounded-xl px-4 py-2.5">
                        Back To LSPs
                    </x-warehouse.button>
                </x-slot>
            </x-warehouse.page-header>

            <form method="GET" action="{{ route('warehouse.salesmen.report', $salesman) }}" class="border-t border-b border-slate-200 bg-slate-50 px-4 py-4 sm:px-6">
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
                            <x-warehouse.button href="{{ route('warehouse.salesmen.report', $salesman) }}" variant="secondary" icon="rotate-ccw" class="w-full lg:w-auto">Reset</x-warehouse.button>
                        @endif
                    </div>
                </div>
            </form>
        </div>

        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-2xl border border-sky-200 bg-white p-5 shadow-sm"><p class="m-0 text-xs font-bold uppercase tracking-wider text-sky-700">Total Sales</p><p class="mb-0 mt-3 text-3xl font-extrabold text-slate-900">{{ number_format($summary->sale_count) }}</p></div>
            <div class="rounded-2xl border border-violet-200 bg-white p-5 shadow-sm"><p class="m-0 text-xs font-bold uppercase tracking-wider text-violet-700">Quantity Sold</p><p class="mb-0 mt-3 text-3xl font-extrabold text-slate-900">{{ number_format($summary->total_quantity, 2) }}</p></div>
            <div class="rounded-2xl border border-amber-200 bg-white p-5 shadow-sm"><p class="m-0 text-xs font-bold uppercase tracking-wider text-amber-700">Unique Products</p><p class="mb-0 mt-3 text-3xl font-extrabold text-slate-900">{{ number_format($summary->product_count) }}</p></div>
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-5 shadow-sm"><p class="m-0 text-xs font-bold uppercase tracking-wider text-emerald-700">Collected Sales Amount</p><p class="mb-0 mt-3 text-3xl font-extrabold text-emerald-900">{{ number_format($summary->total_sales_amount, 2) }}</p></div>
        </section>

        <section class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 px-4 py-4 sm:px-6">
                <h2 class="m-0 text-lg font-bold text-slate-900">Product Sales Summary</h2>
            </div>
            <x-warehouse.table min-width="min-w-[760px]">
                <x-slot name="header"><tr><x-warehouse.table.th>SL</x-warehouse.table.th><x-warehouse.table.th>Product</x-warehouse.table.th><x-warehouse.table.th>Sales</x-warehouse.table.th><x-warehouse.table.th>Quantity Sold</x-warehouse.table.th><x-warehouse.table.th>Sales Amount</x-warehouse.table.th></tr></x-slot>
                @forelse($productSales as $productSale)
                    <tr class="hover:bg-slate-50">
                        <x-warehouse.table.td>{{ $loop->iteration }}</x-warehouse.table.td>
                        <x-warehouse.table.td class="font-semibold text-slate-900">{{ $productSale->product?->product_name ?: 'Deleted product' }}</x-warehouse.table.td>
                        <x-warehouse.table.td>{{ number_format($productSale->sale_count) }}</x-warehouse.table.td>
                        <x-warehouse.table.td>{{ number_format($productSale->total_quantity, 2) }} {{ $productSale->product?->unit?->short_name ?: $productSale->product?->unit?->actual_name }}</x-warehouse.table.td>
                        <x-warehouse.table.td class="font-bold text-slate-900">{{ number_format($productSale->total_sales_amount, 2) }}</x-warehouse.table.td>
                    </tr>
                @empty
                    <tr><x-warehouse.table.td colspan="5" :nowrap="false" class="py-10 text-center">No product sales found for {{ $filters['label'] }}.</x-warehouse.table.td></tr>
                @endforelse
            </x-warehouse.table>
        </section>

        <section class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 px-4 py-4 sm:px-6">
                <h2 class="m-0 text-lg font-bold text-slate-900">Sales Transactions</h2>
            </div>
            <x-warehouse.table min-width="min-w-[980px]">
                <x-slot name="header"><tr><x-warehouse.table.th>SL</x-warehouse.table.th><x-warehouse.table.th>Date</x-warehouse.table.th><x-warehouse.table.th>Invoice</x-warehouse.table.th><x-warehouse.table.th>Customer</x-warehouse.table.th><x-warehouse.table.th>Products</x-warehouse.table.th><x-warehouse.table.th>Qty</x-warehouse.table.th><x-warehouse.table.th>Paid / Due</x-warehouse.table.th><x-warehouse.table.th>Total</x-warehouse.table.th><x-warehouse.table.th>Profit</x-warehouse.table.th></tr></x-slot>
                @forelse($sales as $sale)
                    <tr class="hover:bg-slate-50">
                        <x-warehouse.table.td>{{ $sales->firstItem() + $loop->index }}</x-warehouse.table.td>
                        <x-warehouse.table.td>{{ \Carbon\Carbon::parse($sale->sale_date)->format('d-m-Y') }}<div class="text-xs text-slate-400">{{ $sale->created_at?->format('h:i A') }}</div></x-warehouse.table.td>
                        <x-warehouse.table.td class="font-semibold text-slate-900">{{ $sale->invoice_no }}</x-warehouse.table.td>
                        <x-warehouse.table.td>{{ $sale->customer_name ?: '-' }}<div class="text-xs text-slate-400">{{ $sale->customer_phone }}</div></x-warehouse.table.td>
                        <x-warehouse.table.td>
                            @foreach($sale->items as $item)
                                <div class="flex min-w-[240px] justify-between gap-3 border-b border-dashed border-slate-200 py-1 last:border-0"><span>{{ $item->product?->product_name ?: 'Deleted product' }}</span><span class="text-slate-500">{{ number_format($item->quantity, 2) }}</span></div>
                            @endforeach
                        </x-warehouse.table.td>
                        <x-warehouse.table.td>{{ number_format($sale->total_quantity, 2) }}</x-warehouse.table.td>
                        <x-warehouse.table.td><div class="flex flex-col items-start gap-1"><span class="inline-flex whitespace-nowrap rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-bold text-emerald-800 ring-1 ring-inset ring-emerald-200">Paid {{ number_format((float) $sale->paid_amount, 2) }}</span><span class="inline-flex whitespace-nowrap rounded-full px-2.5 py-1 text-xs font-bold ring-1 ring-inset {{ (float) $sale->due_amount > 0 ? 'bg-amber-100 text-amber-800 ring-amber-200' : 'bg-slate-100 text-slate-600 ring-slate-200' }}">Due {{ number_format((float) $sale->due_amount, 2) }}</span></div></x-warehouse.table.td>
                        <x-warehouse.table.td class="font-bold text-slate-900">{{ number_format($sale->total_amount, 2) }}</x-warehouse.table.td><x-warehouse.table.td class="font-bold {{ $sale->profit_amount < 0 ? 'text-rose-600' : 'text-emerald-600' }}">{{ number_format($sale->profit_amount, 2) }}</x-warehouse.table.td>
                    </tr>
                @empty
                    <tr><x-warehouse.table.td colspan="9" :nowrap="false" class="py-10 text-center">No sales found for {{ $filters['label'] }}.</x-warehouse.table.td></tr>
                @endforelse
            </x-warehouse.table>
            <div class="flex flex-col gap-3 border-t border-slate-200 px-4 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
                <p class="m-0 text-sm text-slate-500">Showing {{ $sales->firstItem() ?? 0 }} to {{ $sales->lastItem() ?? 0 }} of {{ $sales->total() }} transactions</p>
                {{ $sales->onEachSide(1)->links('warehouse-portal.partials.pagination') }}
            </div>
        </section>
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


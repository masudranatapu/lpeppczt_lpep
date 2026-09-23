<x-warehouse-layout title="Sales">
    @php
        $perPageOptions = ['all', 10, 25, 50, 100];
        $paymentStatus = in_array((string) request('payment_status', $paymentStatus ?? 'all'), ['paid', 'due'], true)
            ? (string) request('payment_status', $paymentStatus ?? 'all')
            : 'all';
        $salesmanFilterOptions = $salesmanOptions->mapWithKeys(function ($salesman) {
            return [(string) $salesman->id => [
                'value' => (string) $salesman->id,
                'label' => $salesman->name,
                'description' => $salesman->email ?: 'Area Office salesman',
            ]];
        });
        $isPaginated = $sales instanceof \Illuminate\Pagination\AbstractPaginator;
        $rows = $isPaginated ? $sales->getCollection() : $sales;
        $firstItem = $isPaginated ? $sales->firstItem() : 1;
    @endphp

    <div x-data="{ filterOpen: false }" x-on:keydown.escape.window="filterOpen = false" x-effect="document.body.classList.toggle('overflow-hidden', filterOpen)" class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
        <x-warehouse.page-header title="Sales" description="Search and review LSP sales from one table.">
            <x-slot name="actions">
                <button type="button" x-on:click="filterOpen = true" class="inline-flex items-center justify-center gap-2 rounded-lg border border-red-200 bg-white px-4 py-3 text-sm font-semibold text-red-700 shadow-sm transition hover:bg-red-50" aria-controls="sales-filter-drawer">
                    <x-warehouse.icon name="funnel" size-class="h-4 w-4" />
                    Filter
                </button>
                @if (Auth::guard('warehouse_salesman')->check())
                    <x-warehouse.button href="{{ route('warehouse.sales.create') }}" variant="primary" icon="plus" class="rounded-xl px-5 py-3">
                        New Sale
                    </x-warehouse.button>
                @endif
            </x-slot>
        </x-warehouse.page-header>

        <div>
            <div x-cloak x-show="filterOpen" x-transition.opacity x-on:click="filterOpen = false" class="fixed inset-0 z-[60] bg-slate-950/60 backdrop-blur-[1px]" aria-hidden="true"></div>

            <form id="sales-filter-drawer" method="GET" action="{{ route('warehouse.sales.index') }}" x-cloak x-show="filterOpen" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full" class="fixed inset-y-0 right-0 z-[70] w-[min(92vw,460px)] overflow-y-auto border-l border-slate-200 bg-slate-50 px-4 py-4 shadow-2xl sm:px-6">
                <div class="mb-5 flex items-center justify-between border-b border-slate-200 pb-4">
                    <div>
                        <h2 class="m-0 text-lg font-bold text-slate-900">Filter Sales</h2>
                        <p class="mb-0 mt-1 text-xs text-slate-500">Apply filters to update the sales report.</p>
                    </div>
                    <button type="button" x-on:click="filterOpen = false" class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-600 shadow-sm" aria-label="Close filters">
                        <x-warehouse.icon name="x" size-class="h-5 w-5" />
                    </button>
                </div>
            <div class="grid gap-4">
                <div>
                    <x-warehouse.input-label for="search" value="Search" class="mb-2 text-slate-700" />
                    <input type="text" id="search" name="search" value="{{ $search }}" placeholder="Invoice, customer, phone, address, date" class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-red-500 focus:ring-2 focus:ring-red-500/20">
                </div>
                <div>
                    <x-warehouse.input-label for="from_date" value="From" class="mb-2 text-slate-700" />
                    <input type="date" id="from_date" name="from_date" value="{{ $fromDate }}" class="block h-11 w-full rounded-xl border border-slate-300 bg-white px-3 text-sm text-slate-800 outline-none focus:border-red-500 focus:ring-2 focus:ring-red-500/20">
                </div>
                <div>
                    <x-warehouse.input-label for="to_date" value="To" class="mb-2 text-slate-700" />
                    <input type="date" id="to_date" name="to_date" value="{{ $toDate }}" class="block h-11 w-full rounded-xl border border-slate-300 bg-white px-3 text-sm text-slate-800 outline-none focus:border-red-500 focus:ring-2 focus:ring-red-500/20">
                </div>
                @if (!Auth::guard('warehouse_salesman')->check())
                    <div>
                        <x-warehouse.searchable-select name="salesman_id" id="salesman-filter" label="LSP" :options="$salesmanFilterOptions" :selected="$salesmanId" placeholder="All LSPs" search-placeholder="Search salesman" />
                    </div>
                @endif
                <div>
                    <x-warehouse.input-label for="payment_status" value="Payment" class="mb-2 text-slate-700" />
                    <select name="payment_status" id="payment_status" class="block h-11 w-full rounded-xl border border-slate-300 bg-white px-3 text-sm text-slate-800 outline-none focus:border-red-500 focus:ring-2 focus:ring-red-500/20">
                        <option value="all" {{ $paymentStatus === 'all' ? 'selected' : '' }}>All</option>
                        <option value="paid" {{ $paymentStatus === 'paid' ? 'selected' : '' }}>Paid</option>
                        <option value="due" {{ $paymentStatus === 'due' ? 'selected' : '' }}>Due</option>
                    </select>
                </div>
                <div>
                    <x-warehouse.input-label for="per_page" value="Per page" class="mb-2 text-slate-700" />
                    <select name="per_page" id="per_page" class="block h-11 w-full rounded-xl border border-slate-300 bg-white px-3 text-sm text-slate-800 outline-none focus:border-red-500 focus:ring-2 focus:ring-red-500/20">
                        @foreach ($perPageOptions as $option)
                            <option value="{{ $option }}" @selected((string) $perPage === (string) $option)>{{ $option === 'all' ? 'All' : $option }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex flex-wrap gap-3 border-t border-slate-200 pt-4">
                    <x-loading-submit type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-xl border border-transparent bg-red-600 px-5 py-3 text-sm font-semibold text-white shadow-sm" icon="funnel" loading-text="Filtering...">Apply Filter</x-loading-submit>
                    @if ($search !== '' || $salesmanId || $fromDate || $toDate || $paymentStatus !== 'all' || $perPage !== 'all')
                        <x-warehouse.button href="{{ route('warehouse.sales.index') }}" variant="secondary" class="w-full px-5 py-3 lg:w-auto" icon="rotate-ccw">Reset</x-warehouse.button>
                    @endif
                </div>
            </div>
            </form>
        </div>

        <div class="grid grid-cols-1 gap-2 border-b border-slate-200 bg-white px-3 py-2 sm:grid-cols-4 sm:px-5">
            <div class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2">
                <p class="m-0 text-xs font-bold uppercase tracking-wider text-slate-500">Filtered Sales</p>
                <p class="mb-0 mt-0.5 text-xl font-extrabold text-slate-900">{{ number_format($isPaginated ? $sales->total() : $rows->count()) }}</p>
            </div>
            <div class="rounded-lg border border-blue-200 bg-blue-50 px-3 py-2">
                <p class="m-0 text-xs font-bold uppercase tracking-wider text-blue-700">Sales Amount</p>
                <p class="mb-0 mt-0.5 text-xl font-extrabold text-blue-900">{{ number_format($filteredSalesAmount, 2) }} <span class="text-xs font-bold">BDT</span></p>
            </div>
            <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2">
                <p class="m-0 text-xs font-bold uppercase tracking-wider text-emerald-700">Collected Sales Amount</p>
                <p class="mb-0 mt-0.5 text-xl font-extrabold text-emerald-900">{{ number_format($filteredTotalAmount, 2) }} <span class="text-xs font-bold">BDT</span></p>
            </div>
            <div class="rounded-lg border border-amber-200 bg-amber-50 px-3 py-2">
                <p class="m-0 text-xs font-bold uppercase tracking-wider text-amber-700">Total Due Amount</p>
                <p class="mb-0 mt-0.5 text-xl font-extrabold text-amber-900">{{ number_format($filteredDueAmount, 2) }} <span class="text-xs font-bold">BDT</span></p>
            </div>
        </div>

        <x-warehouse.table min-width="min-w-[900px]">
            <x-slot name="header">
                <tr>
                    <x-warehouse.table.th padding-class="px-4 py-2 sm:px-5">SL</x-warehouse.table.th>
                    <x-warehouse.table.th padding-class="px-4 py-2 sm:px-5">Invoice</x-warehouse.table.th>
                    <x-warehouse.table.th padding-class="px-4 py-2 sm:px-5">Date</x-warehouse.table.th>
                    <x-warehouse.table.th padding-class="px-4 py-2 sm:px-5">Customer</x-warehouse.table.th>
                    <x-warehouse.table.th padding-class="px-4 py-2 sm:px-5">LSP</x-warehouse.table.th>
                    <x-warehouse.table.th padding-class="px-4 py-2 sm:px-5">Payment</x-warehouse.table.th>
                    <x-warehouse.table.th padding-class="px-4 py-2 sm:px-5">Paid</x-warehouse.table.th>
                    <x-warehouse.table.th padding-class="px-4 py-2 sm:px-5">Due</x-warehouse.table.th>
                    <x-warehouse.table.th padding-class="px-4 py-2 sm:px-5">Total</x-warehouse.table.th>
                    <x-warehouse.table.th padding-class="px-4 py-2 sm:px-5">Actions</x-warehouse.table.th>
                </tr>
            </x-slot>

            @forelse ($rows as $sale)
                @php
                    $saleDate = $sale->sale_date instanceof \DateTimeInterface
                        ? $sale->sale_date->format('d-m-Y')
                        : \Carbon\Carbon::parse($sale->sale_date)->format('d-m-Y');
                @endphp
                <tr class="hover:bg-slate-50">
                    <x-warehouse.table.td padding-class="px-4 py-1.5 sm:px-5" class="font-medium text-slate-900">{{ $firstItem + $loop->index }}</x-warehouse.table.td>
                    <x-warehouse.table.td padding-class="px-4 py-1.5 sm:px-5" class="font-semibold text-slate-900">{{ $sale->invoice_no }}</x-warehouse.table.td>
                    <x-warehouse.table.td padding-class="px-4 py-1.5 sm:px-5">{{ $saleDate ?: '-' }}</x-warehouse.table.td>
                    <x-warehouse.table.td padding-class="px-4 py-1.5 sm:px-5">{{ $sale->customer_name ?: '-' }}</x-warehouse.table.td>
                    <x-warehouse.table.td padding-class="px-4 py-1.5 sm:px-5">{{ $sale->salesman?->name ?: '-' }}</x-warehouse.table.td>
                    <x-warehouse.table.td padding-class="px-4 py-1.5 sm:px-5">{{ $sale->payment_method ?: '-' }}</x-warehouse.table.td>
                    <x-warehouse.table.td padding-class="px-4 py-1.5 sm:px-5"><span class="inline-flex whitespace-nowrap rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-bold text-emerald-800 ring-1 ring-inset ring-emerald-200">Paid {{ number_format((float) $sale->paid_amount, 2) }}</span></x-warehouse.table.td>
                    <x-warehouse.table.td padding-class="px-4 py-1.5 sm:px-5"><span class="inline-flex whitespace-nowrap rounded-full px-2.5 py-1 text-xs font-bold ring-1 ring-inset {{ (float) $sale->due_amount > 0 ? 'bg-amber-100 text-amber-800 ring-amber-200' : 'bg-slate-100 text-slate-600 ring-slate-200' }}">Due {{ number_format((float) $sale->due_amount, 2) }}</span></x-warehouse.table.td>
                    <x-warehouse.table.td padding-class="px-4 py-1.5 sm:px-5" class="font-semibold text-slate-900">{{ number_format($sale->total_amount, 2) }}</x-warehouse.table.td>
                    <x-warehouse.table.td padding-class="px-4 py-1.5 sm:px-5" class="align-middle">
                        <div class="flex items-center gap-2 whitespace-nowrap leading-none">
                            <a href="{{ route('warehouse.sales.show', $sale) }}" title="View" aria-label="View" class="inline-flex h-10 w-10 items-center justify-center rounded-lg border border-slate-300 bg-white text-slate-700 shadow-sm transition hover:border-slate-400 hover:bg-slate-50 hover:text-slate-900">
                                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M1.75 10s3-5.25 8.25-5.25S18.25 10 18.25 10 15.25 15.25 10 15.25 1.75 10 1.75 10Z" /><circle cx="10" cy="10" r="2.5" /></svg>
                            </a>
                            @if (Auth::guard('warehouse_salesman')->check())
                                <a href="{{ route('warehouse.sales.edit', $sale) }}" title="Edit Sale" aria-label="Edit Sale" class="inline-flex h-10 w-10 items-center justify-center rounded-lg border border-blue-200 bg-blue-50 text-blue-700 shadow-sm transition hover:border-blue-300 hover:bg-blue-100 hover:text-blue-800">
                                    <x-warehouse.icon name="square-pen" size-class="h-5 w-5" />
                                </a>
                            @endif
                            <a href="{{ route('warehouse.sales.invoice', $sale) }}" target="_blank" rel="noopener" title="Print Invoice" aria-label="Print Invoice" class="inline-flex h-10 w-10 items-center justify-center rounded-lg border border-emerald-200 bg-emerald-50 text-emerald-700 shadow-sm transition hover:border-emerald-300 hover:bg-emerald-100 hover:text-emerald-800">
                                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6 7V4.75A1.75 1.75 0 0 1 7.75 3h4.5A1.75 1.75 0 0 1 14 4.75V7m-8 7h8m-9.25-6h10.5A1.75 1.75 0 0 1 17 9.75v3.5A1.75 1.75 0 0 1 15.25 15H14v1.25A1.75 1.75 0 0 1 12.25 18h-4.5A1.75 1.75 0 0 1 6 16.25V15H4.75A1.75 1.75 0 0 1 3 13.25v-3.5A1.75 1.75 0 0 1 4.75 8Z" /></svg>
                            </a>
                            @if ((float) $sale->due_amount > 0)
                                @php
                                    $paymentHistory = $sale->payments->sortByDesc('payment_date')->take(3)->map(function ($payment) {
                                        return [
                                            'date' => optional($payment->payment_date)->format('d-m-Y'),
                                            'method' => $payment->payment_method,
                                            'amount' => number_format((float) $payment->amount, 2),
                                            'notes' => $payment->notes,
                                        ];
                                    })->values()->all();
                                @endphp
                                <button type="button" data-due-payment-button data-action="{{ route('warehouse.sales.payments.store', $sale) }}" data-invoice="{{ $sale->invoice_no }}" data-due="{{ number_format((float) $sale->due_amount, 2, '.', '') }}" data-history="{{ e(json_encode($paymentHistory)) }}" title="Pay Due" aria-label="Pay Due" class="inline-flex h-10 w-10 items-center justify-center rounded-lg border border-amber-200 bg-amber-50 text-amber-700 shadow-sm transition hover:border-amber-300 hover:bg-amber-100 hover:text-amber-800">
                                    <x-warehouse.icon name="wallet-cards" size-class="h-5 w-5" />
                                </button>
                            @endif
                        </div>
                    </x-warehouse.table.td>
                </tr>
            @empty
                <tr>
                    <x-warehouse.table.td colspan="10" :nowrap="false" class="py-12 text-center">
                        <x-warehouse.empty-state title="No sales found" description="Try a different search term or create the first sale." />
                    </x-warehouse.table.td>
                </tr>
            @endforelse
        </x-warehouse.table>

        <div class="flex flex-col gap-3 border-t border-slate-200 px-4 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
            <p class="text-sm text-slate-600">
                @if($isPaginated)
                    Showing {{ $sales->firstItem() ?? 0 }} to {{ $sales->lastItem() ?? 0 }} of {{ $sales->total() }} records
                @else
                    Showing all {{ $rows->count() }} records
                @endif
            </p>
            <div>
                @if($isPaginated)
                    {{ $sales->onEachSide(1)->links('warehouse-portal.partials.pagination') }}
                @endif
            </div>
        </div>
    </div>

    <div id="due-payment-modal" class="fixed inset-0 z-[80] hidden items-center justify-center bg-slate-950/60 p-4 backdrop-blur-sm">
        <div class="w-full max-w-lg overflow-hidden rounded-2xl bg-white shadow-2xl">
            <div class="flex items-center justify-between border-b border-slate-200 bg-slate-50 px-5 py-4">
                <div>
                    <h2 class="m-0 text-lg font-bold text-slate-900">Pay Due Amount</h2>
                    <p class="mb-0 mt-1 text-xs text-slate-500">Invoice <span id="due-payment-invoice">-</span></p>
                </div>
                <button type="button" id="close-due-payment-modal" class="inline-flex h-9 w-9 items-center justify-center rounded-lg text-slate-500 hover:bg-slate-200" aria-label="Close"><x-warehouse.icon name="x" size-class="h-5 w-5" /></button>
            </div>
            <form id="due-payment-form" method="POST" class="p-5">
                @csrf
                <div class="mb-4 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3">
                    <p class="m-0 text-xs font-bold uppercase tracking-wider text-amber-700">Current Due</p>
                    <p class="mb-0 mt-1 text-2xl font-extrabold text-amber-900"><span id="due-payment-balance">0.00</span> BDT</p>
                    <p class="mb-0 mt-1 text-xs font-semibold text-amber-700">Remaining after payment: <span id="due-payment-remaining">0.00</span> BDT</p>
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div><x-warehouse.input-label for="modal_payment_date" value="Payment Date" class="mb-2 text-slate-700" /><input type="date" id="modal_payment_date" name="payment_date" value="{{ now()->format('Y-m-d') }}" required class="block h-11 w-full rounded-lg border border-slate-300 px-3 text-sm"></div>
                    <div><x-warehouse.input-label for="modal_payment_amount" value="Amount" class="mb-2 text-slate-700" /><input type="number" id="modal_payment_amount" name="amount" min="0.01" step="0.01" required class="block h-11 w-full appearance-none rounded-lg border border-slate-300 px-3 text-sm [&::-webkit-inner-spin-button]:appearance-none [&::-webkit-outer-spin-button]:appearance-none"></div>
                    <div class="sm:col-span-2"><x-warehouse.input-label for="modal_payment_method" value="Method" class="mb-2 text-slate-700" /><select id="modal_payment_method" name="payment_method" required class="block h-11 w-full rounded-lg border border-slate-300 px-3 text-sm"><option value="Cash">Cash</option><option value="Mobile Banking">Mobile Banking</option></select></div>
                </div>
                <div class="mt-5 flex justify-end gap-2">
                    <button type="button" id="cancel-due-payment-modal" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700">Cancel</button>
                    <x-loading-submit type="submit" class="inline-flex items-center justify-center rounded-lg bg-amber-600 px-5 py-2 text-sm font-bold text-white hover:bg-amber-500" icon="save" loading-text="Paying...">Pay</x-loading-submit>
                </div>
            </form>
        </div>
    </div>

    <script>
        (function () {
            const modal = document.getElementById('due-payment-modal');
            const form = document.getElementById('due-payment-form');
            const invoice = document.getElementById('due-payment-invoice');
            const balance = document.getElementById('due-payment-balance');
            const remaining = document.getElementById('due-payment-remaining');
            const amount = document.getElementById('modal_payment_amount');
            const close = () => { modal.classList.add('hidden'); modal.classList.remove('flex'); document.body.classList.remove('overflow-hidden'); };
            document.addEventListener('click', function (event) {
                const button = event.target.closest('[data-due-payment-button]');
                if (!button) return;
                event.preventDefault();
                    const due = button.dataset.due;
                    form.action = button.dataset.action;
                    invoice.textContent = button.dataset.invoice;
                    balance.textContent = due;
                    amount.max = due;
                    amount.value = due;
                    remaining.textContent = '0.00';
                    modal.classList.remove('hidden'); modal.classList.add('flex'); document.body.classList.add('overflow-hidden');
            });
            amount.addEventListener('input', function () {
                const due = parseFloat(balance.textContent) || 0;
                const paid = parseFloat(this.value) || 0;
                remaining.textContent = Math.max(0, due - paid).toFixed(2);
            });
            document.getElementById('close-due-payment-modal').addEventListener('click', close);
            document.getElementById('cancel-due-payment-modal').addEventListener('click', close);
            modal.addEventListener('click', event => { if (event.target === modal) close(); });
        })();
    </script>
</x-warehouse-layout>

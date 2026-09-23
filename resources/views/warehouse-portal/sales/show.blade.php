<x-warehouse-layout :title="'Sale ' . $sale->invoice_no">
    @php
        $saleDate = $sale->sale_date instanceof \DateTimeInterface
            ? $sale->sale_date->format('M d, Y')
            : \Carbon\Carbon::parse($sale->sale_date)->format('M d, Y');
        $totalQuantity = $sale->items->sum(fn ($item) => (float) $item->quantity);
        $itemCount = $sale->items->count();
    @endphp

    <style>
        @media print {
            .warehouse-sale-print-hide {
                display: none !important;
            }

            .warehouse-sale-print-shell {
                box-shadow: none !important;
                border: 0 !important;
                overflow: visible !important;
            }
        }
    </style>

    <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm warehouse-sale-print-shell">
        <x-warehouse.page-header
            :title="'Sale ' . $sale->invoice_no"
            description="Review customer details, sold products, and the final warehouse sale amount."
        >
            <x-slot name="actions">
                <div class="warehouse-sale-print-hide flex flex-wrap items-center gap-2">
                    <x-warehouse.button
                        href="{{ route('warehouse.sales.index') }}"
                        variant="secondary"
                        icon="arrow-left"
                        class="rounded-xl px-4 py-2.5"
                    >
                        Back To Sales
                    </x-warehouse.button>

                    <button
                        type="button"
                        onclick="window.print()"
                        class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:border-slate-400 hover:bg-slate-50 hover:text-slate-900"
                    >
                        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.9" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 7V4.75A1.75 1.75 0 0 1 7.75 3h4.5A1.75 1.75 0 0 1 14 4.75V7m-8 2.5h8M6 14h8m-9.25-6h10.5A1.75 1.75 0 0 1 17 9.75v3.5A1.75 1.75 0 0 1 15.25 15H14v1.25A1.75 1.75 0 0 1 12.25 18h-4.5A1.75 1.75 0 0 1 6 16.25V15H4.75A1.75 1.75 0 0 1 3 13.25v-3.5A1.75 1.75 0 0 1 4.75 8Z" />
                        </svg>
                        Print
                    </button>
                </div>
            </x-slot>
        </x-warehouse.page-header>

        <div class="border-t border-slate-200 bg-slate-50/70 px-4 py-4 sm:px-6">
            <div class="grid gap-3 md:grid-cols-4">
                <div class="rounded-2xl border border-slate-200 bg-white px-4 py-3 shadow-sm">
                    <p class="m-0 text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Invoice</p>
                    <p class="mb-0 mt-2 text-xl font-bold text-slate-900">{{ $sale->invoice_no }}</p>
                    <p class="mb-0 mt-1 text-xs text-slate-500">{{ $saleDate }}</p>
                </div>

                <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 shadow-sm">
                    <p class="m-0 text-xs font-semibold uppercase tracking-[0.18em] text-emerald-700">Items / Qty</p>
                    <p class="mb-0 mt-2 text-xl font-bold text-emerald-900">{{ $itemCount }} / {{ number_format($totalQuantity, 0) }}</p>
                    <p class="mb-0 mt-1 text-xs text-emerald-700/80">Total line items and sold quantity</p>
                </div>

                <div class="rounded-2xl border border-slate-900 bg-slate-900 px-4 py-3 text-white shadow-sm">
                    <p class="m-0 text-xs font-semibold uppercase tracking-[0.18em] text-slate-300">Grand Total</p>
                    <p class="mb-0 mt-2 text-xl font-bold">{{ number_format($sale->total_amount, 2) }}</p>
                    <p class="mb-0 mt-1 text-xs text-slate-300">Final warehouse sale amount</p>
                </div>

                <div class="rounded-2xl border border-violet-200 bg-violet-50 px-4 py-3 shadow-sm">
                    <p class="m-0 text-xs font-semibold uppercase tracking-[0.18em] text-violet-700">Payment</p>
                    <p class="mb-0 mt-2 text-xl font-bold text-violet-900">{{ $sale->payment_method ?: '-' }}</p>
                    <p class="mb-0 mt-1 text-xs text-violet-700/80">
                        Paid {{ number_format((float) $sale->paid_amount, 2) }} / Due {{ number_format((float) $sale->due_amount, 2) }}
                    </p>
                </div>
            </div>
        </div>

        <div class="grid gap-4 border-t border-slate-200 px-4 py-5 sm:px-6 lg:grid-cols-[1.05fr_0.95fr]">
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 px-4 py-3">
                    <h2 class="m-0 text-sm font-bold text-slate-900">Sale Information</h2>
                </div>

                <div class="grid gap-0 sm:grid-cols-2">
                    <div class="border-b border-slate-100 px-4 py-3 sm:border-r">
                        <p class="m-0 text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-400">Area Office</p>
                        <p class="mb-0 mt-1 text-sm font-semibold text-slate-900">{{ $warehouse->name ?? '-' }}</p>
                    </div>

                    <div class="border-b border-slate-100 px-4 py-3">
                        <p class="m-0 text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-400">LSP</p>
                        <p class="mb-0 mt-1 text-sm font-semibold text-slate-900">{{ $sale->salesman?->name ?? '-' }}</p>
                    </div>

                    <div class="border-b border-slate-100 px-4 py-3 sm:border-r sm:border-b-0">
                        <p class="m-0 text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-400">Sale Date</p>
                        <p class="mb-0 mt-1 text-sm font-semibold text-slate-900">{{ $saleDate }}</p>
                    </div>

                    <div class="px-4 py-3">
                        <p class="m-0 text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-400">Recorded Total</p>
                        <p class="mb-0 mt-1 text-sm font-semibold text-slate-900">{{ number_format($sale->total_amount, 2) }}</p>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 px-4 py-3">
                    <h2 class="m-0 text-sm font-bold text-slate-900">Customer Information</h2>
                </div>

                <div class="grid gap-0 sm:grid-cols-2">
                    <div class="border-b border-slate-100 px-4 py-3 sm:border-r">
                        <p class="m-0 text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-400">Customer Name</p>
                        <p class="mb-0 mt-1 text-sm font-semibold text-slate-900">{{ $sale->customer_name ?: '-' }}</p>
                    </div>

                    <div class="border-b border-slate-100 px-4 py-3">
                        <p class="m-0 text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-400">Phone Number</p>
                        <p class="mb-0 mt-1 text-sm font-semibold text-slate-900">{{ $sale->customer_phone ?: '-' }}</p>
                    </div>

                    <div class="px-4 py-3 sm:col-span-2">
                        <p class="m-0 text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-400">Address</p>
                        <p class="mb-0 mt-1 text-sm leading-6 text-slate-700">{{ $sale->customer_address ?: '-' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="border-t border-slate-200 px-4 py-5 sm:px-6">
            @if ((float) $sale->due_amount > 0)
                <div class="warehouse-sale-print-hide mb-5 rounded-2xl border border-amber-200 bg-amber-50 p-4">
                    <h2 class="m-0 text-base font-bold text-amber-900">Collect Due Payment</h2>
                    <p class="mb-3 mt-1 text-sm text-amber-700">Current due: {{ number_format((float) $sale->due_amount, 2) }} BDT. The amount is counted on the payment date.</p>
                    <form method="POST" action="{{ route('warehouse.sales.payments.store', $sale) }}" class="grid gap-3 md:grid-cols-4 md:items-end">
                        @csrf
                        <div><x-warehouse.input-label for="payment_date" value="Payment Date" /><input id="payment_date" name="payment_date" type="date" value="{{ old('payment_date', now()->format('Y-m-d')) }}" required class="mt-1 block h-11 w-full rounded-lg border border-slate-300 px-3"></div>
                        <div><x-warehouse.input-label for="amount" value="Amount" /><input id="amount" name="amount" type="number" value="{{ old('amount') }}" min="0.01" max="{{ number_format((float) $sale->due_amount, 2, '.', '') }}" step="0.01" required class="mt-1 block h-11 w-full rounded-lg border border-slate-300 px-3">@error('amount')<x-warehouse.input-error :messages="$message" class="mt-1" />@enderror</div>
                        <div><x-warehouse.input-label for="payment_method" value="Method" /><select id="payment_method" name="payment_method" required class="mt-1 block h-11 w-full rounded-lg border border-slate-300 px-3"><option value="Cash">Cash</option><option value="Mobile Banking">Mobile Banking</option></select></div>
                        <button type="submit" class="h-11 rounded-lg bg-amber-600 px-4 text-sm font-bold text-white hover:bg-amber-500">Record Payment</button>
                    </form>
                </div>
            @endif

            @if ($sale->payments->isNotEmpty())
                <div class="mb-5 rounded-2xl border border-slate-200 bg-white">
                    <div class="border-b border-slate-200 px-4 py-3"><h2 class="m-0 text-sm font-bold text-slate-900">Payment History</h2></div>
                    <div class="overflow-x-auto"><table class="w-full text-sm"><thead class="bg-slate-50 text-left text-slate-500"><tr><th class="px-4 py-2">Date</th><th class="px-4 py-2">Method</th><th class="px-4 py-2 text-right">Amount</th></tr></thead><tbody>@foreach ($sale->payments->sortByDesc('payment_date') as $payment)<tr class="border-t border-slate-100"><td class="px-4 py-2">{{ $payment->payment_date->format('d M Y') }}</td><td class="px-4 py-2">{{ $payment->payment_method }}</td><td class="px-4 py-2 text-right font-bold">{{ number_format($payment->amount, 2) }}</td></tr>@endforeach</tbody></table></div>
                </div>
            @endif

            <div class="mb-4 flex items-center justify-between gap-3">
                <div>
                    <h2 class="m-0 text-base font-bold text-slate-900">Sold Products</h2>
                    <p class="mb-0 mt-1 text-sm text-slate-500">Product-wise sale quantity, price, and line total.</p>
                </div>
            </div>

            <x-warehouse.table min-width="min-w-[760px]">
                <x-slot name="header">
                    <tr>
                        <x-warehouse.table.th>SL</x-warehouse.table.th>
                        <x-warehouse.table.th>Product</x-warehouse.table.th>
                        <x-warehouse.table.th>Qty</x-warehouse.table.th>
                        <x-warehouse.table.th>Sale Price</x-warehouse.table.th>
                        <x-warehouse.table.th>Total</x-warehouse.table.th>
                    </tr>
                </x-slot>

                @forelse ($sale->items as $item)
                    <tr class="hover:bg-slate-50">
                        <x-warehouse.table.td class="font-medium text-slate-900">
                            {{ $loop->iteration }}
                        </x-warehouse.table.td>
                        <x-warehouse.table.td class="font-semibold text-slate-900">
                            {{ $item->product?->product_name ?: '-' }}
                        </x-warehouse.table.td>
                        <x-warehouse.table.td>
                            {{ number_format($item->quantity, 0) }}
                        </x-warehouse.table.td>
                        <x-warehouse.table.td>
                            {{ number_format($item->sale_price, 2) }}
                        </x-warehouse.table.td>
                        <x-warehouse.table.td class="font-semibold text-slate-900">
                            {{ number_format($item->total, 2) }}
                        </x-warehouse.table.td>
                    </tr>
                @empty
                    <tr>
                        <x-warehouse.table.td colspan="5" :nowrap="false" class="py-12 text-center">
                            <x-warehouse.empty-state
                                title="No sale items found"
                                description="This sale does not have any product rows yet."
                            />
                        </x-warehouse.table.td>
                    </tr>
                @endforelse
            </x-warehouse.table>

            <div class="mt-4 flex justify-end">
                @php($invoiceDiscount = max(0, (float) $sale->items->sum('total') - (float) $sale->total_amount))
                <div class="w-full max-w-sm overflow-hidden rounded-2xl border border-slate-300 bg-white">
                    <table class="w-full text-sm">
                        <tbody>
                            <tr class="border-b border-slate-200"><th class="px-4 py-3 text-left font-medium text-slate-600">Total Quantity</th><td class="px-4 py-3 text-right font-bold text-slate-900">{{ number_format($totalQuantity, 0) }}</td></tr>
                        </tbody>
                        <tfoot class="bg-slate-50">
                            @if ($invoiceDiscount > 0)
                                <tr class="border-b border-slate-200"><th class="px-4 py-3 text-right font-medium text-slate-600">Initial Total</th><td class="px-4 py-3 text-right font-bold text-slate-900">{{ number_format((float) $sale->items->sum('total'), 2) }} BDT</td></tr>
                                <tr class="border-b border-slate-200"><th class="px-4 py-3 text-right font-medium text-slate-600">Discount (-)</th><td class="px-4 py-3 text-right font-bold text-slate-900">{{ number_format($invoiceDiscount, 2) }} BDT</td></tr>
                            @endif
                            <tr class="border-b border-slate-300"><th class="px-4 py-3 text-right font-semibold text-slate-700">Total</th><td class="px-4 py-3 text-right text-lg font-extrabold text-slate-900">{{ number_format($sale->total_amount, 2) }} BDT</td></tr>
                            <tr class="border-b border-slate-200"><th class="px-4 py-2 text-right font-medium text-emerald-700">Paid</th><td class="px-4 py-2 text-right font-bold text-emerald-700">{{ number_format($sale->paid_amount, 2) }} BDT</td></tr>
                            <tr><th class="px-4 py-2 text-right font-medium text-amber-700">Due</th><td class="px-4 py-2 text-right font-bold text-amber-700">{{ number_format($sale->due_amount, 2) }} BDT</td></tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-warehouse-layout>

<x-warehouse-layout title="Edit Sale">
    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <x-warehouse.page-header title="Edit Sale" description="Update sale details and quantities." :back-href="route('warehouse.sales.index')" back-label="Sales" />
        <form method="POST" action="{{ route('warehouse.sales.update', $sale) }}">
            @csrf @method('PUT')
            <div class="space-y-5 p-4 sm:p-6">
                <section class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                        <div><x-warehouse.input-label for="sale_date" value="Sale Date" class="mb-2" /><input id="sale_date" type="date" name="sale_date" value="{{ old('sale_date', \Carbon\Carbon::parse($sale->sale_date)->format('Y-m-d')) }}" required class="h-11 w-full rounded-xl border border-slate-300 px-3 text-sm"></div>
                        <div><x-warehouse.input-label for="customer_name" value="Customer Name" class="mb-2" /><input id="customer_name" name="customer_name" value="{{ old('customer_name', $sale->customer_name) }}" required class="h-11 w-full rounded-xl border border-slate-300 px-3 text-sm"></div>
                        <div><x-warehouse.input-label for="customer_phone" value="Customer Phone" class="mb-2" /><input id="customer_phone" name="customer_phone" value="{{ old('customer_phone', $sale->customer_phone) }}" required class="h-11 w-full rounded-xl border border-slate-300 px-3 text-sm"></div>
                        <div><x-warehouse.input-label for="customer_address" value="Address" class="mb-2" /><input id="customer_address" name="customer_address" value="{{ old('customer_address', $sale->customer_address) }}" class="h-11 w-full rounded-xl border border-slate-300 px-3 text-sm"></div>
                        <div>
                            <x-warehouse.input-label for="payment_method" value="Payment Method" class="mb-2" />
                            <select id="payment_method" name="payment_method" required class="h-11 w-full rounded-xl border border-slate-300 px-3 text-sm">
                                @foreach (['Cash', 'Credit', 'Mobile Banking'] as $method)
                                    <option value="{{ $method }}" {{ old('payment_method', $sale->payment_method) === $method ? 'selected' : '' }}>{{ $method }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div><x-warehouse.input-label for="discount" value="Discount (BDT)" class="mb-2" /><input id="discount" type="number" name="discount" value="{{ old('discount', 0) }}" min="0" step="0.01" readonly aria-readonly="true" class="h-11 w-full cursor-not-allowed rounded-xl border border-slate-300 bg-slate-100 px-3 text-sm text-slate-600"></div>
                        <div><x-warehouse.input-label for="paid_amount" value="Paid Amount" class="mb-2" /><input id="paid_amount" type="number" name="paid_amount" value="{{ old('paid_amount', $sale->paid_amount) }}" min="0" step="0.01" readonly aria-readonly="true" class="h-11 w-full cursor-not-allowed rounded-xl border border-slate-300 bg-slate-100 px-3 text-sm text-slate-600"></div>
                    </div>
                </section>
                @foreach($sale->items as $index => $item)
                    <input type="hidden" name="items[{{ $index }}][product_id]" value="{{ $item->product_id }}">
                    <input type="hidden" name="items[{{ $index }}][quantity]" value="{{ old("items.$index.quantity", $item->quantity) }}">
                    <input type="hidden" name="items[{{ $index }}][sale_price]" value="{{ old("items.$index.sale_price", $item->sale_price) }}">
                @endforeach
            </div>
            <div class="flex flex-wrap justify-end gap-2 border-t border-slate-200 bg-slate-900 p-4"><x-warehouse.button href="{{ route('warehouse.sales.index') }}" variant="secondary">Cancel</x-warehouse.button><x-loading-submit type="submit" icon="save" class="inline-flex h-11 items-center justify-center gap-2 rounded-lg border border-transparent bg-emerald-600 px-4 text-sm font-semibold tracking-tight text-white shadow-sm transition hover:bg-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2" loading-text="Updating Sale...">Update Sale</x-loading-submit></div>
        </form>
    </div>
    <x-slot name="script">
        <script>
            document.querySelectorAll('input[name$="[quantity]"]').forEach(function (input) {
                input.addEventListener('input', function () {
                    if (this.value !== '') this.value = Number(this.value).toFixed(2);
                });
                input.addEventListener('change', function () {
                    const value = Math.max(0.01, Number(this.value || 0));
                    this.value = value.toFixed(2);
                });
            });
        </script>
    </x-slot>
</x-warehouse-layout>

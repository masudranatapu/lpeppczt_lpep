<x-warehouse-layout title="Assign Stock">
    @php
        $oldItems = collect(old('items', []));
        $productRows = $products->map(function ($product) {
            return [
                'id' => (string) $product->id,
                'name' => $product->product_name,
                'code' => $product->code ?: '',
                'available' => (float) $product->available_quantity,
            ];
        })->values();
        $productLookup = $productRows->keyBy('id');
        $salesmanOptions = $salesmen->mapWithKeys(function ($salesman) {
            return [(string) $salesman->id => [
                'value' => (string) $salesman->id,
                'label' => $salesman->name,
                'description' => $salesman->email ?: 'Area Office salesman',
            ]];
        });
        $productOptions = $productRows->mapWithKeys(function ($product) {
            return [$product['id'] => [
                'value' => $product['id'],
                'label' => $product['name'],
                'description' => trim(($product['code'] ? $product['code'] . ' · ' : '') . 'Available ' . number_format($product['available'], 2)),
            ]];
        });
    @endphp

    <div class="overflow-visible rounded-2xl border border-slate-200 bg-white shadow-sm">
        <x-warehouse.page-header
            title="Assign Stock to LSP"
            description="Allocate currently unassigned inventory from {{ $warehouse->name }} to a salesman."
            :back-href="route('warehouse.salesman-assignments.index')"
            back-label="Assignments"
        />

        <form method="POST" action="{{ route('warehouse.salesman-assignments.store') }}" id="assignment-form" novalidate>
            @csrf
            <input type="hidden" name="warehouse_id" value="{{ $warehouse->id }}">

            <div class="space-y-6 p-4 sm:p-6">
                <section class="rounded-2xl border border-slate-200 bg-slate-50 p-4 sm:p-5">
                    <div class="grid gap-4 lg:grid-cols-3">
                        <div>
                            <x-warehouse.searchable-select
                                name="warehouse_salesman_id"
                                id="salesman-picker"
                                label="LSP"
                                :options="$salesmanOptions"
                                :selected="old('warehouse_salesman_id')"
                                placeholder="Search and select salesman"
                                search-placeholder="Search salesman name or email"
                                required
                            />
                            <p id="salesman-client-error" class="mb-0 mt-2 hidden text-sm font-medium text-red-600"></p>
                        </div>
                        <div>
                            <x-warehouse.input-label for="assignment_date" value="Assignment Date" class="mb-2 text-slate-700" />
                            <x-warehouse.text-input id="assignment_date" name="assignment_date" type="date" value="{{ old('assignment_date', now()->format('Y-m-d')) }}" class="block h-11 w-full rounded-xl" required />
                            @error('assignment_date')<x-warehouse.input-error :messages="$message" class="mt-2" />@enderror
                        </div>
                        <div>
                            <x-warehouse.input-label for="notes" value="Notes (optional)" class="mb-2 text-slate-700" />
                            <x-warehouse.text-input id="notes" name="notes" type="text" value="{{ old('notes') }}" placeholder="Reference or assignment note" class="block h-11 w-full rounded-xl" />
                            @error('notes')<x-warehouse.input-error :messages="$message" class="mt-2" />@enderror
                        </div>
                    </div>
                </section>

                <section class="overflow-visible rounded-2xl border border-slate-200 bg-white">
                    <p id="product-picker-error" class="m-0 hidden border-b border-red-100 bg-red-50 px-5 py-3 text-sm font-medium text-red-600"></p>

                    <div id="assignment-table-wrapper">
                        <x-warehouse.table min-width="min-w-[760px]" table-class="w-full table-fixed divide-y divide-slate-200" wrapper-class="overflow-visible" body-id="assignment-items" :overflow="false">
                            <x-slot name="header"><tr><x-warehouse.table.th class="w-[48%]">Product</x-warehouse.table.th><x-warehouse.table.th align="right" class="w-[18%]">Unassigned Stock</x-warehouse.table.th><x-warehouse.table.th align="center" class="w-[24%]">Assign Quantity</x-warehouse.table.th><x-warehouse.table.th align="center" class="w-[10%]">Action</x-warehouse.table.th></tr></x-slot>
                            @foreach($oldItems as $index => $item)
                                @php $oldProduct = $productLookup->get((string) ($item['product_id'] ?? '')); @endphp
                                @if($oldProduct)
                                    <tr data-assignment-row data-product-id="{{ $oldProduct['id'] }}" data-available="{{ $oldProduct['available'] }}">
                                        <x-warehouse.table.td :nowrap="false" class="min-w-[310px]"><x-warehouse.searchable-select id="assignment-product-{{ $index }}" :options="$productOptions" :selected="$oldProduct['id']" placeholder="Search and select product" search-placeholder="Search product name or code" data-product-picker /><p class="mb-0 mt-1 hidden text-xs font-medium text-red-600" data-row-error></p></x-warehouse.table.td>
                                        <x-warehouse.table.td align="right"><span class="inline-flex rounded-full bg-emerald-50 px-3 py-1 text-sm font-bold text-emerald-700 ring-1 ring-emerald-200" data-available-label>{{ number_format($oldProduct['available'], 2) }}</span></x-warehouse.table.td>
                                        <x-warehouse.table.td align="center"><div class="inline-flex items-stretch" data-quantity-control><button type="button" class="inline-flex h-10 w-10 items-center justify-center rounded-l-lg bg-rose-500 text-lg font-bold text-white transition hover:bg-rose-600" data-quantity-minus aria-label="Decrease quantity">−</button><input type="number" name="items[{{ $index }}][quantity]" value="{{ $item['quantity'] ?? 1 }}" min="0.01" max="{{ $oldProduct['available'] }}" step="0.01" class="h-10 w-16 border-y border-x-0 border-slate-300 px-2 text-center text-sm font-bold text-slate-900 outline-none focus:border-emerald-500 focus:ring-0" data-quantity required><button type="button" class="inline-flex h-10 w-10 items-center justify-center rounded-r-lg bg-emerald-500 text-lg font-bold text-white transition hover:bg-emerald-600" data-quantity-plus aria-label="Increase quantity">+</button></div></x-warehouse.table.td>
                                        <x-warehouse.table.td align="center"><button type="button" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-red-200 bg-red-50 text-red-700 transition hover:bg-red-100" data-remove aria-label="Remove product"><x-warehouse.icon name="trash-2" /></button></x-warehouse.table.td>
                                    </tr>
                                @endif
                            @endforeach
                        </x-warehouse.table>
                    </div>
                    @error('items')<div class="border-t border-red-100 bg-red-50 px-5 py-3"><x-warehouse.input-error :messages="$message" /></div>@enderror
                </section>

                <div class="grid gap-3 sm:grid-cols-3">
                    <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3"><p class="m-0 text-xs font-semibold uppercase tracking-wide text-slate-400">Area Office</p><p class="mb-0 mt-1 font-bold text-slate-900">{{ $warehouse->name }}</p></div>
                    <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3"><p class="m-0 text-xs font-semibold uppercase tracking-wide text-slate-400">Selected Products</p><p class="mb-0 mt-1 text-xl font-bold text-slate-900" id="summary-items">{{ $oldItems->count() }}</p></div>
                    <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3"><p class="m-0 text-xs font-semibold uppercase tracking-wide text-emerald-600">Total Quantity</p><p class="mb-0 mt-1 text-xl font-bold text-emerald-800" id="summary-quantity">{{ number_format($oldItems->sum(fn($item) => (float) ($item['quantity'] ?? 0)), 2) }}</p></div>
                </div>
            </div>

            <div class="sticky bottom-0 z-20 flex flex-col gap-2 border-t border-slate-200 bg-slate-900 px-4 py-3 sm:flex-row sm:items-center sm:justify-end sm:px-6">
                <x-warehouse.button href="{{ route('warehouse.salesman-assignments.index') }}" variant="secondary" class="w-full sm:w-auto">Cancel</x-warehouse.button>
                <x-loading-submit type="submit" id="assignment-submit" class="inline-flex w-full min-w-[190px] items-center justify-center gap-2 rounded-xl border border-transparent bg-emerald-600 px-4 py-3 text-sm font-semibold text-white shadow-sm hover:bg-emerald-500 sm:w-auto" icon="save" loading-text="Saving Assignment...">Save Assignment</x-loading-submit>
            </div>
        </form>
    </div>

    <template id="assignment-row-template">
        <tr data-assignment-row data-product-id="" data-available="0">
            <x-warehouse.table.td :nowrap="false" class="min-w-[310px]"><x-warehouse.searchable-select id="assignment-product-template" :options="$productOptions" placeholder="Search and select product" search-placeholder="Search product name or code" data-product-picker /><p class="mb-0 mt-1 hidden text-xs font-medium text-red-600" data-row-error></p></x-warehouse.table.td>
            <x-warehouse.table.td align="right"><span class="inline-flex rounded-full bg-slate-100 px-3 py-1 text-sm font-bold text-slate-500 ring-1 ring-slate-200" data-available-label>0.00</span></x-warehouse.table.td>
            <x-warehouse.table.td align="center"><div class="inline-flex items-stretch" data-quantity-control><button type="button" class="inline-flex h-10 w-10 items-center justify-center rounded-l-lg bg-rose-500 text-lg font-bold text-white transition hover:bg-rose-600 disabled:cursor-not-allowed disabled:bg-slate-200 disabled:text-slate-400" data-quantity-minus aria-label="Decrease quantity" disabled>−</button><input type="number" value="1" min="0.01" step="0.01" class="h-10 w-16 border-y border-x-0 border-slate-300 px-2 text-center text-sm font-bold text-slate-900 outline-none focus:border-emerald-500 focus:ring-0 disabled:cursor-not-allowed disabled:bg-slate-100 disabled:text-slate-400" data-quantity required disabled><button type="button" class="inline-flex h-10 w-10 items-center justify-center rounded-r-lg bg-emerald-500 text-lg font-bold text-white transition hover:bg-emerald-600 disabled:cursor-not-allowed disabled:bg-slate-200 disabled:text-slate-400" data-quantity-plus aria-label="Increase quantity" disabled>+</button></div></x-warehouse.table.td>
            <x-warehouse.table.td align="center"><button type="button" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 bg-slate-100 text-slate-400 transition disabled:cursor-not-allowed" data-remove aria-label="Remove product" disabled><x-warehouse.icon name="trash-2" /></button></x-warehouse.table.td>
        </tr>
    </template>

    <x-slot name="script">
        <script>
            (function () {
                const products = @json($productRows);
                const productMap = products.reduce((carry, product) => { carry[String(product.id)] = product; return carry; }, {});
                const form = document.getElementById('assignment-form');
                const body = document.getElementById('assignment-items');
                const template = document.getElementById('assignment-row-template');
                const pickerError = document.getElementById('product-picker-error');
                const submit = document.getElementById('assignment-submit');

                function rows() { return Array.from(body.querySelectorAll('[data-assignment-row]')); }
                function completedRows() { return rows().filter(row => row.dataset.productId); }
                function sync() {
                    let quantity = 0;
                    let completedIndex = 0;
                    rows().forEach(function (row) {
                        const productInput = row.querySelector('[data-product-picker] input[type="hidden"]');
                        const quantityInput = row.querySelector('[data-quantity]');
                        if (row.dataset.productId) {
                            productInput.name = `items[${completedIndex}][product_id]`;
                            quantityInput.name = `items[${completedIndex}][quantity]`;
                            quantityInput.disabled = false;
                            quantity += Number(quantityInput.value || 0);
                            completedIndex++;
                        } else {
                            productInput.removeAttribute('name');
                            quantityInput.removeAttribute('name');
                            quantityInput.disabled = true;
                        }
                    });
                    document.getElementById('summary-items').textContent = completedIndex;
                    document.getElementById('summary-quantity').textContent = quantity.toFixed(2);
                }
                function bind(row) {
                    const quantityInput = row.querySelector('[data-quantity]');
                    row.querySelector('[data-quantity-minus]').addEventListener('click', function () {
                        quantityInput.value = Math.max(0.01, Number((Number(quantityInput.value || 1) - 1).toFixed(2)));
                        sync();
                    });
                    row.querySelector('[data-quantity-plus]').addEventListener('click', function () {
                        const available = Number(row.dataset.available || 0);
                        const next = Number((Number(quantityInput.value || 0) + 1).toFixed(2));
                        quantityInput.value = available > 0 ? Math.min(next, available) : next;
                        sync();
                    });
                    quantityInput.addEventListener('input', sync);
                    quantityInput.addEventListener('change', function () {
                        const available = Number(row.dataset.available || 0);
                        let value = Math.max(0.01, Number(Number(quantityInput.value || 1).toFixed(2)));
                        if (available > 0) value = Math.min(value, available);
                        quantityInput.value = value;
                        sync();
                    });
                    row.querySelector('[data-remove]').addEventListener('click', function () { row.remove(); ensureBlankRow(); sync(); });
                }
                function appendBlankRow() {
                    const row = template.content.firstElementChild.cloneNode(true);
                    body.appendChild(row);
                    bind(row);
                    return row;
                }
                function ensureBlankRow() {
                    if (!rows().some(row => !row.dataset.productId)) appendBlankRow();
                }
                function selectProduct(row, selectedValue) {
                    const product = productMap[String(selectedValue)];
                    const rowError = row.querySelector('[data-row-error]');
                    pickerError.classList.add('hidden');
                    rowError.classList.add('hidden');
                    if (!product) return;
                    const existing = completedRows().find(other => other !== row && other.dataset.productId === String(product.id));
                    if (existing) {
                        rowError.textContent = 'This product is already selected.';
                        rowError.classList.remove('hidden');
                        row.querySelector('[data-product-picker]').dispatchEvent(new CustomEvent('warehouse-searchable-select-reset'));
                        row.dataset.productId = '';
                        ensureBlankRow(); sync();
                        return;
                    }
                    row.dataset.productId = product.id;
                    row.dataset.available = product.available;
                    const availableLabel = row.querySelector('[data-available-label]');
                    availableLabel.textContent = Number(product.available).toFixed(2);
                    availableLabel.className = 'inline-flex rounded-full bg-emerald-50 px-3 py-1 text-sm font-bold text-emerald-700 ring-1 ring-emerald-200';
                    const quantityInput = row.querySelector('[data-quantity]');
                    quantityInput.max = product.available;
                    quantityInput.value = Math.min(Math.max(0.01, Number(quantityInput.value || 1)), Number(product.available));
                    quantityInput.disabled = false;
                    row.querySelector('[data-quantity-minus]').disabled = false;
                    row.querySelector('[data-quantity-plus]').disabled = false;
                    row.querySelector('[data-remove]').disabled = false;
                    row.querySelector('[data-remove]').className = 'inline-flex h-9 w-9 items-center justify-center rounded-lg border border-red-200 bg-red-50 text-red-700 transition hover:bg-red-100';
                    ensureBlankRow(); sync();
                }

                rows().forEach(bind);
                body.addEventListener('warehouse-searchable-select-change', function (event) {
                    const row = event.target.closest('[data-assignment-row]');
                    if (row) selectProduct(row, event.detail.value);
                });
                ensureBlankRow();
                form.addEventListener('submit', function (event) {
                    const salesman = document.getElementById('salesman-picker');
                    const salesmanError = document.getElementById('salesman-client-error');
                    salesmanError.classList.add('hidden');
                    if (!salesman.value) { event.preventDefault(); salesmanError.textContent = 'Select a salesman.'; salesmanError.classList.remove('hidden'); document.getElementById('salesman-picker-button').focus(); return; }
                    if (!completedRows().length) { event.preventDefault(); pickerError.textContent = 'Select at least one product in the table.'; pickerError.classList.remove('hidden'); return; }
                    if (!form.checkValidity()) { event.preventDefault(); form.reportValidity(); return; }
                    submit.disabled = true;
                    submit.setAttribute('aria-busy', 'true');
                    submit.querySelector('[data-submit-default]').classList.add('hidden');
                    const loading = submit.querySelector('[data-submit-loading]');
                    loading.classList.remove('hidden'); loading.classList.add('inline-flex');
                });
                sync();
            })();
        </script>
    </x-slot>
</x-warehouse-layout>

<x-warehouse-layout title="New POS">
    @php
        $editingSale = null;
        $saleDate = old('sale_date', now()->format('Y-m-d'));
        $oldItems = old('items');
        $hasOldItems = is_array($oldItems) && count($oldItems) > 0;

        $productRows = $products->map(function ($product) {
            $available = (float) ($product->warehouse_purchase_qty ?? 0) + (float) ($product->warehouse_transfer_qty ?? 0) - (float) ($product->warehouse_sale_qty ?? 0);
            $imagePath = $product->image ? json_decode($product->image) : null;

            return [
                'id' => $product->id,
                'name' => $product->product_name,
                'code' => $product->code,
                'selling_price' => (float) $product->selling_price,
                'image_url' => $imagePath ? asset($imagePath) : asset('logo.jpeg'),
                'available' => $available,
                'state' => $available <= 0 ? 'blocked' : ($available <= 10 ? 'low' : 'ready'),
            ];
        })->values();

        $productLookup = $productRows->keyBy('id');
        $beneficiaryOptions = ($customers ?? collect())->mapWithKeys(fn ($customer) => [(string) $customer->id => ['value' => (string) $customer->id, 'label' => $customer->name, 'description' => $customer->mobile ?: ($customer->beneficiary_number ?: '')]]);
                        $beneficiaryRows = ($customers ?? collect())->map(fn ($customer) => ['id' => (string) $customer->id, 'name' => $customer->name, 'mobile' => $customer->mobile, 'beneficiary_number' => $customer->beneficiary_number, 'group_number' => $customer->group_number, 'village' => $customer->village, 'union' => $customer->unions])->values();
        $lowStockCount = $productRows->where('state', 'low')->count();
        $blockedCount = $productRows->where('state', 'blocked')->count();
        $sellableProductCount = $productRows->count() - $blockedCount;
        $visibleRows = $hasOldItems ? count($oldItems) : 0;
        $visibleQuantity = $hasOldItems
            ? collect($oldItems)->sum(fn ($item) => (float) ($item['quantity'] ?? 0))
            : 0;
    @endphp

    <style>
        .warehouse-qty-control { display: flex; align-items: center; justify-content: center; min-width: 108px; }
        .warehouse-qty-control .qty-input {
            width: 56px;
            min-width: 0;
            height: 27px;
            border: 1px solid #cbd5e1;
            border-left: 0;
            border-right: 0;
            border-radius: 0;
            background: #fff;
            padding: 2px 4px;
            color: #0f172a;
            font-size: 13px;
            font-weight: 700;
            line-height: 1;
            box-shadow: none;
            -moz-appearance: textfield;
        }
        .warehouse-qty-control .qty-button {
            display: inline-flex;
            width: 27px;
            height: 27px;
            align-items: center;
            justify-content: center;
            border: 0;
            border-radius: 0;
            padding: 0;
            color: #fff;
            font-size: 10px;
            line-height: 1;
            box-shadow: none;
        }
        .warehouse-qty-control .qty-minus { border-radius: 2px 0 0 2px; background: #e11d48; }
        .warehouse-qty-control .qty-plus { border-radius: 0 2px 2px 0; background: #059669; }
        .warehouse-qty-control .qty-minus:hover { background: #be123c; }
        .warehouse-qty-control .qty-plus:hover { background: #047857; }
        .warehouse-qty-control .qty-input::-webkit-inner-spin-button,
        .warehouse-qty-control .qty-input::-webkit-outer-spin-button { margin: 0; appearance: none; }
        .stock-out-badge {
            position: relative;
            overflow: hidden;
            animation: stockOutBadgePulse 1.8s ease-in-out infinite;
        }
        .stock-out-badge::after {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: inherit;
            border: 1px solid rgba(225, 29, 72, 0.55);
            transform: scale(0.96);
            opacity: 0;
            animation: stockOutBadgeRing 1.8s ease-out infinite;
        }
        @keyframes stockOutBadgePulse {
            0%, 100% { box-shadow: 0 0 0 0 rgba(225, 29, 72, 0.12); }
            50% { box-shadow: 0 0 0 3px rgba(225, 29, 72, 0.16); }
        }
        @keyframes stockOutBadgeRing {
            0% { transform: scale(0.96); opacity: 0; }
            30% { opacity: 0.65; }
            100% { transform: scale(1.04); opacity: 0; }
        }
        .sale-cart-table {
            border-collapse: separate;
            border-spacing: 0 5px;
        }
        .sale-cart-table thead th {
            border-bottom: 0 !important;
        }
        .sale-cart-table tbody td {
            padding-top: 7px !important;
            padding-bottom: 7px !important;
            background: #fff;
            border-top: 1px solid #e2e8f0 !important;
            border-bottom: 1px solid #e2e8f0 !important;
            vertical-align: middle !important;
        }
        .sale-cart-table tbody td:first-child {
            border-left: 1px solid #e2e8f0 !important;
            border-radius: 10px 0 0 10px;
            padding-left: 10px !important;
        }
        .sale-cart-table tbody td:last-child {
            border-right: 1px solid #e2e8f0 !important;
            border-radius: 0 10px 10px 0;
            padding-right: 10px !important;
        }
        #sale-submit-overlay { display: none; }
        #sale-submit-overlay.is-loading { display: flex; }
        @media (max-width: 640px) {
            #sale-form > .grid { display: flex !important; flex-direction: column !important; }
            #sale-form > .grid > section:first-child { max-height: 52vh !important; min-height: 0 !important; padding: 10px !important; }
            #sale-form > .grid > section:last-child { min-height: 0 !important; padding: 10px !important; }
            #table-scroller { overflow: visible !important; }
            .sale-cart-table, .sale-cart-table tbody { display: block; width: 100% !important; }
            .sale-cart-table thead { display: none; }
            .sale-cart-table tbody tr { display: grid; grid-template-columns: minmax(0, 1fr) auto auto; gap: 8px; margin-bottom: 8px; padding: 9px; border: 1px solid #e2e8f0; border-radius: 12px; background: #fff; }
            .sale-cart-table tbody td { display: block; min-width: 0 !important; width: auto !important; padding: 3px 0 !important; border: 0 !important; background: transparent !important; }
            .sale-cart-table tbody td:first-child { grid-column: 1; grid-row: 1; border: 0 !important; border-radius: 0; padding: 0 !important; }
            .sale-cart-table tbody td:nth-child(3) { grid-column: 1; grid-row: 2; }
            .sale-cart-table tbody td:nth-child(4) { grid-column: 2; grid-row: 1; align-self: center; text-align: right !important; font-weight: 700; }
            .sale-cart-table tbody td:nth-child(5) { grid-column: 2; grid-row: 2; align-self: center; text-align: right !important; font-weight: 700; }
            .sale-cart-table tbody td:nth-child(6) { grid-column: 3; grid-row: 1; display: flex; justify-content: flex-end; }
            .sale-cart-table tbody td:nth-child(6) button { display: inline-flex !important; visibility: visible !important; opacity: 1 !important; }
            .sale-cart-table tbody td:nth-child(3)::before { content: 'Quantity'; display: block; margin-bottom: 3px; color: #64748b; font-size: 10px; font-weight: 700; text-transform: uppercase; }
            .sale-cart-table tbody td:nth-child(5)::before { content: 'Total'; display: block; color: #64748b; font-size: 10px; font-weight: 700; text-transform: uppercase; }
            .sale-cart-table .warehouse-qty-control { justify-content: flex-start; min-width: 0; }
            .sale-cart-table .warehouse-qty-control .qty-input { width: 42px; }
            .sale-cart-table .warehouse-qty-control .qty-button { width: 28px; height: 28px; }
            .sale-cart-table .sale-price-input { height: 28px; width: 90px; padding: 2px 0; font-size: 12px; }
            .sale-cart-table tbody td:nth-child(2) { display: none !important; }
            #sale-cart-body:empty + #cart-empty-state { display: block !important; }
            #sale-form > .grid > section:first-child > div > div:first-child { flex-direction: column !important; align-items: stretch !important; gap: 6px !important; }
            #sale-form > .grid > section:first-child .stock-filter-btn { padding: 4px 7px !important; font-size: 10px !important; line-height: 14px !important; gap: 3px !important; }
            #sale-form > .grid > section:first-child .stock-filter-btn svg { width: 12px !important; height: 12px !important; }
            #sale-form > .grid > section:first-child > div > div:first-child > div:last-child { display: flex !important; flex-wrap: wrap !important; gap: 4px !important; }
            #sale-form > .grid > section:last-child { padding: 8px !important; }
            #sale-form > .grid > section:last-child > div { gap: 8px !important; }
            #sale-form > .grid > section:last-child .mt-2 { margin-top: 4px !important; }
            #open-quick-beneficiary { display: inline-flex !important; width: 36px !important; height: 36px !important; min-width: 36px !important; visibility: visible !important; opacity: 1 !important; }
            #open-quick-beneficiary svg { width: 16px !important; height: 16px !important; }
            #sale-summary-total { font-size: 14px !important; line-height: 20px !important; white-space: normal !important; overflow: visible !important; text-overflow: clip !important; }
            #sale-summary-items, #sale-summary-quantity { font-size: 15px !important; }
            #sale-submit { padding-top: 8px !important; padding-bottom: 8px !important; font-size: 13px !important; }
            #sale-form > .sticky { padding: 6px !important; }
            #sale-form > .sticky > div { gap: 6px !important; }
            #sale-form > .sticky > div > div:first-child { grid-template-columns: 1fr 1fr 1.6fr !important; gap: 5px !important; width: 100% !important; }
            #sale-form > .sticky > div > div:first-child > div { min-height: 48px !important; padding: 6px 8px !important; }
            #sale-form > .sticky > div > div:first-child > div p:last-child { font-size: 15px !important; }
            #sale-form > .sticky > div > div:first-child > div:last-child p:last-child { font-size: 17px !important; }
            #sale-form > .sticky > div > div:last-child { gap: 6px !important; }
        }
    </style>

    <div class="overflow-hidden border border-slate-200 bg-slate-100 shadow-sm xl:min-h-[calc(100vh-90px)]">
        <div id="sale-submit-overlay" class="fixed inset-0 z-[200] items-center justify-center bg-slate-950/75 p-6 backdrop-blur-sm" aria-hidden="true">
            <div class="w-full max-w-sm rounded-2xl bg-white p-6 text-center shadow-2xl">
                <span class="mx-auto inline-flex h-12 w-12 animate-spin rounded-full border-4 border-emerald-100 border-t-emerald-600" aria-hidden="true"></span>
                <p class="mb-0 mt-4 text-lg font-extrabold text-slate-900">Saving Sale...</p>
                <p class="mb-0 mt-1 text-sm text-slate-500">Please wait. Do not refresh or close this page.</p>
            </div>
        </div>
        <form method="POST" action="{{ route('warehouse.sales.store') }}" id="sale-form" data-products-empty="{{ $sellableProductCount === 0 ? 1 : 0 }}">
            @csrf
            <input type="hidden" id="sale-beneficiary-number" name="sale_beneficiary_number" value="">
            <input type="hidden" id="sale-group-number" name="sale_group_number" value="">

            <div class="grid gap-0 xl:grid-cols-[5fr_7fr]">
                <section class="max-h-[58vh] overflow-y-auto border-b border-slate-200 bg-slate-50 p-3 sm:p-4 xl:h-[calc(100vh-245px)] xl:max-h-none xl:border-b-0 xl:border-r">
                    <div class="flex flex-col gap-4">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <h2 class="m-0 text-base font-bold text-slate-900">Products</h2>
                                <p class="mb-0 mt-1 text-xs text-slate-500">Search or tap a product to add it.</p>
                            </div>

                            <div class="flex flex-wrap items-center gap-2">
                                <button
                                    type="button"
                                    class="stock-filter-btn inline-flex items-center gap-1.5 rounded-full border border-slate-300 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700"
                                    data-stock-filter="all"
                                >
                                    <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M3.5 3.5h5v5h-5v-5Zm8 0h5v5h-5v-5Zm-8 8h5v5h-5v-5Zm8 0h5v5h-5v-5Z" /></svg>
                                    All
                                </button>
                                <button
                                    type="button"
                                    class="stock-filter-btn inline-flex items-center gap-1.5 rounded-full border border-slate-300 bg-slate-900 px-3 py-1.5 text-xs font-semibold text-white"
                                    data-stock-filter="in"
                                >
                                    <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m4 10 3.5 3.5L16 5" /></svg>
                                    Stock In
                                </button>
                                <button
                                    type="button"
                                    class="stock-filter-btn inline-flex items-center gap-1.5 rounded-full border border-slate-300 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700"
                                    data-stock-filter="low"
                                >
                                    <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M10 3 2.75 16h14.5L10 3Zm0 4.5v4m0 2.25h.01" /></svg>
                                    Low
                                </button>
                                <button
                                    type="button"
                                    class="stock-filter-btn inline-flex items-center gap-1.5 rounded-full border border-slate-300 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700"
                                    data-stock-filter="blocked"
                                >
                                    <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="10" cy="10" r="7" /><path stroke-linecap="round" d="m5 5 10 10" /></svg>
                                    Out of Stock
                                </button>
                            </div>
                        </div>

                        <div class="relative overflow-hidden rounded-xl border border-slate-300 bg-white shadow-sm transition focus-within:border-emerald-500 focus-within:ring-2 focus-within:ring-emerald-500/20">
                            <span class="pointer-events-none absolute left-3 top-1/2 inline-flex h-8 w-8 -translate-y-1/2 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 5.25v13.5M7 5.25v13.5m3-13.5v13.5m4-13.5v13.5m3-13.5v13.5m3.25-13.5v13.5" />
                                </svg>
                            </span>
                            <input
                                class="h-12 w-full border-0 bg-transparent py-2 pl-14 pr-24 text-sm font-medium text-slate-900 outline-none placeholder:text-slate-400 focus:ring-0"
                                id="product-search"
                                placeholder="Search product name, code, or scan barcode"
                                autofocus
                                autocomplete="off"
                                type="search"
                            >
                            <button type="button" id="product-search-clear" class="absolute right-2 top-1/2 hidden -translate-y-1/2 rounded-lg px-3 py-1.5 text-xs font-semibold text-slate-500 transition hover:bg-slate-100 hover:text-slate-800">
                                Clear
                            </button>
                        </div>
                    </div>

                    <div class="mt-4">
                        @if ($productRows->isEmpty())
                            <x-warehouse.empty-state
                                title="No products available"
                                description="There are no warehouse products to display yet."
                                maxWidth="max-w-md"
                            />
                        @else
                            <div class="grid grid-cols-2 gap-1 sm:grid-cols-3 xl:grid-cols-3 2xl:grid-cols-4" id="product-grid">
                                @foreach ($productRows as $product)
                                    <button
                                        type="button"
                                        class="group flex h-[178px] flex-col overflow-hidden rounded-lg border bg-white text-left shadow-sm transition hover:-translate-y-0.5 hover:border-emerald-400 hover:shadow-md disabled:cursor-not-allowed disabled:opacity-60 {{ $product['state'] === 'blocked' ? 'border-rose-200' : ($product['state'] === 'low' ? 'border-amber-200' : 'border-slate-200') }}"
                                        data-add-product
                                        data-product-id="{{ $product['id'] }}"
                                        data-search-key="{{ strtolower($product['name'] . ' ' . ($product['code'] ?? '')) }}"
                                        data-stock-state="{{ $product['state'] }}"
                                        style="{{ $product['state'] === 'blocked' ? 'display:none' : '' }}"
                                        @if ($product['state'] === 'blocked') disabled @endif
                                    >
                                        <div class="flex h-[86px] w-full items-center justify-center border-b border-slate-100 bg-slate-50 p-2">
                                            <img
                                                src="{{ $product['image_url'] }}"
                                                alt="{{ $product['name'] }}"
                                                class="h-full w-full object-contain"
                                                loading="lazy"
                                                onerror="this.onerror=null;this.src='{{ asset('logo.jpeg') }}';"
                                            >
                                        </div>

                                        <div class="flex flex-1 flex-col p-2.5">
                                            <div class="h-9 overflow-hidden">
                                                <p class="m-0 text-xs font-bold leading-[18px] text-slate-900" title="{{ $product['name'] }}">
                                                    {{ $product['name'] }}
                                                </p>
                                            </div>

                                            <div class="mt-auto flex items-center justify-between gap-2 border-t border-slate-100 pt-2">
                                                @if ($product['state'] === 'blocked')
                                                    <span class="stock-out-badge inline-flex w-full items-center justify-center rounded-md bg-rose-50 px-2 py-1.5 text-[10px] font-bold uppercase tracking-wide text-rose-700 ring-1 ring-rose-200">
                                                        Stock Out
                                                    </span>
                                                @else
                                                    <p class="m-0 text-sm font-extrabold text-slate-900">{{ number_format($product['selling_price'], 2) }}</p>
                                                    <span class="inline-flex items-center rounded-full px-1 py-1 text-[10px] font-bold {{ $product['state'] === 'low' ? 'bg-amber-50 text-amber-700 ring-1 ring-amber-200' : 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200' }}">
                                                        Available {{ number_format($product['available'], 0) }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </button>
                                @endforeach
                            </div>

                            <div class="mt-4 hidden rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-600" id="product-grid-empty">
                                No products match your search.
                            </div>
                        @endif
                    </div>
                </section>

                <section class="bg-white p-3 sm:p-4 xl:h-[calc(100vh-245px)] xl:overflow-y-auto">
                    <div class="flex flex-col gap-4">
                        <div class="rounded-lg border border-slate-200 bg-slate-50 p-3">
                            {{-- <div class="flex items-center justify-between gap-3">
                                <x-warehouse.input-label for="sale-beneficiary" value="Select Customer" class="text-slate-700" />
                                <button type="button" id="open-sale-details" class="inline-flex shrink-0 items-center gap-2 rounded-lg border border-emerald-200 bg-white px-3 py-2 text-xs font-bold text-emerald-700 shadow-sm transition hover:bg-emerald-50" title="Customer details">
                                    <svg class="h-4 w-4 shrink-0" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M10 4v12M4 10h12" /></svg>
                                    <span data-customer-button-label>Details</span>
                                </button>
                            </div> --}}
                            <div class="mt-2 flex items-center gap-2">
                                  <div class="relative min-w-0 flex-1 overflow-visible">
                                    <x-warehouse.searchable-select name="beneficiary_id" id="sale-beneficiary" :options="$beneficiaryOptions" placeholder="Select Customer" search-placeholder="Search customer name or phone" />
                                </div>
                                 <button type="button" id="open-quick-beneficiary" onclick="document.getElementById('quick-beneficiary-modal')?.classList.remove('hidden'); document.getElementById('quick-beneficiary-modal')?.classList.add('flex'); document.body.classList.add('overflow-hidden');" class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-emerald-600 text-white shadow-md transition hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-300" title="Add new customer" aria-label="Add new customer"><svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.8" aria-hidden="true"><path stroke-linecap="round" d="M12 5v14M5 12h14" /></svg></button>
                            </div>
                            <p id="sale-details-summary" class="mb-0 mt-2 truncate text-xs font-medium text-slate-500">Select an existing customer or add a new customer.</p>
                        </div>
                    </div>

                    <div class="mt-4 overflow-auto" id="table-scroller">
                        <table class="sale-cart-table table table-bordered m-0 w-full">
                            <thead class="text-center" style="background: #00a65a">
                                <tr style="height: 25px; color: #fff;">
                                    <th style="padding:4px 0; margin:0; width: 34%;">Name</th>
                                    <th style="padding:4px 0; margin:0; width: 24%;">Quantity</th>
                                    <th style="padding:4px 0; margin:0; width: 17%;">Price</th>
                                    <th style="padding:4px 0; margin:0; width: 17%;">Total</th>
                                    <th style="padding:4px 0; margin:0; width: 8%;">Action</th>
                                </tr>
                            </thead>
                            <tbody id="sale-cart-body">
                                @if ($hasOldItems)
                                    @foreach ($oldItems as $index => $item)
                                        @php
                                            $selectedProductId = (int) ($item['product_id'] ?? 0);
                                            $selectedProduct = $productLookup->get($selectedProductId);
                                        @endphp

                                        @if ($selectedProduct)
                                            <tr
                                                data-sale-row
                                                data-product-id="{{ $selectedProduct['id'] }}"
                                                data-available="{{ number_format($selectedProduct['available'], 2, '.', '') }}"
                                                data-state="{{ $selectedProduct['state'] }}"
                                                class="align-middle"
                                            >
                                                <td class="text-left">
                                                    <input type="hidden" value="{{ $selectedProduct['id'] }}" data-row-product-id>
                                                    <div class="text-xs font-semibold leading-4 text-slate-900" data-row-name>{{ $selectedProduct['name'] }}</div>
                                                    @if ($selectedProduct['code'])
                                                        <div class="text-xs text-slate-500" data-row-code>{{ $selectedProduct['code'] }}</div>
                                                    @endif
                                                    <div class="hidden mt-1 text-xs font-medium text-slate-500" data-row-stock-status>
                                                        {{ $selectedProduct['state'] === 'blocked' ? 'Out of stock. This product cannot be sold.' : ($selectedProduct['state'] === 'low' ? 'Low stock: only ' . number_format($selectedProduct['available'], 2) . ' left.' : number_format($selectedProduct['available'], 2) . ' available in this warehouse.') }}
                                                    </div>
                                                    @error('items.' . $index . '.product_id')
                                                        <x-warehouse.input-error :messages="$message" class="mt-2" />
                                                    @enderror
                                                </td>
                                                <td class="hidden">
                                                    <span class="inline-flex items-center rounded-full border px-3 py-1.5 text-sm font-bold {{ $selectedProduct['state'] === 'blocked' ? 'border-rose-200 bg-rose-50 text-rose-700' : ($selectedProduct['state'] === 'low' ? 'border-amber-200 bg-amber-50 text-amber-700' : 'border-emerald-200 bg-emerald-50 text-emerald-700') }}" data-row-stock-badge>
                                                        {{ $selectedProduct['state'] === 'blocked' ? 'Out Of Stock' : ($selectedProduct['state'] === 'low' ? 'Low' : 'Ready') }}
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    <div class="warehouse-qty-control">
                                                        <button type="button" class="btn btn-danger qty-button qty-minus" data-qty-decrement title="Decrease quantity" aria-label="Decrease quantity"><svg class="h-3 w-3" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" d="M4 10h12" /></svg></button>
                                                        <input
                                                            type="number"
                                                            step="1"
                                                            min="1"
                                                            class="form-control qty-input sale-qty-input text-center"
                                                            data-row-qty
                                                            value="{{ old("items.$index.quantity", $item['quantity'] ?? 1) }}"
                                                            data-max="{{ number_format($selectedProduct['available'], 2, '.', '') }}"
                                                            inputmode="numeric"
                                                            autocomplete="off"
                                                        >
                                                        <button type="button" class="btn btn-success qty-button qty-plus" data-qty-increment title="Increase quantity" aria-label="Increase quantity"><svg class="h-3 w-3" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" d="M10 4v12M4 10h12" /></svg></button>
                                                    </div>
                                                    @error('items.' . $index . '.quantity')
                                                        <x-warehouse.input-error :messages="$message" class="mt-2" />
                                                    @enderror
                                                </td>
                                                <td class="text-center">
                                                    <input
                                                        type="number"
                                                        step="0.01"
                                                        min="0"
                                                        class="sale-price-input form-control cursor-default bg-white text-center border-0 shadow-none"
                                                        data-row-price
                                                        value="{{ old("items.$index.sale_price", $item['sale_price'] ?? $selectedProduct['selling_price']) }}"
                                                        readonly
                                                        tabindex="-1"
                                                    >
                                                    @error('items.' . $index . '.sale_price')
                                                        <x-warehouse.input-error :messages="$message" class="mt-2" />
                                                    @enderror
                                                </td>
                                                <td class="text-center" data-row-total>
                                                    0.00
                                                </td>
                                                <td class="text-center">
                                                    <button type="button" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-rose-200 bg-rose-50 p-0 text-rose-700 transition hover:border-rose-300 hover:bg-rose-100" data-row-remove title="Remove item" aria-label="Remove item">
                                                        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.9" aria-hidden="true">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 3.75h5m-8 2h11m-9.75 0 .5 8.5A1.75 1.75 0 0 0 8 16h4a1.75 1.75 0 0 0 1.75-1.75l.5-8.5M8.5 8.5v4.5m3-4.5v4.5" />
                                                        </svg>
                                                    </button>
                                                </td>
                                            </tr>
                                        @else
                                            <tr
                                                data-sale-row
                                                data-product-id="{{ $selectedProductId }}"
                                                data-available="0"
                                                data-state="blocked"
                                                class="align-middle"
                                            >
                                                <td class="text-left">
                                                    <input type="hidden" value="{{ $selectedProductId }}" data-row-product-id>
                                                    <div class="text-xs font-semibold leading-4 text-slate-900" data-row-name>Product #{{ $selectedProductId }}</div>
                                                    <div class="hidden text-xs font-medium text-rose-700" data-row-stock-status>
                                                        This product is not available in the current warehouse list.
                                                    </div>
                                                    @error('items.' . $index . '.product_id')
                                                        <x-warehouse.input-error :messages="$message" class="mt-2" />
                                                    @enderror
                                                </td>
                                                <td class="hidden">
                                                    <span class="inline-flex items-center rounded-full border border-rose-200 bg-rose-50 px-3 py-1.5 text-sm font-bold text-rose-700" data-row-stock-badge>
                                                        Out Of Stock
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    <div class="warehouse-qty-control">
                                                        <button type="button" class="btn btn-danger qty-button qty-minus" data-qty-decrement title="Decrease quantity" aria-label="Decrease quantity"><svg class="h-3 w-3" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" d="M4 10h12" /></svg></button>
                                                        <input
                                                            type="number"
                                                            step="1"
                                                            min="1"
                                                            class="form-control qty-input sale-qty-input text-center"
                                                            data-row-qty
                                                            value="{{ old("items.$index.quantity", $item['quantity'] ?? 1) }}"
                                                            data-max="0"
                                                            inputmode="numeric"
                                                            autocomplete="off"
                                                        >
                                                        <button type="button" class="btn btn-success qty-button qty-plus" data-qty-increment title="Increase quantity" aria-label="Increase quantity"><svg class="h-3 w-3" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" d="M10 4v12M4 10h12" /></svg></button>
                                                    </div>
                                                    @error('items.' . $index . '.quantity')
                                                        <x-warehouse.input-error :messages="$message" class="mt-2" />
                                                    @enderror
                                                </td>
                                                <td class="text-center">
                                                    <input
                                                        type="number"
                                                        step="0.01"
                                                        min="0"
                                                        class="sale-price-input form-control cursor-default bg-white text-center border-0 shadow-none"
                                                        data-row-price
                                                        value="{{ old("items.$index.sale_price", $item['sale_price'] ?? 0) }}"
                                                        readonly
                                                        tabindex="-1"
                                                    >
                                                    @error('items.' . $index . '.sale_price')
                                                        <x-warehouse.input-error :messages="$message" class="mt-2" />
                                                    @enderror
                                                </td>
                                                <td class="text-center" data-row-total>0.00</td>
                                                <td class="text-center">
                                                    <button type="button" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-rose-200 bg-rose-50 p-0 text-rose-700 transition hover:border-rose-300 hover:bg-rose-100" data-row-remove title="Remove item" aria-label="Remove item">
                                                        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.9" aria-hidden="true">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 3.75h5m-8 2h11m-9.75 0 .5 8.5A1.75 1.75 0 0 0 8 16h4a1.75 1.75 0 0 0 1.75-1.75l.5-8.5M8.5 8.5v4.5m3-4.5v4.5" />
                                                        </svg>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                @endif
                            </tbody>
                        </table>

                        <div class="mt-4 hidden rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-600" id="cart-empty-state">
                            No items in cart. Tap a product card to add it.
                        </div>
                    </div>

                    @error('items')
                        <div class="mt-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-medium text-rose-800">
                            {{ $message }}
                        </div>
                    @enderror
                </section>
            </div>

            <div id="sale-details-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/70 p-4 backdrop-blur-sm">
                <div class="w-full max-w-2xl overflow-hidden rounded-2xl bg-white shadow-2xl">
                    <div class="flex items-center justify-between border-b border-slate-200 bg-slate-50 px-4 py-3 sm:px-5">
                        <div>
                            <h3 class="m-0 text-lg font-bold text-slate-900">Customer Details</h3>
                            <p class="mb-0 mt-1 text-xs text-slate-500">Add the POS date and customer information.</p>
                        </div>
                        <button type="button" id="close-sale-details" class="inline-flex h-9 w-9 items-center justify-center rounded-lg text-slate-400 transition hover:bg-slate-200 hover:text-slate-700" aria-label="Close POS details">
                            <i class="fa fa-times"></i>
                        </button>
                    </div>

                    <div class="grid max-h-[70vh] gap-4 overflow-y-auto p-4 sm:grid-cols-2 sm:p-5">
                        <div>
                            <x-warehouse.input-label for="sale_date" value="POS Date" class="mb-2 text-slate-700" />
                            <input type="date" id="sale_date" name="sale_date" value="{{ $saleDate }}" required
                                class="block h-11 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20">
                            @error('sale_date')<x-warehouse.input-error :messages="$message" class="mt-2" />@enderror
                        </div>

                        <div>
                            <x-warehouse.input-label for="customer_name" value="Customer Name" class="mb-2 text-slate-700" />
                            <input type="text" id="customer_name" name="customer_name" value="{{ old('customer_name', $editingSale?->customer_name) }}"
                                placeholder="Guest customer or account" autocomplete="off" required
                                class="block h-11 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 outline-none placeholder:text-slate-400 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20">
                            @error('customer_name')<x-warehouse.input-error :messages="$message" class="mt-2" />@enderror
                        </div>

                        <div>
                            <x-warehouse.input-label for="customer_phone" value="Phone Number" class="mb-2 text-slate-700" />
                            <input type="tel" id="customer_phone" name="customer_phone" value="{{ old('customer_phone', $editingSale?->customer_phone) }}"
                                placeholder="Enter customer phone number" inputmode="tel" autocomplete="tel" required
                                class="block h-11 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 outline-none placeholder:text-slate-400 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20">
                            @error('customer_phone')<x-warehouse.input-error :messages="$message" class="mt-2" />@enderror
                        </div>

                        <div>
                            <x-warehouse.input-label for="customer_address" value="Address" class="mb-2 text-slate-700" />
                            <textarea id="customer_address" name="customer_address" rows="2" placeholder="Enter customer address"
                                autocomplete="street-address"
                                class="block w-full resize-none rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 outline-none placeholder:text-slate-400 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20">{{ old('customer_address', $editingSale?->customer_address) }}</textarea>
                            @error('customer_address')<x-warehouse.input-error :messages="$message" class="mt-2" />@enderror
                        </div>

                    </div>

                    <div class="flex justify-end gap-2 border-t border-slate-200 bg-slate-50 px-4 py-3 sm:px-5">
                        <button type="button" id="cancel-sale-details" class="inline-flex items-center gap-2 rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" d="M6 6l12 12M18 6 6 18" /></svg>
                            Cancel
                        </button>
                        <button type="button" id="save-sale-details" class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-5 py-2 text-sm font-bold text-white shadow-sm hover:bg-emerald-500">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M5 3.75h12l2 2v14.5H5V3.75Zm3 0v6h8v-6M8 20.25v-7h8v7" /></svg>
                            Save Details
                        </button>
                    </div>
                </div>
            </div>

            <div class="mb-3 flex items-center justify-end gap-3 rounded-xl border border-amber-200 bg-amber-50 px-4 py-1.5">
                <x-warehouse.input-label for="discount" value="Discount (BDT)" class="shrink-0 text-amber-900" />
                <input type="number" id="discount" name="discount" value="{{ old('discount', 0) }}" min="0" step="0.01" inputmode="decimal" class="block h-9 w-32 appearance-none rounded-lg border border-amber-300 bg-white px-3 text-right text-sm font-semibold text-slate-900 outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 [&::-webkit-inner-spin-button]:appearance-none [&::-webkit-outer-spin-button]:appearance-none">
                @error('discount')<x-warehouse.input-error :messages="$message" />@enderror
            </div>

            <div id="payment-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/70 p-4 backdrop-blur-sm">
                <div class="flex max-h-[94vh] w-full max-w-lg flex-col overflow-hidden rounded-2xl bg-white shadow-2xl">
                    <div class="flex shrink-0 items-center justify-between border-b border-slate-200 bg-slate-50 px-3 py-2.5 sm:px-4">
                        <div>
                            <h3 class="m-0 text-lg font-bold text-slate-900">Payment Details</h3>
                            <p class="mb-0 mt-1 text-xs text-slate-500">Select how the customer will pay.</p>
                        </div>
                        <button type="button" id="close-payment-modal" class="inline-flex h-9 w-9 items-center justify-center rounded-lg text-slate-400 transition hover:bg-slate-200 hover:text-slate-700" aria-label="Close payment details">
                            <i class="fa fa-times"></i>
                        </button>
                    </div>

                    <div class="min-h-0 overflow-y-auto p-3 sm:p-4">
                        <div class="mb-3 rounded-xl border border-emerald-200 bg-emerald-50 p-3">
                            <p class="m-0 mb-3 text-xs font-bold uppercase tracking-wide text-emerald-700">Customer Information</p>
                            <div class="mb-3">
                                <label for="payment_sale_date" class="mb-1 block text-sm font-semibold text-slate-700">Sales Date</label>
                                <input type="date" id="payment_sale_date" value="{{ $saleDate }}" class="block h-11 w-full rounded-lg border border-slate-300 bg-white px-3 text-sm">
                            </div>
                             <div class="grid gap-3 sm:grid-cols-2">
                                 <div>
                                     <input type="text" id="payment_customer_name" class="block h-11 w-full rounded-lg border border-slate-300 bg-white px-3 text-sm" placeholder="Enter customer name">
                                </div>
                                <div>
                                    <input type="tel" id="payment_customer_phone" class="block h-11 w-full rounded-lg border border-slate-300 bg-white px-3 text-sm" placeholder="Enter customer phone">
                                 </div>
                             </div>
                             <div class="mt-3 grid gap-3 sm:grid-cols-2">
                                 <div><label for="payment_beneficiary_number" class="sr-only">Beneficiary Number</label><select id="payment_beneficiary_number" class="block h-11 w-full rounded-lg border border-slate-300 bg-white px-3 text-sm"><option value="">Beneficiary Number</option>@for($number = 1; $number <= 40; $number++)<option value="{{ $number }}">{{ $number }}</option>@endfor</select></div>
                                 <div><label for="payment_group_number" class="sr-only">Beneficiary Group Number</label><select id="payment_group_number" class="block h-11 w-full rounded-lg border border-slate-300 bg-white px-3 text-sm"><option value="">Beneficiary Group Number</option>@for($group = 1; $group <= 28; $group++)<option value="{{ $group }}">{{ $group }}</option>@endfor</select></div>
                             </div>
                            <div class="mt-3 grid gap-3 sm:grid-cols-2">
                                <div><input type="text" id="payment_customer_village" name="customer_village" class="block h-11 w-full rounded-lg border border-slate-300 bg-white px-3 text-sm" placeholder="Enter village"></div>
                                <div><input type="text" id="payment_customer_union" name="customer_union" class="block h-11 w-full rounded-lg border border-slate-300 px-3 text-sm" placeholder="Enter union"></div>
                            </div>
                            <p class="mb-0 mt-2 text-xs text-slate-500">This customer will be saved for future sales.</p>
                        </div>
                        <div class="mb-4 rounded-2xl border border-violet-200 bg-violet-50 px-4 py-3 text-center">
                            <p class="m-0 text-xs font-bold uppercase tracking-[0.18em] text-violet-600">Total Amount</p>
                            <p class="mb-0 mt-2 text-3xl font-extrabold text-violet-900">
                                <span id="payment-modal-total">0.00</span>
                                <span class="text-base">BDT</span>
                            </p>
                        </div>

                        <div>
                            <x-warehouse.input-label for="payment_method" value="Payment Method" class="mb-2 text-slate-700" />
                            <select id="payment_method" name="payment_method"
                                class="block h-11 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20">
                                <option value="">Select payment method</option>
                                @foreach (['Cash', 'Credit', 'Mobile Banking'] as $paymentMethod)
                                    <option value="{{ $paymentMethod }}" {{ old('payment_method', $editingSale?->payment_method) === $paymentMethod ? 'selected' : '' }}>
                                        {{ $paymentMethod }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="mb-0 mt-1 text-xs text-slate-500">For credit, enter what the customer pays now; the balance remains due.</p>
                            @error('payment_method')<x-warehouse.input-error :messages="$message" class="mt-2" />@enderror
                        </div>

                        <div id="credit-paid-amount-wrap" class="mt-4 hidden">
                            <x-warehouse.input-label for="paid_amount" value="Amount Paid Now" class="mb-2 text-slate-700" />
                            <input type="number" id="paid_amount" name="paid_amount" value="{{ old('paid_amount', $editingSale?->paid_amount ?? 0) }}" min="0" step="0.01" inputmode="decimal" class="block h-11 w-full appearance-none rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 [&::-webkit-inner-spin-button]:appearance-none [&::-webkit-outer-spin-button]:appearance-none">
                            <div class="mt-2 flex items-center justify-between rounded-lg bg-amber-50 px-3 py-2 text-sm"><span class="font-medium text-amber-700">Due after this payment</span><span class="font-bold text-amber-900"><span id="credit-due-preview">0.00</span> BDT</span></div>
                            @error('paid_amount')<x-warehouse.input-error :messages="$message" class="mt-2" />@enderror
                        </div>
                    </div>

                    <div class="flex shrink-0 justify-end gap-2 border-t border-slate-200 bg-slate-50 px-3 py-2.5 sm:px-4">
                        <button type="button" id="cancel-payment-modal" class="inline-flex items-center gap-2 rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700">
                            Cancel
                        </button>
                        <button type="button" id="confirm-payment" class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-5 py-2 text-sm font-bold text-white shadow-sm hover:bg-emerald-500">
                            <svg class="h-4 w-4" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 10 3.5 3.5 7.5-8" />
                            </svg>
                            Confirm &amp; Save POS
                        </button>
                    </div>
                </div>
            </div>

            <div id="quick-beneficiary-modal" class="fixed inset-0 z-[60] hidden items-end justify-center bg-slate-900/70 p-0 backdrop-blur-sm sm:items-center sm:p-4">
                <div class="flex h-[94vh] w-full max-w-5xl flex-col overflow-hidden rounded-t-3xl bg-slate-50 shadow-2xl sm:h-auto sm:max-h-[92vh] sm:rounded-3xl">
                    <div class="flex items-center justify-between border-b border-emerald-100 bg-gradient-to-r from-emerald-600 to-teal-600 px-5 py-4 text-white sm:px-6"><div><p class="m-0 text-[10px] font-bold uppercase tracking-[0.18em] text-emerald-100">LSP Customer</p><h3 class="m-0 mt-1 text-xl font-extrabold">Add New Customer</h3><p class="mb-0 mt-1 text-xs text-emerald-50">Create a beneficiary and use it for this sale.</p></div><button type="button" id="close-quick-beneficiary" onclick="document.getElementById('quick-beneficiary-modal')?.classList.add('hidden'); document.getElementById('quick-beneficiary-modal')?.classList.remove('flex'); document.body.classList.remove('overflow-hidden');" class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-white/15 text-2xl leading-none text-white transition hover:bg-white/25" aria-label="Close">&times;</button></div>
                    <div id="quick-beneficiary-form" class="overflow-y-auto p-4 sm:p-6">
                        <div class="grid gap-4 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm md:grid-cols-2 xl:grid-cols-3 sm:p-5">
                            <label class="text-sm font-semibold text-slate-700">Beneficiary Name *<input id="quick-beneficiary-name" name="name" data-quick-required placeholder="Enter Name" class="mt-2 block h-11 w-full rounded-xl border border-slate-300 px-3 text-sm"></label>
                            <label class="text-sm font-semibold text-slate-700">Beneficiary Cell Number *<input id="quick-beneficiary-phone" name="mobile" data-quick-required placeholder="Enter Mobile No." class="mt-2 block h-11 w-full rounded-xl border border-slate-300 px-3 text-sm"></label>
                             <label class="text-sm font-semibold text-slate-700">Beneficiary Number *<select id="quick-beneficiary-number" name="beneficiary_number" data-quick-required class="mt-2 block h-11 w-full rounded-xl border border-slate-300 bg-white px-3 text-sm"><option value="">Select Number</option>@for($number = 1; $number <= 40; $number++)<option value="{{ $number }}">{{ $number }}</option>@endfor</select></label>
                             <label class="text-sm font-semibold text-slate-700">Beneficiary Group Number *<select name="group_number" data-quick-required class="mt-2 block h-11 w-full rounded-xl border border-slate-300 bg-white px-3 text-sm"><option value="">Select Group Number</option>@for($group = 1; $group <= 28; $group++)<option value="{{ $group }}">Group {{ $group }}</option>@endfor</select></label>
                            <label class="text-sm font-semibold text-slate-700">Email<input name="email" type="email" value="{{ $warehouseEmail ?? '' }}" placeholder="Email address" class="mt-2 block h-11 w-full rounded-xl border border-slate-300 px-3 text-sm"></label>
                            <label class="text-sm font-semibold text-slate-700">Password<input name="password" type="password" placeholder="Enter password" class="mt-2 block h-11 w-full rounded-xl border border-slate-300 px-3 text-sm"></label>
                            <label class="text-sm font-semibold text-slate-700">Village<input id="quick-beneficiary-village" name="village" placeholder="Enter village" class="mt-2 block h-11 w-full rounded-xl border border-slate-300 px-3 text-sm"></label>
                            <label class="text-sm font-semibold text-slate-700">Union<input name="union" placeholder="Enter Union" class="mt-2 block h-11 w-full rounded-xl border border-slate-300 px-3 text-sm"></label>
                        </div>
                        <div class="mt-5 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5"><h4 class="m-0 text-sm font-extrabold text-slate-900">Number of Livestock</h4><div class="mt-3 grid gap-3 sm:grid-cols-2 lg:grid-cols-5">@foreach(['cow' => 'Cow', 'bull' => 'Bull', 'bakna' => 'Bakna', 'goat' => 'Goat', 'khasi' => 'Khasi'] as $field => $label)<label class="text-sm font-semibold text-slate-700">{{ $label }}<input name="{{ $field }}" type="number" min="0" placeholder="Enter number of {{ strtolower($label) }}s" class="mt-2 block h-11 w-full rounded-xl border border-slate-300 px-3 text-sm outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100"></label>@endforeach</div></div>
                        <div class="mt-5 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5"><h4 class="m-0 text-sm font-extrabold text-slate-900">Memo No. of Services Provided</h4><div class="mt-3 grid gap-3 md:grid-cols-2 xl:grid-cols-3">@foreach(['membership' => 'Membership', 'deworming' => 'Deworming / Vaccination', 'bringing' => 'Bringing to the Fore', 'fattening' => 'Fattening', 'treatment' => 'Treatment & Other', 'ai' => 'Artificial Insemination (AI)', 'medicine' => 'Medicine'] as $field => $label)<label class="text-sm font-semibold text-slate-700">{{ $label }}<input name="{{ $field }}" placeholder="Enter text of {{ strtolower($label) }}" class="mt-2 block h-11 w-full rounded-xl border border-slate-300 px-3 text-sm outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100"></label>@endforeach</div></div>
                        <p id="quick-beneficiary-error" class="m-0 mt-4 hidden text-sm text-rose-600"></p><div class="sticky bottom-0 mt-5 flex justify-end gap-3 border-t border-slate-200 bg-white px-1 pt-4"><button type="button" id="cancel-quick-beneficiary" onclick="document.getElementById('quick-beneficiary-modal')?.classList.add('hidden'); document.getElementById('quick-beneficiary-modal')?.classList.remove('flex'); document.body.classList.remove('overflow-hidden');" class="rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">Cancel</button><button type="button" id="save-quick-beneficiary" class="rounded-xl bg-emerald-600 px-5 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-emerald-500">Save Customer</button></div>
                    </div>
                </div>
            </div>

            <div class="sticky bottom-0 z-20 border-t border-slate-700 bg-slate-950 px-2 py-2.5 text-white shadow-[0_-8px_24px_rgba(15,23,42,0.25)] sm:px-4 sm:py-3">
                <div class="flex flex-col gap-2.5 lg:flex-row lg:items-center lg:justify-between">
                    <div class="grid grid-cols-3 gap-1.5 lg:w-[460px] lg:grid-cols-[1fr_1fr_1.8fr] lg:gap-2">
                        <div class="flex items-center justify-between rounded-xl border border-slate-700 bg-slate-800 px-2.5 py-2 lg:block lg:px-3">
                            <p class="m-0 text-[9px] font-semibold uppercase tracking-wider text-slate-400">Items</p>
                            <p class="m-0 text-lg font-bold leading-6 text-white" id="sale-summary-items">{{ $visibleRows }}</p>
                        </div>

                        <div class="flex items-center justify-between rounded-xl border border-slate-700 bg-slate-800 px-2.5 py-2 lg:block lg:px-3">
                            <p class="m-0 text-[9px] font-semibold uppercase tracking-wider text-slate-400">Total Qty</p>
                            <p class="m-0 text-lg font-bold leading-6 text-white" id="sale-summary-quantity">{{ number_format($visibleQuantity, 0) }}</p>
                        </div>

                        <div class="flex min-w-0 items-center justify-between gap-2 rounded-xl border border-violet-400/30 bg-gradient-to-r from-violet-600 to-purple-600 px-2.5 py-2 text-white shadow-sm lg:gap-2 lg:px-3">
                            <p class="m-0 shrink-0 text-[10px] font-semibold uppercase tracking-wider text-violet-100">Total BDT</p>
                            <p class="m-0 whitespace-nowrap text-xl font-extrabold leading-7" id="sale-summary-total">0.00</p>
                        </div>
                    </div>

                    <div class="flex w-full flex-nowrap items-center gap-2 lg:w-auto lg:justify-end">
                        <x-warehouse.button
                            href="{{ route('warehouse.sales.index') }}"
                            variant="secondary"
                            size="sm"
                            class="flex w-1/2 min-w-0 flex-1 items-center justify-center gap-2 rounded-lg border border-slate-300 bg-white px-3 py-3 text-slate-700 shadow-sm hover:border-slate-400 hover:bg-slate-50 hover:text-slate-900 lg:w-auto lg:min-w-[140px] lg:flex-none lg:gap-3 lg:px-4"
                        >
                            <svg class="h-4 w-4 shrink-0" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2.1" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12.5 5 7.5 10l5 5" />
                            </svg>
                            Cancel
                        </x-warehouse.button>

                        <x-loading-submit
                            type="submit"
                            id="sale-submit"
                            class="flex w-1/2 min-w-0 flex-1 items-center justify-center gap-2 rounded-lg border border-violet-400/30 bg-violet-600 px-3 py-3 text-white shadow-sm hover:bg-violet-500 lg:w-auto lg:min-w-[170px] lg:flex-none lg:gap-3 lg:px-4"
                            loading-text="Processing Payment..."
                            icon="save"
                        >
                            Payment
                        </x-loading-submit>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <template id="sale-row-template">
        <tr data-sale-row data-product-id="" data-available="0" data-state="ready" class="align-middle">
            <td class="text-left">
                <input type="hidden" value="" data-row-product-id>
                <div class="text-xs font-semibold leading-4 text-slate-900" data-row-name></div>
                <div class="text-xs text-slate-500" data-row-code></div>
                <div class="hidden mt-1 text-xs font-medium text-slate-500" data-row-stock-status></div>
            </td>
            <td class="hidden">
                <span class="inline-flex items-center rounded-full border px-3 py-1.5 text-sm font-bold" data-row-stock-badge>Ready</span>
            </td>
            <td class="text-center">
                <div class="warehouse-qty-control">
                    <button type="button" class="btn btn-danger qty-button qty-minus" data-qty-decrement title="Decrease quantity" aria-label="Decrease quantity"><svg class="h-3 w-3" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" d="M4 10h12" /></svg></button>
                    <input
                        type="number"
                        step="1"
                        min="1"
                        class="form-control qty-input sale-qty-input text-center"
                        data-row-qty
                        value="1"
                        data-max="0"
                        inputmode="numeric"
                        autocomplete="off"
                    >
                    <button type="button" class="btn btn-success qty-button qty-plus" data-qty-increment title="Increase quantity" aria-label="Increase quantity"><svg class="h-3 w-3" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" d="M10 4v12M4 10h12" /></svg></button>
                </div>
            </td>
            <td class="text-center">
                <input
                    type="number"
                    step="0.01"
                    min="0"
                    class="sale-price-input form-control cursor-default bg-white text-center border-0 shadow-none"
                    data-row-price
                    value="0"
                    readonly
                    tabindex="-1"
                >
            </td>
            <td class="text-center" data-row-total>0.00</td>
            <td class="text-center">
                <button type="button" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-rose-200 bg-rose-50 p-0 text-rose-700 transition hover:border-rose-300 hover:bg-rose-100" data-row-remove title="Remove item" aria-label="Remove item">
                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.9" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 3.75h5m-8 2h11m-9.75 0 .5 8.5A1.75 1.75 0 0 0 8 16h4a1.75 1.75 0 0 0 1.75-1.75l.5-8.5M8.5 8.5v4.5m3-4.5v4.5" />
                    </svg>
                </button>
            </td>
        </tr>
    </template>

    <x-slot name="script">
        <script>
            (function () {
                const products = @json($productRows);
                const productMap = products.reduce(function (carry, product) {
                    carry[String(product.id)] = product;
                    return carry;
                }, {});

                const saleForm = document.getElementById('sale-form');
                const saleSubmitOverlay = document.getElementById('sale-submit-overlay');
                const productSearch = document.getElementById('product-search');
                const productSearchClear = document.getElementById('product-search-clear');
                const saleDetailsModal = document.getElementById('sale-details-modal');
                const openSaleDetails = document.getElementById('open-sale-details');
                const customerButtonLabel = openSaleDetails?.querySelector('[data-customer-button-label]');
                const closeSaleDetails = document.getElementById('close-sale-details');
                const cancelSaleDetails = document.getElementById('cancel-sale-details');
                const saveSaleDetails = document.getElementById('save-sale-details');
                const saleDetailsSummary = document.getElementById('sale-details-summary');
                const saleDateInput = document.getElementById('sale_date');
                const customerNameInput = document.getElementById('customer_name');
                const customerPhoneInput = document.getElementById('customer_phone');
                const customerAddressInput = document.getElementById('customer_address');
                const paymentCustomerNameInput = document.getElementById('payment_customer_name');
                const paymentCustomerPhoneInput = document.getElementById('payment_customer_phone');
                const paymentCustomerVillageInput = document.getElementById('payment_customer_village');
                 const paymentCustomerUnionInput = document.getElementById('payment_customer_union');
                 const paymentBeneficiaryNumberInput = document.getElementById('payment_beneficiary_number');
                 const paymentGroupNumberInput = document.getElementById('payment_group_number');
                 const saleBeneficiaryNumberInput = document.getElementById('sale-beneficiary-number');
                 const saleGroupNumberInput = document.getElementById('sale-group-number');
                const paymentSaleDateInput = document.getElementById('payment_sale_date');
                const beneficiaryData = @json($beneficiaryRows);
                const quickBeneficiaryModal = document.getElementById('quick-beneficiary-modal');
                const quickBeneficiaryForm = document.getElementById('quick-beneficiary-form');
                const paymentModal = document.getElementById('payment-modal');
                const paymentMethodInput = document.getElementById('payment_method');
                const paymentModalTotal = document.getElementById('payment-modal-total');
                const paidAmountInput = document.getElementById('paid_amount');
                const creditPaidAmountWrap = document.getElementById('credit-paid-amount-wrap');
                const creditDuePreview = document.getElementById('credit-due-preview');
                const closePaymentModal = document.getElementById('close-payment-modal');
                const cancelPaymentModal = document.getElementById('cancel-payment-modal');
                const confirmPayment = document.getElementById('confirm-payment');
                const productGrid = document.getElementById('product-grid');
                const productGridEmpty = document.getElementById('product-grid-empty');
                const stockFilterButtons = document.querySelectorAll('[data-stock-filter]');
                const cartBody = document.getElementById('sale-cart-body');
                const cartTemplate = document.getElementById('sale-row-template');
                const cartEmptyState = document.getElementById('cart-empty-state');
                const submitButton = document.getElementById('sale-submit');
                const submitDefault = submitButton?.querySelector('[data-submit-default]');
                const submitLoading = submitButton?.querySelector('[data-submit-loading]');
                const saleTotal = document.getElementById('sale-summary-total');
                const saleItems = document.getElementById('sale-summary-items');
                const discountInput = document.getElementById('discount');
                const saleQuantity = document.getElementById('sale-summary-quantity');
                const saleWarnings = document.getElementById('sale-summary-warnings');
                const saleBlocked = document.getElementById('sale-summary-blocked');
                const itemCount = document.getElementById('sale-item-count');
                const warningCount = document.getElementById('sale-warning-count');
                const blockedCount = document.getElementById('sale-blocked-count');
                const productsEmpty = saleForm?.dataset.productsEmpty === '1';
                let activeStockFilter = 'in';
                let paymentConfirmed = false;

                window.addEventListener('warehouse-searchable-select-change', event => {
                    if (event.detail.id !== 'sale-beneficiary') return;
                    const customer = beneficiaryData.find(item => item.id === String(event.detail.value));
                    if (!customer) return;
                    customerNameInput.value = customer.name || '';
                    customerPhoneInput.value = customer.mobile || '';
                    customerAddressInput.value = customer.village || '';
                     if (paymentCustomerVillageInput) paymentCustomerVillageInput.value = customer.village || '';
                     if (paymentCustomerUnionInput) paymentCustomerUnionInput.value = customer.union || '';
                     if (paymentBeneficiaryNumberInput) paymentBeneficiaryNumberInput.value = customer.beneficiary_number || '';
                     if (paymentGroupNumberInput) paymentGroupNumberInput.value = customer.group_number || '';
                     if (saleBeneficiaryNumberInput) saleBeneficiaryNumberInput.value = customer.beneficiary_number || '';
                     if (saleGroupNumberInput) saleGroupNumberInput.value = customer.group_number || '';
                    updateSaleDetailsSummary();
                });
                const showQuickBeneficiary = () => { if (!quickBeneficiaryModal) return; quickBeneficiaryModal.classList.remove('hidden'); quickBeneficiaryModal.classList.add('flex'); document.body.classList.add('overflow-hidden'); };
                document.getElementById('open-quick-beneficiary')?.addEventListener('click', showQuickBeneficiary);
                const closeQuickBeneficiary = () => { quickBeneficiaryModal?.classList.add('hidden'); quickBeneficiaryModal?.classList.remove('flex'); document.body.classList.remove('overflow-hidden'); };
                document.getElementById('close-quick-beneficiary')?.addEventListener('click', closeQuickBeneficiary);
                document.getElementById('cancel-quick-beneficiary')?.addEventListener('click', closeQuickBeneficiary);
                document.getElementById('save-quick-beneficiary')?.addEventListener('click', async () => { const saveButton = document.getElementById('save-quick-beneficiary'); const error = document.getElementById('quick-beneficiary-error'); const requiredFields = quickBeneficiaryForm?.querySelectorAll('[required]') || []; for (const field of requiredFields) { if (!field.reportValidity()) return; } error.classList.add('hidden'); saveButton.disabled = true; saveButton.textContent = 'Saving...'; try { const formData = new FormData(); quickBeneficiaryForm.querySelectorAll('[name]').forEach(field => formData.append(field.name, field.value)); const response = await fetch('{{ route('warehouse.sales.beneficiary.store') }}', { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' }, body: formData }); const data = await response.json(); if (!response.ok) throw new Error(Object.values(data.errors || {}).flat().join(' ') || data.message || 'Unable to save customer.'); const newCustomer = { id: String(data.customer.id), name: data.customer.name || '', mobile: data.customer.mobile || '', beneficiary_number: data.customer.beneficiary_number || '', village: data.customer.village || '' }; beneficiaryData.unshift(newCustomer); window.dispatchEvent(new CustomEvent('warehouse-searchable-select-options', { detail: { id: 'sale-beneficiary', selected: newCustomer.id, options: beneficiaryData.map(customer => ({ value: customer.id, label: customer.name, description: customer.mobile || customer.beneficiary_number || '' })) } })); customerNameInput.value = newCustomer.name; customerPhoneInput.value = newCustomer.mobile; customerAddressInput.value = newCustomer.village; updateSaleDetailsSummary(); closeQuickBeneficiary(); quickBeneficiaryForm.querySelectorAll('input, select').forEach(field => { field.value = ''; }); } catch (exception) { error.textContent = exception.message; error.classList.remove('hidden'); } finally { saveButton.disabled = false; saveButton.textContent = 'Save Customer'; } });

                function showSaleDetailsModal() {
                    saleDetailsModal.classList.remove('hidden');
                    saleDetailsModal.classList.add('flex');
                    document.body.classList.add('overflow-hidden');
                    window.setTimeout(function () { customerNameInput.focus(); }, 50);
                }

                function hideSaleDetailsModal() {
                    saleDetailsModal.classList.add('hidden');
                    saleDetailsModal.classList.remove('flex');
                    document.body.classList.remove('overflow-hidden');
                }

                function showPaymentModal() {
                    paymentModal.classList.remove('hidden');
                    paymentModal.classList.add('flex');
                    document.body.classList.add('overflow-hidden');
                    window.setTimeout(function () {
                        if (paymentCustomerNameInput) paymentCustomerNameInput.value = customerNameInput.value;
                        if (paymentCustomerPhoneInput) paymentCustomerPhoneInput.value = customerPhoneInput.value;
                        if (paymentSaleDateInput) paymentSaleDateInput.value = saleDateInput.value;
                        if (paymentCustomerVillageInput) paymentCustomerVillageInput.value = customerAddressInput.value;
                        (paymentCustomerNameInput?.value.trim() ? paymentMethodInput : paymentCustomerNameInput)?.focus();
                    }, 50);
                }

                function resetPaymentButton() {
                    if (submitButton) {
                        submitButton.disabled = false;
                        submitButton.dataset.loading = 'false';
                        submitButton.removeAttribute('aria-busy');
                    }
                    if (submitDefault) {
                        submitDefault.classList.remove('hidden');
                        submitDefault.style.display = 'inline-flex';
                    }
                    if (submitLoading) {
                        submitLoading.classList.add('hidden');
                        submitLoading.classList.remove('inline-flex');
                        submitLoading.style.display = 'none';
                    }
                }

                function showSaleSubmitOverlay() {
                    saleSubmitOverlay?.classList.add('is-loading');
                    saleSubmitOverlay?.setAttribute('aria-hidden', 'false');
                    document.body.classList.add('overflow-hidden');
                    // Keep form inputs enabled so their values are included in the
                    // POST payload. The fullscreen overlay already blocks edits;
                    // only actionable buttons and links need to be locked.
                    document.querySelectorAll('button, a').forEach(function (element) {
                        if (element.closest('#sale-submit-overlay')) return;
                        element.setAttribute('data-sale-locked', 'true');
                        element.setAttribute('tabindex', '-1');
                        if ('disabled' in element) element.disabled = true;
                    });
                }

                function hidePaymentModal(resetButton = true) {
                    paymentModal.classList.add('hidden');
                    paymentModal.classList.remove('flex');
                    paymentMethodInput.setCustomValidity('');
                    document.body.classList.remove('overflow-hidden');
                    if (resetButton) resetPaymentButton();
                }

                function updateSaleDetailsSummary() {
                    const customer = customerNameInput.value.trim();
                    const phone = customerPhoneInput.value.trim();
                    saleDetailsSummary.textContent = customer
                        ? `${customer}${phone ? ' · ' + phone : ''}`
                        : 'Customer details not added';
                    if (customerButtonLabel) customerButtonLabel.textContent = customer ? 'Edit Customer' : 'Add Customer';
                }

                openSaleDetails?.addEventListener('click', showSaleDetailsModal);
                closeSaleDetails?.addEventListener('click', hideSaleDetailsModal);
                cancelSaleDetails?.addEventListener('click', hideSaleDetailsModal);
                saleDetailsModal?.addEventListener('click', function (event) {
                    if (event.target === saleDetailsModal) hideSaleDetailsModal();
                });
                saveSaleDetails?.addEventListener('click', function () {
                    if (!saleDateInput?.reportValidity() || !customerNameInput?.reportValidity() || !customerPhoneInput?.reportValidity()) return;
                    updateSaleDetailsSummary();
                    hideSaleDetailsModal();
                });
                submitButton?.addEventListener('click', function (event) {
                    if (!saleDateInput.checkValidity() || !customerNameInput.checkValidity() || !customerPhoneInput.checkValidity()) {
                        event.preventDefault();
                        resetPaymentButton();
                        if (!customerNameInput.value.trim() || !customerPhoneInput.value.trim()) showPaymentModal();
                        else showSaleDetailsModal();
                        window.setTimeout(function () {
                            if (!saleDateInput.checkValidity()) saleDateInput.reportValidity();
                            else if (!customerNameInput.checkValidity()) customerNameInput.reportValidity();
                            else customerPhoneInput.reportValidity();
                        }, 75);
                    }
                });
                closePaymentModal.addEventListener('click', hidePaymentModal);
                cancelPaymentModal.addEventListener('click', hidePaymentModal);
                function updateCreditPayment() {
                    const isCredit = paymentMethodInput.value === 'Credit';
                    creditPaidAmountWrap.classList.toggle('hidden', !isCredit);
                    const total = parseNumber(paymentModalTotal.textContent);
                    const paid = Math.max(0, parseNumber(paidAmountInput.value));
                    paidAmountInput.max = formatMoney(total);
                    creditDuePreview.textContent = formatMoney(Math.max(0, total - paid));
                }
                paymentMethodInput.addEventListener('change', function () {
                    paymentMethodInput.setCustomValidity('');
                    updateCreditPayment();
                });
                paidAmountInput.addEventListener('input', updateCreditPayment);
                paymentCustomerNameInput?.addEventListener('input', () => { customerNameInput.value = paymentCustomerNameInput.value; });
                paymentCustomerPhoneInput?.addEventListener('input', () => { customerPhoneInput.value = paymentCustomerPhoneInput.value; });
                paymentCustomerVillageInput?.addEventListener('input', () => { customerAddressInput.value = paymentCustomerVillageInput.value; });
                 paymentCustomerUnionInput?.addEventListener('input', () => { customerAddressInput.value = [paymentCustomerVillageInput?.value, paymentCustomerUnionInput.value].filter(Boolean).join(', '); });
                 paymentBeneficiaryNumberInput?.addEventListener('change', () => { if (saleBeneficiaryNumberInput) saleBeneficiaryNumberInput.value = paymentBeneficiaryNumberInput.value; });
                 paymentGroupNumberInput?.addEventListener('change', () => { if (saleGroupNumberInput) saleGroupNumberInput.value = paymentGroupNumberInput.value; });
                paymentSaleDateInput?.addEventListener('change', () => { saleDateInput.value = paymentSaleDateInput.value; });
                confirmPayment.addEventListener('click', function () {
                    if (!paymentSaleDateInput?.value) {
                        paymentSaleDateInput?.reportValidity();
                        return;
                    }
                    saleDateInput.value = paymentSaleDateInput.value;
                     const selectedBeneficiary = document.getElementById('sale-beneficiary')?.value;
                     if (!selectedBeneficiary && (!paymentBeneficiaryNumberInput?.value || !paymentGroupNumberInput?.value)) {
                         if (paymentBeneficiaryNumberInput) paymentBeneficiaryNumberInput.setCustomValidity(paymentBeneficiaryNumberInput.value ? '' : 'Select a beneficiary number.');
                         if (paymentGroupNumberInput) paymentGroupNumberInput.setCustomValidity(paymentGroupNumberInput.value ? '' : 'Select a beneficiary group number.');
                         (paymentBeneficiaryNumberInput?.value ? paymentGroupNumberInput : paymentBeneficiaryNumberInput)?.reportValidity();
                         return;
                     }
                     paymentBeneficiaryNumberInput?.setCustomValidity('');
                     paymentGroupNumberInput?.setCustomValidity('');
                     if (!customerNameInput.value.trim() || !customerPhoneInput.value.trim()) {
                        paymentCustomerNameInput?.reportValidity();
                        paymentCustomerPhoneInput?.reportValidity();
                        return;
                    }
                    if (!paymentMethodInput.value) {
                        paymentMethodInput.setCustomValidity('Please select a payment method.');
                        paymentMethodInput.reportValidity();
                        return;
                    }
                    if (paymentMethodInput.value === 'Credit' && parseNumber(paidAmountInput.value) > parseNumber(paymentModalTotal.textContent)) {
                        paidAmountInput.setCustomValidity('Paid amount cannot exceed the sale total.');
                        paidAmountInput.reportValidity();
                        return;
                    }

                    paymentMethodInput.setCustomValidity('');
                    paidAmountInput.setCustomValidity('');
                    paymentConfirmed = true;
                    hidePaymentModal(false);
                    saleForm.requestSubmit();
                });
                saleForm.addEventListener('submit', function (event) {
                    if (!saleDateInput.checkValidity() || !customerNameInput.checkValidity() || !customerPhoneInput.checkValidity()) {
                        event.preventDefault();
                        if (!customerNameInput.value.trim() || !customerPhoneInput.value.trim()) showPaymentModal();
                        else showSaleDetailsModal();
                        window.setTimeout(function () {
                            if (!saleDateInput.checkValidity()) saleDateInput.reportValidity();
                            else if (!customerNameInput.checkValidity()) customerNameInput.reportValidity();
                            else customerPhoneInput.reportValidity();
                        }, 75);

                        return;
                    }

                    if (!paymentConfirmed) {
                        event.preventDefault();
                        showPaymentModal();
                        return;
                    }

                    if (submitButton) {
                        submitButton.disabled = true;
                        submitButton.setAttribute('aria-busy', 'true');
                    }
                    showSaleSubmitOverlay();
                    if (submitDefault) submitDefault.classList.add('hidden');
                    if (submitLoading) {
                        submitLoading.classList.remove('hidden');
                        submitLoading.classList.add('inline-flex');
                    }
                });

                function parseNumber(value) {
                    const number = parseFloat(value);
                    return Number.isNaN(number) ? 0 : number;
                }

                function formatMoney(value) {
                    return Number(value || 0).toFixed(2);
                }

                function formatQuantity(value) {
                    return String(Number(Number(value || 0).toFixed(2)));
                }

                function syncNames() {
                    cartBody.querySelectorAll('[data-sale-row]').forEach(function (row, index) {
                        const productId = row.querySelector('[data-row-product-id]');
                        const qty = row.querySelector('[data-row-qty]');
                        const price = row.querySelector('[data-row-price]');

                        if (productId) {
                            productId.name = `items[${index}][product_id]`;
                        }

                        if (qty) {
                            qty.name = `items[${index}][quantity]`;
                        }

                        if (price) {
                            price.name = `items[${index}][sale_price]`;
                        }
                    });
                }

                function applyRowState(row, product, quantity) {
                    const stockBadge = row.querySelector('[data-row-stock-badge]');
                    const stockStatus = row.querySelector('[data-row-stock-status]');

                    if (!stockBadge || !stockStatus || !product) {
                        return { blocked: false, warning: false };
                    }

                    let blocked = false;
                    let warning = false;
                    let badgeClass = 'border-emerald-200 bg-emerald-50 text-emerald-700';
                    let badgeText = 'Ready';
                    let message = `${formatMoney(product.available)} available in this warehouse.`;
                    let messageClass = 'text-xs font-medium text-slate-500';

                    row.classList.remove('border-slate-200', 'border-amber-200', 'border-rose-200', 'bg-slate-50/80', 'bg-amber-50/80', 'bg-rose-50/80');
                    row.classList.add('border-slate-200', 'bg-slate-50/80');

                    if (product.state === 'blocked') {
                        blocked = true;
                        badgeClass = 'border-rose-200 bg-white text-rose-700';
                        badgeText = 'Out Of Stock';
                        message = 'Out of stock. This product cannot be sold.';
                        messageClass = 'text-xs font-semibold text-rose-700';
                        row.classList.remove('border-slate-200', 'bg-slate-50/80');
                        row.classList.add('border-rose-200', 'bg-rose-50/80');
                    } else if (product.state === 'low') {
                        warning = true;
                        badgeClass = 'border-amber-200 bg-white text-amber-700';
                        badgeText = 'Low';
                        message = `Low stock: only ${formatMoney(product.available)} left.`;
                        messageClass = 'text-xs font-semibold text-amber-700';
                        row.classList.remove('border-slate-200', 'bg-slate-50/80');
                        row.classList.add('border-amber-200', 'bg-amber-50/80');
                    }

                    if (quantity > product.available && product.available > 0) {
                        blocked = true;
                        warning = true;
                        badgeClass = 'border-rose-200 bg-white text-rose-700';
                        badgeText = 'Over limit';
                        message = `Requested quantity exceeds stock by ${formatMoney(quantity - product.available)}.`;
                        messageClass = 'text-xs font-semibold text-rose-700';
                        row.classList.remove('border-slate-200', 'border-amber-200', 'bg-slate-50/80', 'bg-amber-50/80');
                        row.classList.add('border-rose-200', 'bg-rose-50/80');
                    }

                    if (quantity <= 0) {
                        blocked = true;
                        badgeClass = 'border-rose-200 bg-white text-rose-700';
                        badgeText = 'Invalid';
                        message = 'Quantity must be greater than zero.';
                        messageClass = 'text-xs font-semibold text-rose-700';
                        row.classList.remove('border-slate-200', 'border-amber-200', 'bg-slate-50/80', 'bg-amber-50/80');
                        row.classList.add('border-rose-200', 'bg-rose-50/80');
                    }

                    stockBadge.className = `inline-flex items-center rounded-full border px-3 py-1.5 text-sm font-bold ${badgeClass}`;
                    stockBadge.textContent = badgeText;
                    stockStatus.className = `hidden mt-1 ${messageClass}`;
                    stockStatus.textContent = message;

                    return { blocked, warning };
                }

                function updateRow(row) {
                    const productIdInput = row.querySelector('[data-row-product-id]');
                    const qtyInput = row.querySelector('[data-row-qty]');
                    const priceInput = row.querySelector('[data-row-price]');
                    const totalCell = row.querySelector('[data-row-total]');

                    if (!productIdInput || !qtyInput || !priceInput || !totalCell) {
                        return { blocked: false, warning: false, total: 0 };
                    }

                    const product = productMap[String(productIdInput.value)];

                    if (!product) {
                        totalCell.textContent = formatMoney(0);
                        return { blocked: true, warning: false, total: 0 };
                    }

                    const quantity = parseNumber(qtyInput.value);
                    const price = parseNumber(priceInput.value);
                    const total = quantity * price;

                    totalCell.textContent = formatMoney(total);

                    const stockState = applyRowState(row, product, quantity);

                    if (quantity > product.available && product.available > 0) {
                        qtyInput.setAttribute('max', product.available);
                    } else if (product.available > 0) {
                        qtyInput.setAttribute('max', product.available);
                    } else {
                        qtyInput.setAttribute('max', 0);
                    }

                    return {
                        blocked: stockState.blocked,
                        warning: stockState.warning,
                        total,
                    };
                }

                function updateSummary() {
                    let total = 0;
                    let totalQuantity = 0;
                    let warnings = 0;
                    let blocked = 0;
                    let count = 0;

                    cartBody.querySelectorAll('[data-sale-row]').forEach(function (row) {
                        const result = updateRow(row);
                        const qtyInput = row.querySelector('[data-row-qty]');

                        total += result.total;
                        totalQuantity += parseNumber(qtyInput?.value);
                        count += 1;

                        if (result.warning) warnings += 1;
                        if (result.blocked) blocked += 1;
                    });

                    const discount = Math.min(parseNumber(discountInput?.value), total);
                    const finalTotal = Math.max(0, total - discount);
                    if (saleTotal) saleTotal.textContent = formatMoney(finalTotal);
                    if (paymentModalTotal) paymentModalTotal.textContent = formatMoney(finalTotal);
                    updateCreditPayment();
                    if (saleItems) saleItems.textContent = String(count);
                    if (saleQuantity) saleQuantity.textContent = formatQuantity(totalQuantity);
                    if (saleWarnings) saleWarnings.textContent = String(warnings);
                    if (saleBlocked) saleBlocked.textContent = String(blocked);
                    if (itemCount) itemCount.textContent = String(count);
                    if (warningCount) warningCount.textContent = String(warnings);
                    if (blockedCount) blockedCount.textContent = String(blocked);
                    if (submitButton) submitButton.disabled = productsEmpty || count === 0 || blocked > 0;

                    if (cartEmptyState) {
                        cartEmptyState.classList.toggle('hidden', count > 0);
                    }
                }

                discountInput?.addEventListener('input', updateSummary);

                function bindRow(row) {
                    const productIdInput = row.querySelector('[data-row-product-id]');
                    const qtyInput = row.querySelector('[data-row-qty]');
                    const priceInput = row.querySelector('[data-row-price]');
                    const removeButton = row.querySelector('[data-row-remove]');
                    const decrementButton = row.querySelector('[data-qty-decrement]');
                    const incrementButton = row.querySelector('[data-qty-increment]');

                    if (qtyInput) {
                        qtyInput.addEventListener('input', function () {
                            updateRow(row);
                            updateSummary();
                        });
                    }

                    if (priceInput) {
                        priceInput.addEventListener('input', function () {
                            updateRow(row);
                            updateSummary();
                        });
                    }

                    if (decrementButton && qtyInput) {
                        decrementButton.addEventListener('click', function () {
                            qtyInput.value = Math.max(0, parseNumber(qtyInput.value) - 1);
                            updateRow(row);
                            updateSummary();
                        });
                    }

                    if (incrementButton && qtyInput && productIdInput) {
                        incrementButton.addEventListener('click', function () {
                            const product = productMap[String(productIdInput.value)];
                            const next = parseNumber(qtyInput.value) + 1;
                            qtyInput.value = product && product.available > 0 && next > product.available ? product.available : next;
                            updateRow(row);
                            updateSummary();
                        });
                    }

                    if (removeButton) {
                        removeButton.addEventListener('click', function () {
                            row.remove();
                            syncNames();
                            updateSummary();
                        });
                    }

                    updateRow(row);
                }

                function createRow(product, quantity = 1) {
                    if (!cartTemplate || !cartBody || !product) {
                        return;
                    }

                    const existing = cartBody.querySelector(`[data-sale-row][data-product-id="${product.id}"]`);

                    if (existing) {
                        const qtyInput = existing.querySelector('[data-row-qty]');
                        if (qtyInput) {
                            const next = parseNumber(qtyInput.value) + quantity;
                            qtyInput.value = product.available > 0 && next > product.available ? product.available : next;
                        }
                        updateRow(existing);
                        updateSummary();
                        existing.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        return;
                    }

                    const row = cartTemplate.content.firstElementChild.cloneNode(true);
                    row.dataset.productId = String(product.id);
                    row.dataset.available = product.available;
                    row.dataset.state = product.state;

                    row.querySelector('[data-row-product-id]').value = product.id;
                    row.querySelector('[data-row-name]').textContent = product.name;
                    row.querySelector('[data-row-code]').textContent = product.code || '';
                    row.querySelector('[data-row-stock-badge]').textContent = product.state === 'blocked' ? 'Out Of Stock' : (product.state === 'low' ? 'Low' : 'Ready');
                    row.querySelector('[data-row-stock-status]').textContent = product.state === 'blocked'
                        ? 'Out of stock. This product cannot be sold.'
                        : (product.state === 'low'
                            ? `Low stock: only ${formatMoney(product.available)} left.`
                            : `${formatMoney(product.available)} available in this warehouse.`);
                    row.querySelector('[data-row-qty]').value = quantity;
                    row.querySelector('[data-row-price]').value = formatMoney(product.selling_price);
                    row.querySelector('[data-row-qty]').setAttribute('max', product.available > 0 ? product.available : 0);

                    cartBody.appendChild(row);
                    bindRow(row);
                    syncNames();
                    updateSummary();
                    row.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }

                function filterProducts() {
                    if (!productGrid) {
                        return;
                    }

                    const term = (productSearch?.value || '').trim().toLowerCase();
                    let visible = 0;

                    productGrid.querySelectorAll('[data-add-product]').forEach(function (card) {
                        const stockState = card.dataset.stockState || 'ready';
                        const matchesSearch = term === '' || (card.dataset.searchKey || '').includes(term);
                        const matchesFilter = activeStockFilter === 'all'
                            || (activeStockFilter === 'in' && stockState !== 'blocked')
                            || stockState === activeStockFilter;
                        const show = matchesSearch && matchesFilter;

                        // Product cards already use `display:flex`; toggling Tailwind's
                        // `hidden` class can be overridden by that utility. Use the
                        // element's display value so stock filtering is deterministic.
                        card.style.display = show ? '' : 'none';
                        if (show) visible += 1;
                    });

                    if (productGridEmpty) {
                        productGridEmpty.classList.toggle('hidden', visible !== 0);
                    }

                    if (productSearchClear) {
                        productSearchClear.classList.toggle('hidden', term === '');
                    }
                }

                productSearch?.addEventListener('input', filterProducts);
                productSearchClear?.addEventListener('click', function () {
                    productSearch.value = '';
                    filterProducts();
                    productSearch.focus();
                });

                stockFilterButtons.forEach(function (button) {
                    button.addEventListener('click', function () {
                        activeStockFilter = button.dataset.stockFilter || 'all';

                        stockFilterButtons.forEach(function (item) {
                            const active = item.dataset.stockFilter === activeStockFilter;
                            item.classList.toggle('bg-slate-900', active);
                            item.classList.toggle('text-white', active);
                            item.classList.toggle('bg-white', !active);
                            item.classList.toggle('text-slate-700', !active);
                        });

                        filterProducts();
                    });
                });

                document.querySelectorAll('[data-add-product]').forEach(function (card) {
                    card.addEventListener('click', function () {
                        const product = productMap[String(card.dataset.productId)];
                        createRow(product, 1);
                    });
                });

                if (cartBody) {
                    cartBody.querySelectorAll('[data-sale-row]').forEach(bindRow);
                }

                syncNames();
                updateSummary();
                filterProducts();
                updateSaleDetailsSummary();
                updateCreditPayment();

                document.addEventListener('keydown', function (event) {
                    if (event.key === 'Escape' && !saleDetailsModal.classList.contains('hidden')) {
                        hideSaleDetailsModal();
                    } else if (event.key === 'Escape' && !paymentModal.classList.contains('hidden')) {
                        hidePaymentModal();
                    }
                });

                @if ($errors->has('sale_date') || $errors->has('customer_name') || $errors->has('customer_phone') || $errors->has('customer_address'))
                    showSaleDetailsModal();
                @endif

                @if ($errors->has('payment_method') || $errors->has('paid_amount'))
                    showPaymentModal();
                @endif
            })();
        </script>
    </x-slot>
</x-warehouse-layout>

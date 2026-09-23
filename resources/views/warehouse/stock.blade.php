@extends('layouts.dashboard')
@section('title', 'Stock Summary')

@section('content')
    @php
        $formatMoney = fn ($value) => rtrim(rtrim(number_format((float) $value, 2, '.', ''), '0'), '.') ?: '0';
        $exportQuery = request()->only(['product_name', 'warehouse_id', 'salesman_id', 'per_page']);
    @endphp

    <div class="row">
        <div class="col-12">
            <div class="card-box table-responsive mt-4 stock-report-card">
                <div class="stock-report-header">
                    <div>
                        <h4 class="m-t-0 header-title mb-1"><b>Stock Summary</b></h4>
                        <div class="text-muted">Area Office stock summary with quick status indicators.</div>
                    </div>
                    <div class="stock-export-toolbar">
                        <div class="d-flex flex-wrap stock-action-buttons">
                            <a href="{{ route('warehouses.stock.print', $exportQuery) }}" target="_blank" rel="noopener" class="btn btn-outline-dark d-inline-flex align-items-center gap-2 shadow-sm">
                                <svg class="flex-shrink-0" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 8V4.75h10V8M6.5 17.25H5A1.75 1.75 0 0 1 3.25 15.5V10.75A1.75 1.75 0 0 1 5 9h14a1.75 1.75 0 0 1 1.75 1.75v4.75A1.75 1.75 0 0 1 19 17.25h-1.5M8 14h8M8 18.25h8v-5.5H8v5.5Z" />
                                </svg>
                                <span>Print</span>
                            </a>
                            <a href="{{ route('warehouses.stock.pdf', $exportQuery) }}" class="btn btn-outline-danger d-inline-flex align-items-center gap-2 shadow-sm">
                                <svg class="flex-shrink-0" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14 3.75H7.5A1.75 1.75 0 0 0 5.75 5.5v13a1.75 1.75 0 0 0 1.75 1.75h9a1.75 1.75 0 0 0 1.75-1.75V8L14 3.75Z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14 3.75V8h4.25" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.75 12h6.5M8.75 15h6.5" />
                                </svg>
                                <span>PDF</span>
                            </a>
                            <a href="{{ route('warehouses.stock.excel', $exportQuery) }}" class="btn btn-success d-inline-flex align-items-center gap-2 shadow-sm">
                                <svg class="flex-shrink-0" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14 3.75H7.5A1.75 1.75 0 0 0 5.75 5.5v13A1.75 1.75 0 0 0 7.5 20.25h9A1.75 1.75 0 0 0 18.25 18.5V8L14 3.75Z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14 3.75V8h4.25" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 11v5m0 0-2.25-2.25M12 16l2.25-2.25" />
                                </svg>
                                <span>Excel</span>
                            </a>
                            <button type="button" id="open-stock-filters" class="d-none" aria-controls="stock-filter-drawer" aria-expanded="true">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 5h16l-6.5 7.25v5.25l-3 1.5v-6.75L4 5Z" />
                                </svg>
                                <span>Filter</span>
                                @if(request()->filled('product_name') || request()->filled('warehouse_id') || request()->filled('salesman_id') || (string) request('per_page', 'all') !== 'all')
                                    <span class="stock-filter-active-dot" title="Filters are active"></span>
                                @endif
                            </button>
                        </div>
                    </div>
                </div>

                <section id="stock-filter-drawer" class="stock-filter-drawer is-open" aria-hidden="false" aria-labelledby="stock-filter-title">
                    <form method="GET" action="{{ route('warehouses.stock') }}" id="stock-filter-form" class="stock-filter-panel">
                        <div class="form-group">
                            <label>Show</label>
                            <select name="per_page" class="form-control">
                                <option value="all" @selected((string) $perPage === 'all')>All</option>
                                <option value="25" @selected((string) $perPage === '25')>25</option>
                                <option value="50" @selected((string) $perPage === '50')>50</option>
                                <option value="100" @selected((string) $perPage === '100')>100</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Product</label>
                            <input type="text" name="product_name" value="{{ $search }}" class="form-control" placeholder="Enter Product Name">
                        </div>
                        <div class="form-group">

                            <label>Area Office</label>
                            <select name="warehouse_id" class="form-control select2">
                                <option value="">All Area Offices</option>
                                @foreach($warehouseOptions as $warehouse)
                                    @php
                                        $warehouseName = preg_replace('/^(?:id:\s*)?id\)\>\s*/i', '', trim((string) $warehouse->name)) ?? $warehouse->name;
                                    @endphp
                                    <option value="{{ $warehouse->id }}" @selected((string) request('warehouse_id') === (string) $warehouse->id)>{{ $warehouseName }}{{ $warehouse->code ? ' - ' . $warehouse->code : '' }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>LSP</label>
                            <select name="salesman_id" class="form-control select2">
                                <option value="">All LSPs</option>
                                @foreach($salesmanOptions as $salesman)
                                    @php
                                        $salesmanName = preg_replace('/^(?:id:\s*)?id\)\>\s*/i', '', trim((string) $salesman->name)) ?? $salesman->name;
                                        $salesmanWarehouseName = $salesman->warehouse?->name
                                            ? (preg_replace('/^(?:id:\s*)?id\)\>\s*/i', '', trim((string) $salesman->warehouse->name)) ?? $salesman->warehouse->name)
                                            : null;
                                    @endphp
                                    <option value="{{ $salesman->id }}" @selected((string) request('salesman_id') === (string) $salesman->id)>{{ $salesmanName }}{{ $salesmanWarehouseName ? ' - ' . $salesmanWarehouseName : '' }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="stock-filter-actions">
                            <a href="{{ route('warehouses.stock') }}" class="btn stock-filter-reset">Reset</a>
                            <x-loading-submit type="submit" class="btn stock-filter-search" id="stock-filter-submit" loading-text="Applying...">Apply Filters</x-loading-submit>
                        </div>
                    </form>
                </section>

                <div class="table-rep-plugin">
                    <div class="table-responsive" id="tablefixed">
                        <table class="table table-hover mt-0 stock-report-table" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <th style="width: 5%;">SL</th>
                                    <th style="width: 22%;">Product</th>
                                    <th style="width: 9%;">Purchase Price</th>
                                    <th style="width: 9%;">Selling Price</th>
                                    <th>Area Office Received</th>
                                    <th>LSP Assign</th>
                                    {{-- <th>LSP Return</th> --}}
                                    <th>LSP Sale</th>
                                    <th>Area Office Stock</th>
                                    <th>LSP Stock</th>
                                    <th>Total Remaining</th>
                                    <th>Total Selling Price</th>
                                    <th>Total Purchase Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($products as $product)
                                    @php
                                        $stockQty = (float) ($product->total_remaining_qty ?? 0);
                                        $stockClass = $stockQty > 0
                                            ? 'stock-pill stock-pill-in'
                                            : ($stockQty < 0 ? 'stock-pill stock-pill-out' : 'stock-pill stock-pill-low');
                                        $rowClass = $stockQty < 0
                                            ? 'stock-row stock-row-out'
                                            : ($stockQty === 0 ? 'stock-row stock-row-low' : 'stock-row stock-row-in');
                                    @endphp
                                    <tr class="{{ $rowClass }}">
                                        <td>{{ $products->firstItem() + $loop->index }}</td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <img class="stock-product-image" src="{{ checkImage($product->image) }}" alt="{{ $product->product_name }}">
                                                <div>
                                                    <strong class="d-block text-slate-900">{{ $product->product_name }}</strong>
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ $formatMoney($product->purchase_price ?? 0) }}</td>
                                        <td>{{ $formatMoney($product->selling_price ?? 0) }}</td>
                                        <td><span class="qty-pill qty-pill-in">{{ number_format((float) ($product->received_qty ?? 0), 2) }}</span></td>
                                        <td><span class="qty-pill qty-pill-assign">{{ number_format((float) ($product->assigned_qty ?? 0), 2) }}</span></td>
                                        {{-- <td><span class="qty-pill qty-pill-return">{{ number_format((float) ($product->returned_qty ?? 0), 2) }}</span></td> --}}
                                        <td><span class="qty-pill qty-pill-sale">{{ number_format((float) ($product->lsp_sale_qty ?? 0), 2) }}</span></td>
                                        <td><span class="stock-pill stock-pill-in">{{ number_format((float) ($product->office_stock_qty ?? 0), 2) }}</span></td>
                                        <td><span class="stock-pill stock-pill-low">{{ number_format((float) ($product->lsp_stock_qty ?? 0), 2) }}</span></td>
                                        <td><span class="{{ $stockClass }}">{{ number_format($stockQty, 2) }}</span></td>
                                        <td>{{ $formatMoney($product->stock_sale_price ?? 0) }}</td>
                                        <td>{{ $formatMoney($product->stock_purchase_price ?? 0) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="13" class="text-center text-muted">No products found for the selected filters.</td>
                                    </tr>
                                @endforelse
                                <tr class="stock-summary-row font-weight-bold">
                                    <th colspan="4" class="text-left stock-summary-label">Total</th>
                                    <th>{{ number_format((float) ($summary->received_qty ?? 0), 2) }}</th>
                                    <th>{{ number_format((float) ($summary->assigned_qty ?? 0), 2) }}</th>
                                    {{-- <th>{{ number_format((float) ($summary->returned_qty ?? 0), 2) }}</th> --}}
                                    <th>{{ number_format((float) ($summary->lsp_sale_qty ?? 0), 2) }}</th>
                                    <th>{{ number_format((float) ($summary->office_stock_qty ?? 0), 2) }}</th>
                                    <th>{{ number_format((float) ($summary->lsp_stock_qty ?? 0), 2) }}</th>
                                    <th>{{ number_format((float) ($summary->total_remaining_qty ?? 0), 2) }}</th>
                                    <th>{{ $formatMoney($summary->stock_sale_price ?? 0) }}</th>
                                    <th>{{ $formatMoney($summary->stock_purchase_price ?? 0) }}</th>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div>
                    {{ $products->appends(request()->input())->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(function () {
            $('.stock-filter-panel .select2').select2({
                width: '100%',
                dropdownParent: $('#stock-filter-drawer'),
                templateResult: function (item) {
                    if (!item.id) {
                        return item.text;
                    }

                    return String(item.text ?? '').replace(/^(?:id:\s*)?id\)\>\s*/i, '');
                },
                templateSelection: function (item) {
                    return String(item.text ?? '').replace(/^(?:id:\s*)?id\)\>\s*/i, '');
                },
            });
            $('select[name="warehouse_id"]').val(@json((string) request('warehouse_id'))).trigger('change.select2');
            $('select[name="salesman_id"]').val(@json((string) request('salesman_id'))).trigger('change.select2');

            document.getElementById('stock-filter-form')?.addEventListener('submit', function () {
                const button = document.getElementById('stock-filter-submit');
                if (!button || button.dataset.loading === 'true') return;
                button.dataset.loading = 'true';
                button.disabled = true;
                button.setAttribute('aria-busy', 'true');
                const defaultLabel = button.querySelector('[data-submit-default]');
                const loadingLabel = button.querySelector('[data-submit-loading]');
                if (defaultLabel) defaultLabel.style.display = 'none';
                if (loadingLabel) loadingLabel.style.display = 'inline-flex';
            });

        });
    </script>
@endsection

@push('css')
    <style>
        .stock-report-card {
            border-top: 3px solid #157c34;
            background: linear-gradient(180deg, #ffffff 0%, #fbfffd 100%);
            overflow: visible !important;
        }

        .stock-report-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 20px;
            margin-bottom: 18px;
            padding: 18px 20px;
            border: 1px solid #e6eef3;
            border-radius: 14px;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.94) 0%, rgba(248, 251, 255, 0.96) 100%);
            box-shadow: 0 12px 30px rgba(15, 23, 42, 0.05);
        }

        .stock-export-toolbar {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 14px;
            flex-wrap: wrap;
        }

        .stock-action-buttons {
            gap: 10px;
            justify-content: flex-end;
        }

        .stock-export-kicker {
            color: #94a3b8;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: .18em;
            text-transform: uppercase;
        }

        .stock-filter-panel {
            display: grid;
            grid-template-columns: minmax(110px, .7fr) minmax(180px, 1.3fr) minmax(190px, 1.4fr) minmax(190px, 1.4fr) auto;
            gap: 14px;
            align-items: end;
            padding: 16px 20px 18px;
            border: 0;
            background: #fff;
            position: relative;
            overflow: visible;
        }
        .stock-filter-panel .form-group { margin: 0; }
        .stock-filter-panel label { margin-bottom: 6px; color: #475569; font-size: 12px; font-weight: 700; }

        .stock-filter-active-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #facc15;
            box-shadow: 0 0 0 3px rgba(250, 204, 21, .2);
        }

        .stock-filter-drawer {
            display: none;
            width: 100%;
            margin: -6px 0 18px;
            overflow: visible;
            background: #fff;
            border: 1px solid #dbe7ef;
            border-radius: 12px;
            box-shadow: 0 12px 26px rgba(15, 23, 42, .07);
        }

        .stock-filter-drawer.is-open { display: block; }

        .stock-filter-drawer-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 14px 20px;
            border-bottom: 1px solid #e2e8f0;
            background: linear-gradient(135deg, #f0fdfa, #f8fafc);
        }

        .stock-filter-close {
            display: inline-flex;
            width: 38px;
            height: 38px;
            align-items: center;
            justify-content: center;
            border: 1px solid #cbd5e1;
            border-radius: 10px;
            background: #fff;
            color: #475569;
            font-size: 26px;
            line-height: 1;
            cursor: pointer;
        }

        @media (max-width: 1100px) {
            .stock-filter-panel { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .stock-filter-actions { grid-column: span 2; }
        }
        @media (max-width: 575px) {
            .stock-filter-panel { grid-template-columns: 1fr; }
            .stock-filter-actions { grid-column: auto; }
        }

        .stock-report-table thead th {
            position: sticky;
            top: 0;
            z-index: 2;
            background: #0f766e;
            color: #fff;
            border-color: #0f766e !important;
            font-size: 12px;
            letter-spacing: .02em;
            text-transform: uppercase;
        }

        .stock-report-table th,
        .stock-report-table td {
            border: 1px solid #d8e2ea !important;
        }

        .stock-summary-row th,
        .stock-summary-row td {
            background: #000 !important;
            color: #fff !important;
            font-weight: 800 !important;
            text-transform: none;
            border-color: #000 !important;
        }

        .stock-summary-label {
            text-align: left !important;
            font-size: 13px;
            letter-spacing: .02em;
        }

        .stock-filter-panel .select2-container--open {
            z-index: 1060;
        }

        .stock-filter-actions {
            display: flex;
            align-items: center;
            gap: 8px;
            width: 100%;
        }

        .stock-filter-search,
        .stock-filter-reset {
            display: inline-flex !important;
            min-width: 86px;
            height: 38px;
            align-items: center;
            justify-content: center;
            gap: 7px;
            border-radius: 7px;
            padding: 0 16px;
            font-size: 13px;
            font-weight: 700;
            line-height: 1;
            white-space: nowrap;
            box-shadow: 0 2px 5px rgba(15, 23, 42, .12);
            transition: background-color .15s ease, border-color .15s ease, box-shadow .15s ease, transform .15s ease;
        }

        .stock-filter-search {
            flex: 1.2 1 0;
            border: 1px solid #147dcc;
            background: #1689dc;
            color: #fff !important;
        }

        .stock-filter-search:hover,
        .stock-filter-search:focus {
            border-color: #0f6fb8;
            background: #1178c4;
            box-shadow: 0 4px 9px rgba(22, 137, 220, .24);
            color: #fff !important;
        }

        .stock-filter-reset {
            flex: 1 1 0;
            border: 1px solid #cbd5e1;
            background: #fff;
            color: #475569 !important;
        }

        .stock-filter-reset:hover,
        .stock-filter-reset:focus {
            border-color: #94a3b8;
            background: #f8fafc;
            color: #1e293b !important;
        }

        .stock-filter-search:hover,
        .stock-filter-reset:hover {
            transform: translateY(-1px);
        }

        .stock-filter-search svg {
            display: block;
            width: 16px !important;
            height: 16px !important;
            min-width: 16px;
            flex: 0 0 16px;
        }

        .stock-filter-search [data-submit-default] {
            display: inline-flex !important;
            flex-direction: row !important;
            align-items: center;
            justify-content: center;
            gap: 7px;
        }

        .stock-filter-search [data-submit-default] > span {
            order: 1;
        }

        .stock-filter-search [data-submit-default] > svg {
            order: 2;
        }

        @media (max-width: 767.98px) {
            .stock-filter-actions {
                margin-top: 4px;
            }

            .stock-filter-search,
            .stock-filter-reset {
                flex: 1 1 0;
            }
        }

        .stock-report-table tbody tr {
            transition: background-color .15s ease, transform .15s ease;
        }

        .stock-report-table tbody tr:hover {
            background: #f6fbff;
        }

        .stock-row-in {
            background: rgba(22, 163, 74, .04);
        }

        .stock-row-low {
            background: rgba(245, 158, 11, .05);
        }

        .stock-row-out {
            background: rgba(220, 38, 38, .05);
        }

        .stock-product-image {
            width: 42px;
            height: 42px;
            object-fit: cover;
            border-radius: 10px;
            border: 1px solid #e5edf3;
            background: #fff;
        }

        .qty-pill,
        .stock-pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 54px;
            padding: 6px 10px;
            border-radius: 999px;
            font-weight: 700;
            line-height: 1;
        }

        .qty-pill-in, .stock-pill-in {
            background: #e9f8ef;
            color: #12824a;
        }

        .qty-pill-assign {
            border: 1px solid #bfdbfe;
            background: #eff6ff;
            color: #1d4ed8;
        }

        .qty-pill-return {
            border: 1px solid #fde68a;
            background: #fffbeb;
            color: #b45309;
        }

        .qty-pill-sale {
            border: 1px solid #ef4444;
            background: #fff1f2;
            color: #dc2626;
            animation: lsp-sale-border-pulse 1.8s ease-in-out infinite;
        }

        @keyframes lsp-sale-border-pulse {
            0%, 100% { border-color: #fecaca; box-shadow: 0 0 0 0 rgba(220, 38, 38, 0); }
            50% { border-color: #dc2626; box-shadow: 0 0 0 3px rgba(220, 38, 38, .13); }
        }

        @media (prefers-reduced-motion: reduce) {
            .qty-pill-sale { animation: none; }
        }

        .qty-pill-out, .stock-pill-out {
            background: #fff0f0;
            color: #c61b1b;
        }

        .stock-pill-low {
            background: #fff6dd;
            color: #9a6700;
        }
    </style>
@endpush

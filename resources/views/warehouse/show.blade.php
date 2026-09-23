@extends('layouts.dashboard')

@section('content')
<style>
    .warehouse-stat-card {
        position: relative;
        min-height: 112px;
        overflow: hidden;
        border: 0;
        border-radius: 12px;
        padding: 20px;
        color: #fff;
        box-shadow: 0 8px 22px rgba(31, 45, 61, .12);
    }
    .warehouse-stat-card .stat-label {
        margin-bottom: 7px;
        font-size: 13px;
        font-weight: 600;
        letter-spacing: .04em;
        text-transform: uppercase;
        opacity: .88;
    }
    .warehouse-stat-card .stat-value {
        margin: 0;
        font-size: 30px;
        line-height: 1;
        font-weight: 700;
    }
    .warehouse-stat-card .stat-icon {
        position: absolute;
        right: 18px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 48px;
        opacity: .22;
    }
    .warehouse-stat-salesmen { background: linear-gradient(135deg, #536dfe, #304ffe); }
    .warehouse-stat-purchases { background: linear-gradient(135deg, #26a69a, #00897b); }
    .warehouse-stat-sales { background: linear-gradient(135deg, #ff9f43, #f57c00); }
    .warehouse-stat-products { background: linear-gradient(135deg, #ab47bc, #7b1fa2); }
    .period-metric-card {
        position: relative;
        min-height: 106px;
        overflow: hidden;
        border: 1px solid #e8edf3;
        border-radius: 11px;
        padding: 17px 16px 15px 19px;
        background: #fff;
        box-shadow: 0 5px 16px rgba(31, 45, 61, .07);
        transition: transform .2s ease, box-shadow .2s ease;
    }
    .period-metric-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 9px 22px rgba(31, 45, 61, .11);
    }
    .period-metric-card::before {
        position: absolute;
        top: 0;
        bottom: 0;
        left: 0;
        width: 5px;
        content: '';
        background: var(--metric-color);
    }
    .period-metric-label {
        margin-bottom: 9px;
        color: #7b8794;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: .07em;
        text-transform: uppercase;
    }
    .period-metric-value {
        position: relative;
        z-index: 1;
        margin: 0;
        color: #25313c;
        font-size: 23px;
        line-height: 1.15;
        font-weight: 700;
    }
    .period-metric-icon {
        position: absolute;
        right: 13px;
        bottom: 8px;
        color: var(--metric-color);
        font-size: 38px;
        opacity: .13;
    }
    .period-metric-sales { --metric-color: #2aa9b9; }
    .period-metric-profit { --metric-color: #20a464; }
    .period-metric-cost { --metric-color: #ef8354; }
    .period-metric-quantity { --metric-color: #536dfe; }
    .period-metric-invoices { --metric-color: #ab47bc; }
    .period-metric-products { --metric-color: #f0a202; }
</style>
<div class="row">
    <div class="col-md-12">
        <div class="card-box mt-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h4 class="mb-1">{{ $warehouse->name }}</h4>
                    <div>Code: {{ $warehouse->code }}</div>
                    <div>Address: {{ $warehouse->address ?: '-' }}</div>
                </div>
                <div class="text-right">
                    <a href="{{ route('warehouse-stock-transfers.create', ['warehouse_id' => $warehouse->id]) }}" class="btn btn-success">Transfer Stock</a>
                    <a href="{{ route('warehouse-salesman-assignments.create', ['warehouse_id' => $warehouse->id]) }}" class="btn btn-warning">Assign Stock</a>
                    <a href="{{ route('warehouse-salesmen.index', ['warehouse_id' => $warehouse->id]) }}" class="btn btn-info">LSPs</a>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-xl-3 col-md-6 mb-3">
                    <div class="warehouse-stat-card warehouse-stat-salesmen">
                        <div class="stat-label">LSPs</div>
                        <p class="stat-value">{{ $warehouse->salesmen->count() }}</p>
                        <i class="mdi mdi-account-multiple stat-icon"></i>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 mb-3">
                    <div class="warehouse-stat-card warehouse-stat-purchases">
                        <div class="stat-label">Stock Receipts</div>
                        <p class="stat-value">{{ $warehouse->purchases->count() + $warehouse->stockTransfers->count() }}</p>
                        <i class="mdi mdi-cart-plus stat-icon"></i>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 mb-3">
                    <div class="warehouse-stat-card warehouse-stat-sales">
                        <div class="stat-label">Sales</div>
                        <p class="stat-value">{{ $warehouse->sales->count() }}</p>
                        <i class="mdi mdi-chart-line stat-icon"></i>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 mb-3">
                    <div class="warehouse-stat-card warehouse-stat-products">
                        <div class="stat-label">Products</div>
                        <p class="stat-value">{{ $stock->count() }}</p>
                        <i class="mdi mdi-package-variant-closed stat-icon"></i>
                    </div>
                </div>
            </div>

            <div class="border rounded p-3 mb-4">
                <form id="sales-period-form" method="GET" action="{{ route('warehouses.show', $warehouse) }}" class="mb-3">
                    <div class="row align-items-end">
                        <div class="col-lg-4 col-md-5 mb-2">
                            <label for="period" class="font-weight-bold">Sales Dashboard</label>
                            <select id="period" name="period" class="form-control">
                                <option value="today" {{ $period === 'today' ? 'selected' : '' }}>Today</option>
                                <option value="week" {{ $period === 'week' ? 'selected' : '' }}>This Week</option>
                                <option value="month" {{ $period === 'month' ? 'selected' : '' }}>This Month</option>
                                <option value="year" {{ $period === 'year' ? 'selected' : '' }}>This Year</option>
                                <option value="month_year" {{ $period === 'month_year' ? 'selected' : '' }}>Select Month &amp; Year</option>
                                <option value="custom" {{ $period === 'custom' ? 'selected' : '' }}>Custom Date</option>
                            </select>
                            <small class="text-muted">Showing: {{ $periodLabel }}</small>
                        </div>
                        <div id="month-year-fields" class="col-lg-8 col-md-7 {{ $period === 'month_year' ? '' : 'd-none' }}">
                            <div class="row align-items-end">
                                <div class="col-sm-4 mb-2">
                                    <label for="report_month">Month</label>
                                    <select id="report_month" name="report_month" class="form-control">
                                        @foreach(range(1, 12) as $monthNumber)
                                            <option value="{{ $monthNumber }}" {{ $reportMonth === $monthNumber ? 'selected' : '' }}>
                                                {{ \Carbon\Carbon::create(null, $monthNumber, 1)->format('F') }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-4 mb-2">
                                    <label for="report_year">Year</label>
                                    <select id="report_year" name="report_year" class="form-control">
                                        @foreach($reportYearOptions as $yearOption)
                                            <option value="{{ $yearOption }}" {{ $reportYear === $yearOption ? 'selected' : '' }}>{{ $yearOption }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-4 mb-2">
                                    <x-loading-submit type="submit" class="btn btn-info btn-block" icon="funnel" loading-text="Applying...">Apply Month</x-loading-submit>
                                </div>
                            </div>
                        </div>
                        <div id="custom-date-fields" class="col-lg-8 col-md-7 {{ $period === 'custom' ? '' : 'd-none' }}">
                            <div class="row align-items-end">
                                <div class="col-sm-4 mb-2">
                                    <label for="start_date">From</label>
                                    <input type="date" id="start_date" name="start_date" value="{{ old('start_date', $startDate) }}" class="form-control" {{ $period === 'custom' ? 'required' : '' }}>
                                </div>
                                <div class="col-sm-4 mb-2">
                                    <label for="end_date">To</label>
                                    <input type="date" id="end_date" name="end_date" value="{{ old('end_date', $endDate) }}" class="form-control" {{ $period === 'custom' ? 'required' : '' }}>
                                </div>
                                <div class="col-sm-4 mb-2">
                                    <x-loading-submit type="submit" class="btn btn-info btn-block" icon="funnel" loading-text="Applying...">Apply Filter</x-loading-submit>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>

                <div class="row mb-3">
                    <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
                        <div class="period-metric-card period-metric-sales">
                            <div class="period-metric-label">Sales</div>
                            <p class="period-metric-value">{{ number_format($periodSummary->sales_amount, 2) }}</p>
                            <i class="mdi mdi-cash-multiple period-metric-icon"></i>
                        </div>
                    </div>
                    <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
                        <div class="period-metric-card period-metric-profit">
                            <div class="period-metric-label">Profit</div>
                            <p class="period-metric-value {{ $periodSummary->profit < 0 ? 'text-danger' : 'text-success' }}">{{ number_format($periodSummary->profit, 2) }}</p>
                            <i class="mdi mdi-trending-up period-metric-icon"></i>
                        </div>
                    </div>
                    <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
                        <div class="period-metric-card period-metric-cost">
                            <div class="period-metric-label">Purchase Cost</div>
                            <p class="period-metric-value">{{ number_format($periodSummary->purchase_cost, 2) }}</p>
                            <i class="mdi mdi-wallet period-metric-icon"></i>
                        </div>
                    </div>
                    <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
                        <div class="period-metric-card period-metric-quantity">
                            <div class="period-metric-label">Sold Qty</div>
                            <p class="period-metric-value">{{ number_format($periodSummary->quantity, 2) }}</p>
                            <i class="mdi mdi-cart-outline period-metric-icon"></i>
                        </div>
                    </div>
                    <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
                        <div class="period-metric-card period-metric-invoices">
                            <div class="period-metric-label">Invoices</div>
                            <p class="period-metric-value">{{ $periodSummary->sale_count }}</p>
                            <i class="mdi mdi-receipt period-metric-icon"></i>
                        </div>
                    </div>
                    <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
                        <div class="period-metric-card period-metric-products">
                            <div class="period-metric-label">Products</div>
                            <p class="period-metric-value">{{ $periodSummary->product_count }}</p>
                            <i class="mdi mdi-package-variant period-metric-icon"></i>
                        </div>
                    </div>
                </div>

                <h6>Products Sold</h6>
                <div class="table-responsive">
                    <table class="table table-bordered table-sm mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Product</th>
                                <th class="text-right">Sold Qty</th>
                                <th class="text-right">Avg. Sale Price</th>
                                <th class="text-right">Sales</th>
                                <th class="text-right">Purchase Cost</th>
                                <th class="text-right">Profit</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($salesByProduct as $saleProduct)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $saleProduct->product?->product_name ?: 'Deleted product' }}</td>
                                    <td class="text-right">{{ number_format($saleProduct->quantity, 2) }}</td>
                                    <td class="text-right">{{ number_format($saleProduct->average_sale_price, 2) }}</td>
                                    <td class="text-right">{{ number_format($saleProduct->sales_amount, 2) }}</td>
                                    <td class="text-right">{{ number_format($saleProduct->purchase_cost, 2) }}</td>
                                    <td class="text-right {{ $saleProduct->profit < 0 ? 'text-danger' : 'text-success' }}">{{ number_format($saleProduct->profit, 2) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="text-center text-muted">No sales found for this period.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <h5>Stock &amp; Profit Summary</h5>
            <div class="row mb-3">
                <div class="col-md-4 mb-2">
                    <div class="p-3 border rounded">
                        Total Purchase Value: <strong>{{ number_format($totals->purchase_amount, 2) }}</strong>
                    </div>
                </div>
                <div class="col-md-4 mb-2">
                    <div class="p-3 border rounded">
                        Total Sold Value: <strong>{{ number_format($totals->sale_amount, 2) }}</strong>
                    </div>
                </div>
                <div class="col-md-4 mb-2">
                    <div class="p-3 border rounded">
                        Total Profit: <strong class="{{ $totals->profit < 0 ? 'text-danger' : 'text-success' }}">{{ number_format($totals->profit, 2) }}</strong>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Product</th>
                            <th class="text-right">Purchase Price</th>
                            <th class="text-right">Sale Price</th>
                            <th class="text-right">Purchase Qty</th>
                            <th class="text-right">Sold Qty</th>
                            <th class="text-right">Available Qty</th>
                            <th class="text-right">Profit</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($stock as $item)
                            <tr class="{{ $item->available_qty <= 0 ? 'table-secondary' : '' }}">
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    {{ $item->product->product_name }}
                                    @if($item->available_qty <= 0)
                                        <span class="badge badge-danger ml-1">Out of stock</span>
                                    @endif
                                </td>
                                <td class="text-right">{{ number_format($item->purchase_price, 2) }}</td>
                                <td class="text-right">{{ number_format($item->sale_price, 2) }}</td>
                                <td class="text-right">{{ number_format($item->purchase_qty, 2) }}</td>
                                <td class="text-right">{{ number_format($item->sold_qty, 2) }}</td>
                                <td class="text-right"><strong>{{ number_format($item->available_qty, 2) }}</strong></td>
                                <td class="text-right {{ $item->profit < 0 ? 'text-danger' : 'text-success' }}">{{ number_format($item->profit, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted">No Area Office stock records found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr class="font-weight-bold">
                            <th colspan="2" class="text-right">Total Summary</th>
                            <th class="text-center text-muted">&mdash;</th>
                            <th class="text-center text-muted">&mdash;</th>
                            <th class="text-right">{{ number_format($totals->purchase_qty, 2) }}</th>
                            <th class="text-right">{{ number_format($totals->sold_qty, 2) }}</th>
                            <th class="text-right">{{ number_format($totals->available_qty, 2) }}</th>
                            <th class="text-right {{ $totals->profit < 0 ? 'text-danger' : 'text-success' }}">{{ number_format($totals->profit, 2) }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
<script>
    (function () {
        var periodSelect = document.getElementById('period');
        var periodForm = document.getElementById('sales-period-form');
        var monthYearFields = document.getElementById('month-year-fields');
        var customFields = document.getElementById('custom-date-fields');
        var reportMonth = document.getElementById('report_month');
        var reportYear = document.getElementById('report_year');
        var startDate = document.getElementById('start_date');
        var endDate = document.getElementById('end_date');

        periodSelect.addEventListener('change', function () {
            var isCustom = this.value === 'custom';
            var isMonthYear = this.value === 'month_year';

            customFields.classList.toggle('d-none', !isCustom);
            monthYearFields.classList.toggle('d-none', !isMonthYear);
            startDate.required = isCustom;
            endDate.required = isCustom;
            reportMonth.required = isMonthYear;
            reportYear.required = isMonthYear;

            if (!isCustom && !isMonthYear) {
                startDate.disabled = true;
                endDate.disabled = true;
                reportMonth.disabled = true;
                reportYear.disabled = true;
                periodForm.submit();
            } else if (isCustom) {
                startDate.disabled = false;
                endDate.disabled = false;
                reportMonth.disabled = true;
                reportYear.disabled = true;
                startDate.focus();
            } else {
                startDate.disabled = true;
                endDate.disabled = true;
                reportMonth.disabled = false;
                reportYear.disabled = false;
                reportMonth.focus();
            }
        });
    })();
</script>
@endsection

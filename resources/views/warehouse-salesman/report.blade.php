@extends('layouts.dashboard')

@section('title', 'Sales Report - ' . $salesman->name)

@section('content')
    <style>
        .sales-report-header { border-top: 3px solid #2aa9b9; }
        .report-filter-panel { padding: 18px; border: 1px solid #e5ebf1; border-radius: 10px; background: #f8fafc; }
        .report-stat { height: 100%; padding: 17px; border: 1px solid #e5ebf1; border-radius: 10px; background: #fff; }
        .report-stat-label { color: #7a8796; font-size: 11px; font-weight: 700; letter-spacing: .04em; text-transform: uppercase; }
        .report-stat-value { margin-top: 6px; color: #172033; font-size: 24px; font-weight: 800; }
        .report-section-title { margin: 0; color: #172033; font-size: 16px; font-weight: 700; }
        .product-lines { min-width: 250px; }
        .product-line { display: flex; justify-content: space-between; gap: 15px; padding: 3px 0; border-bottom: 1px dashed #e5e7eb; }
        .product-line:last-child { border-bottom: 0; }
        @media print { .d-print-none, .left-side-menu, .topbar, .content-page > .footer { display: none !important; } }
    </style>

    <div class="card-box mt-4 sales-report-header">
        <div class="d-flex justify-content-between align-items-start flex-wrap mb-3">
            <div>
                <h4 class="mb-1">Sales Report: {{ $salesman->name }}</h4>
                <div class="text-muted">
                    {{ $salesman->warehouse?->name ?: 'No Area Office assigned' }}
                    @if($salesman->warehouse?->code) ({{ $salesman->warehouse->code }}) @endif
                    <span class="mx-1">&bull;</span> {{ $filters['label'] }}
                </div>
            </div>
            <a href="{{ route('warehouse-salesmen.index', $salesman->warehouse_id ? ['warehouse_id' => $salesman->warehouse_id] : []) }}" class="btn btn-secondary mt-1">
                <i class="fa fa-arrow-left mr-1"></i> Back to LSPs
            </a>
        </div>

        <form method="GET" action="{{ route('warehouse-salesmen.reports', $salesman) }}" class="report-filter-panel d-print-none">
            <div class="row align-items-end">
                <div class="col-lg-3 col-md-6 form-group mb-lg-0">
                    <label for="report-period">Report Period</label>
                    <select name="period" id="report-period" class="form-control">
                        <option value="today" {{ $filters['period'] === 'today' ? 'selected' : '' }}>Today</option>
                        <option value="this_week" {{ $filters['period'] === 'this_week' ? 'selected' : '' }}>This Week</option>
                        <option value="this_month" {{ $filters['period'] === 'this_month' ? 'selected' : '' }}>This Month</option>
                        <option value="month" {{ $filters['period'] === 'month' ? 'selected' : '' }}>Select Month &amp; Year</option>
                        <option value="custom" {{ $filters['period'] === 'custom' ? 'selected' : '' }}>Custom Date Range</option>
                    </select>
                </div>

                <div class="col-lg-2 col-md-3 form-group mb-lg-0 month-filter-field">
                    <label for="report-month">Month</label>
                    <select name="month" id="report-month" class="form-control select2">
                        @foreach(range(1, 12) as $monthNumber)
                            <option value="{{ $monthNumber }}" {{ $filters['month'] === $monthNumber ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create(null, $monthNumber, 1)->format('F') }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-lg-2 col-md-3 form-group mb-lg-0 month-filter-field">
                    <label for="report-year">Year</label>
                    <select name="year" id="report-year" class="form-control select2">
                        @foreach($yearOptions as $year)
                            <option value="{{ $year }}" {{ $filters['year'] === $year ? 'selected' : '' }}>{{ $year }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-lg-2 col-md-3 form-group mb-lg-0 custom-filter-field">
                    <label for="report-start-date">Start Date</label>
                    <input type="date" name="start_date" id="report-start-date" class="form-control" value="{{ request('start_date', $filters['start_date']) }}">
                </div>

                <div class="col-lg-2 col-md-3 form-group mb-lg-0 custom-filter-field">
                    <label for="report-end-date">End Date</label>
                    <input type="date" name="end_date" id="report-end-date" class="form-control" value="{{ request('end_date', $filters['end_date']) }}">
                </div>

                <div class="col-lg-3 col-md-6 mt-lg-3">
                    <button type="submit" class="btn btn-primary"><i class="fa fa-filter mr-1"></i> Apply Filter</button>
                    <a href="{{ route('warehouse-salesmen.reports', $salesman) }}" class="btn btn-light">Reset</a>
                </div>
            </div>
        </form>

        @php
            $exportParameters = array_merge(
                ['warehouse_salesman' => $salesman],
                request()->only(['period', 'month', 'year', 'start_date', 'end_date'])
            );
        @endphp
        <div class="d-flex justify-content-end flex-wrap mt-3 d-print-none">
            <a href="{{ route('warehouse-salesmen.reports.print', $exportParameters) }}" target="_blank" class="btn btn-dark mr-2 mb-2">
                <i class="fa fa-print mr-1"></i> Print
            </a>
            <a href="{{ route('warehouse-salesmen.reports.pdf', $exportParameters) }}" class="btn btn-danger mr-2 mb-2">
                <i class="fa fa-file-pdf-o mr-1"></i> PDF
            </a>
            <a href="{{ route('warehouse-salesmen.reports.excel', $exportParameters) }}" class="btn btn-success mb-2">
                <i class="fa fa-file-excel-o mr-1"></i> Excel
            </a>
        </div>

        <div class="row mt-2">
            <div class="col-lg-3 col-md-6 mb-3"><div class="report-stat"><div class="report-stat-label">Total Sales</div><div class="report-stat-value">{{ $summary->sale_count }}</div></div></div>
            <div class="col-lg-3 col-md-6 mb-3"><div class="report-stat"><div class="report-stat-label">Products Sold</div><div class="report-stat-value">{{ number_format($summary->total_quantity, 2) }}</div></div></div>
            <div class="col-lg-3 col-md-6 mb-3"><div class="report-stat"><div class="report-stat-label">Unique Products</div><div class="report-stat-value">{{ $summary->product_count }}</div></div></div>
            <div class="col-lg-3 col-md-6 mb-3"><div class="report-stat"><div class="report-stat-label">Total Sales Amount</div><div class="report-stat-value">{{ number_format($summary->total_sales_amount, 2) }}</div></div></div>
        </div>
    </div>

    <div class="card-box mt-3">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="report-section-title">Product Sales Summary</h5>
            <span class="badge badge-info">{{ $filters['label'] }}</span>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered table-hover mb-0">
                <thead class="theme-primary text-white">
                    <tr><th>#</th><th>Product</th><th class="text-right">Sales</th><th class="text-right">Quantity Sold</th><th class="text-right">Sales Amount</th></tr>
                </thead>
                <tbody>
                    @forelse($productSales as $productSale)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $productSale->product?->product_name ?: 'Deleted product' }}</td>
                            <td class="text-right">{{ $productSale->sale_count }}</td>
                            <td class="text-right">{{ number_format($productSale->total_quantity, 2) }} {{ $productSale->product?->unit?->short_name ?: $productSale->product?->unit?->actual_name }}</td>
                            <td class="text-right"><strong>{{ number_format($productSale->total_sales_amount, 2) }}</strong></td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted py-4">No product sales found for this period.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="card-box mt-3">
        <h5 class="report-section-title mb-3">Sales Transactions</h5>
        <div class="table-responsive">
            <table class="table table-bordered table-hover mb-0">
                <thead class="theme-primary text-white">
                    <tr><th>#</th><th>Sale Date &amp; Recorded Time</th><th>Invoice</th><th>Customer</th><th>Products</th><th class="text-right">Quantity</th><th class="text-right">Total Amount</th></tr>
                </thead>
                <tbody>
                    @forelse($sales as $sale)
                        <tr>
                            <td>{{ $sales->firstItem() + $loop->index }}</td>
                            <td>
                                <strong>{{ \Carbon\Carbon::parse($sale->sale_date)->format('d M Y') }}</strong>
                                <div class="small text-muted">Recorded {{ $sale->created_at?->format('h:i A') ?: '-' }}</div>
                            </td>
                            <td>{{ $sale->invoice_no }}</td>
                            <td>{{ $sale->customer_name ?: '-' }}<div class="small text-muted">{{ $sale->customer_phone ?: '' }}</div></td>
                            <td class="product-lines">
                                @foreach($sale->items as $item)
                                    <div class="product-line">
                                        <span>{{ $item->product?->product_name ?: 'Deleted product' }}</span>
                                        <span>{{ number_format($item->quantity, 2) }} &times; {{ number_format($item->sale_price, 2) }}</span>
                                    </div>
                                @endforeach
                            </td>
                            <td class="text-right">{{ number_format($sale->total_quantity, 2) }}</td>
                            <td class="text-right"><strong>{{ number_format($sale->total_amount, 2) }}</strong></td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted py-4">No sales found for {{ $filters['label'] }}.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($sales->hasPages())
            <div class="mt-3">{{ $sales->links() }}</div>
        @endif
    </div>
@endsection

@section('script')
    <script>
        (function () {
            const period = document.getElementById('report-period');
            const monthFields = document.querySelectorAll('.month-filter-field');
            const customFields = document.querySelectorAll('.custom-filter-field');

            function updateReportFields() {
                monthFields.forEach(function (field) {
                    field.style.display = period.value === 'month' ? '' : 'none';
                });
                customFields.forEach(function (field) {
                    field.style.display = period.value === 'custom' ? '' : 'none';
                });
            }

            period.addEventListener('change', updateReportFields);
            updateReportFields();
        })();
    </script>
@endsection

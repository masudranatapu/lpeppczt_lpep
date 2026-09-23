@extends('layouts.dashboard')
@section('title', ' | LSP Sales Report')
@section('content')
<style>
    .lsp-report-card { margin-top: 28px; border-top: 3px solid #43a783; padding: 18px 18px 24px; }
    .lsp-report-title { margin: 0 0 14px; color: #263238; font-size: 18px; font-weight: 700; }
    .lsp-report-filters .form-control { height: 32px; font-size: 12px; }
    .lsp-report-filters label { display: none; }
    .lsp-report-filters .btn { height: 32px; padding: 5px 16px; font-size: 12px; background: #43b978; border-color: #43b978; }
    .lsp-report-table { min-width: 1250px; font-size: 12px; }
    .lsp-report-table thead th { padding: 10px 8px; background: #43a783; color: #fff; border-color: #63b996; white-space: nowrap; }
    .lsp-report-table tbody td, .lsp-report-table tfoot td { padding: 8px; vertical-align: top; }
    .lsp-report-table tbody tr:nth-child(even) { background: #f8faf9; }
    .lsp-report-table tfoot td { background: #edf8f2; font-weight: 700; }
    .lsp-customer-number { display: block; color: #6c757d; font-size: 10px; }\n    .product-tags { display: flex; flex-wrap: wrap; gap: 4px; min-width: 220px; }\n    .product-tag { display: inline-block; max-width: 100%; padding: 4px 9px; border: 1px solid #cbd5e1; border-radius: 6px; background: #f1f5f9; color: #334155; font-size: 12px; font-weight: 600; line-height: 1.3; white-space: normal; overflow-wrap: anywhere; }
</style>
<div class="row"><div class="col-md-12"><div class="card-box lsp-report-card">
<h4 class="lsp-report-title">LSP Sales Report</h4>
<form method="GET" class="mb-3 lsp-report-filters"><div class="row align-items-end">
<div class="col-md-3"><label>LSP</label><select name="lsp" class="form-control select2"><option value="">All LSPs</option>@foreach($salesmen as $salesman)<option value="{{ $salesman->id }}" @selected(request('lsp') == $salesman->id)>{{ $salesman->name }} ({{ $salesman->warehouse?->name ?: '-' }})</option>@endforeach</select></div>
<div class="col-md-3"><label>Product</label><select name="product" class="form-control select2"><option value="">All Products</option>@foreach($products as $product)<option value="{{ $product->id }}" @selected(request('product') == $product->id)>{{ $product->product_name }}</option>@endforeach</select></div>
<div class="col-md-2"><label>From Date</label><input type="date" name="start_date" value="{{ $startDate }}" class="form-control" placeholder="Start Date"></div><div class="col-md-2"><label>To Date</label><input type="date" name="end_date" value="{{ $endDate }}" class="form-control" placeholder="End Date"></div><div class="col-md-1"><label>Per page</label><select name="per_page" class="form-control"><option value="all" @selected($perPage === 'all')>All</option>@foreach([10,20,50,100] as $size)<option value="{{ $size }}" @selected($perPage === (string) $size)>{{ $size }}</option>@endforeach</select></div><div class="col-md-1"><label>&nbsp;</label><button class="btn btn-success btn-block">Search</button></div>
</div></form>
@php($total = $allSales->sum('total_amount')) @php($paid = $allSales->sum('paid_amount')) @php($due = $allSales->sum('due_amount'))
<div class="mb-3 text-right"><a href="{{ route('report.lsp.sale.print', request()->query()) }}" target="_blank" class="btn btn-dark btn-sm mr-1"><i class="fa fa-print mr-1"></i>Print</a><a href="{{ route('report.lsp.sale.pdf', request()->query()) }}" class="btn btn-danger btn-sm mr-1"><i class="fa fa-file-pdf-o mr-1"></i>PDF</a><a href="{{ route('report.lsp.sale.excel', request()->query()) }}" class="btn btn-success btn-sm"><i class="fa fa-file-excel-o mr-1"></i>Excel</a></div>
<div class="d-flex justify-content-between align-items-center mb-2"><small>Show <strong>{{ $perPage === 'all' ? $allSales->count() : $perPage }}</strong> entries</small><small class="text-muted">Search using the filters above</small></div>
<div class="table-responsive"><table class="table table-bordered lsp-report-table"><thead><tr><th>NO.</th><th>DATE</th><th>INVOICE NO.</th><th>PRODUCTS</th><th>CUSTOMER</th><th>SELL BY</th><th>AREA OFFICE</th><th>QTY</th><th>TOTAL AMOUNT</th><th>PAYING AMOUNT</th><th>DUE</th><th>PROFIT</th><th>PAYMENT STATUS</th><th>ACTIONS</th></tr></thead><tbody>
@forelse($sales as $sale)<tr><td>{{ $loop->iteration }}</td><td>{{ $sale->sale_date ? date('d-m-Y', strtotime($sale->sale_date)) : '-' }}</td><td>{{ $sale->invoice_no }}</td><td><div class="product-tags">@foreach($sale->items as $item)<span class="product-tag">{{ $item->product?->product_name ?: '-' }}</span>@endforeach</div></td><td>{{ $sale->customer_name ?: '-' }}<span class="lsp-customer-number">{{ $sale->customer_phone ?: '(No Number)' }}</span></td><td>{{ $sale->salesman?->name ?: 'Area Office' }}</td><td>{{ $sale->warehouse?->name ?: $sale->salesman?->warehouse?->name ?: '-' }}</td><td>{{ number_format($sale->items->sum('quantity'), 2) }}</td><td>{{ number_format($sale->total_amount, 2) }}</td><td>{{ number_format($sale->paid_amount, 2) }}</td><td>{{ number_format($sale->due_amount, 2) }}</td><td>{{ number_format($sale->profit_amount, 2) }}</td><td><span class="badge badge-{{ $sale->due_amount > 0 ? 'warning' : 'success' }}">{{ $sale->due_amount > 0 ? 'Due' : 'Paid' }}</span></td><td><a href="{{ route('report.lsp.sale.invoice', $sale) }}" target="_blank" rel="noopener" class="btn btn-sm btn-outline-primary" title="View Invoice"><i class="fa fa-eye mr-1"></i>View</a></td></tr>@empty<tr><td colspan="14" class="text-center">No LSP sales found.</td></tr>@endforelse
</tbody><tfoot><tr><td colspan="7" class="text-right">Total</td><td>{{ number_format($allSales->sum(fn ($sale) => $sale->items->sum('quantity')), 2) }}</td><td>{{ number_format($total, 2) }}</td><td>{{ number_format($paid, 2) }}</td><td>{{ number_format($due, 2) }}</td><td>{{ number_format($allSales->sum('profit_amount'), 2) }}</td><td></td><td></td></tr></tfoot></table></div></div></div>
@if($perPage !== 'all')<div class="mt-3">{{ $sales->links() }}</div>@endif
@endsection




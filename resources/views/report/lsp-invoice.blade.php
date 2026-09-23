@extends('layouts.dashboard')

@section('title', ' | LSP Sale Invoice')

@section('content')
<style>
    .admin-invoice { max-width: 980px; margin: 28px auto; }
    .admin-invoice .card { border: 0; box-shadow: 0 4px 18px rgba(20, 35, 55, .10); }
    .admin-invoice-head { border-bottom: 3px solid #243b53; }
    .admin-invoice-label { color: #6c757d; font-size: 11px; text-transform: uppercase; letter-spacing: .08em; }
    .admin-invoice-box { background: #f8fafc; border: 1px solid #dee6ef; border-radius: 6px; padding: 10px 12px; height: 100%; }
    .admin-invoice-parties { display: flex; flex-wrap: nowrap; margin-left: -8px; margin-right: -8px; }
    .admin-invoice-party { flex: 0 0 50%; max-width: 50%; padding: 0 8px; }
    .admin-sale-info-row { display: flex; justify-content: space-between; gap: 18px; margin-bottom: 7px; }
    .admin-sale-info-row:last-child { margin-bottom: 0; }
    .admin-invoice table thead th { background: #40546d; color: #fff; border: 0; font-size: 11px; text-transform: uppercase; }
    .admin-invoice table td { vertical-align: middle; }
    .admin-payment { max-width: 420px; margin-left: auto; }
    .admin-payment .row { border-bottom: 1px solid #dee2e6; padding: 7px 0; }
    .admin-payment .total { border-top: 2px solid #40546d; font-weight: 700; }
    .admin-payment .paid strong { color: #198754; }
    .admin-payment .due strong { color: #dc3545; }
    @media (max-width: 767px) { .admin-invoice-parties { flex-wrap: wrap; } .admin-invoice-party { flex-basis: 100%; max-width: 100%; margin-bottom: 12px; } }
    @media print {
        @page { size: A4; margin: 12mm; }
        html, body { background: #fff !important; color: #172033 !important; }
        .no-print, .navbar, .sidebar, .main-sidebar, .debugbar { display:none !important; }
        .admin-invoice { margin:0; max-width:none; }
        .admin-invoice .card { box-shadow:none !important; border:0 !important; }
        .admin-invoice .card-body { padding:0 !important; }
        .admin-invoice-parties { display:flex !important; flex-wrap:nowrap !important; width:100% !important; margin-left:-8px !important; margin-right:-8px !important; }
        .admin-invoice-party { display:block !important; flex:0 0 50% !important; max-width:50% !important; width:50% !important; margin-bottom:0 !important; }
        .admin-invoice-parties { margin-bottom:12px !important; }
        .admin-invoice-box { padding:8px 10px !important; }
        .admin-invoice-label { margin-bottom:4px !important; }
        .admin-sale-info-row { margin-bottom:3px !important; }
        .admin-invoice table thead { display: table-header-group; }
        .admin-invoice table thead th { background:#40546d !important; color:#fff !important; -webkit-print-color-adjust:exact; print-color-adjust:exact; }
        .admin-invoice table td { color:#172033 !important; }
        .admin-invoice-box { background:#f8fafc !important; -webkit-print-color-adjust:exact; print-color-adjust:exact; }
        .admin-payment .row { break-inside: avoid; }
    }
</style>

<div class="admin-invoice">
    <div class="d-flex justify-content-end mb-3 no-print">
        <a href="{{ url()->previous() }}" class="btn btn-outline-secondary btn-sm mr-2">Back</a>
        <button type="button" onclick="window.print()" class="btn btn-danger btn-sm"><i class="fa fa-print mr-1"></i> Print Invoice</button>
    </div>
    <div class="card">
        <div class="card-body p-4">
            <div class="admin-invoice-head d-flex justify-content-between pb-3 mb-3">
                <div><h3 class="mb-2 font-weight-bold">{{ $business->name ?? config('app.name', 'LPEP') }}</h3><div class="text-muted small">LSP Sales Invoice</div></div>
                <div class="text-right"><h2 class="font-weight-bold mb-2">INVOICE</h2><div>Invoice No: <strong>{{ $sale->invoice_no }}</strong></div><div>Invoice Date: <strong>{{ $sale->sale_date ? date('d M, Y', strtotime($sale->sale_date)) : '-' }}</strong></div></div>
            </div>
            <div class="admin-invoice-parties mb-4">
                <div class="admin-invoice-party"><div class="admin-invoice-box"><div class="admin-invoice-label mb-2">Bill To</div><strong>{{ $sale->customer_name ?: 'Walk-in Customer' }}</strong><div class="small text-muted">Phone: {{ $sale->customer_phone ?: 'N/A' }}</div></div></div>
                <div class="admin-invoice-party"><div class="admin-invoice-box"><div class="admin-invoice-label mb-2">Sale Information</div><div class="admin-sale-info-row"><span>Area Office</span><strong>{{ $warehouse->name ?? 'N/A' }}</strong></div><div class="admin-sale-info-row"><span>LSP</span><strong>{{ $sale->salesman?->name ?: 'Area Office' }}</strong></div></div></div>
            </div>
            <div class="table-responsive"><table class="table table-bordered mb-3"><thead><tr><th width="8%">SL</th><th>Product Description</th><th class="text-right">Quantity</th><th class="text-right">Unit Price</th><th class="text-right">Amount (BDT)</th></tr></thead><tbody>
                @foreach($sale->items as $item)<tr><td>{{ $loop->iteration }}</td><td><strong>{{ $item->product?->product_name ?: '-' }}</strong></td><td class="text-right">{{ number_format($item->quantity, 2) }}</td><td class="text-right">{{ number_format($item->sale_price ?? 0, 2) }}</td><td class="text-right">{{ number_format($item->total ?? (($item->quantity ?? 0) * ($item->sale_price ?? 0)), 2) }}</td></tr>@endforeach
            </tbody></table></div>
            @php($invoiceDiscount = max(0, (float) $sale->items->sum('total') - (float) $sale->total_amount))
            <div class="admin-payment">@if ($invoiceDiscount > 0)<div class="row subtotal"><div class="col-6">Initial Total</div><div class="col-6 text-right"><strong>{{ number_format((float) $sale->items->sum('total'), 2) }} BDT</strong></div></div><div class="row discount"><div class="col-6">Discount (-)</div><div class="col-6 text-right"><strong>{{ number_format($invoiceDiscount, 2) }} BDT</strong></div></div>@endif<div class="row total"><div class="col-6">Total</div><div class="col-6 text-right"><strong>{{ number_format($sale->total_amount, 2) }} BDT</strong></div></div><div class="row paid"><div class="col-6">Paid</div><div class="col-6 text-right"><strong>{{ number_format($sale->paid_amount, 2) }} BDT</strong></div></div><div class="row due"><div class="col-6">Due</div><div class="col-6 text-right"><strong>{{ number_format($sale->due_amount, 2) }} BDT</strong></div></div></div>
        </div>
    </div>
</div>
@endsection


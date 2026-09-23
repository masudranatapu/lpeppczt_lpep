<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Area Office Sales Report - {{ $filters['label'] }}</title>
    <style>
        @page { size: A4 landscape; margin: 10mm; }
        * { box-sizing: border-box; }
        body { margin: 0; color: #172033; font-family: DejaVu Sans, Arial, sans-serif; font-size: 9px; }
        .header { display: table; width: 100%; padding-bottom: 12px; border-bottom: 2px solid #172033; }
        .header-left, .header-right { display: table-cell; width: 50%; vertical-align: top; }
        .header-right { text-align: right; }
        h1 { margin: 0 0 4px; font-size: 19px; }
        h2 { margin: 18px 0 7px; font-size: 12px; }
        .muted { color: #667085; line-height: 1.6; }
        .summary { width: 100%; margin: 12px 0; border-collapse: separate; border-spacing: 5px; }
        .summary td { width: 25%; padding: 9px; border: 1px solid #d9e1e8; }
        .summary-label { color: #667085; font-size: 7px; font-weight: bold; text-transform: uppercase; }
        .summary-value { margin-top: 4px; font-size: 14px; font-weight: bold; }
        .report-table { width: 100%; border-collapse: collapse; }
        .report-table thead { display: table-header-group; }
        .report-table th { padding: 6px; background: #172033; color: #fff; font-size: 7px; text-align: left; text-transform: uppercase; }
        .report-table td { padding: 5px 6px; border: 1px solid #dfe5eb; vertical-align: top; }
        .report-table tr { page-break-inside: avoid; }
        .right { text-align: right !important; }
        .center { text-align: center !important; }
        .footer { margin-top: 15px; color: #667085; font-size: 7px; text-align: right; }
        @media print { .no-print { display: none !important; } }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-left">
            <h1>Area Office Sales Report</h1>
            <strong>{{ $business->invoice_name ?: $business->name }}</strong>
            <div class="muted">{{ $warehouse->name }} @if($warehouse->code) ({{ $warehouse->code }}) @endif</div>
        </div>
        <div class="header-right">
            <strong>{{ $filters['label'] }}</strong>
            <div class="muted">{{ \Carbon\Carbon::parse($filters['start_date'])->format('d-m-Y') }} to {{ \Carbon\Carbon::parse($filters['end_date'])->format('d-m-Y') }}</div>
            <div class="muted">Generated {{ now()->format('d-m-Y h:i A') }}</div>
        </div>
    </div>

    <table class="summary"><tr>
        <td><div class="summary-label">Total Sales</div><div class="summary-value">{{ $summary->sale_count }}</div></td>
        <td><div class="summary-label">Sales Amount (BDT)</div><div class="summary-value">{{ number_format($summary->total_amount, 2) }}</div></td>
        <td><div class="summary-label">Paid Amount (BDT)</div><div class="summary-value">{{ number_format($summary->paid_amount, 2) }}</div></td>
        <td><div class="summary-label">Due Amount (BDT)</div><div class="summary-value">{{ number_format($summary->due_amount, 2) }}</div></td>
        <td><div class="summary-label">Quantity Sold</div><div class="summary-value">{{ number_format($summary->total_quantity, 0) }}</div></td>
    </tr></table>

    <h2>Sales Transaction Details</h2>
    <table class="report-table">
        <thead><tr><th>#</th><th>Date / Time</th><th>Invoice</th><th>LSP</th><th>Customer</th><th>Product</th><th class="right">Qty</th><th class="right">Unit Price</th><th class="right">Line Total</th></tr></thead>
        <tbody>
            @php $detailNumber = 1; @endphp
            @forelse ($sales as $sale)
                @foreach ($sale->items as $item)
                    <tr><td>{{ $detailNumber++ }}</td><td>{{ \Carbon\Carbon::parse($sale->sale_date)->format('d-m-Y') }}<br><span class="muted">{{ $sale->created_at?->format('h:i A') }}</span></td><td>{{ $sale->invoice_no }}</td><td>{{ $sale->salesman?->name ?: 'Area Office Direct' }}</td><td>{{ $sale->customer_name ?: '-' }}</td><td>{{ $item->product?->product_name ?: 'Deleted product' }}</td><td class="right">{{ number_format($item->quantity, 0) }}</td><td class="right">{{ number_format($item->sale_price, 2) }}</td><td class="right">{{ number_format($item->total, 2) }}</td></tr>
                @endforeach
            @empty
                <tr><td colspan="9" class="center">No sales found for this period.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">System-generated report &middot; {{ config('app.timezone') }}</div>

    @if ($autoPrint)
        <script>window.addEventListener('load', function () { window.setTimeout(function () { window.print(); }, 250); });</script>
    @endif
</body>
</html>

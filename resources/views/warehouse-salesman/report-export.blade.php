<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sales Report - {{ $salesman->name }}</title>
    <style>
        @page { size: A4 landscape; margin: 12mm; }
        * { box-sizing: border-box; }
        body { margin: 0; color: #172033; font-family: DejaVu Sans, Arial, sans-serif; font-size: 10px; }
        h1 { margin: 0 0 5px; font-size: 20px; }
        h2 { margin: 22px 0 8px; font-size: 13px; }
        .muted { color: #667085; }
        .header { padding-bottom: 12px; border-bottom: 2px solid #172033; }
        .summary { width: 100%; margin: 14px 0; border-collapse: separate; border-spacing: 6px; }
        .summary td { width: 25%; padding: 10px; border: 1px solid #d9e1e8; }
        .summary-label { color: #667085; font-size: 8px; text-transform: uppercase; }
        .summary-value { margin-top: 4px; font-size: 15px; font-weight: bold; }
        .report-table { width: 100%; border-collapse: collapse; }
        .report-table th { padding: 7px 6px; background: #172033; color: #fff; text-align: left; font-size: 8px; }
        .report-table td { padding: 6px; border: 1px solid #dfe5eb; vertical-align: top; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .no-break { page-break-inside: avoid; }
        .footer { margin-top: 18px; color: #667085; font-size: 8px; text-align: right; }
        @media print { .no-print { display: none !important; } }
    </style>
</head>
<body>
    <div class="header">
        <h1>LSP Sales Report</h1>
        <strong>{{ $salesman->name }}</strong> &mdash; {{ $salesman->warehouse?->name ?: 'No Area Office assigned' }}
        <div class="muted">Period: {{ $filters['label'] }} | Business dates: {{ $filters['start_date'] }} to {{ $filters['end_date'] }}</div>
    </div>

    <table class="summary">
        <tr>
            <td><div class="summary-label">Total Sales</div><div class="summary-value">{{ $summary->sale_count }}</div></td>
            <td><div class="summary-label">Quantity Sold</div><div class="summary-value">{{ number_format($summary->total_quantity, 2) }}</div></td>
            <td><div class="summary-label">Unique Products</div><div class="summary-value">{{ $summary->product_count }}</div></td>
            <td><div class="summary-label">Total Sales Amount</div><div class="summary-value">{{ number_format($summary->total_sales_amount, 2) }}</div></td>
        </tr>
    </table>

    <h2>Product Sales Summary</h2>
    <table class="report-table">
        <thead><tr><th>#</th><th>Product</th><th class="text-right">Sales</th><th class="text-right">Quantity</th><th class="text-right">Amount</th></tr></thead>
        <tbody>
            @forelse($productSales as $productSale)
                <tr class="no-break">
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $productSale->product?->product_name ?: 'Deleted product' }}</td>
                    <td class="text-right">{{ $productSale->sale_count }}</td>
                    <td class="text-right">{{ number_format($productSale->total_quantity, 2) }}</td>
                    <td class="text-right">{{ number_format($productSale->total_sales_amount, 2) }}</td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center">No product sales found.</td></tr>
            @endforelse
        </tbody>
    </table>

    <h2>Sales Details</h2>
    <table class="report-table">
        <thead><tr><th>#</th><th>Sale Date</th><th>Recorded Time</th><th>Invoice</th><th>Product</th><th class="text-right">Qty</th><th class="text-right">Unit Price</th><th class="text-right">Line Amount</th></tr></thead>
        <tbody>
            @php $detailNumber = 1; @endphp
            @forelse($sales as $sale)
                @foreach($sale->items as $item)
                    <tr class="no-break">
                        <td>{{ $detailNumber++ }}</td>
                        <td>{{ \Carbon\Carbon::parse($sale->sale_date)->format('d M Y') }}</td>
                        <td>{{ $sale->created_at?->format('h:i A') ?: '-' }}</td>
                        <td>{{ $sale->invoice_no }}</td>
                        <td>{{ $item->product?->product_name ?: 'Deleted product' }}</td>
                        <td class="text-right">{{ number_format($item->quantity, 2) }}</td>
                        <td class="text-right">{{ number_format($item->sale_price, 2) }}</td>
                        <td class="text-right">{{ number_format($item->total, 2) }}</td>
                    </tr>
                @endforeach
            @empty
                <tr><td colspan="8" class="text-center">No sales found for this period.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">Generated {{ now()->format('d M Y, h:i A') }} ({{ config('app.timezone') }})</div>

    @if($autoPrint)
        <script>
            window.addEventListener('load', function () {
                window.setTimeout(function () { window.print(); }, 250);
            });
        </script>
    @endif
</body>
</html>

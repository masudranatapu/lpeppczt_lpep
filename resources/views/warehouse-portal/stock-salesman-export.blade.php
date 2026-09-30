<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>LSP Current Stock</title>
    <style>
        @page { size: A4 landscape; margin: 10mm; }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            color: #172033;
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 9px;
            background: #fff;
        }
        .header {
            display: table;
            width: 100%;
            padding-bottom: 12px;
            border-bottom: 2px solid #0f766e;
        }
        .header-left,
        .header-right {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }
        .header-right { text-align: right; }
        h1 { margin: 0 0 4px; font-size: 20px; color: #0f172a; }
        .muted { color: #64748b; line-height: 1.6; }
        .summary {
            width: 100%;
            margin: 12px 0 14px;
            border-collapse: separate;
            border-spacing: 6px;
        }
        .summary td {
            width: 20%;
            padding: 10px 12px;
            border: 1px solid #d8e2ea;
            border-radius: 6px;
            background: #f8fbfd;
            vertical-align: top;
        }
        .summary-label {
            color: #64748b;
            font-size: 7px;
            font-weight: 700;
            letter-spacing: .8px;
            text-transform: uppercase;
        }
        .summary-value {
            margin-top: 4px;
            font-size: 14px;
            font-weight: 800;
            color: #0f172a;
        }
        .report-table {
            width: 100%;
            border-collapse: collapse;
        }
        .report-table thead { display: table-header-group; }
        .report-table th {
            padding: 7px 6px;
            background: #0f766e;
            color: #fff;
            font-size: 7px;
            text-align: left;
            text-transform: uppercase;
        }
        .report-table td {
            padding: 6px 6px;
            border: 1px solid #d9e1e8;
            vertical-align: top;
        }
        .report-table tr { page-break-inside: avoid; }
        .right { text-align: right !important; }
        .center { text-align: center !important; }
        .status-in {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 999px;
            background: #e8f8ef;
            color: #0f7a42;
            font-weight: 700;
        }
        .status-out {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 999px;
            background: #fff1f1;
            color: #c81e1e;
            font-weight: 700;
        }
        .footer {
            margin-top: 14px;
            color: #64748b;
            font-size: 7px;
            text-align: right;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-left">
            <h1>LSP Current Stock</h1>
            <strong>{{ $salesman->name }}</strong>
            <div class="muted">
                Area Office: {{ $warehouse?->name ?? '-' }}
                @if($search !== '')
                    <br>Product: {{ $search }}
                @endif
                @if($status !== 'all')
                    <br>Status: {{ $status === 'in' ? 'In stock' : 'Out of stock' }}
                @endif
            </div>
        </div>
        <div class="header-right">
            <strong>{{ now()->format('d M Y h:i A') }}</strong>
            <div class="muted">Generated report</div>
        </div>
    </div>

    <table class="summary">
        <tr>
            <td><div class="summary-label">Products</div><div class="summary-value">{{ number_format((float) $summary->product_count, 0) }}</div></td>
            <td><div class="summary-label">Received Qty</div><div class="summary-value">{{ number_format((float) $summary->received_qty, 2) }}</div></td>
            <td><div class="summary-label">Sold Qty</div><div class="summary-value">{{ number_format((float) $summary->sold_qty, 2) }}</div></td>
            <td><div class="summary-label">Stock Qty</div><div class="summary-value">{{ number_format((float) $summary->stock_qty, 2) }}</div></td>
            <td><div class="summary-label">Total Selling Price</div><div class="summary-value">{{ number_format((float) $summary->stock_value, 2) }}</div></td>
        </tr>
    </table>

    <table class="report-table">
        <thead>
            <tr>
                <th style="width: 5%;">SL</th>
                <th>Product</th>
                <th>Selling Price</th>
                <th>Received Qty</th>
                <th>Sold Qty</th>
                <th>Stock Qty</th>
                <th>Total Selling Price</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($products as $product)
                @php($stockQty = (float) ($product->salesman_stock_qty ?? 0))
                <tr>
                    <td class="center">{{ $loop->iteration }}</td>
                    <td><strong>{{ $product->product_name }}</strong></td>
                    <td class="right">{{ number_format((float) ($product->selling_price ?? 0), 2) }}</td>
                    <td class="right">{{ number_format((float) ($product->warehouse_purchase_qty ?? 0), 2) }}</td>
                    <td class="right">{{ number_format((float) ($product->salesman_sale_qty ?? 0), 2) }}</td>
                    <td class="right"><span class="{{ $stockQty > 0 ? 'status-in' : 'status-out' }}">{{ number_format($stockQty, 2) }}</span></td>
                    <td class="right">{{ number_format((float) ($product->selling_price ?? 0) * $stockQty, 2) }}</td>
                </tr>
            @empty
                <tr><td colspan="7" class="center">No products found for the selected filters.</td></tr>
            @endforelse
        </tbody>
        @if($products->isNotEmpty())
            <tfoot>
                <tr>
                    <th colspan="3">Total</th>
                    <th class="right">{{ number_format((float) $summary->received_qty, 2) }}</th>
                    <th class="right">{{ number_format((float) $summary->sold_qty, 2) }}</th>
                    <th class="right">{{ number_format((float) $summary->stock_qty, 2) }}</th>
                    <th class="right">{{ number_format((float) $summary->stock_value, 2) }}</th>
                </tr>
            </tfoot>
        @endif
    </table>

    <div class="footer">
        System-generated report &middot; {{ config('app.name') }}
    </div>

    @if (($reportType ?? 'print') === 'print')
        <script>
            window.addEventListener('load', function () {
                window.setTimeout(function () { window.print(); }, 250);
            });
        </script>
    @endif
</body>
</html>

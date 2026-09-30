<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $reportTitle ?? 'Area Office Stock' }}</title>
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
            width: 25%;
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
        @media print {
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>
    @php
        $rows = $products ?? $stock ?? collect();
    @endphp
    @if (($reportType ?? 'print') === 'excel')
        {{-- Excel keeps only table cells, so the owner and summary are plain rows here. --}}
        <table>
            <tr><th colspan="12">{{ $reportTitle ?? 'Area Office Stock' }}</th></tr>
            <tr><td colspan="2"><strong>Area Office</strong></td><td colspan="10">{{ $warehouseLabel ?? 'All Area Offices' }}</td></tr>
            <tr><td colspan="2"><strong>LSP</strong></td><td colspan="10">{{ $salesmanLabel ?? 'All LSPs' }}</td></tr>
            <tr><td colspan="2"><strong>Generated</strong></td><td colspan="10">{{ now()->format('d M Y h:i A') }}</td></tr>
            @if(!empty($search))
                <tr><td colspan="2"><strong>Product filter</strong></td><td colspan="10">{{ $search }}</td></tr>
            @endif
            <tr><td colspan="12"></td></tr>
            <tr><td colspan="2"><strong>Products</strong></td><td colspan="10">{{ (float) ($summary->product_count ?? 0) }}</td></tr>
            <tr><td colspan="2"><strong>Area Office Received</strong></td><td colspan="10">{{ (float) ($summary->received_qty ?? 0) }}</td></tr>
            <tr><td colspan="2"><strong>LSP Sold</strong></td><td colspan="10">{{ (float) ($summary->lsp_sale_qty ?? 0) }}</td></tr>
            <tr><td colspan="2"><strong>Total Remaining</strong></td><td colspan="10">{{ (float) ($summary->total_remaining_qty ?? 0) }}</td></tr>
            <tr><td colspan="12"></td></tr>
        </table>
    @else
        <div class="header">
            <div class="header-left">
                <h1>{{ $reportTitle ?? 'Area Office Stock' }}</h1>
                <strong>{{ $warehouseLabel ?? 'All Area Offices' }}</strong>
                <div class="muted">
                    {{ $salesmanLabel ?? 'All LSPs' }}
                    @if(!empty($search))
                        <br>Product: {{ $search }}
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
                <td><div class="summary-label">Products</div><div class="summary-value">{{ number_format((float) ($summary->product_count ?? 0), 0) }}</div></td>
                <td><div class="summary-label">Area Office Received</div><div class="summary-value">{{ number_format((float) ($summary->received_qty ?? 0), 2) }}</div></td>
                <td><div class="summary-label">LSP Sold</div><div class="summary-value">{{ number_format((float) ($summary->lsp_sale_qty ?? 0), 2) }}</div></td>
                <td><div class="summary-label">Total Remaining</div><div class="summary-value">{{ number_format((float) ($summary->total_remaining_qty ?? 0), 2) }}</div></td>
            </tr>
        </table>
    @endif

    <table class="report-table">
        <thead>
            <tr>
                <th style="width: 4%;">SL</th>
                <th>Product</th>
                <th>Purchase Price</th>
                <th>Selling Price</th>
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
            @forelse ($rows as $product)
                <tr>
                    <td class="center">{{ $loop->iteration }}</td>
                    <td><strong>{{ $product->product_name }}</strong></td>
                    <td class="right">{{ number_format((float) ($product->purchase_price ?? 0), 2) }}</td>
                    <td class="right">{{ number_format((float) ($product->selling_price ?? 0), 2) }}</td>
                    <td class="right">{{ number_format((float) ($product->received_qty ?? 0), 2) }}</td>
                    <td class="right">{{ number_format((float) ($product->assigned_qty ?? 0), 2) }}</td>
                    {{-- <td class="right">{{ number_format((float) ($product->returned_qty ?? 0), 2) }}</td> --}}
                    <td class="right">{{ number_format((float) ($product->lsp_sale_qty ?? 0), 2) }}</td>
                    <td class="right">{{ number_format((float) ($product->office_stock_qty ?? 0), 2) }}</td>
                    <td class="right">{{ number_format((float) ($product->lsp_stock_qty ?? 0), 2) }}</td>
                    <td class="right"><span class="{{ (float) ($product->total_remaining_qty ?? 0) > 0 ? 'status-in' : 'status-out' }}">{{ number_format((float) ($product->total_remaining_qty ?? 0), 2) }}</span></td>
                    <td class="right">{{ number_format((float) ($product->stock_sale_price ?? 0), 2) }}</td>
                    <td class="right">{{ number_format((float) ($product->stock_purchase_price ?? 0), 2) }}</td>
                </tr>
            @empty
                <tr><td colspan="13" class="center">No products found for the selected filters.</td></tr>
            @endforelse
        </tbody>
        @if($rows->isNotEmpty())
            <tfoot>
                <tr>
                    <th colspan="2">Total</th>
                    <th></th>
                    <th></th>
                    <th class="right">{{ number_format((float) ($summary->received_qty ?? 0), 2) }}</th>
                    <th class="right">{{ number_format((float) ($summary->assigned_qty ?? 0), 2) }}</th>
                    {{-- <th class="right">{{ number_format((float) ($summary->returned_qty ?? 0), 2) }}</th> --}}
                    <th class="right">{{ number_format((float) ($summary->lsp_sale_qty ?? 0), 2) }}</th>
                    <th class="right">{{ number_format((float) ($summary->office_stock_qty ?? 0), 2) }}</th>
                    <th class="right">{{ number_format((float) ($summary->lsp_stock_qty ?? 0), 2) }}</th>
                    <th class="right">{{ number_format((float) ($summary->total_remaining_qty ?? 0), 2) }}</th>
                    <th class="right">{{ number_format((float) $rows->sum('stock_sale_price'), 2) }}</th>
                    <th class="right">{{ number_format((float) $rows->sum('stock_purchase_price'), 2) }}</th>
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

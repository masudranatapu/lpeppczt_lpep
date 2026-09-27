<table>
    <tr><th colspan="12">LSP Sales Report</th></tr>
    <tr><td colspan="12">Period: {{ $startDate }} to {{ $endDate }}</td></tr>
    <thead><tr><th>SL</th><th>Date</th><th>Invoice No</th><th>Products</th><th>Customer</th><th>Customer Number</th><th>Sell By</th><th>Area Office</th><th>Qty</th><th>Total</th><th>Paid</th><th>Due</th><th>Profit</th></tr></thead>
    <tbody>@foreach($allSales as $sale)<tr><td>{{ $loop->iteration }}</td><td>{{ $sale->sale_date }}</td><td>{{ $sale->invoice_no }}</td><td>{{ $sale->report_items->pluck('product.product_name')->filter()->implode(', ') }}</td><td>{{ $sale->customer_name }}</td><td>{{ $sale->customer_phone }}</td><td>{{ $sale->salesman?->name ?: 'Area Office' }}</td><td>{{ $sale->warehouse?->name ?: $sale->salesman?->warehouse?->name }}</td><td>{{ $sale->report_quantity }}</td><td>{{ $sale->report_total }}</td><td>{{ $sale->report_paid }}</td><td>{{ $sale->report_due }}</td><td>{{ $sale->profit_amount }}</td></tr>@endforeach</tbody>
</table>


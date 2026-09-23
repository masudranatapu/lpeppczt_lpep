<table>
    <tr><th colspan="9">Area Office Sales Report</th></tr>
    <tr><td>Area Office</td><td colspan="3">{{ $warehouse->name }} ({{ $warehouse->code }})</td><td>Period</td><td colspan="4">{{ $filters['label'] }}: {{ $filters['start_date'] }} to {{ $filters['end_date'] }}</td></tr>
    <tr><td>Total Sales</td><td>{{ $summary->sale_count }}</td><td>Quantity Sold</td><td>{{ $summary->total_quantity }}</td><td>Active LSPs</td><td>{{ $summary->active_salesmen }} / {{ $summary->registered_salesmen }}</td><td>Collected Sales Amount</td><td colspan="2">{{ $summary->total_sales_amount }}</td></tr>
</table>

<table>
    <thead><tr><th>#</th><th>LSP</th><th>Status</th><th>Sales Count</th><th>Quantity Sold</th><th>Average Sale</th><th>Sales Amount</th></tr></thead>
    <tbody>
        @foreach ($salesmen as $salesman)
            <tr><td>{{ $loop->iteration }}</td><td>{{ $salesman->name }}</td><td>{{ (int) $salesman->status === 1 ? 'Active' : 'Inactive' }}</td><td>{{ $salesman->sale_count }}</td><td>{{ $salesman->total_quantity }}</td><td>{{ $salesman->sale_count > 0 ? $salesman->total_sales_amount / $salesman->sale_count : 0 }}</td><td>{{ $salesman->total_sales_amount }}</td></tr>
        @endforeach
        @if ($directSales->sale_count > 0)
            <tr><td>{{ $salesmen->count() + 1 }}</td><td>Area Office Direct</td><td>Direct</td><td>{{ $directSales->sale_count }}</td><td>{{ $directSales->total_quantity }}</td><td>{{ $directSales->total_sales_amount / $directSales->sale_count }}</td><td>{{ $directSales->total_sales_amount }}</td></tr>
        @endif
    </tbody>
</table>

<table>
    <thead><tr><th>#</th><th>Sale Date</th><th>Recorded Time</th><th>Invoice</th><th>LSP</th><th>Customer</th><th>Product</th><th>Quantity</th><th>Unit Price</th><th>Line Amount</th><th>Sale Total</th></tr></thead>
    <tbody>
        @php $detailNumber = 1; @endphp
        @foreach ($sales as $sale)
            @foreach ($sale->items as $item)
                <tr><td>{{ $detailNumber++ }}</td><td>{{ $sale->sale_date }}</td><td>{{ $sale->created_at?->format('H:i:s') }}</td><td>{{ $sale->invoice_no }}</td><td>{{ $sale->salesman?->name ?: 'Area Office Direct' }}</td><td>{{ $sale->customer_name ?: '-' }}</td><td>{{ $item->product?->product_name ?: 'Deleted product' }}</td><td>{{ $item->quantity }}</td><td>{{ $item->sale_price }}</td><td>{{ $item->total }}</td><td>{{ $sale->total_amount }}</td></tr>
            @endforeach
        @endforeach
    </tbody>
</table>

<table>
    <tr><th colspan="8">LSP Sales Report</th></tr>
    <tr><td>LSP</td><td colspan="3">{{ $salesman->name }}</td><td>Area Office</td><td colspan="3">{{ $salesman->warehouse?->name ?: '-' }}</td></tr>
    <tr><td>Period</td><td colspan="7">{{ $filters['label'] }} ({{ $filters['start_date'] }} to {{ $filters['end_date'] }})</td></tr>
    <tr><td>Total Sales</td><td>{{ $summary->sale_count }}</td><td>Quantity Sold</td><td>{{ $summary->total_quantity }}</td><td>Unique Products</td><td>{{ $summary->product_count }}</td><td>Total Amount</td><td>{{ $summary->total_sales_amount }}</td></tr>
</table>

<table>
    <thead><tr><th>#</th><th>Product</th><th>Sales Count</th><th>Quantity Sold</th><th>Sales Amount</th></tr></thead>
    <tbody>
        @foreach($productSales as $productSale)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $productSale->product?->product_name ?: 'Deleted product' }}</td>
                <td>{{ $productSale->sale_count }}</td>
                <td>{{ $productSale->total_quantity }}</td>
                <td>{{ $productSale->total_sales_amount }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<table>
    <thead><tr><th>#</th><th>Sale Date</th><th>Recorded Time</th><th>Invoice</th><th>Customer</th><th>Product</th><th>Quantity</th><th>Unit Price</th><th>Line Amount</th><th>Sale Total</th></tr></thead>
    <tbody>
        @php $detailNumber = 1; @endphp
        @foreach($sales as $sale)
            @foreach($sale->items as $item)
                <tr>
                    <td>{{ $detailNumber++ }}</td>
                    <td>{{ $sale->sale_date }}</td>
                    <td>{{ $sale->created_at?->format('H:i:s') ?: '-' }}</td>
                    <td>{{ $sale->invoice_no }}</td>
                    <td>{{ $sale->customer_name ?: '-' }}</td>
                    <td>{{ $item->product?->product_name ?: 'Deleted product' }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>{{ $item->sale_price }}</td>
                    <td>{{ $item->total }}</td>
                    <td>{{ $sale->total_amount }}</td>
                </tr>
            @endforeach
        @endforeach
    </tbody>
</table>

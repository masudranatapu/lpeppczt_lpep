@extends('layouts.dashboard')
@section('title', 'Area Office Stock Transfers')
@section('content')
<style>
    .transferred-list-table { table-layout: fixed; }
    .transferred-list-table th:nth-child(1), .transferred-list-table td:nth-child(1) { width: 4%; }
    .transferred-list-table th:nth-child(2), .transferred-list-table td:nth-child(2) { width: 10%; }
    .transferred-list-table th:nth-child(3), .transferred-list-table td:nth-child(3) { width: 48%; }
    .transferred-list-table th:nth-child(4), .transferred-list-table td:nth-child(4) { width: 10%; }
    .transferred-list-table th:nth-child(5), .transferred-list-table td:nth-child(5) { width: 10%; }
    .transferred-list-table th:nth-child(6), .transferred-list-table td:nth-child(6) { width: 10%; }
    .transferred-list-table th:nth-child(7), .transferred-list-table td:nth-child(7) { width: 8%; }
    .transferred-list-table .invoice-number { white-space: nowrap; }
    .transferred-list-table .product-tags { display: flex; flex-wrap: wrap; gap: 4px; }
    .transferred-list-table .product-tag { display: inline-block; max-width: 100%; padding: 4px 9px; border: 1px solid #cbd5e1; border-radius: 6px; background: #f1f5f9; color: #334155; font-size: 12px; font-weight: 600; line-height: 1.3; white-space: normal; overflow-wrap: anywhere; }
</style>
<div class="row"><div class="col-12"><div class="card-box mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div><h4 class="mb-1">Transferred List</h4><small class="text-muted">Central admin stock sent to Area Offices.</small></div>
        <a class="btn btn-primary" href="{{ route('warehouse-stock-transfers.create') }}"><i class="fa fa-plus"></i> New Transfer</a>
    </div>
    <form method="GET" action="{{ route('warehouse-stock-transfers.index') }}" class="row align-items-end mb-4">
        <div class="col-md-3 form-group"><label>Area Office</label><select name="warehouse_id" class="form-control"><option value="">All Area Offices</option>@foreach($warehouses as $warehouse)<option value="{{ $warehouse->id }}" {{ request('warehouse_id') == $warehouse->id ? 'selected' : '' }}>{{ $warehouse->name }}</option>@endforeach</select></div>
        <div class="col-md-2 form-group"><label>From Date</label><input type="date" name="from_date" value="{{ $fromDate }}" class="form-control"></div>
        <div class="col-md-2 form-group"><label>To Date</label><input type="date" name="to_date" value="{{ $toDate }}" class="form-control"></div>
        <div class="col-md-3 form-group"><label>Search Text</label><input type="search" name="search" value="{{ $search }}" class="form-control" placeholder="Invoice, total qty or product name"></div>
        <div class="col-md-2 form-group"><button class="btn btn-success mr-1">Filter</button><a href="{{ route('warehouse-stock-transfers.index') }}" class="btn btn-light">Clear</a></div>
    </form>
    <div class="table-responsive"><table class="table table-bordered table-hover transferred-list-table">
        <thead class="theme-primary text-white"><tr><th>SL</th><th>Invoice No.</th><th>Product Name</th><th>Date</th><th>Receiver</th><th class="text-right">Total Quantity</th><th>Action</th></tr></thead>
        <tbody>@forelse($transfers as $transfer)<tr>
            <td>{{ $transfers->firstItem() + $loop->index }}</td><td class="invoice-number">{{ $transfer->invoice_no }}</td>
            <td><div class="product-tags">@foreach($transfer->items as $item)<span class="product-tag">{{ $item->product?->product_name ?: '-' }}</span>@endforeach</div></td><td>{{ $transfer->transfer_date }}</td><td>{{ $transfer->warehouse?->name ?: '-' }}</td>
            <td class="text-right">{{ number_format($transfer->total_quantity, 2) }}</td>
            <td><div class="btn-group"><a href="{{ route('warehouse-stock-transfers.show', $transfer) }}" class="btn btn-sm btn-info" title="View"><i class="fa fa-eye"></i></a><form method="POST" action="{{ route('warehouse-stock-transfers.destroy', $transfer) }}" class="d-inline" onsubmit="return confirm('Delete this transfer?')">@csrf @method('DELETE')<button type="submit" class="btn btn-sm btn-danger" title="Delete"><i class="fa fa-trash"></i></button></form></div></td>
        </tr>@empty<tr><td colspan="7" class="text-center">No transfers found.</td></tr>@endforelse</tbody><tfoot><tr><th colspan="4"></th><th>Total</th><th class="text-right">{{ number_format($filteredTotalQuantity, 2) }}</th><th></th></tr></tfoot>
    </table></div>{{ $transfers->links() }}
</div></div></div>
@endsection

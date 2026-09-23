@extends('layouts.dashboard')
@section('title', 'Area Office Transfer List')
@section('content')
<style>
    .product-tags { display: flex; flex-wrap: wrap; gap: 4px; }
    .product-tag { display: inline-block; max-width: 100%; padding: 4px 9px; border: 1px solid #cbd5e1; border-radius: 6px; background: #f1f5f9; color: #334155; font-size: 12px; font-weight: 600; line-height: 1.3; white-space: normal; overflow-wrap: anywhere; }
</style>
<div class="row"><div class="col-12"><div class="card-box mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4"><div><h4 class="mb-1">Area Office Transfer List</h4><small class="text-muted">Products assigned from Area Offices to LSPs.</small></div></div>
    <form method="GET" action="{{ route('warehouse-salesman-assignments.transfer-list') }}" class="row align-items-end mb-4">
        <div class="col-md-3 form-group"><label>Area Office</label><select name="warehouse_id" id="transfer-list-warehouse" class="form-control"><option value="">All Area Offices</option>@foreach($warehouses as $warehouse)<option value="{{ $warehouse->id }}" {{ (string)$warehouseId === (string)$warehouse->id ? 'selected' : '' }}>{{ $warehouse->name }}</option>@endforeach</select></div>
        <div class="col-md-2 form-group"><label>LSP</label><select name="salesman_id" class="form-control"><option value="">All LSPs</option>@foreach($salesmen as $salesman)<option value="{{ $salesman->id }}" {{ (string)($salesmanId ?? '') === (string)$salesman->id ? 'selected' : '' }}>{{ $salesman->name }}</option>@endforeach</select></div>
        <div class="col-md-2 form-group"><label>From Date</label><input type="date" name="from_date" value="{{ $fromDate ?? '' }}" class="form-control"></div>
        <div class="col-md-2 form-group"><label>To Date</label><input type="date" name="to_date" value="{{ $toDate ?? '' }}" class="form-control"></div>
        <div class="col-md-3 form-group"><label>Search Text</label><input type="search" name="search" value="{{ $search ?? '' }}" class="form-control" placeholder="Invoice, total qty or product name"></div>
        <div class="col-md-2 form-group"><button class="btn btn-success mr-1">Filter</button><a href="{{ route('warehouse-salesman-assignments.transfer-list') }}" class="btn btn-light">Clear</a></div>
    </form>
    <div class="table-responsive"><table class="table table-bordered table-hover"><thead class="theme-primary text-white"><tr><th>SL</th><th>Invoice No.</th><th>Product Name</th><th>Date</th><th>Receiver</th><th class="text-right">Total Quantity</th><th>Action</th></tr></thead><tbody>@forelse($assignments as $assignment)<tr><td>{{ $assignments->firstItem()+$loop->index }}</td><td>{{ $assignment->invoice_no }}</td><td><div class="product-tags">@foreach($assignment->items as $item)<span class="product-tag">{{ $item->product?->product_name ?: '-' }}</span>@endforeach</div></td><td>{{ $assignment->assignment_date }}</td><td>{{ $assignment->salesman?->name ?: '-' }}</td><td class="text-right">{{ number_format($assignment->total_quantity, 2) }}</td><td><a class="btn btn-info btn-sm" href="{{ route('warehouse-salesman-assignments.show', $assignment) }}" title="View"><i class="fa fa-eye"></i></a></td></tr>@empty<tr><td colspan="7" class="text-center">No transfers found.</td></tr>@endforelse</tbody><tfoot><tr><th colspan="4"></th><th>Total</th><th class="text-right">{{ number_format((float) ($filteredTotalQuantity ?? 0), 2) }}</th><th></th></tr></tfoot></table></div>{{ $assignments->links() }}
</div></div></div>
@endsection

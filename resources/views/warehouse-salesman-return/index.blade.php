@extends('layouts.dashboard')
@section('title', 'Area Office Return History')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card-box mt-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="mb-1">Return History</h4>
                    <small class="text-muted">LSP products returned back into Area Office stock.</small>
                </div>
            </div>

            <form method="GET" class="mb-3">
                <div class="row">
                    <div class="col-md-4 form-group">
                        <label for="warehouse_id">Area Office</label>
                        <select name="warehouse_id" id="warehouse_id" class="form-control select2" onchange="this.form.submit()">
                            <option value="">All Area Offices</option>
                            @foreach($warehouses as $warehouse)
                                <option value="{{ $warehouse->id }}" {{ (int) $warehouseId === (int) $warehouse->id ? 'selected' : '' }}>
                                    {{ $warehouse->name }}{{ $warehouse->code ? ' (' . $warehouse->code . ')' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 form-group">
                        <label for="salesman_id">LSP</label>
                        <select name="salesman_id" id="salesman_id" class="form-control select2" onchange="this.form.submit()">
                            <option value="">All LSPs</option>
                            @foreach($salesmen as $salesman)
                                <option value="{{ $salesman->id }}" {{ (int) $salesmanId === (int) $salesman->id ? 'selected' : '' }}>
                                    {{ $salesman->name }}{{ $salesman->email ? ' - ' . $salesman->email : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 form-group">
                        <label for="per_page">Per Page</label>
                        <select name="per_page" id="per_page" class="form-control" onchange="this.form.submit()">
                            @foreach($perPageOptions as $option)
                                <option value="{{ $option }}" {{ (int) $perPage === (int) $option ? 'selected' : '' }}>{{ $option }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 form-group d-flex align-items-end">
                        <a href="{{ route('warehouse-salesman-returns.index') }}" class="btn btn-secondary btn-block">Reset</a>
                    </div>
                </div>
            </form>

            <div class="row mb-3">
                <div class="col-md-6">
                    <div class="alert alert-info mb-0">
                        <strong>Total Records:</strong> {{ number_format($returns->total()) }}
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="alert alert-success mb-0">
                        <strong>Returned Quantity:</strong> {{ number_format($filteredTotalQuantity, 2) }}
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="theme-primary text-white">
                        <tr>
                            <th>SL</th>
                            <th>Return Date Time</th>
                            <th>Invoice</th>
                            <th>Area Office</th>
                            <th>LSP</th>
                            <th>Products</th>
                            <th class="text-right">Quantity</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($returns as $return)
                            <tr>
                                <td>{{ $returns->firstItem() + $loop->index }}</td>
                                <td>{{ \Carbon\Carbon::parse($return->return_date_time)->format('d-m-Y h:i A') }}</td>
                                <td>{{ $return->invoice_no }}</td>
                                <td>{{ $return->warehouse?->name ?: '-' }}</td>
                                <td>{{ $return->salesman?->name ?: '-' }}</td>
                                <td>{{ $return->items->count() }}</td>
                                <td class="text-right">{{ number_format($return->total_quantity, 2) }}</td>
                                <td><a class="btn btn-info btn-sm" href="{{ route('warehouse-salesman-returns.show', $return) }}">View</a></td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="text-center">No return history found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $returns->links() }}
        </div>
    </div>
</div>
@endsection

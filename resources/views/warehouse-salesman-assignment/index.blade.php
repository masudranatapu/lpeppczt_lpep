@extends('layouts.dashboard')
@section('title', 'LSP Stock Assignments')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card-box mt-4">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div>
                    <h4 class="mb-1">LSP Stock Assignments</h4>
                    <small class="text-muted">Area Office stock allocated to individual salesmen.</small>
                </div>
                @if(!empty($warehouseId))
                    <a class="btn btn-primary" href="{{ route('warehouse-salesman-assignments.create', ['warehouse_id' => $warehouseId]) }}">New Assignment</a>
                @endif
            </div>

            <form method="GET" class="mb-3">
                @if(!empty($warehouseId))
                    <input type="hidden" name="warehouse_id" value="{{ $warehouseId }}">
                @endif
                <div class="row">
                    <div class="col-md-3 mb-2">
                        <label>Search</label>
                        <input type="text" name="search" value="{{ $search ?? '' }}" class="form-control" placeholder="Invoice, note, product">
                    </div>
                    <div class="col-md-2 mb-2">
                        <label>From</label>
                        <input type="date" name="from_date" value="{{ $fromDate ?? '' }}" class="form-control">
                    </div>
                    <div class="col-md-2 mb-2">
                        <label>To</label>
                        <input type="date" name="to_date" value="{{ $toDate ?? '' }}" class="form-control">
                    </div>
                    <div class="col-md-2 mb-2">
                        <label>LSP</label>
                        <select name="salesman_id" class="form-control select2">
                            <option value="">All LSPs</option>
                            @foreach($salesmen as $salesman)
                                <option value="{{ $salesman->id }}" @selected((string) ($salesmanId ?? '') === (string) $salesman->id)>{{ $salesman->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 mb-2">
                        <label>Per page</label>
                        <select name="per_page" class="form-control">
                            <option value="all" @selected((string) ($perPage ?? 'all') === 'all')>All</option>
                            <option value="10" @selected((string) ($perPage ?? 'all') === '10')>10</option>
                            <option value="25" @selected((string) ($perPage ?? 'all') === '25')>25</option>
                            <option value="50" @selected((string) ($perPage ?? 'all') === '50')>50</option>
                            <option value="100" @selected((string) ($perPage ?? 'all') === '100')>100</option>
                        </select>
                    </div>
                    <div class="col-md-1 mb-2 d-flex align-items-end">
                        <button class="btn btn-primary btn-block" type="submit">Filter</button>
                    </div>
                </div>
            </form>

            <div class="mb-3 text-muted">
                Total assigned quantity: <strong>{{ number_format((float) ($filteredTotalQuantity ?? 0), 2) }}</strong>
            </div>

            @include('warehouse-salesman-assignment.table', [
                'showRoute' => 'warehouse-salesman-assignments.show',
                'editRoute' => 'warehouse-salesman-assignments.edit',
                'deleteRoute' => 'warehouse-salesman-assignments.destroy',
            ])
        </div>
    </div>
</div>
@endsection

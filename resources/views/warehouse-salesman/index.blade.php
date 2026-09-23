@extends('layouts.dashboard')
@section('title', 'Area Office LSPs')

@section('content')
    <style>
        .sticky-table-header.fixed-solution {
            height: 41px !important;
        }
        .salesman-warehouse-badge {
            display: inline-block;
            min-width: 150px;
            padding: 8px 11px;
            border-left: 4px solid #2aa9b9;
            border-radius: 6px;
            background: #eef9fb;
            color: #167784;
            line-height: 1.2;
        }
        .salesman-warehouse-badge:hover {
            background: #dff4f7;
            color: #105c65;
        }
        .salesman-warehouse-code {
            display: block;
            margin-top: 3px;
            color: #6c757d;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
        }
    </style>

    <div class="row">
        <div class="col-12">
            <div class="card-box table-responsive mt-4" style="border-top: 3px solid #2aa9b9;">
                <div class="row">
                    <div class="col-md-6">
                        <h4 class="m-t-0 header-title mb-2">
                            <b>{{ $selectedWarehouse ? $selectedWarehouse->name . ' LSPs' : 'Area Office LSPs' }}</b>
                        </h4>
                        @if($selectedWarehouse)
                            <p class="text-muted mb-4">Showing only salesmen assigned to {{ $selectedWarehouse->name }} ({{ $selectedWarehouse->code }}).</p>
                        @endif
                    </div>
                    <div class="col-md-6 text-right">
                        @if($selectedWarehouse)
                            <a href="{{ route('warehouses.show', $selectedWarehouse) }}" class="btn btn-secondary waves-effect waves-light m-b-5">
                                <i class="fa fa-arrow-left m-r-5"></i>
                                <span>Back to Area Office</span>
                            </a>
                        @endif
                        <a href="{{ route('warehouse-salesmen.create', $selectedWarehouse ? ['warehouse_id' => $selectedWarehouse->id] : []) }}" class="btn btn-primary waves-effect waves-light m-b-5">
                            <i class="fa fa-plus-square m-r-5"></i>
                            <span>Add LSP</span>
                        </a>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <form method="GET" action="{{ route('warehouse-salesmen.index') }}">
                            <div class="form-group mb-0">
                                <label for="warehouse-filter">Filter by Area Office</label>
                                <select
                                    id="warehouse-filter"
                                    name="warehouse_id"
                                    class="form-control select2"
                                    onchange="this.form.submit()"
                                >
                                    <option value="">All Area Offices</option>
                                    @foreach($warehouses as $warehouse)
                                        <option value="{{ $warehouse->id }}" {{ optional($selectedWarehouse)->id == $warehouse->id ? 'selected' : '' }}>
                                            {{ $warehouse->name }} ({{ $warehouse->code }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="table-rep-plugin">
                    <div class="table-responsive" id="tablefixed">
                        <table id="data-table" class="table table-bordered table-hover mt-0" cellspacing="0" width="100%">
                            <thead class="theme-primary text-white">
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Assigned Area Office</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Address</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($salesmen as $salesman)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $salesman->name }}</td>
                                        <td>
                                            @if($salesman->warehouse)
                                                <a href="{{ route('warehouses.show', $salesman->warehouse) }}" class="salesman-warehouse-badge">
                                                    <strong>{{ $salesman->warehouse->name }}</strong>
                                                    <span class="salesman-warehouse-code">Code: {{ $salesman->warehouse->code }}</span>
                                                </a>
                                            @else
                                                <span class="badge badge-danger">Not assigned</span>
                                            @endif
                                        </td>
                                        <td>{{ $salesman->email }}</td>
                                        <td>{{ $salesman->phone ?: '-' }}</td>
                                        <td>{{ $salesman->address ?: '-' }}</td>
                                        <td>{{ $salesman->status ? 'Active' : 'Inactive' }}</td>
                                        <td>
                                            <div class="btn-group btn-sm">
                                                <button type="button" class="btn btn-info dropdown-toggle waves-effect btn-sm"
                                                    data-toggle="dropdown" aria-expanded="false">
                                                    Action <span class="caret"></span>
                                                </button>
                                                <div class="dropdown-menu" x-placement="bottom-start"
                                                    style="position: absolute; transform: translate3d(0px, 35px, 0px); top: 0px; left: 0px; will-change: transform;">
                                                    <a href="{{ route('warehouse-salesmen.show', $salesman) }}" class="dropdown-item">View</a>
                                                    <a href="{{ route('warehouse-salesmen.reports', $salesman) }}" class="dropdown-item">Reports</a>
                                                    <a href="{{ route('warehouse-salesmen.edit', $salesman) }}" class="dropdown-item">Edit</a>
                                                    <form action="{{ route('warehouse-salesmen.destroy', $salesman) }}" method="POST"
                                                        onsubmit="return confirm('Delete this salesman?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button class="dropdown-item text-danger" type="submit">Delete</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">No salesman found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

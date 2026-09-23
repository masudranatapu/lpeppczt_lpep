@extends('layouts.dashboard')
@section('title', 'Area Offices')

@section('content')
    <style>
        .sticky-table-header.fixed-solution {
            height: 41px !important;
        }
    </style>

    <div class="row">
        <div class="col-12">
            <div class="card-box table-responsive mt-4" style="border-top: 3px solid #2aa9b9;">
                <div class="row">
                    <div class="col-md-6">
                        <h4 class="m-t-0 header-title mb-5"><b>Area Offices</b></h4>
                    </div>
                    <div class="col-md-6 text-right">
                        <a href="{{ route('warehouses.create') }}" class="btn btn-primary waves-effect waves-light m-b-5">
                            <i class="fa fa-plus-square m-r-5"></i>
                            <span>Add Area Office</span>
                        </a>
                    </div>
                </div>

                <div class="table-rep-plugin">
                    <div class="table-responsive" id="tablefixed">
                        <table id="data-table" class="table table-bordered table-hover mt-0" cellspacing="0" width="100%">
                            <thead class="theme-primary text-white">
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Code</th>
                                    <th>LSPs</th>
                                    <th>Stock Receipts</th>
                                    <th>Sales</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($warehouses as $warehouse)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $warehouse->name }}</td>
                                        <td>{{ $warehouse->code }}</td>
                                        <td>{{ $warehouse->salesmen_count }}</td>
                                        <td>{{ $warehouse->purchases_count + $warehouse->stock_transfers_count }}</td>
                                        <td>{{ $warehouse->sales_count }}</td>
                                        <td>{{ $warehouse->status ? 'Active' : 'Inactive' }}</td>
                                        <td>
                                            <div class="btn-group btn-sm">
                                                <button type="button" class="btn btn-info dropdown-toggle waves-effect btn-sm"
                                                    data-toggle="dropdown" aria-expanded="false">
                                                    Action <span class="caret"></span>
                                                </button>
                                                <div class="dropdown-menu" x-placement="bottom-start"
                                                    style="position: absolute; transform: translate3d(0px, 35px, 0px); top: 0px; left: 0px; will-change: transform;">
                                                    <a href="{{ route('warehouses.show', $warehouse) }}" class="dropdown-item">View</a>
                                                    <a href="{{ route('warehouses.edit', $warehouse) }}" class="dropdown-item">Edit</a>
                                                    <form action="{{ route('warehouses.destroy', $warehouse) }}" method="POST"
                                                        onsubmit="return confirm('Delete this Area Office?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item text-danger">Delete</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">No Area Office found.</td>
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

@extends('layouts.dashboard')
@section('title', 'Area Manager Daily Report')

@section('content')
    @php($exportQuery = ['warehouse_id' => $warehouse->id, 'month' => $month])

    <div class="row">
        <div class="col-12">
            <div class="card-box mt-4" style="border-top: 3px solid #43a783;">
                <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                    <h4 class="header-title m-0"><b>Area Manager Daily Report</b></h4>
                    <div>
                        <a href="{{ route('warehouses.area-manager-daily.pdf', $exportQuery) }}" class="btn btn-danger btn-sm mr-1"><i class="fa fa-file-pdf-o mr-1"></i>PDF</a>
                        <a href="{{ route('warehouses.area-manager-daily.excel', $exportQuery) }}" class="btn btn-success btn-sm"><i class="fa fa-file-excel-o mr-1"></i>Excel</a>
                    </div>
                </div>

                <form method="GET" action="{{ route('warehouses.area-manager-daily') }}" class="row align-items-end mb-3">
                    <div class="col-md-3 mb-2">
                        <label for="warehouse_id">Area Office</label>
                        <select name="warehouse_id" id="warehouse_id" class="form-control">
                            @foreach($warehouses as $office)
                                <option value="{{ $office->id }}" {{ (int) $office->id === (int) $warehouse->id ? 'selected' : '' }}>{{ $office->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-2">
                        <label for="month">Month</label>
                        <input type="month" name="month" id="month" value="{{ $month }}" class="form-control" required>
                    </div>
                    <div class="col-md-2 mb-2">
                        <button type="submit" class="btn btn-primary btn-block">Show</button>
                    </div>
                    <div class="col-md-4 mb-2 text-md-right">
                        <small class="text-muted">
                            Daily target: <strong>{{ number_format($target) }} BDT</strong>
                            (<a href="{{ route('warehouses.edit', $warehouse) }}">change</a>)
                        </small>
                    </div>
                </form>

                <div class="table-responsive">
                    <div style="min-width: 1000px;">
                        @include('warehouse-portal.reports.partials.area-manager-daily-sheet')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@extends('layouts.dashboard')
@section('title', 'Sold Product Report')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card mt-4">
                <div class="card-header">
                    <h3 class="m-t-0 header-title mb-2"><b>{{ __('sidebar.soldproductreport') }}</b></h3>
                </div>
                <div class="card-body">
                    <form method="get" id="filter" class="mb-4">
                        <div class="row mb-4 d-flex justify-content-end">
                            <div class="col-md-3">
                                <input class="form-control datePicker" value="{{ request('start_date') ?? '' }}"
                                    type="text" value="{{ request('start_date') }}" name="start_date" placeholder="Start Date" data-date-format="mm-dd-yyyy"
                                    required autocomplete="off">
                            </div>
                            <div class="col-md-3">
                                <input class="form-control datePicker" value="{{ request('end_date') ?? '' }}" type="text"
                                    name="end_date" value="{{ request('end_date') }}" placeholder="end Date" data-date-format="mm-dd-yyyy" required
                                    autocomplete="off">
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-outline-primary btn-block" style="cursor: pointer">
                                    <i class="fa fa-search"></i> Search
                                </button>
                            </div>
                        </div>
                    </form>
                    <div class="table-rep-plugin">
                        <div class="table-responsive" id="tablefixed">
                            {!! $dataTable->table(['class' => 'table table-bordered table-hover', 'id' => 'data-table'], true) !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="/vendor/datatables/buttons.server-side.js"></script>
    {!! $dataTable->scripts() !!}
    <script>
        $(function() {
            $(".datePicker").datepicker({
                autoclose: true,
                todayHighlight: true,
                dateFormat: 'yyyy-MM-dd',
                format: 'yyyy-mm-dd',
            })
        });
    </script>
@endsection

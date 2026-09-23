@extends('layouts.dashboard')

@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="card table-responsive mt-4">
                <div class="card-header">
                    <h3 class="header-title"><b>{{ __('page.preport')[0] }}</b></h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <form action="" class="mb-4" method="GET">
                                <div class="row d-flex justify-content-end">
                                    <div class="col-md-5">
                                        <div class="">
                                            <div class="
                                            input-daterange input-group" id="date-range">
                                            <input type="text" class="form-control datePicker startdate" name="start_date"
                                                value="{{ request('start_date') ?? '' }}" placeholder="Date"
                                                autocomplete="off">
                                            <span class="input-group-addon bg-primary b-0 text-white">to</span>
                                            <input type="text" class="form-control datePicker enddate" name="end_date"
                                                value="{{ request('end_date') ?? '' }}" placeholder="Date"
                                                autocomplete="off">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <input type="text" name="search_by" value="{{ request('search_by') ?? '' }}"
                                        class="form-control" placeholder="search ....">
                                </div>

                                <div class="col-md-1">
                                    <button class="btn btn-info btn-block" type="submit">Search</button>
                                </div>
                            </form>
                        </div>

                    </div>

                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="table-rep-plugin">
                            <div class="table-responsive" id="tablefixed">
                                {!! $dataTable->table(['class' => 'table table-bordered table-hover', 'id' => 'data-table'], true) !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    <!-- end row -->

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
        $(".startdate").change(function() {
            $(".enddate").val($(this).val())
        })
    </script>
@endsection

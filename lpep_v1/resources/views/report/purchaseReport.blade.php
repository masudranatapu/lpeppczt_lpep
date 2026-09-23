@extends('layouts.dashboard')

@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="card mt-4">
                <div class="card-header">
                    <div class="col-md-3">
                        <h4 class="m-t-0 header-title mt-2"><b>{{ __('page.pureport')[0] }}</b></h4>
                    </div>
                </div>
                <div class="card-body">
                    <form action="" method="GET" class="mb-4">
                        <div class="row d-flex justify-content-end">
                            <div class="col-md-7">
                                <div class="input-daterange input-group" id="date-range">
                                    <input type="text" class="form-control datepicker" name="start_date"
                                        placeholder="Start Date" value="{{ request('start_date') }}"  required />
                                    <span class="input-group-addon bg-primary b-0 text-white">to</span>
                                    <input type="text" value="{{ request('end_date') }}" class="form-control datepicker" name="end_date"
                                        placeholder="End Date" required />
                                </div>
                            </div>
                            <div class="col-md-2">
                                <button class="btn btn-outline-primary btn-block" type="submit" style="cursor: pointer">
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
            $(".datepicker").datepicker({
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

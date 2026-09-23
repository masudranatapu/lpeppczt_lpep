@extends('layouts.dashboard')

@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="card mt-4">
                <div class="card-header">
                    <div class="col-md-4">
                        <h4 class="m-t-0 header-title mt-2"><b>{{ __('page.sreport')[11] }}</b></h4>
                    </div>
                </div>
                <div class="card-body">

                    <form action="" class="mb-4 row d-flex justify-content-end" method="GET">
                        <div class="col-md-3">
                            <select name="month" class="form-control" id="">
                                <option value="1">January</option>
                                <option value="2">February</option>
                                <option value="3">March</option>
                                <option value="4">April</option>
                                <option value="5">May</option>
                                <option value="6">June</option>
                                <option value="7">July</option>
                                <option value="8">August</option>
                                <option value="9">September</option>
                                <option value="11">October</option>
                                <option value="12">December</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-outline-primary btn-block" style="cursor: pointer" type="submit">
                                <i class="fa fa-search"></i> Search
                            </button>
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
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.0.3/css/buttons.dataTables.min.css">
    <script src="https://cdn.datatables.net/buttons/1.0.3/js/dataTables.buttons.min.js"></script>
    <script src="/vendor/datatables/buttons.server-side.js"></script>
    {!! $dataTable->scripts() !!}
@endsection

@extends('layouts.dashboard')

@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="card mt-4">
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-8">
                            <h4 class="m-t-0 header-title mb-3"><b>{{ __('page.sreport')[10] }}</b></h4>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form action="" method="get">
                        <div class="row justify-content-end" style="margin-right: 0px !important;">
                            <div class="col-md-1">
                                <button type="submit" class="btn btn-outline-primary">
                                    <i class="fa fa-search"></i> Search
                                </button>
                            </div>
                        </div>
                    </form>

                    <div class="table-rep-plugin mt-3">
                        <div class="table-responsive" id="tablefixed">
                            {!! $dataTable->table(['class' => 'table table-bordered table-hover', 'id' => 'data-table'], true) !!}
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

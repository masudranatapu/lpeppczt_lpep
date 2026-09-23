@extends('layouts.dashboard')

@section('content')
    <style>
        .dt-buttons {
            margin-left: -3px !important;
        }
    </style>
    <div class="row">
        <div class="col-12">
            <div class="card-box table-responsive mt-4" style="border-top: 3px solid #2aa9b9;">
                <div class="row">
                    <div class="col-md-6">
                        <h4 class="m-t-0 header-title mb-5"><b>{{ __('Types Of Income') }}</b></h4>
                    </div>

                    <div class="col-md-6 text-right">
                        <a href="{{ route('incomeType.create') }}"
                            class="btn btn-success waves-effect waves-light m-b-5 btn-rounded">
                            <i class="fa fa-plus-square m-r-5"></i> <span>{{ __('Add New') }}</span>
                        </a>

                        <a href="{{ route('incomeType.report') }}" class="btn btn-success btn-rounded waves-effect waves-light m-b-5">
                            <span>{{ __('Income Type Sales Report') }}</span>
                        </a>
                    </div>

                </div>

                <table id="yagent-table" class="table table-striped table-bordered mt-4" cellspacing="0" width="100%">
                    <thead class="theme-primary text-white">
                        <tr>
                            <th> {{ __('page.income')[2] }} </th>
                            <th> {{ __('Type') }} </th>
                            <th>{{ __('page.income')[7] }}</th>
                        </tr>
                    </thead>

                </table>

            </div>
        </div>
    </div>
    <!-- end row -->
@endsection
@section('script')
    <script>
        $(document).ready(function() {
            if (!$.fn.DataTable.isDataTable('#yagent-table')) { // Prevent reinitialization
                var table = $('#yagent-table').DataTable({
                    processing: true,
                    retrieve: true,
                    serverSide: true,
                    paginate: true,
                    searchDelay: 700,
                    bDeferRender: true,
                    responsive: true,
                    autoWidth: false,
                    ajax: "{{ route('incomeType.index') }}",
                    columns: [{
                            "data": 'DT_RowIndex',
                            orderable: false,
                            searchable: false
                        },

                        {
                            data: 'income_type',
                            name: 'income_type'
                        },

                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: true
                        },
                    ]
                });
            }

        })
    </script>
@endsection

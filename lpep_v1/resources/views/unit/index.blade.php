@extends('layouts.dashboard')

@section('content')

    <div class="row">
        <div class="col-12">
            <div class="card-box table-responsive mt-4" style="border-top: 3px solid #2aa9b9;">
                <div class="row">
                    <div class="col-md-6">
                        <h4 class="m-t-0 header-title mb-5"><b>{{ __('page.unit')[0] }}</b></h4>
                    </div>
                    <div class="col-md-6 text-right">
                        @if (permission('uni2'))
                            <button class="btn btn-primary waves-effect waves-light m-b-5" id="addNew"> <i
                                    class="fa fa-plus-square m-r-5"></i> <span>{{ __('page.unit')[1] }}</span> </button>
                        @endif
                    </div>
                </div>

                <table id="data-table" class="table table-striped table-bordered mt-4" cellspacing="0" width="100%">
                    <thead class="theme-primary text-white">
                        <tr>
                            <th>{{ __('page.unit')[2] }}</th>
                            <th>{{ __('page.unit')[3] }}</th>
                            <th>{{ __('page.unit')[4] }}</th>
                            <th>{{ __('page.unit')[8] }}</th>
                            <th>{{ __('page.unit')[5] }}</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- end row -->

    <!-- Business Modal Start -->
    <div class="modal fade bd-example-modal-xl" id="unitModal" tabindex="-1" role="dialog"
        aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog" style="width: 100%">
            <div class="modal-content" id="modalcontent">

            </div>
        </div>
    </div>
    <!-- Business Modal End -->


@endsection
@section('script')
    <script>
        $(document).ready(function() {
            function getData() {
                $('#data-table').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: "{{ route('all.unit.json') }}",
                    columns: [{
                            "data": 'DT_RowIndex',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'actual_name',
                            name: 'actual_name'
                        },
                        {
                            data: 'short_name',
                            name: 'short_name'
                        },
                        {
                            data: 'decimal',
                            name: 'decimal'
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
            getData();

            $('body').on('click', "#addNew", function() {
                $.get("{{ route('unit.create') }}", function(data) {
                    $('#modalcontent').html(data)
                    $("#unitModal").modal('show')
                });
            })

            $('body').on('click', "#submit", function(e) {
                e.preventDefault();
                let actual_name = $("input[name=actual_name]").val();
                let short_name = $("input[name=short_name]").val();
                let is_decimal = $("input[name=is_decimal]").prop('checked');
                let _token = "{{ csrf_token() }}"
                $.post("{{ route('unit.store') }}", {
                    _token: _token,
                    actual_name: actual_name,
                    short_name: short_name,
                    is_decimal: is_decimal
                }, function(data) {
                    toastr.success(data)
                    var oTable = $('#data-table').dataTable();
                    oTable.fnDraw(false);
                    $("#unitModal").modal('hide')
                })
            })

            $('body').on('click', "#submitUpdate", function(e) {
                e.preventDefault();
                let id = $("input[name=id]").val();
                let actual_name = $("input[name=actual_name]").val();
                let short_name = $("input[name=short_name]").val();
                let is_decimal = $("input[name=is_decimal]").prop('checked');
                let _token = "{{ csrf_token() }}"
                $.post("{{ route('unit.updated') }}", {
                    _token: _token,
                    id: id,
                    actual_name: actual_name,
                    short_name: short_name,
                    is_decimal: is_decimal
                }, function(data) {
                    toastr.success(data)
                    var oTable = $('#data-table').dataTable();
                    oTable.fnDraw(false);
                    $("#unitModal").modal('hide')
                })
            })

            $('body').on('click', "#unitEdit", function() {
                let id = $(this).data('id')

                $.get(`/unit/${id}/edit`, function(data) {
                    $('#modalcontent').html(data)
                    $("#unitModal").modal('show')
                });
            })

            $('body').on('click', "#deleteData", function() {
                let id = $(this).data('id')

                swal({
                        title: "Are you Want to Delete?",
                        text: "Once Delete, This will be permanently Delete!",
                        icon: "warning",
                        buttons: true,
                        dangerMode: true,
                    })
                    .then((willDelete) => {
                        if (willDelete) {
                            let _token = "{{ csrf_token() }}"
                            $.post("{{ route('unit.delete') }}", {
                                _token: _token,
                                id: id
                            }, function(data) {
                                toastr.success(data)
                                var oTable = $('#data-table').dataTable();
                                oTable.fnDraw(false);
                            })
                        } else {
                            swal("Cancelled", "Your Data Is Safe :)", "error");
                        }
                    });
            })
        })
    </script>
@endsection

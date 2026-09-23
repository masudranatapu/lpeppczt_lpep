@extends('layouts.dashboard')

@section('content')

    <div class="row">
        <div class="col-12">
            <div class="card-box table-responsive mt-4" style="border-top: 3px solid #2aa9b9;">
                <div class="row">
                    <div class="col-md-6">
                        <h4 class="m-t-0 header-title mb-5"><b>{{ __('page.insurance-company')[0] }}</b></h4>
                    </div>
                    <div class="col-md-6 text-right">
                        @if (permission('uni2'))
                            <button class="btn btn-primary waves-effect waves-light m-b-5" id="addNew"><i
                                    class="fa fa-plus-square m-r-5"></i> <span>{{ __('page.insurance-company')[1] }}</span>
                            </button>
                        @endif
                    </div>
                </div>

                <table id="data-table" class="table table-striped table-bordered mt-4" cellspacing="0" width="100%">
                    <thead class="theme-primary text-white">
                    <tr>
                        <th>{{ __('page.insurance-company')[2] }}</th>
                        <th>{{ __('page.insurance-company')[3] }}</th>
                        <th>{{ __('page.insurance-company')[4] }}</th>
                    </tr>
                    </thead>

                </table>
            </div>
        </div>
    </div>
    <!-- end row -->

    <!-- Modal Start -->
    <div class="modal fade bd-example-modal-xl" id="tableModal" tabindex="-1" role="dialog"
         aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog" style="width: 100%">
            <div class="modal-content" id="modalContent">

            </div>
        </div>
    </div>
    <!-- Modal End -->


@endsection
@section('script')
    <script>

        $(document).ready(function () {

            function getData() {
                $('#data-table').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: "{{ route('insurance-companies.json.all') }}",
                    order: [ [0, 'desc'] ],
                    columns: [
                        {
                            width: '8%',
                            data: 'DT_RowIndex',
                            name: 'id',
                            orderable: true,
                            searchable: false
                        },
                        {width: '72%', data: 'name', name: 'name'},
                        {
                            width: '20%',
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: true
                        },
                    ],
                    drawCallback: function (settings) {
                        $("[data-toggle=popover]").popover();
                    }
                });
            }

            getData();

            $('body').on('click', "#addNew", function () {
                $.get("{{ route('insurance-companies.create') }}", function (data) {
                    $('#modalContent').html(data)
                    $("#tableModal").modal('show')
                });
            })

            $('body').on('click', "#submit", function (e) {
                e.preventDefault();
                let name = $("input[name=name]").val();

                $.ajax({
                    url: "{{ route('insurance-companies.store') }}",
                    data: {
                        _token: "{{ csrf_token() }}", user_id: '{{auth()->id()}}',
                        name: name
                    },
                    method: 'POST'
                })
                    .then(function (data) {
                        toastr.success(data)
                        const oTable = $('#data-table').dataTable();
                        oTable.fnDraw(false);
                        $("#tableModal").modal('hide')
                    });
            })

            $('body').on('click', "#submitUpdate", function (e) {
                e.preventDefault();
                const id = $("input[name=id]").val();
                const name = $("input[name=name]").val();

                $.ajax({
                    url: "{{ route('insurance-companies.update', '#id') }}".replace('#id', id),
                    data: {
                        _token: "{{ csrf_token() }}", user_id: '{{auth()->id()}}',
                        name: name
                    },
                    method: 'PUT'
                })
                    .then(function (data) {
                        toastr.success(data)
                        const oTable = $('#data-table').dataTable();
                        oTable.fnDraw(false);
                        $("#tableModal").modal('hide')
                    });
            })

            $('body').on('click', "#tableEdit", function () {
                let id = $(this).data('id')

                $.get('{{route('insurance-companies.edit', '#id')}}'.replace('#id', id), function (data) {
                    $('#modalContent').html(data)
                    $("#tableModal").modal('show')
                });
            })

            $('body').on('click', "#deleteData", function () {
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
                            $.ajax({
                                url: "{{ route('insurance-companies.destroy', '#id') }}".replace('#id', id),
                                data: {_token: "{{ csrf_token() }}"},
                                method: 'DELETE'
                            })
                                .then(function (data) {
                                    toastr.success(data)
                                    const oTable = $('#data-table').dataTable();
                                    oTable.fnDraw(false);
                                });
                        } else {
                            swal("Cancelled", "Your Data Is Safe :)", "error");
                        }
                    });
            })
        })

    </script>
@endsection

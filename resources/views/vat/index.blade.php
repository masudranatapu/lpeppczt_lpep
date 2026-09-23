@extends('layouts.dashboard')

@section('content')

    <div class="row">
        <div class="col-12">
            <div class="card-box table-responsive mt-4" style="border-top: 3px solid #2aa9b9;">
                <div class="row">
                    <div class="col-md-6">
                        <h4 class="m-t-0 header-title mb-5"><b>{{ __('page.vatsd')[0] }}</b></h4>
                    </div>
                    <div class="col-md-6 text-right">
                        <button class="btn btn-primary waves-effect waves-light m-b-5" id="addNew"> <i
                                class="fa fa-plus-square m-r-5"></i> <span>{{ __('page.vatsd')[1] }}</span> </button>
                    </div>
                </div>

                <table id="data-table" class="table table-bordered mt-4" cellspacing="0" width="100%">
                    <thead class="theme-primary text-white">
                        <tr>
                            <th>{{ __('page.vatsd')[2] }}</th>
                            <th>{{ __('page.vatsd')[3] }}</th>
                            <th>{{ __('page.vatsd')[4] }} <strong>(%)</strong></th>
                            <th>{{ __('page.vatsd')[5] }}</th>
                        </tr>
                    </thead>

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
                    ajax: "{{ route('vat-group.all') }}",
                    columns: [{
                            "data": 'DT_RowIndex',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'vat_group_name',
                            name: 'vat_group_name'
                        },
                        {
                            data: 'vat_percent',
                            name: 'vat_percent'
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
                $.get("{{ route('vat-sd.create') }}", function(data) {
                    $('#modalcontent').html(data)
                    $("#unitModal").modal('show')
                });
            })

            $('body').on('click', "#submit", function(e) {
                e.preventDefault();
                let vat_group_name = $("input[name=vat_group_name]").val();
                let vat_percent = $("input[name=vat_percent]").val();
                let _token = "{{ csrf_token() }}"
                $.post("{{ route('vat-sd.store') }}", {
                    _token: _token,
                    vat_group_name: vat_group_name,
                    vat_percent: vat_percent
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
                let vat_group_name = $("input[name=vat_group_name]").val();
                let vat_percent = $("input[name=vat_percent]").val();
                let _token = "{{ csrf_token() }}"
                $.post("{{ route('vat-sd.update') }}", {
                    _token: _token,
                    id: id,
                    vat_group_name: vat_group_name,
                    vat_percent: vat_percent
                }, function(data) {
                    toastr.success(data)
                    var oTable = $('#data-table').dataTable();
                    oTable.fnDraw(false);
                    $("#unitModal").modal('hide')
                })
            })

            $('body').on('click', "#unitEdit", function() {
                let id = $(this).data('id')

                $.get(`/vat-sd/group/${id}/edit`, function(data) {
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
                            $.post("{{ route('vat-group.delete') }}", {
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

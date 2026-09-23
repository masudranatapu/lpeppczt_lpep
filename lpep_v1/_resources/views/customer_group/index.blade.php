@extends('layouts.dashboard')

@section('content')

    <div class="row">
        <div class="col-12">
            <div class="card-box table-responsive mt-4" style="border-top: 3px solid #00BCD4;">
                <div class="row">
                    <div class="col-md-6">
                        <h4 class="m-t-0 header-title mb-5"><b>{{ __('page.customergroup')[0] }}</b></h4>
                    </div>
                    <div class="col-md-6 text-right">
                        <button class="btn btn-primary waves-effect waves-light m-b-5" id="addNew"> <i
                                class="fa fa-plus-square m-r-5"></i> <span>{{ __('page.customergroup')[1] }}</span>
                        </button>
                    </div>
                </div>

                <table id="data-table" class="table table-striped table-bordered mt-4" cellspacing="0" width="100%">
                    <thead class="theme-primary text-white">
                        <tr>
                            <th>{{ __('page.customergroup')[2] }}</th>
                            <th>{{ __('page.customergroup')[3] }}</th>
                            <th>{{ __('page.customergroup')[4] }} (%)</th>
                            <th>{{ __('page.customergroup')[5] }}</th>
                        </tr>
                    </thead>

                </table>
            </div>
        </div>
    </div>
    <!-- end row -->

    <!-- Business Modal Start -->
    <div class="modal fade bd-example-modal-xl" id="customerGroupModal" tabindex="-1" role="dialog"
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
                    ajax: "{{ route('allcustomer.json') }}",
                    columns: [{
                            "data": 'DT_RowIndex',
                            orderable: false,
                            searchable: false,
                            width: '15%',
                        },
                        {
                            data: 'name',
                            name: 'name',
                            width: '30%',
                        },
                        {
                            data: 'amount',
                            name: 'amount',
                            width: '30%',
                        },
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: true,
                            width: '25%',
                        },
                    ],
                    drawCallback: function(settings) {
                        $("[data-toggle=popover]").popover();
                    }
                });
            }
            getData();

            $('body').on('click', "#addNew", function() {
                $.get("{{ route('customer-group.create') }}", function(data) {
                    $('#modalcontent').html(data)
                    $("#customerGroupModal").modal('show')
                });
            })

            $('body').on('click', "#submit", function(e) {
                e.preventDefault();
                let name = $("input[name=name]").val();
                let amount = $("input[name=amount]").val();
                let _token = "{{ csrf_token() }}"
                $.post("{{ route('customer-group.store') }}", {
                    _token: _token,
                    name: name,
                    amount: amount
                }, function(data) {
                    toastr.success(data)
                    var oTable = $('#data-table').dataTable();
                    oTable.fnDraw(false);
                    $("#customerGroupModal").modal('hide')
                })
            })

            $('body').on('click', "#submitUpdate", function(e) {
                e.preventDefault();
                let id = $("input[name=id]").val();
                let name = $("input[name=name]").val();
                let amount = $("input[name=amount]").val();
                let _token = "{{ csrf_token() }}"
                $.post("{{ route('customer-group.updateded') }}", {
                    _token: _token,
                    id: id,
                    name: name,
                    amount: amount
                }, function(data) {
                    toastr.success(data)
                    var oTable = $('#data-table').dataTable();
                    oTable.fnDraw(false);
                    $("#customerGroupModal").modal('hide')
                })
            })

            $('body').on('click', "#customerGroupEdit", function() {
                let id = $(this).data('id')

                $.get(`/customer-group/${id}/edit`, function(data) {
                    $('#modalcontent').html(data)
                    $("#customerGroupModal").modal('show')
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
                            $.post("{{ route('customer-group.delete') }}", {
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

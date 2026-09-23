@extends('layouts.dashboard')

@push('css')
    <style>
        #deleteData.disabled {
            pointer-events: none;
            background: #2aa9b9;
        }

    </style>
@endpush

@section('content')

    <div class="row">
        <div class="col-12">
            <div class="card-box table-responsive mt-4" style="border-top: 3px solid #2aa9b9;">
                <div class="row">
                    <div class="col-md-6">
                        <h4 class="m-t-0 header-title mb-5"><b>{{ __('page.admin_deposit.deposit_list') }}</b></h4>
                    </div>
                    <div class="col-md-6 text-right">
                        <button class="btn btn-primary waves-effect waves-light m-b-5" id="addNew"> <i
                                class="fa fa-plus-square m-r-5"></i>
                            <span>{{ __('page.admin_deposit.add_deposit') }}</span>
                        </button>
                    </div>
                </div>

                <table id="data-table" class="table table-striped table-bordered mt-4" cellspacing="0" width="100%">
                    <thead class="theme-primary text-white">
                        <tr>
                            <th>{{ __('page.common.sl') }}</th>
                            <th>{{ __('page.common.invoice_no') }}</th>
                            <th>{{ __('page.common.date') }}</th>
                            <th>{{ __('page.common.amount') }}</th>
                            <th>{{ __('page.admin_deposit.loan_amount') }}</th>
                            <th>{{ __('page.admin_deposit.available_amount') }}</th>
                            <th>{{ __('page.common.action') }}</th>
                        </tr>
                    </thead>

                </table>
            </div>
        </div>
    </div>
    <!-- end row -->

    <!-- Business Modal Start -->
    <div class="modal fade bd-example-modal-xl" id="pointModal" tabindex="-1" role="dialog"
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

            function datePicker() {
                $(".datepicker").datepicker({
                    autoclose: true,
                    todayHighlight: true,
                    dateFormat: 'yyyy-MM-dd',
                    format: 'yyyy-mm-dd',
                })
            }

            function getData() {
                $('#data-table').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: "{{ route('admin-deposit.index') }}",
                    columns: [{
                            width: '10%',
                            data: 'DT_RowIndex',
                            orderable: true,
                            searchable: false
                        },
                        {
                            width: '15%',
                            data: 'invoice_no',
                            name: 'invoice_no'
                        },
                        {
                            width: '15%',
                            data: 'date',
                            name: 'date'
                        },
                        {
                            width: '15%',
                            data: 'amount',
                            name: 'amount'
                        },
                        {
                            width: '15%',
                            data: 'loan_amount',
                            name: 'loan_amount'
                        },
                        {
                            width: '15%',
                            data: 'available_amount',
                            name: 'available_amount'
                        },
                        {
                            width: '15%',
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: true
                        },
                    ],
                    drawCallback: function(settings) {
                        $("[data-toggle=popover]").popover();
                    }
                });
            }
            getData();

            $('body').on('click', "#addNew", function() {
                $.get("{{ route('admin-deposit.create') }}", function(data) {
                    $('#modalcontent').html(data)
                    $("#pointModal").modal('show')
                    datePicker()
                });
            })

            $('body').on('submit', "#depositStoreForm", function(e) {
                e.preventDefault();
                $.post("{{ route('admin-deposit.store') }}", $(this).serialize(), function(res) {
                        toastr.success(res.message)
                        var oTable = $('#data-table').dataTable();
                        oTable.fnDraw(false);
                        $("#pointModal").modal('hide')
                    })
                    .fail(function(error) {
                        console.log(error)
                    });
            })

            $('body').on('click', "#editDeposit", function() {
                let id = $(this).data('id')

                $.get('{{ route('admin-deposit.edit', '#id') }}'.replace('#id', id), function(data) {
                    $('#modalcontent').html(data)
                    $("#pointModal").modal('show')
                    datePicker()
                });
            })

            $('body').on('submit', "#depositUpdateForm", function(e) {
                e.preventDefault();
                let id = $("input[name=id]").val();

                $.ajax({
                    url: '{{ route('admin-deposit.update', '#id') }}'.replace('#id', id),
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function(res) {
                        toastr.success(res.message)
                        var oTable = $('#data-table').dataTable();
                        oTable.fnDraw(false);
                        $("#pointModal").modal('hide')
                    }
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
                            $.ajax({
                                    url: "{{ route('admin-deposit.delete', '#id') }}".replace(
                                        '#id', id),
                                    method: 'get'
                                })
                                .then(function(res) {
                                    if (res.message) {
                                        toastr.success(res.message)
                                        const oTable = $('#data-table').dataTable();
                                        oTable.fnDraw(false);
                                    } else {
                                        toastr.error(res.error)
                                    }
                                });
                        } else {
                            swal("Cancelled", "Your Data Is Safe :)", "error");
                        }
                    });
            })
        })
    </script>
@endsection

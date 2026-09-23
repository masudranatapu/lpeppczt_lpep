@extends('layouts.dashboard')
@section('title', 'Contact List')
@section('content')
    <style>
        table tr.bg-danger {
            background-color: rgb(220, 223, 192) !important;
        }

    </style>

    <div class="row">
        <div class="col-12">
            <div class="card-box table-responsive mt-4" style="border-top: 3px solid #00BCD4;">
                <div class="row">
                    <div class="col-md-6">
                        <h4 class="m-t-0 header-title mb-5">
                            <b>{{ $type == 'supplier' ? __('page.supplier')[0] : __('page.customer')[0] }}</b>
                        </h4>
                    </div>
                    <div class="col-md-6 text-right">
                        @if ($type == 'supplier')
                            @if (permission('s2'))
                                <button class="btn btn-primary waves-effect waves-light m-b-5" id="addNewSupplier"> <i
                                        class="fa fa-plus-square m-r-5"></i> <span>{{ __('page.supplier')[1] }}</span>
                                </button>
                            @endif
                        @else
                            @if (permission('c2'))
                                <button class="btn btn-primary waves-effect waves-light m-b-5" id="addNewCustomer"> <i
                                        class="fa fa-plus-square m-r-5"></i> <span>{{ __('page.customer')[1] }}</span>
                                </button>
                            @endif
                        @endif

                    </div>
                </div>

                <table id="data-table" class="table table-bordered mt-4" cellspacing="0" width="100%">
                    <thead class="theme-primary text-white">
                        <tr>
                            <th>{{ __('page.supplier')[2] }}</th>
                            <th>{{ __('page.supplier')[3] }}</th>
                            <th>{{ __('page.supplier')[5] }}</th>
                            <th>{{ __('page.supplier')[6] }}</th>
                            @if ($type == 'customer')
                                <th>{{ __('page.customer')[12] }}</th>
                            @else
                                <th>{{ __('page.address') }}</th>
                            @endif
                            <th>{{ __('page.supplier')[10] }}</th>
                            <th>{{ __('page.supplier')[11] }}</th>
                        </tr>
                    </thead>

                </table>
            </div>
        </div>
    </div>
    <!-- end row -->

    <!-- Business Modal Start -->
    <div class="modal fade bd-example-modal-xl" id="contactModal" tabindex="-1" role="dialog"
        aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" style="width: 100%;padding: 0px !important">
            <div class="modal-content" id="modalcontent" style="padding: 0px !important">

            </div>
        </div>
    </div>
    <!-- Business Modal End -->

    <div class="modal fade bd-example-modal-xl" id="payModal" tabindex="-1" role="dialog"
        aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog" style="width: 100%">
            <div class="modal-content" id="modalcontent2">

            </div>
        </div>
    </div>


@endsection
@section('script')

<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script>

        const ROUTE_CUSTOMER_DELETE = "{{ route('customer.delete','#') }}";
        const TOKEN = "{{ csrf_token() }}";
        $(document).ready(function() {
            const URL_PAYMENT_ACCOUNT = "{{ route('returnsale.payment.account') }}";

            function getData() {
                $('#data-table').DataTable({
                    processing: true,
                    serverSide: true,
                    @if ($type == 'supplier')
                        ajax: "{{ url('/contact/list/json/supplier') }}",
                    @else
                        ajax: "{{ url('/contact/list/json/customer') }}",
                    @endif
                    columns: [{
                            "data": 'DT_RowIndex',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'name',
                            name: 'name'
                        },
                        {
                            data: 'email',
                            name: 'email'
                        },
                        {
                            data: 'mobile',
                            name: 'mobile'
                        },
                        @if ($type != 'supplier')
                            {data: 'group_name', name: 'group_name'},
                        @else
                            {data: 'address',name: 'address'},
                        @endif {
                            data: 'note',
                            name: 'note'
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

            $('body').on('click', "#pay", function() {
                let id = $(this).data('id')
                let _token = "{{ csrf_token() }}"
                let type = "{{ $_GET['type'] }}"
                $.post("{{ route('contact.pay') }}", {
                    _token: _token,
                    id: id,
                    type: type
                }, function(data) {
                    $('#modalcontent2').html(data)
                    $("#payModal").modal('show')
                })
            })

            $('body').on('click', "#addNewSupplier", function() {
                $.get("{{ route('contact.supplier.create') }}", function(data) {
                    $('#modalcontent').html(data)
                    $("#contactModal").modal('show')
                });
            })

            $('body').on('click', "#addNewCustomer", function() {
                $.get("{{ route('contact.customer.create') }}", function(data) {
                    $('#modalcontent').html(data)
                    $("#contactModal").modal('show')
                });
            })

            $('body').on("submit", "#customerStore", function (e) {
                e.preventDefault();
                $.post($(this)[0].action, $(this).serialize(), function (res) {
                    toastr.success(res)
                    var oTable = $('#data-table').dataTable();
                    oTable.fnDraw(false);
                    $("#contactModal").modal("hide");
                });
            });

            $('body').on('click', "#submitUpdate", function(e) {
                e.preventDefault();
                let id = $("input[name=id]").val();
                let name = $("input[name=name]").val();
                let type = $("input[name=type]").val();
                let supplier_business_name = $("input[name=supplier_business_name]").val();
                let email = $("input[name=email]").val();
                let mobile = $("input[name=mobile]").val();
                // let city = $("input[name=city]").val();
                // let zip = $("input[name=zip]").val();
                // let country = $("input[name=country]").val();
                let customer_group_id = $("#customer_group_id").val();
                let note = $("textarea[name=note]").val();
                let address = $("input[name=address]").val();
                let password = $("input[name=password]").val();
                let password_confirmation = $("input[name=password_confirmation]").val();
                let _token = "{{ csrf_token() }}"
                $.post("{{ route('contact.update') }}", {
                    _token: _token,
                    id: id,
                    customer_group_id: customer_group_id,
                    type: type,
                    name: name,
                    supplier_business_name: supplier_business_name,
                    email: email,
                    // city: city,
                    // zip: zip,
                    mobile: mobile,
                    // country: country,
                    note: note,
                    address: address,
                    password: password,
                    password_confirmation: password_confirmation,
                }, function(data) {
                    toastr.success(data)
                    var oTable = $('#data-table').dataTable();
                    oTable.fnDraw(false);
                    $("#contactModal").modal('hide')
                })
            })

            $('body').on('click', "#contactEdit", function() {
                let id = $(this).data('id')
                $.get(`/contact/edit/${id}`, function(data) {
                    $('#modalcontent').html(data)
                    $("#contactModal").modal('show')
                });
            })

            $('body').on('click', "#deleteData", function() {
                let id = $(this).data('id')
                let _token = "{{ csrf_token() }}"
                $.post("{{ route('status.update') }}", {
                    _token: _token,
                    id: id
                }, function(data) {
                    toastr.success(data)
                    var oTable = $('#data-table').dataTable();
                    oTable.fnDraw(false);
                })

            })

            $("body").on('change', "#pay_by", function() {
                let account_type = $(this).val();
                $.post(URL_PAYMENT_ACCOUNT, {
                    _token: TOKEN,
                    account_type: account_type,
                    type: 'div'
                }, function(data) {
                    $("#account_list").html(data)
                    $(".select3").val(account_id)
                    $(".select3").select2();
                })
            })


        })
        function customerDelete(id) {
            swal({
                title: "Are you sure?",
                text: "Once deleted, you will not be able to recover this imaginary file!",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            })
            .then((willDelete) => {
            if (willDelete) {
                console.log('id'+id);
                $.post(ROUTE_CUSTOMER_DELETE.replace("#", id), {
                    _token: TOKEN,
                }) .done( function(data) {
                    swal(''+data, {icon: "success"});
                    var oTable = $('#data-table').dataTable();
                    oTable.fnDraw(false);
                } )
                .fail( function(xhr, textStatus, errorThrown) {
                    swal(xhr.responseText, {icon: "error",});
                });
            } else {
                //swal("Your imaginary file is safe!");
            }
            });
        }
    </script>
@endsection

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
                        <h4 class="m-t-0 header-title mb-5"><b>{{ __('page.income')[0] }}</b></h4>
                    </div>
                    <div class="col-md-6 text-right">

                        @if (permission('ex2'))
                            <a href="{{ route('income.create') }}" class="btn btn-success btn-rounded waves-effect waves-light m-b-5">
                                <i class="fa fa-plus-square m-r-5"></i>
                                <span>{{ __('page.income')[1] }}</span>
                            </a>
                        @endif
                        <a href="{{ route('income.history') }}" class="btn btn-success btn-rounded waves-effect waves-light m-b-5">
                            <span>{{ __('Income History') }}</span>
                        </a>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="d-flex justify-content-between">

                            <div class="d-flex">
                                <select id="year-select" name="year" class="form-control" style="width: 200px;">
                                    <option value="">Select Year</option>
                                    @for ($year = 2024; $year <= now()->year; $year++)
                                        <option value="{{ $year }}">{{ $year }}</option>
                                    @endfor
                                </select>
                                <select id="month-select" name="month" class="form-control" style="width: 200px;">
                                    <option value="">Select Month</option>
                                    @for ($month = 1; $month <= 12; $month++)
                                        <option value="{{ $month }}">
                                            {{ DateTime::createFromFormat('!m', $month)->format('F') }}
                                        </option>
                                    @endfor
                                </select>
                                <button class="btn btn-sm btn-success btn-rounded" id="filter-button"
                                    style="width: 60px;height: 38px;margin-left: 10px;">
                                    Filter
                                </button>

                            </div>

                        <div>
                            <h4>Total Amount : <span class="text-danger" id="total-income"></span></h4>
                        </div>
                    </div>
                </div>
                <br> <br>
                <table id="income-table" class="table table-striped table-bordered mt-4" cellspacing="0" width="100%">
                    <thead class="theme-primary text-white">
                        <tr>
                            <th> {{ __('page.income')[2] }} </th>
                            <th> {{ __('page.income')[4] }} </th>
                            <th> {{ __('page.income')[5] }} </th>
                            <th>{{ __('page.income')[7] }}</th>
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
            // var table = $('#income-table').DataTable({
            //     processing: true,
            //     serverSide: true,
            //     ajax: {
            //         url: "{{ route('income.all') }}",
            //         data: function(d) {
            //             d.year = $('#year-select').val();
            //             d.month = $('#month-select').val();
            //         },
            //         dataSrc: function(json) {
            //             $('#total-income').html(json.total_income);
            //             return json.data;
            //         }
            //     },
            //     dom: 'Blfrtip',
            //         buttons: [
            //              'csv', 'excel', 'pdf'
            //         ],
            //     columns: [
            //         {
            //             "data": 'DT_RowIndex',
            //             orderable: false,
            //             searchable: false
            //         },
            //         {
            //             data: 'income_type.income_type',
            //             name: 'income_type'
            //         },
            //         {
            //             data: 'income_date',
            //             name: 'income_date'
            //         },
            //         {
            //             data: 'amount',
            //             name: 'amount'
            //         },
            //         {
            //             data: 'note',
            //             name: 'note'
            //         },
            //         {
            //             data: 'action',
            //             name: 'action',
            //             orderable: false,
            //             searchable: true
            //         },
            //     ]
            // });


            if (!$.fn.DataTable.isDataTable('#income-table')) { // Prevent reinitialization
                var table = $('#income-table').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('income.all') }}",
                        data: function(d) {
                            d.year = $('#year-select').val();
                            d.month = $('#month-select').val();
                        },
                        dataSrc: function(json) {
                            $('#total-income').html(json.total_income);
                            return json.data;
                        }
                    },
                    dom: 'Blfrtip',
                    buttons: ['csv', 'excel', 'pdf'],
                    columns: [{
                            "data": 'DT_RowIndex',
                            orderable: false,
                            searchable: false
                        },

                        {
                            data: 'income_date',
                            name: 'income_date'
                        },
                        {
                            data: 'details_sum_total',
                            name: 'amount'
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

            $('#filter-button').click(function() {
                if ($('#year-select').val() != '' && $('#month-select').val() == '') {
                    alert("Please select month");
                } else if ($('#month-select').val() != '' && $('#year-select').val() == '') {
                    alert("Please select year");
                } else {
                    table.ajax.reload();
                }
            });


            // Filter button click event

            $('body').on('click', "#addNew", function() {
                $.get("{{ route('income.create') }}", function(data) {
                    $('#modalcontent').html(data)
                    $("#unitModal").modal('show')
                });
            })

            $('body').on('click', "#submit", function(e) {
                e.preventDefault();
                let income_date = $("input[name=income_date]").val();
                let amount = $("input[name=amount]").val();
                let note = $("textarea[name=note]").val();
                let income_type_id = $("select[name=income_type_id]").val();
                let payment_type = $("select[name=payment_type]").val();
                let account_id = $("select[name=account_id]").val();
                let _token = "{{ csrf_token() }}"
                $.post("{{ route('income.store') }}", {
                    _token: _token,
                    pay_by: payment_type,
                    income_date: income_date,
                    amount: amount,
                    income_type_id: income_type_id,
                    note: note,
                    account_id: account_id
                }, function(data) {
                    toastr.success(data)
                    // var oTable = $('#income-table').dataTable();
                    // oTable.fnDraw(false);
                    $('#income-table').DataTable().ajax.reload(null, false);
                    $("#unitModal").modal('hide')
                })
            })

            $('body').on('click', "#submitUpdate", function(e) {
                e.preventDefault();

                // Fetch form data
                let id = $("input[name=id]").val();
                let income_date = $("input[name=income_date]").val();
                let amount = $("input[name=amount]").val();
                let note = $("textarea[name=note]").val();
                let income_type_id = $("select[name=income_type_id]").val();
                let payment_type = $("select[name=payment_type]").val();
                let _token = "{{ csrf_token() }}";

                // Laravel route with dynamic ID
                let updateRoute = "{{ route('income.update', ':id') }}";
                let url = updateRoute.replace(':id', id); // Replace placeholder with actual ID
                // AJAX request
                $.ajax({
                    url: url, // Dynamic Laravel route
                    type: 'PUT', // Use PUT method
                    data: {
                        _token: _token,
                        pay_by: payment_type,
                        income_date: income_date,
                        amount: amount,
                        income_type_id: income_type_id,
                        note: note
                    },
                    success: function(data) {
                        // Show success message
                        toastr.success(data);

                        // Reload DataTable without resetting pagination
                        var oTable = $('#income-table').DataTable();
                        oTable.ajax.reload(null, false);

                        // Close modal
                        $("#unitModal").modal('hide');
                    },
                    error: function(xhr, status, error) {
                        // Show error message
                        toastr.error("Failed to update. Please try again.");
                        console.error("Update Error:", xhr.responseText);
                    }
                });
            });


            $('body').on('click', "#unitEdit", function() {
                let id = $(this).data('id')

                $.get(`/income/${id}/edit`, function(data) {
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
                            // $.delete(`/income/${id}`, function(data) {
                            //     toastr.success(data)
                            //     var oTable = $('#income-table').dataTable();
                            //     oTable.fnDraw(false);
                            // })

                            $.ajax({
                                url: `/income/${id}`,
                                type: 'DELETE', // Use DELETE method
                                data: {
                                    _token: _token, // Include the CSRF token
                                },
                                success: function(data) {
                                    toastr.success(data);
                                    var oTable = $('#income-table')
                                        .DataTable(); // Ensure you're using DataTable() and not dataTable()
                                    oTable.ajax.reload(null,
                                        false
                                    ); // Reload table data without resetting pagination
                                },
                                error: function(xhr, status, error) {
                                    toastr.error("Failed to delete. Please try again.");
                                    console.error("Delete Error:", xhr.responseText);
                                }
                            });
                        } else {
                            swal("Cancelled", "Your Data Is Safe :)", "error");
                        }
                    });
            })
        })

        $("body").on('change', "#pay_by", function() {
            // purchase.payment.account
            let account_type = $(this).val()
            let _token = "{{ csrf_token() }}"
            $.post("{{ route('expense.payment.account') }}", {
                _token: _token,
                account_type: account_type
            }, function(data) {
                $("#account_info").html(data)
                $(".select3").select2();
            })

        })
    </script>
@endsection


@push('css')
    <style>
        .modal-dialog {
            max-width: 100%;
            margin: 0;
            top: 0;
            bottom: 0;
            left: 0;
            right: 0;
            height: 100vh;
            display: flex;
        }
    </style>
@endpush

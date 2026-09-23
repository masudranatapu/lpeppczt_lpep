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
                        <h4 class="m-t-0 header-title mb-5"><b>{{ __('page.expense')[0] }}</b></h4>
                    </div>
                    <div class="col-md-6 text-right">
                        @if (permission('ex2'))
                            <button class="btn btn-primary waves-effect waves-light m-b-5" id="addNew"> <i
                                    class="fa fa-plus-square m-r-5"></i>
                                    <span>{{ __('page.expense')[1] }}</span>
                            </button>
                        @endif
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="d-flex justify-content-between">
                        <div class="d-flex">
                             <select id="year-select" class="form-control" style="width: 200px;">
                                <option value="">Select Year</option>
                                @for ($year = 2020; $year <= now()->year; $year++)
                                    <option value="{{ $year }}">{{ $year }}</option>
                                @endfor
                            </select>
                            <select id="month-select" class="form-control" style="width: 200px;">
                                <option value="">Select Month</option>
                                @for ($month = 1; $month <= 12; $month++)
                                    <option value="{{ $month }}">{{ DateTime::createFromFormat('!m', $month)->format('F') }}</option>
                                @endfor
                            </select>
                            <button class="btn btn-sm btn-primary" id="filter-button" style="    width: 60px;
    height: 38px;
    margin-left: 10px;">Filter</button>
                        </div>
                        <div>
                            <h4>Total Amount : <span class="text-danger" id="total-expense"></span></h4>
                        </div>
                       </div>
                </div>
                <br> <br>
                <table id="data-table" class="table table-striped table-bordered mt-4" cellspacing="0" width="100%">
                    <thead class="theme-primary text-white">
                        <tr>
                            <th> {{ __('page.expense')[2] }} </th>
                            <th> {{ __('page.expense')[3] }} </th>
                            <th> {{ __('page.expense')[4] }} </th>
                            <th> {{ __('page.expense')[5] }} </th>
                            <th> {{ __('page.expense')[6] }} </th>
                            <th>{{ __('page.expense')[7] }}</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th colspan="3" style="text-align:right">Total Expense Amount:</th>
                            <th id="page-total-expense"></th>
                            <th colspan="2"></th>
                        </tr>
                    </tfoot>
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
            var table = $('#data-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('expense.all') }}",
                    data: function(d) {
                        d.year = $('#year-select').val();
                        d.month = $('#month-select').val();
                    },
                    dataSrc: function(json) {
                        $('#total-expense').html(json.total_expense);
                        return json.data;
                    }
                },
                dom: 'Blfrtip',
                buttons: ['csv', 'excel', 'pdf'],
                columns: [
                    { data: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'expense_type', name: 'expense_type' },
                    { data: 'expanse_date', name: 'expanse_date' },
                    { data: 'amount', name: 'amount' },
                    { data: 'note', name: 'note' },
                    { data: 'action', name: 'action', orderable: false, searchable: true },
                ],
            
                footerCallback: function (row, data, start, end, display) {
                    var api = this.api();
            
                    var total = api
                        .column(3, { page: 'current' })
                        .data()
                        .reduce(function (a, b) {
                            var x = typeof a === 'string' ? a.replace(/[^0-9.-]+/g, '') : a;
                            var y = typeof b === 'string' ? b.replace(/[^0-9.-]+/g, '') : b;
            
                            return parseFloat(x || 0) + parseFloat(y || 0);
                        }, 0);
            
                    $('#page-total-expense').html(total.toFixed(2));
                }
            });
            $('#filter-button').click(function() {
                if ($('#year-select').val() != '' && $('#month-select').val() == '') {
                    alert("Please select month");
                }else if($('#month-select').val() != '' && $('#year-select').val() == ''){
                    alert("Please select year");
                }
                 else {
                    table.ajax.reload();
                }
            });


            // Filter button click event

            $('body').on('click', "#addNew", function() {
                $.get("{{ route('expense.create') }}", function(data) {
                    $('#modalcontent').html(data)
                    $("#unitModal").modal('show')
                });
            })

            $('body').on('click', "#submit", function(e) {
                e.preventDefault();
                let expanse_date = $("input[name=expanse_date]").val();
                let amount = $("input[name=amount]").val();
                let note = $("textarea[name=note]").val();
                let expense_type_id = $("select[name=expense_type_id]").val();
                let payment_type = $("select[name=payment_type]").val();
                let account_id = $("select[name=account_id]").val();
                let _token = "{{ csrf_token() }}"
                $.post("{{ route('expense.store') }}", {
                    _token: _token,
                    pay_by: payment_type,
                    expanse_date: expanse_date,
                    amount: amount,
                    expense_type_id: expense_type_id,
                    note: note,
                    account_id: account_id
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
                let expanse_date = $("input[name=expanse_date]").val();
                let amount = $("input[name=amount]").val();
                let note = $("textarea[name=note]").val();
                let expense_type_id = $("select[name=expense_type_id]").val();
                let payment_type = $("select[name=payment_type]").val();
                let account_id = $("select[name=account_id]").val();
                let _token = "{{ csrf_token() }}"
                $.post("{{ route('expense.update') }}", {
                    _token: _token,
                    id: id,
                    pay_by: payment_type,
                    expanse_date: expanse_date,
                    amount: amount,
                    expense_type_id: expense_type_id,
                    note: note,
                    account_id: account_id
                }, function(data) {
                    toastr.success(data)
                    var oTable = $('#data-table').dataTable();
                    oTable.fnDraw(false);
                    $("#unitModal").modal('hide')
                })
            })

            $('body').on('click', "#unitEdit", function() {
                let id = $(this).data('id')

                $.get(`/expense/${id}/edit`, function(data) {
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
                            $.get(`/expense/${id}/delete`, function(data) {
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

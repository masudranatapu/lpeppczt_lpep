@extends('layouts.dashboard')
@section('title', ' | Sale List')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card-box table-responsive mt-4">
                <div class="row">
                    <div class="col-md-12">
                        <h4 class="m-t-0 header-title"><b>{{ __('page.sale')[0] }}</b></h4>
                    </div>
                    <div class="col-md-12">
                        <form action="" id="searching">
                            <div class="row justify-content-end">
                                <div class="col-md-3">
                                    {{-- @include('inc.appCustomer', [
                                        'hideLabel' => 1,
                                        'customer' => $customer,
                                    ]) --}}
                                    <div class="form-group">
                                        <div class="input-group">
                                            <span class="input-group-addon">
                                                <i class="fa fa-user"></i>
                                            </span>
                                            <select name="app_customer_id" class="form-control select2" id="app_customer_id">
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <input type="text" name="sale_date" value="{{ request('sale_date') ?? '' }}"
                                            class="form-control datepicker" placeholder="Sale Date" autocomplete="off">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <input type="text" name="search" value="{{ request('search') ?? '' }}"
                                            class="form-control" placeholder="Search...">
                                    </div>
                                </div>
                                <div class="col-md-1">
                                    <button type="submit" class="btn btn-success">Search</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="table-rep-plugin" style="margin-top: -10px;">
                    <div class="table-responsive" id="okkkk">
                        <table id="tech-companies-1" class="table table-striped table-hover" style="margin-bottom: 110px;"
                            cellspacing="0">
                            <thead class="theme-primary text-white" style="">
                                <tr>
                                    <th>{{ __('page.sale')[1] }}</th>
                                    <th>{{ __('page.sale')[2] }}</th>
                                    <th>{{ __('page.sale')[3] }}</th>
                                    <th>{{ __('page.sale')[5] }}</th>
                                    <th>{{ __('page.sale')[7] }}</th>
                                    <th>{{ __('page.sale')[9] }}</th>
                                    <th>{{ __('page.sale')[11] }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($sales as $sale)
                                    <tr>
                                        <td>{{ $loop->index + 1 }}</td>
                                        <td onclick="viewSale({{ $sale->id }})">{{ $sale->sale_date }}</td>
                                        <td onclick="viewSale({{ $sale->id }})">{{ $sale->invoice_no }}</td>
                                        <td onclick="viewSale({{ $sale->id }})">

                                            @if ($sale->customer_name || $sale->customer_phone )
                                                {{ $sale->customer_name }} <br>
                                                <small>({{ $sale->customer_phone }})</small>
                                            @else
                                                {{ $sale->customer?->name }} <br>
                                                <small>({{ $sale->customer?->mobile }})</small>
                                            @endif
                                        </td>
                                        <td onclick="viewSale({{ $sale->id }})">{{ $sale->total_amount }}</td>
                                        <td onclick="viewSale({{ $sale->id }})">
                                            @if ($sale->total_amount - $sale->paying_amount == 0)
                                                <span class="badge badge-success">Paid</span>
                                            @else
                                                <span class="badge badge-danger">Due
                                                    {{ $sale->total_amount - $sale->paying_amount }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group btn-sm">
                                                <button type="button"
                                                    class="btn btn-info dropdown-toggle waves-effect btn-sm"
                                                    data-toggle="dropdown" aria-expanded="false"> Action <span
                                                        class="caret"></span>
                                                </button>
                                                <div class="dropdown-menu" x-placement="bottom-start"
                                                    style="position: absolute; transform: translate3d(0px, 35px, 0px); top: 0px; left: 0px; will-change: transform;">
                                                    <button onclick="viewSale({{ $sale->id }})"
                                                        class="dropdown-item">View</button>
                                                    <a href="{{ route('sale.destroy', $sale->id) }}" class="dropdown-item"
                                                        id="delete">Delete</a>
                                                    <a href="{{ route('sale.invoice', $sale->id) }}"
                                                        class="dropdown-item">Invoice</a>
                                                    <a href="{{ route('pos.invoice', $sale->id) }}" target="_blank"
                                                        class="dropdown-item">Print Pos Invoice</a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach

                            </tbody>
                            <tfoot>
                                <td colspan="100" class="text-right">
                                    {{ $sales->appends(request()->input())->links() }}
                                </td>
                            </tfoot>
                        </table>
                    </div>
                </div>



            </div>
        </div>
    </div>
    <!-- end row -->


    <!-- Business Modal Start -->
    <div class="modal fade bd-example-modal-xl" id="unitModal" tabindex="-1" role="dialog"
        aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" style="width: 100%">
            <div class="modal-content" id="modalcontent" style="width: 100%">

            </div>
        </div>
    </div>
    <!-- Business Modal End -->

@endsection

@section('script')
    <script>
        function viewSale(id) {
            $.get(`/sale/${id}/view`, function(data) {
                console.log(data);
                $('#modalcontent').html(data)
                $("#unitModal").modal('show')
            });
        }
    </script>
    <script>
        $(function() {
            $('#okkkk').responsiveTable({
                addFocusBtn: false
            });
        });
    </script>
    <script>
        $(function() {
            $(".datepicker").datepicker({
                autoclose: true,
                todayHighlight: true,
                dateFormat: 'yyyy-MM-dd',
                format: 'yyyy-mm-dd',
            })
        });

        $(document).ready(function() {
            function getCus() {
            $.get("{{ route('app.customer.select2-ajax') }}", function(data) {
                let selectedCustomerId = "{{ $customer->id ?? '' }}";
                $("#app_customer_id").html('<option value="" selected disabled>Select Customer</option>' + data);

                if (selectedCustomerId) {
                    $("#app_customer_id").val(selectedCustomerId).trigger('change'); // Set the selected value
                }
            });
        }
        getCus();
            getCus();

        });
    </script>


<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

<!-- Buttons after -->
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>

<script>
    function viewSale(id) {
        $.get(`/sale/${id}/view`, function(data) {
            $('#modalcontent').html(data)
            $("#unitModal").modal('show')
        });
    }

    function saleSearch(e) {
        $('#searching').submit();
    }

    $(document).ready(function() {
        // Initialize DataTable with export buttons
        var table = $('#sales-table').DataTable({
            dom: '<"row"<"col-sm-12"B>><"row"<"col-sm-6"l><"col-sm-6"f>>rt<"row"<"col-sm-6"i><"col-sm-6"p>>',
            
            buttons: [
                {
                    extend: 'copy',
                    text: 'Copy',
                    className: 'dt-button'
                },
                {
                    extend: 'excel',
                    text: 'Excel',
                    className: 'dt-button',
                    title: 'Agent Sales Report',
                    exportOptions: {
                        columns: ':visible'
                    }
                },
                {
                    extend: 'pdf',
                    text: 'PDF',
                    className: 'dt-button',
                    title: 'Agent Sales Report',
                    exportOptions: {
                        columns: ':visible'
                    }
                },
                {
                    extend: 'print',
                    text: 'Print',
                    className: 'dt-button',
                    title: 'Agent Sales Report',
                    exportOptions: {
                        columns: ':visible'
                    }
                }
            ],
            pageLength: 25,
            order: [[1, 'desc']],
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            columnDefs: [{
                searchable: false,
                orderable: false,
                targets: 0
            }],
            drawCallback: function(settings) {
                var api = this.api();
                api.column(0, {
                    search: 'applied',
                    order: 'applied'
                }).nodes().each(function(cell, i) {
                    cell.innerHTML = i + 1;
                });
            }
        });

        // Remove the responsive table initialization
        $('#okkkk').removeData('responsiveTable');
    });
</script>

<script>
    $(function() {
        $(".datepicker").datepicker({
            autoclose: true,
            todayHighlight: true,
            dateFormat: 'yyyy-MM-dd',
            format: 'yyyy-mm-dd',
        })
    });

    $(document).ready(function() {
        $('select.customer-dropdown').select2({
            ajax: {
                url: "/contact/ajax?type=customer",
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        q: params.term,
                        page: params.page
                    };
                },
                processResults: function(data, params) {
                    params.page = params.page || 1;
                    return {
                        results: data.items,
                        pagination: {
                            more: data.pagination.more
                        }
                    };
                },
                cache: true
            },
            allowClear: true,
            placeholder: 'Search Customer',
            minimumInputLength: 0
        });
    });
</script> -->

@endsection

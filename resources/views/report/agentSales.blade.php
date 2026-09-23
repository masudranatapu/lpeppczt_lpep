@extends('layouts.dashboard')
@section('title', ' | Sale List')
@push('css')
<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<!-- DataTables Buttons CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">
<style>
    /* Center the buttons container */
    .dt-buttons {
        text-align: center !important;
        display: flex !important;
        justify-content: center !important;
        align-items: center !important;
        margin-bottom: 15px !important;
        gap: 10px !important;
    }
    
    /* Style individual buttons */
    .dt-button {
        background: #5bb318 !important;
        color: #fff !important;
        border: none !important;
        padding: 8px 18px !important;
        border-radius: 6px !important;
        font-weight: 600 !important;
        cursor: pointer !important;
        transition: background 0.3s ease !important;
    }
    
    .dt-button:hover {
        background: #4a9614 !important;
    }
    
    /* Ensure buttons are visible */
    .dt-buttons .dt-button {
        display: inline-block !important;
        visibility: visible !important;
        opacity: 1 !important;
    }
    
    /* Style for dropdown collection button */
    .dt-button-collection {
        background: white !important;
        border: 1px solid #ddd !important;
        box-shadow: 0 2px 8px rgba(0,0,0,0.15) !important;
    }
</style>
@endpush

@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="card-box table-responsive mt-4">
                <div class="row">
                    <div class="col-md-12">
                        <h4 class="m-t-0 header-title"><b>{{ __('Agent Sales Report') }}</b></h4>
                    </div>
                    <div class="col-md-12">
                        <form action="" id="searching">
                            <div class="row justify-content-end">
                                @if (permission('filterByUser') || Auth::user()?->email == 'faridpur@gmail.com')
                                    <div class="col-md-3">
                                        <select name="user" class="form-control select2">
                                            <option value="">All User</option>
                                            @foreach ($agents as $user)
                                                <option value="{{ $user->id }}"
                                                    {{ request('user') == $user->id ? 'selected' : '' }}>
                                                    {{ $user->name }} (<small>{{ $user->employee_name }}</small>)
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endif
                                <div class="col-md-3">
                                    <select name="product" class="form-control select2">
                                        <option value="">All Product</option>
                                        @foreach ($products as $product)
                                            <option value="{{ $product->id }}"
                                                {{ request('product') == $product->id ? 'selected' : '' }}>
                                                {{ $product->product_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-5">
                                    <div class="">
                                        <div class="input-daterange input-group" id="date-range">
                                            <input type="text" placeholder="Start Date"
                                                class="form-control datepicker startdate" name="start_date"
                                                value="{{ request('start_date') ?? '' }}" autocomplete="off">
                                            <span class="input-group-addon bg-success b-0 text-white">to</span>
                                            <input type="text" placeholder="End Date"
                                                class="form-control datepicker enddate" name="end_date"
                                                value="{{ request('end_date') ?? '' }}" autocomplete="off">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-1">
                                    <button type="submit" class="btn btn-success" style="cursor: pointer;">Search</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
@php
    $totalQty = 0;
@endphp
                <div class="table-rep-plugin mt-4">
                    <div class="table-responsive" data-pattern="priority-columns" id="okkkk">
                        <table id="sales-table" class="table table-bordered display">
                            <thead class="theme-primary text-white">
                                <tr>
                                    <th>No.</th>
                                    <th>{{ __('page.sale')[2] }}</th>
                                    <th>{{ __('page.sale')[3] }}</th>
                                    <th>{{ __('Sector Beneficiary') }}</th>
                                    <th>{{ __('page.sale')[5] }}</th>
                                    <th>{{ __('page.sale')[6] }}</th>
                                    <th>Qty</th>
                                    <th>{{ __('page.sale')[7] }}</th>
                                    <th>{{ __('page.sale')[8] }}</th>
                                    <th>{{ __('page.sale')[10] }}</th>
                                    <th>{{ __('Profit') }}</th>
                                    <th>{{ __('page.sale')[9] }}</th>
                                    <th>Due Paid Date</th>
                                    <th>Note</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($sales as $sale)
                                @php
                                    $totalQty += $sale->qty;
                                @endphp
                                    <tr>
                                        <td>{{ $loop->index + 1 }}</td>
                                        <td>{{ $sale->sale_date }}</td>
                                        <td>{{ date('Y') . $sale->id }}</td>
                                        <td>
                                            @forelse ($sale->saleProducts as $data)
                                                <span>{{ $data->product?->product_name }},</span>
                                            @empty
                                            @endforelse
                                        </td>
                                        <td>
                                            @if ($sale->customer_name || $sale->customer_phone)
                                                {{ $sale->customer_name }} <br>
                                                <small>({{ $sale->customer_phone }})</small>
                                            @else
                                                {{ $sale->customer?->name }} <br>
                                                <small>({{ $sale->customer?->mobile }})</small>
                                            @endif
                                        </td>
                                        <td>{{ $sale->agent->name }}</td>
                                        <td>{{ $sale->qty }}</td>
                                        <td>{{ $sale->total_amount }}</td>
                                        <td>{{ $sale->paying_amount }}</td>
                                        <td>{{ $sale->total_amount - $sale->paying_amount }}</td>
                                        <td>{{ $sale->profit_amount }}</td>
                                        <td>
                                            @if ($sale->total_amount - $sale->paying_amount == 0)
                                                <span class="badge badge-success">Paid</span>
                                            @else
                                                <span class="badge badge-danger">Due</span>
                                            @endif
                                        </td>
                                        <td>{{ $sale->due_date ?? "--"}}</td>
                                        <td>{{ $sale->note ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            
                            <tfoot>
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td><strong>{{$totalQty}}</strong></td>
                                    <td>
                                        <strong>
                                            {{ $sales->sum('total_amount') }}
                                        </strong>
                                    </td>
                                    <td>
                                        <strong>
                                            {{ $sales->sum('paying_amount') }}
                                        </strong>
                                    </td>
                                    <td>
                                        <strong>{{ $sales->sum('total_amount') - $sales->sum('paying_amount') }}</strong>
                                    </td>
                                    <td><strong>{{ $total_profit }}</strong></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
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
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

<!-- DataTables -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<!-- Required first -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
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
</script>




@endsection
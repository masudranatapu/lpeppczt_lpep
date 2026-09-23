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
                                @if (permission('filterByUser'))
                                    <div class="col-md-3">
                                        <select name="user" class="form-control">
                                            <option value="">All User</option>
                                            @foreach ($users as $user)
                                                <option value="{{ $user->id }}"
                                                    {{ request('user') == $user->id ? 'selected' : '' }}>
                                                    {{ $user->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endif
                                <div class="col-md-3">
                                    <select name="customer" class="form-control customer-dropdown">
                                        @if ($customer)
                                            <option value="{{ $customer->id }}"
                                                {{ request('customer') == $customer->id ? 'selected' : '' }} selected>
                                                {{ $customer->name }}
                                            </option>
                                        @endif
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <input type="text" name="sale_date" value="{{ request('sale_date') ?? '' }}"
                                            onchange="saleSearch()" class="form-control datepicker" placeholder="Sale Date"
                                            autocomplete="off">
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
                                    <th>{{ __('page.sale')[6] }}</th>
                                    <th>{{ __('page.sale')[7] }}</th>
                                    <th>{{ __('page.sale')[8] }}</th>
                                    <th>{{ __('page.sale')[9] }}</th>
                                    <th>{{ __('page.sale')[10] }}</th>
                                    <th>{{ __('page.sale')[11] }}</th>
                                </tr>
                            </thead>
                            <tbody>

                                @foreach ($sales as $sale)
                                    <tr>
                                        <td>{{ $loop->index + 1 }}</td>
                                        <td onclick="viewSale({{ $sale->id }})">{{ $sale->sale_date }}</td>
                                        <td onclick="viewSale({{ $sale->id }})">{{ date('Y') . $sale->id }}</td>
                                        <td onclick="viewSale({{ $sale->id }})">
                                            @if ($sale->customer_name || $sale->customer_phone )
                                                {{ $sale->customer_name }} <br>
                                                <small>({{ $sale->customer_phone }})</small>
                                            @else
                                                {{ $sale->customer->name }} <br>
                                                <small>({{ $sale->customer->mobile }})</small>
                                            @endif
                                        </td>
                                        <td onclick="viewSale({{ $sale->id }})">{{ $sale->user->name }}</td>
                                        <td onclick="viewSale({{ $sale->id }})">{{ $sale->total_amount }}</td>
                                        <td onclick="viewSale({{ $sale->id }})">{{ $sale->paying_amount }}</td>
                                        <td onclick="viewSale({{ $sale->id }})">
                                            @if ($sale->total_amount - $sale->paying_amount == 0)
                                                <span class="badge badge-success">Paid</span>
                                            @else
                                                <span class="badge badge-danger">Due</span>
                                            @endif
                                        </td>
                                        <td onclick="viewSale({{ $sale->id }})">
                                            {{ $sale->total_amount - $sale->paying_amount }}</td>
                                        <td>
                                            <div class="btn-group btn-sm">
                                                <button type="button"
                                                    class="btn btn-info dropdown-toggle waves-effect btn-sm"
                                                    data-toggle="dropdown" aria-expanded="false"> Action <span
                                                        class="caret"></span> </button>
                                                <div class="dropdown-menu" x-placement="bottom-start"
                                                    style="position: absolute; transform: translate3d(0px, 35px, 0px); top: 0px; left: 0px; will-change: transform;">
                                                    @if (permission('sa2'))
                                                        <a onclick="viewSale({{ $sale->id }})"
                                                            href="javascript:void(0);" class="dropdown-item">View</a>
                                                    @endif
                                                    {{-- @if (permission('sa2'))
                                                        <a href="{{ route('sale.edit',$sale->id) }}" class="dropdown-item">Edit</a>
                                                    @endif --}}
                                                    @if (permission('sa3'))
                                                        <a href="{{ route('sale.destroy', $sale->id) }}"
                                                            class="dropdown-item" id="delete">Delete</a>
                                                    @endif
                                                    <a href="{{ route('sale.invoice', $sale->id) }}"
                                                        class="dropdown-item">Invoice</a>
                                                    <a href="{{ route('pos.invoice', $sale->id) }}" target="_blank"
                                                        class="dropdown-item">Print Pos Invoice</a>
                                                    @if (permission('sr1'))
                                                        <a href="{{ route('sale.return', $sale->id) }}"
                                                            class="dropdown-item" id="">Sell Return </a>
                                                    @endif
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
                $('#modalcontent').html(data)
                $("#unitModal").modal('show')
            });
        }

        function saleSearch(e) {
            $('#searching').submit();
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
            $('select.customer-dropdown').select2({
                ajax: {
                    url: "/contact/ajax?type=customer",
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            q: params.term, // search term
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

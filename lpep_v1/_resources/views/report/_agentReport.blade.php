@extends('layouts.dashboard')
@section('title', ' | Sale List')

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
                                @if (permission('filterByUser'))
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
                                {{-- <div class="col-md-3">
                                    <select name="customer" class="form-control customer-dropdown">
                                        @if ($customer)
                                            <option value="{{ $customer->id }}"
                                                {{ request('customer') == $customer->id ? 'selected' : '' }} selected>
                                                {{ $customer->name }}
                                            </option>
                                        @endif
                                    </select>
                                </div> --}}
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

                <div class="table-rep-plugin" style="margin-top: -10px;">
                    <div class="table-responsive" id="okkkk">
                        <table id="tech-companies-1" class="table table-bordered">
                            <thead class="theme-primary text-white" style="">
                                <tr>
                                    <th>{{ __('page.sale')[1] }}</th>
                                    <th>{{ __('page.sale')[2] }}</th>
                                    <th>{{ __('page.sale')[3] }}</th>
                                    <th>{{ __('Sector Beneficiary') }}</th>
                                    <th>{{ __('page.sale')[5] }}</th>
                                    <th>{{ __('page.sale')[6] }}</th>
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
                                </tr>
                            </tfoot>

                            {{-- <tfoot>
                                <td colspan="100" class="text-right">
                                    {{ $sales->appends(request()->input())->links() }}
                                </td>
                            </tfoot> --}}
                        </table>

                        <div class="text-center">
                            {{ $sales->appends(request()->input())->links() }}
                        </div>
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

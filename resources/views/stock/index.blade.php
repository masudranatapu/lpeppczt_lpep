
@extends('layouts.dashboard')

@section('content')

<div class="row">
    <div class="col-md-12">
        <div class="card-box table-responsive mt-4" style="border-top: 3px solid #157c34;">
            <h4 class="m-t-0 header-title mb-2"><b>{{ __('page.stock')[0] }}</b></h4>

            <form action="" id="searching" method="GET">
                <div class="row align-items-end justify-content-between">
                    <div class="col-md-1">
                        <div class="form-group mb-2">
                            <label class="mb-1">Show</label>
                            <select name="per_page" id="per_page" class="form-control">
                                <option value="25"  {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                                <option value="50"  {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                                 <option value="50"  {{ request('per_page') == 80 ? 'selected' : '' }}>80</option>
                                <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                                <option value="all" {{ request('per_page') == 'all' ? 'selected' : '' }}>All</option>
                            </select>
                         
                        </div>
                    </div>

                    <div class="col-md-9">
                        <div class="row justify-content-end">

                            <div class="col-md-4">
                                <div class="form-group mb-2">
                                    <label class="mb-1">Product</label>
                                    <input type="text"
                                        name="product_name"
                                        id="product_name"
                                        value="{{ request('product_name', $search) }}"
                                        class="form-control"
                                        placeholder="Enter Product Name">
                                </div>
                            </div>

                            @if(!isRole(ROLE_AGENT))
                                <div class="col-md-4">
                                    <div class="form-group mb-2">
                                        <label class="mb-1">Agent</label>
                                        <select name="agent_id" id="agent_id" class="form-control">
                                            <option value="">All Agents</option>
                                            @foreach($agents as $agent)
                                                <option value="{{ $agent->id }}"
                                                    {{ (string)request('agent_id', $selected_agent ?? '') === (string)$agent->id ? 'selected' : '' }}>
                                                    {{ $agent->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            @endif

                            <div class="col-md-4 mt-4">
                                <div class="form-group d-flex mb-2">
                                    <button type="submit" class="btn btn-primary">Search</button>
                                    <a href="{{ route('stock') }}" class="btn btn-secondary ml-2">Reset</a>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>
            </form>

            <div class="table-rep-plugin">
                <div class="table-responsive" id="tablefixed">
                    <table class="table table-bordered table-hover mt-0" cellspacing="0">
                        <thead class="theme-primary text-white">
                            <tr>
                                <th style="width: 5%;">{{ __('page.stock')[1] }}</th>
                                <th style="width: 15%;">{{ __('page.stock')[2] }}</th>

                                @if(!isRole(ROLE_AGENT))
                                    <th style="width: 10%;">{{ __('page.stock')[4] }}</th>
                                @endif

                                <th style="width: 10%;">{{ __('page.stock')[5] }}</th>
                                <th style="width: 10%;">{{ __('page.stock')[6] }}</th>
                                <th style="width: 10%;">{{ __('page.stock')[7] }}</th>
                                <th style="width: 10%;">{{ __('page.stock')[8] }}</th>
                                <th style="width: 10%;">{{ __('page.stock')[9] }}</th>
                                <th style="width: 10%;">{{ __('page.stock')[10] }}</th>
                            </tr>
                        </thead>

                        <tbody>
                            @php
                                $stock_sale_price = 0;
                                $stock_purchase_price = 0;
                                 $total_stock_in = 0;
                            @endphp

                            @foreach ($products as $product)
                                <tr>
                                    {{-- SERIAL number based on paginator --}}
                                    <td>{{ ($products->currentPage() - 1) * $products->perPage() + $loop->iteration }}</td>

                                    <td>
                                        <img src="{{ checkImage($product->image) }}"
                                             alt="{{ $product->product_name }}" height="45px"><br>
                                        <strong>{{ $product->product_name }}</strong>
                                    </td>

                                    @if(!isRole(ROLE_AGENT))
                                        <td>{{ $product->purchase_price }}</td>
                                    @endif

                                    <td>{{ $product->selling_price }}</td>

                                    @php
                                        $total_stock_in = round($product->purchase_qty + $product->sale_return_qty + $product->transferred_in_qty, 2);
                                        $total_stock_out = round($product->sale_qty + $product->purchase_return_qty + $product->transferred_out_qty, 2);
                                        $stock_qty = $total_stock_in - $total_stock_out;

                                        $sale_price = $stock_qty * $product->selling_price;
                                        $purchase_price = $stock_qty * $product->purchase_price;

                                        $stock_sale_price += $sale_price;
                                        $stock_purchase_price += $purchase_price;
                                        $total_stock_in += $stock_qty;
                                    @endphp

                                    <td>{{ $total_stock_in }}</td>
                                    <td>{{ $total_stock_out }}</td>
                                    <td>{{ $stock_qty }}</td>
                                    <td>{{ $sale_price }}</td>
                                    <td>{{ $purchase_price }}</td>
                                </tr>
                            @endforeach
                        </tbody>

                        <tfoot>
                            <tr>
                                <td colspan="{{ !isRole(ROLE_AGENT) ? 6 : 5 }}"></td>
                                <td><b>{{$total_stock_in}}</b></td>
                                <td><b>{{ $stock_sale_price }} BDT</b></td>
                                <td><b>{{ $stock_purchase_price }} BDT</b></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            {{-- Pagination always works now --}}
            <div>
                {{ $products->appends(request()->input())->links() }}
            </div>

        </div>
    </div>
</div>

<!-- Business Modal Start -->
<div class="modal fade bd-example-modal-xl" id="unitModal" tabindex="-1" role="dialog"
    aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" style="width: 100%">
        <div class="modal-content" id="modalcontent" style="width: 100%"></div>
    </div>
</div>
<!-- Business Modal End -->

@endsection

@section('script')
<script>
    $(function() {
        $('#tablefixed').responsiveTable({ addFocusBtn: false });

        // Auto submit on dropdown change (datatable style)
        $('#per_page, #agent_id').on('change', function () {
            const url = new URL(window.location.href);
            url.searchParams.delete('page'); // go back to page 1
            window.history.replaceState({}, '', url.toString());
            $('#searching').submit();
        });

        // Optional: auto search on typing (debounce)
        let typingTimer = null;
        $('#product_name').on('keyup', function () {
            clearTimeout(typingTimer);
            typingTimer = setTimeout(function () {
                const url = new URL(window.location.href);
                url.searchParams.delete('page');
                window.history.replaceState({}, '', url.toString());
                $('#searching').submit();
            }, 600);
        });
    });
</script>
@endsection
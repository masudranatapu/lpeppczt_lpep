@extends('layouts.dashboard')

@section('content')
    <link rel="stylesheet" href="/css/remove-number-arrows.css">
    <form action="{{ route('stock-transfer.store') }}" method="post">
        @csrf
        <div class="row">
            <div class="col-md-12">
                <div class="card-box table-responsive mt-4" style="border-top: 3px solid #157c34;">
                    <div class="row">
                        <div class="col-md-12">
                            <h4 class="m-t-0 header-title mb-2">
                                <b>{{ __('page.stock_transfer.stock_transfer_edit') }}</b>
                            </h4>
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="date">{{ __('page.common.agent') }}
                                    <i class="fa fa-info-circle text-info hover-q no-print "></i>
                                </label>
                                <select required name="agent_id" id="agent_id" class="form-control">
                                    <option value="">Select Agent</option>
                                    @foreach (getCachedAgents() as $agent)
                                        <option value="{{ $agent->id }}">{{ $agent->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="date">{{ __('page.stock_transfer.date') }}
                                    <i class="fa fa-info-circle text-info hover-q no-print "></i>
                                </label>
                                <input required id="date" type="text" name="date"
                                    class="form-control datepicker text-center" value="{{ date('Y-m-d') }}">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-box table-responsive mt-4">
                    <div class="row">
                        <div class="col-md-8 offset-md-2">

                            <div class="form-group">
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-search"></i>
                                    </span>

                                    <input class="form-control prevent" id="search_product"
                                        placeholder="{{ __('page.purchase')[7] }}" autofocus name="search_product"
                                        type="text">

                                    <div id="showSearchProducts" style="display: none">

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">

                        <div class="col-md-8 offset-md-2">
                            <div class="table-responsive">
                                <table class="table table-condensed table-bordered" id="purchase_entry_table">
                                    <thead>
                                        <tr class="theme-primary text-white">
                                            <th style="width: 36%;">{{ __('page.purchase')[8] }}</th>
                                            <th style="width: 27%;">{{ 'Stock Quantity' }}</th>
                                            <th style="width: 27%;">{{ 'Transfer Quantity' }}</th>
                                            <th style="width: 10%"><i class="fa fa-trash" aria-hidden="true"></i></th>
                                        </tr>
                                    </thead>
                                    <tbody id="mytab1">

                                        @foreach ($transfer->details as $detail)
                                        {{ $detail }}
                                        <tr class="pr-136">
                                            <td style="vertical-align: middle; padding-left: 10px;">
                                               {{ $detail->product->product_name }}
                                            </td>

                                            <td class="text-center">
                                                <select class="form-control batch_id" name="purchase_product_id[136]"
                                                    required="">
                                                    <option value="7" data-quantity="5" selected="">
                                                        987 (5)
                                                    </option>
                                                </select>
                                            </td>

                                            <td style="vertical-align: middle">
                                                <div class="input-group">
                                                    <span class="input-group-btn">
                                                        <button type="button" onclick="qtyDecrement(this,136)"
                                                            style="cursor: pointer;"
                                                            class="quantity-left-minus btn btn-danger btn-number"
                                                            data-type="minus" data-field="">
                                                            <span class="glyphicon glyphicon-minus"><i
                                                                    class="fa fa-minus"></i></span>
                                                        </button>
                                                    </span>

                                                    <input oninput="transferQty(this, 136)" id="qty136"
                                                        style="width: 150px; margin: auto;" type="number"
                                                        class="form-control quantity text-center" value="1"
                                                        data-is-decimal="undefined" autocomplete="off" min="1"
                                                        name="quantity[136]">

                                                    <span class="input-group-btn">
                                                        <button type="button" onclick="qtyIncrement(this,136)"
                                                            style="cursor: pointer;"
                                                            class="quantity-right-plus btn btn-success btn-number"
                                                            data-type="plus" data-field="">
                                                            <span class="glyphicon glyphicon-plus"><i
                                                                    class="fa fa-plus"></i></span>
                                                        </button>
                                                    </span>
                                                </div>
                                            </td>

                                            <td style="vertical-align: middle">
                                                <button id="DeleteButton" type="button" class="btn btn-sm btn-danger">
                                                    <i class="fa fa-trash" aria-hidden="true"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <button type="submit" class="btn btn-primary float-right" style="cursor: pointer">Transfer
                                Stock</button>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@section('script')
    <script>
        const CSRF_TOKEN = "{{ csrf_token() }}";
        const ROUTE_TRANSFER_AUTOCOMPLETE = "{{ route('autocomplete.transfer') }}";
        const PRODUCTS = [];
    </script>

    <script src="/js/stock-transfer.js?v=0.3"></script>
@endsection

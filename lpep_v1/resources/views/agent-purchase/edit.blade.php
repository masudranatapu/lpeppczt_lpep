@extends('layouts.dashboard')

@section('content')

    <div class="row">
        <div class="col-12">
            <div class="mt-4">
                <h4>Purchase Product</h4>
            </div>

            <form action="{{ route('purchase.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" value="{{ $purchase->id }}" name="purchase_id">
                <input type="hidden" value="{{ $purchase->total }}" name="old_total">
                <input type="hidden" value="{{ $purchase->total_pay }}" name="old_total_pay">

                <div class="card-box table-responsive mt-4">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="row">
                                <div class="col-md-12">
                                    @include('inc.appCustomer', ['customer' => $purchase->customer, 'readOnly' => true])
                                </div>

                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="invoice_no">{{ __('page.purchase')[2] }}</label>
                                        <input type="text" class="form-control" name="invoice_no"
                                            value="{{ $purchase->invoice_no }}" id="invoice_no" placeholder="Invoice No">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="invoice_no">Total Loan</label>
                                        <input type="text" class="form-control" id="total-loan"
                                            value="{{ $purchase->total + $loan_amount }}" disabled>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="purchase_date">{{ __('page.purchase')[3] }}<i
                                                class="fa fa-info-circle text-info hover-q no-print "></i></label>
                                        <input type="date" class="form-control" name="purchase_date"
                                            placeholder="Purchase Date" value="<?php echo date('Y-m-d'); ?>" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label for="">{{ __('page.purchase')[6] }}</label>
                            <textarea name="note" class="form-control" style="height: 74%"></textarea>
                        </div>
                    </div>
                </div>

                <div class="card-box table-responsive mt-4" style="border-top: 3px solid #296bc2;">

                    <div class="row">
                        <div class="col-sm-12">
                            @php
                                $purchaseProducts = collect($purchase->purchaseProducts);
                            @endphp

                            @if ($purchaseProducts->count())
                                <div class="table-responsive" id="regular-purchase">
                                    <table class="table table-condensed table-bordered text-center table-striped">
                                        <thead>
                                            <tr class="bg-success">
                                                <th style="width: 15%;">{{ __('page.purchase')[8] }}</th>
                                                <th style="width: 15%;">{{ 'Batch Id' }}</th>
                                                <th style="width: 15%;">{{ __('page.purchase')[9] }}</th>
                                                <th style="width: 15%;">{{ __('page.purchase')[10] }}</th>
                                                <th style="width: 15%;">{{ __('page.purchase')[11] }}</th>
                                                <th style="width: 15%;"><i class="fa fa-trash" aria-hidden="true"></i>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($purchaseProducts as $key => $purchaseProduct)
                                                <tr class="pr-{{ $purchaseProduct->product->id }}-{{ $key + 1 }}">
                                                    <td style="vertical-align: middle">
                                                        <input type="hidden" value="{{ $purchaseProduct->product->id }}"
                                                            class="product_id"
                                                            name="reg_product_id[{{ $purchaseProduct->product->id }}-{{ $key + 1 }}]"
                                                            data-counter="{{ $key + 1 }}">
                                                        {{ $purchaseProduct->product->product_name }}
                                                        <input type="hidden"
                                                            name="reg_purchase_product_id[{{ $purchaseProduct->product->id }}-{{ $key + 1 }}]"
                                                            value="{{ $purchaseProduct->id }}">
                                                        <input type="hidden"
                                                            name="reg_old_quantity[{{ $purchaseProduct->product->id }}-{{ $key + 1 }}]"
                                                            value="{{ $purchaseProduct->quantity }}">
                                                    </td>

                                                    <td style="vertical-align: middle">
                                                        <input type="text"
                                                            id="batch_id{{ $purchaseProduct->product->id }}"
                                                            style="width: 150px; margin: auto;"
                                                            class="form-control text-center batch_id"
                                                            value="{{ $purchaseProduct->batch_id }}" readonly
                                                            name="reg_batch_id[{{ $purchaseProduct->product->id }}-{{ $key + 1 }}]">
                                                    </td>

                                                    <td style="vertical-align: middle">
                                                        <div class="input-group">
                                                            <span class="input-group-btn">
                                                                <button type="button"
                                                                    onclick="purchaseQtyDecrement(this,{{ $purchaseProduct->product->id }})"
                                                                    style="cursor: pointer;"
                                                                    class="quantity-left-minus btn btn-danger btn-number"
                                                                    data-type="minus" data-field="">
                                                                    <span class="glyphicon glyphicon-minus"><i
                                                                            class="fa fa-minus"></i></span>
                                                                </button>
                                                            </span>

                                                            <input
                                                                oninput="purchaseQty(this, {{ $purchaseProduct->product->id }})"
                                                                id="qty{{ $purchaseProduct->product->id }}-{{ $key + 1 }}"
                                                                style="width: 150px; margin: auto;" type="text"
                                                                class="form-control purchase_quantity text-center"
                                                                name="reg_purchase_quantity[{{ $purchaseProduct->product->id }}-{{ $key + 1 }}]"
                                                                value="{{ $purchaseProduct->quantity }}">

                                                            <span class="input-group-btn">
                                                                <button type="button"
                                                                    onclick="purchaseQtyIncrement(this,{{ $purchaseProduct->product->id }})"
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
                                                        <input
                                                            oninput="purchasePrice(this, {{ $purchaseProduct->product->id }})"
                                                            type="text"
                                                            id="purchase_price{{ $purchaseProduct->product->id }}"
                                                            style="width: 150px; margin: auto;"
                                                            class="form-control text-center purchase_rate"
                                                            autocomplete="off"
                                                            value="{{ $purchaseProduct->purchase_price }}"
                                                            name="reg_purchase_price[{{ $purchaseProduct->product->id }}-{{ $key + 1 }}]">
                                                    </td>

                                                    <td style="vertical-align: middle">
                                                        <span class="text-center purchase_total">
                                                            {{ $purchaseProduct->total_price }}
                                                        </span>
                                                        <input style="width: 150px; margin: auto;" type="hidden"
                                                            class="form-control text-center purchase_total"
                                                            value="{{ $purchaseProduct->total_price }}"
                                                            name="reg_purchase_total[{{ $purchaseProduct->product->id }}-{{ $key + 1 }}]">
                                                    </td>

                                                    <td style="vertical-align: middle">
                                                        <button id="DeleteButton" type="button"
                                                            class="btn btn-sm btn-danger">
                                                            <i class="fa fa-trash" aria-hidden="true"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach

                                        </tbody>
                                    </table>
                                </div>
                            @endif

                            <hr />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6"></div>

                        <div class="col-md-6 text-right">
                            <label class="text-right">{{ __('page.purchase')[12] }}:</label>
                            <input type="text"
                                style="padding-right:6px;height:34px;margin-top: 6px;border-radius: 3px;width: 185px;text-align: right; border: 1px solid #b3afaf; background-color: #e6e6e6;"
                                id="total" name="total" value="{{ $purchase->total }}" readonly>
                        </div>

                        <div class="col-md-12">
                            <div class="text-right mt-4">
                                <button type="submit" class="btn btn-primary" id="savePurchase">Update</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <!-- end row -->
@endsection

@section('script')
    <script>
        const CSRF_TOKEN = "{{ csrf_token() }}";
        const ROUTE_PURCHASE_SEARCH = "{{ route('purchase.search') }}";
        const ROUTE_PURCHASE_PAYMENT_ACCOUNT = "{{ route('purchase.payment.account') }}";
        const ROUTE_DOES_BATCH_ID_EXIST = "{{ route('purchase.does-batch-id-exist') }}";
        const PRODUCTS = JSON.parse('{!! $purchase->purchaseProducts !!}');
        const USER_ID = {{ auth()->id() }};
    </script>

    <script src="/js/purchaseAgent.js?v=0.8"></script>
@endsection

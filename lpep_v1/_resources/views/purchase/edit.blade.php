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
                <div class="card-box table-responsive mt-4" style="border-top: 3px solid #00a65a;">
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="contact_id">Supplier Name<i
                                        class="fa fa-info-circle text-info hover-q no-print "></i></label>
                                <select class="form-control select2" required name="contact_id">
                                    <option value="">select</option>
                                    @foreach ($contacts as $conatct)
                                        <option value="{{ $conatct->id }}"
                                            {{ $conatct->id == $purchase->contact_id ? 'selected' : '' }}>
                                            {{ $conatct->name }} ({{ $conatct->mobile }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="invoice_no">Invoice No</label>
                                <input type="text" class="form-control" name="invoice_no"
                                    value="{{ $purchase->invoice_no }}" placeholder="Invoice No">
                            </div>
                        </div>

                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="purchase_date">Purchase Date<i
                                        class="fa fa-info-circle text-info hover-q no-print "></i></label>
                                <input type="date" class="form-control" value="{{ $purchase->purchase_date }}"
                                    name="purchase_date" placeholder="Purchase Date" value="<?php echo date('Y-m-d'); ?>" required>
                            </div>
                        </div>

                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="barcode_type">Attachment</label>
                                <input type="file" class="form-control" name="attachment">
                            </div>
                        </div>

                        <div class="col-md-12">
                            <label for="">Note</label>
                            <textarea name="note" class="form-control" rows="2"> {{ $purchase->note }} </textarea>
                        </div>
                    </div>
                </div>

                <div class="card-box table-responsive mt-4" style="border-top: 3px solid #296bc2;">

                    <div class="row">
                        <div class="col-sm-12">

                            @php
                                $purchaseProducts = collect($purchase->purchaseProducts);
                                $medicineProducts = $purchaseProducts->filter(fn($item) => $item->product->is_medicine == 1);
                                $regularProducts = $purchaseProducts->filter(fn($item) => $item->product->is_medicine != 1);
                            @endphp

                            @if ($regularProducts->count())
                                <div class="table-responsive" id="regular-purchase">
                                    <table class="table table-condensed table-bordered text-center table-striped">
                                        <thead>
                                            <tr class="bg-success">
                                                <th style="width: 15%;">{{ __('page.purchase')[8] }}</th>
                                                <th style="width: 15%;">{{ 'Batch Id' }}</th>
                                                <th style="width: 15%;">{{ __('page.purchase')[9] }}</th>
                                                <th style="width: 15%;">{{ __('page.purchase')[10] }}</th>
                                                <th style="width: 15%;">{{ __('page.purchase.17') }}</th>
                                                <th style="width: 15%;">{{ __('page.purchase.18') }}</th>
                                                <th style="width: 15%;">{{ __('page.purchase')[11] }}</th>
                                                <th style="width: 15%;"><i class="fa fa-trash" aria-hidden="true"></i>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($regularProducts as $key => $purchaseProduct)
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
                                                        <input type="text" style="width: 150px; margin: auto;"
                                                            class="form-control text-center purchase_subtotal"
                                                            value="{{ $purchaseProduct->subtotal_price ?? $purchaseProduct->total_price }}"
                                                            name="reg_purchase_subtotal[{{ $purchaseProduct->product->id }}-{{ $key + 1 }}]">
                                                    </td>

                                                    <td style="vertical-align: middle">
                                                        <input type="text" style="width: 150px; margin: auto;"
                                                            class="form-control text-center purchase_other_cost"
                                                            value="{{ $purchaseProduct->other_cost }}"
                                                            name="reg_purchase_other_cost[{{ $purchaseProduct->product->id }}-{{ $key + 1 }}]">
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
                                                            class="btn btn-sm btn-danger"><i class="fa fa-trash"
                                                                aria-hidden="true"></i></button>
                                                    </td>
                                                </tr>
                                            @endforeach

                                        </tbody>
                                    </table>
                                </div>
                            @endif


                            @if ($medicineProducts->count())
                                <div class="table-responsive" id="medicine-purchase">
                                    <table class="table table-condensed table-bordered text-center table-striped">
                                        <thead>
                                            <tr class="bg-success">
                                                <th style="width: 10%;">{{ __('page.purchase')[8] }}</th>
                                                <th style="width: 10%;">{{ 'Batch Id' }}</th>
                                                <th style="width: 10%;">{{ 'Expiry Date' }}</th>
                                                <th style="width: 10%;">{{ 'Box Pattern' }}</th>
                                                <th style="width: 10%;">{{ 'Box Qty' }}</th>
                                                <th style="width: 10%;">{{ __('page.purchase')[9] }}</th>
                                                <th style="width: 10%;">{{ __('page.purchase')[10] }}</th>
                                                <th style="width: 10%;">{{ __('page.purchase.17') }}</th>
                                                <th style="width: 10%;">{{ __('page.purchase.18') }}</th>
                                                <th style="width: 10%;">{{ __('page.purchase')[11] }}</th>
                                                <th style="width: 10%;"><i class="fa fa-trash" aria-hidden="true"></i>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($medicineProducts as $key => $purchaseProduct)
                                                <tr class="pr-{{ $purchaseProduct->product->id }}-{{ $key + 1 }}">
                                                    <td style="vertical-align: middle">
                                                        <input type="hidden" value="{{ $purchaseProduct->product->id }}"
                                                            class="product_id"
                                                            name="med_product_id[{{ $purchaseProduct->product->id }}-{{ $key + 1 }}]"
                                                            data-counter="{{ $key + 1 }}">
                                                        {{ $purchaseProduct->product->product_name }}
                                                        <input type="hidden"
                                                            name="med_purchase_product_id[{{ $purchaseProduct->product->id }}-{{ $key + 1 }}]"
                                                            value="{{ $purchaseProduct->id }}">
                                                        <input type="hidden"
                                                            name="med_old_quantity[{{ $purchaseProduct->product->id }}-{{ $key + 1 }}]"
                                                            value="{{ $purchaseProduct->quantity }}">
                                                    </td>

                                                    <td style="vertical-align: middle">
                                                        <input type="text"
                                                            id="batch_id{{ $purchaseProduct->product->id }}"
                                                            style="width: 100px; margin: auto;"
                                                            class="form-control text-center batch_id"
                                                            value="{{ $purchaseProduct->batch_id }}" readonly
                                                            name="med_batch_id[{{ $purchaseProduct->product->id }}-{{ $key + 1 }}]">
                                                    </td>

                                                    <td style="vertical-align: middle">
                                                        <input type="text"
                                                            id="expiry_date{{ $purchaseProduct->product->id }}"
                                                            style="width: 100px; margin: auto;"
                                                            class="form-control text-center expiry_date"
                                                            value="{{ $purchaseProduct->expiry_date }}"
                                                            name="med_expiry_date[{{ $purchaseProduct->product->id }}-{{ $key + 1 }}]">
                                                    </td>

                                                    <td style="vertical-align: middle">
                                                        <select class="form-control box_pattern"
                                                            name="med_box_pattern[{{ $purchaseProduct->product->id }}-{{ $key + 1 }}]">
                                                            @foreach ($box_patterns as $pattern)
                                                                <option value="{{ $pattern->id }}"
                                                                    {{ $pattern->id == $purchaseProduct->pattern_id ? 'selected' : '' }}
                                                                    data-quantity="{{ $pattern->quantity }}">
                                                                    {{ $pattern->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </td>

                                                    <td style="vertical-align: middle">
                                                        <div class="input-group">
                                                            <span class="input-group-btn">
                                                                <button type="button"
                                                                    onclick="purchaseQtyDecrement(this,{{ $purchaseProduct->product->id }})"
                                                                    style="cursor: pointer; padding: 8px; height: 94%"
                                                                    class="quantity-left-minus btn btn-danger btn-number"
                                                                    data-type="minus" data-field="">
                                                                    <span class="glyphicon glyphicon-minus"><i
                                                                            class="fa fa-minus"></i></span>
                                                                </button>
                                                            </span>

                                                            <input
                                                                oninput="purchaseQty(this, {{ $purchaseProduct->product->id }})"
                                                                id="qty{{ $purchaseProduct->product->id }}-{{ $key + 1 }}"
                                                                style="width: 80px; margin: auto;" type="text"
                                                                class="form-control purchase_quantity text-center"
                                                                name="med_box_pattern_quantity[{{ $purchaseProduct->product->id }}-{{ $key + 1 }}]"
                                                                value="{{ $purchaseProduct->box_pattern_quantity }}">

                                                            <span class="input-group-btn">
                                                                <button type="button"
                                                                    onclick="purchaseQtyIncrement(this,{{ $purchaseProduct->product->id }})"
                                                                    style="cursor: pointer; padding: 8px; height: 94%"
                                                                    class="quantity-right-plus btn btn-success btn-number"
                                                                    data-type="plus" data-field="">
                                                                    <span class="glyphicon glyphicon-plus"><i
                                                                            class="fa fa-plus"></i></span>
                                                                </button>
                                                            </span>
                                                        </div>
                                                    </td>

                                                    <td>
                                                        <input style="width: 90px; margin: auto;" type="text"
                                                            class="form-control text-center multiplied-quantity"
                                                            value="{{ $purchaseProduct->quantity }}" disabled>
                                                    </td>

                                                    <td style="vertical-align: middle">
                                                        <input
                                                            oninput="purchasePrice(this, {{ $purchaseProduct->product->id }})"
                                                            type="text"
                                                            id="purchase_price{{ $purchaseProduct->product->id }}"
                                                            style="width: 100px; margin: auto;"
                                                            class="form-control text-center purchase_rate"
                                                            value="{{ $purchaseProduct->purchase_price }}"
                                                            autocomplete="off"
                                                            name="med_purchase_price[{{ $purchaseProduct->product->id }}-{{ $key + 1 }}]">
                                                    </td>

                                                    <td style="vertical-align: middle">
                                                        <input type="text" style="width: 100px; margin: auto;"
                                                            class="form-control text-center purchase_subtotal"
                                                            value="{{ $purchaseProduct->subtotal_price ?? $purchaseProduct->total_price }}"
                                                            name="med_purchase_subtotal[{{ $purchaseProduct->product->id }}-{{ $key + 1 }}]">
                                                    </td>

                                                    <td style="vertical-align: middle">
                                                        <input type="text" style="width: 100px; margin: auto;"
                                                            class="form-control text-center purchase_other_cost"
                                                            value="{{ $purchaseProduct->other_cost }}"
                                                            name="med_purchase_other_cost[{{ $purchaseProduct->product->id }}-{{ $key + 1 }}]">
                                                    </td>

                                                    <td style="vertical-align: middle">
                                                        <div style="width: 80px">
                                                            <span class="text-center purchase_total">
                                                                {{ $purchaseProduct->total_price }}
                                                            </span>
                                                            <input style="width: 100px; margin: auto;" type="hidden"
                                                                class="form-control text-center purchase_total"
                                                                value="{{ $purchaseProduct->total_price }}"
                                                                name="med_purchase_total[{{ $purchaseProduct->product->id }}-{{ $key + 1 }}]">
                                                        </div>
                                                    </td>

                                                    <td style="vertical-align: middle">
                                                        <button id="DeleteButton" type="button"
                                                            class="btn btn-sm btn-danger"><i class="fa fa-trash"
                                                                aria-hidden="true"></i></button>
                                                    </td>
                                                </tr>
                                            @endforeach

                                        </tbody>
                                    </table>
                                </div>
                            @endif
                            <hr />
                        </div>

                        <div class="col-md-12 mt-3">
                            <table class="col-md-12">
                                <tbody>
                                    <tr class="hide">
                                        <th class="col-md-7 text-right">Total Amount:</th>
                                        <td class="col-md-5 text-left">
                                            <input type="text"
                                                style="padding-right:6px;height:34px;margin-top: 6px;border-radius: 3px;width: 185px;text-align: right; border: 1px solid #b3afaf; background-color: #e6e6e6;"
                                                id="total" name="total" value="{{ $purchase->total }}" readonly>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="col-md-7 text-right"> Pay By</th>
                                        <th class="col-md-5 text-left">
                                            <input type="hidden" name="contact_payment_id"
                                                value="{{ isset($purchase->supplierPayment->id) ? $purchase->supplierPayment->id : null }}">

                                            @isset($purchase->supplierPayment->pay_by)
                                                <select name="pay_by" id="pay_by" class="form-control" required=""
                                                    style="padding-right:6px;height:34px;margin-top: 6px;border-radius: 3px;width: 185px;text-align: right; border: 1px solid #bbbbbb;">
                                                    <option value="Cash"
                                                        {{ $purchase->supplierPayment->pay_by == 'Cash' ? 'selected' : '' }}>
                                                        Cash</option>
                                                    <option value="Mobile Banking"
                                                        {{ $purchase->supplierPayment->pay_by == 'Mobile Banking' ? 'selected' : '' }}>
                                                        Mobile Banking</option>
                                                    <option value="Card"
                                                        {{ $purchase->supplierPayment->pay_by == 'Card' ? 'selected' : '' }}>
                                                        Card</option>
                                                    <option value="Bank Account"
                                                        {{ $purchase->supplierPayment->pay_by == 'Bank Account' ? 'selected' : '' }}>
                                                        Bank Account</option>
                                                </select>
                                            @else
                                                <select name="pay_by" id="pay_by" class="form-control" required=""
                                                    style="padding-right:6px;height:34px;margin-top: 6px;border-radius: 3px;width: 185px;text-align: right; border: 1px solid #bbbbbb;">
                                                    <option value="">Select</option>
                                                    <option value="Cash">Cash</option>
                                                    <option value="Mobile Banking">Mobile Banking</option>
                                                    <option value="Card">Card</option>
                                                    <option value="Bank Account">Bank Account</option>
                                                </select>
                                            @endisset
                                        </th>
                                    </tr>


                                    <tr id="account_info">
                                        <th class="col-md-7 text-right"> </th>
                                        <th class="col-md-5 text-left">
                                            @isset($purchase->supplierPayment->pay_by)
                                                @if ($purchase->supplierPayment->pay_by == 'Mobile Banking')
                                                    <select name="account_id" id="" class="form-control select2" required=""
                                                        style="padding-right:6px;height:34px;margin-top: 6px;border-radius: 3px;width: 185px;text-align: right; border: 1px solid #bbbbbb;">
                                                        @foreach ($data as $account)
                                                            <option value="{{ $account->id }}"
                                                                {{ $account->id == $purchase->supplierPayment->account_id ? 'selected' : '' }}>
                                                                {{ $account->mobile_number }}
                                                                ({{ $account->mobile_bank_name }})</option>
                                                        @endforeach
                                                    </select>
                                                @elseif($purchase->supplierPayment->pay_by == "Card")
                                                    <select name="account_id" id="" class="form-control select2" required=""
                                                        style="padding-right:6px;height:34px;margin-top: 6px;border-radius: 3px;width: 185px;text-align: right; border: 1px solid #bbbbbb;">
                                                        @foreach ($data as $account)
                                                            <option value="{{ $account->id }}"
                                                                {{ $account->id == $purchase->supplierPayment->account_id ? 'selected' : '' }}>
                                                                {{ $account->card_number }}</option>
                                                        @endforeach
                                                    </select>
                                                @elseif($purchase->supplierPayment->pay_by == "Bank Account")
                                                    <select name="account_id" id="" class="form-control select2" required=""
                                                        style="padding-right:6px;height:34px;margin-top: 6px;border-radius: 3px;width: 185px;text-align: right; border: 1px solid #bbbbbb;">
                                                        @foreach ($data as $account)
                                                            <option value="{{ $account->id }}"
                                                                {{ $account->id == $purchase->supplierPayment->account_id ? 'selected' : '' }}>
                                                                {{ $account->bank_account_number }}
                                                                ({{ $account->bank->bank_name }})</option>
                                                        @endforeach
                                                    </select>
                                                @endif
                                            @endisset
                                        </th>
                                    </tr>


                                    <tr>
                                        <th class="col-md-7 text-right"> Paying Amount </th>
                                        <th class="col-md-5 text-left">
                                            <input type="text"
                                                style="padding-right:6px;height:34px;margin-top: 6px;border-radius: 3px;width: 185px;text-align: right; border: 1px solid #b3afaf;"
                                                oninput="due()" name="paying_amount" id="paying_amount"
                                                value="{{ isset($purchase->supplierPayment->paying_amount) ? $purchase->supplierPayment->paying_amount : 0 }}">
                                        </th>
                                    </tr>


                                    <tr>
                                        <th class="col-md-7 text-right">Total Due:</th>
                                        <td class="col-md-5 text-left">
                                            <input type="text"
                                                style="padding-right:6px;height:34px;margin-top: 6px;border-radius: 3px;width: 185px;text-align: right; border: 1px solid #b3afaf; background-color: #e6e6e6;"
                                                id="total_due"
                                                value="{{ $purchase->total - (isset($purchase->supplierPayment->paying_amount) ? $purchase->supplierPayment->paying_amount : 0) }}"
                                                readonly>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                    </div>

                    <div class="text-right mt-4">
                        <button type="submit" class="btn btn-primary">Update</button>
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
        const BOX_PATTERNS = JSON.parse('{!! $box_patterns !!}');
    </script>

    <script src="/js/purchase.js?v=0.8"></script>
@endsection

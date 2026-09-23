@extends('layouts.dashboard')

@section('content')
@php
    $business = currentBranch();
@endphp
    <div class="row">
        <div class="col-md-12">
            <div class="card-box mt-4" style="border-top: 0px;">
                <div class="panel-body">
                    <div class="clearfix">
                        <div class="pull-left">
                            <img src="/{{ $business->invoice_logo }}"
                                alt="{{ $business->invoice_name ?? $business->name }}"
                                class="logo" alt="" height="60px">
                        </div>
                        <div class="pull-right">
                            <h4>Invoice #{{ $purchase->invoice_no }} <br>
                                <strong>{{ $purchase->purchase_date }}</strong>
                            </h4>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-12">

                            <div class="pull-left m-t-30">
                                <address>
                                    <strong>{{ $purchase->supplier->name }}</strong>
                                    <br>
                                    {{ $purchase->supplier->zip }},{{ $purchase->supplier->city }},{{ $purchase->supplier->country }}
                                    <br>
                                    <abbr title="Phone">P:</abbr> (+880) {{ $purchase->supplier->mobile }}
                                </address>
                            </div>
                            <div class="pull-right m-t-30">
                                <p><strong>Purchase Date: </strong> {{ $purchase->purchase_date }}</p>
                                <p class="m-t-10"><strong>Purchase Status: </strong> <span
                                        class="label label-pink">Delivered</span></p>
                            </div>
                        </div><!-- end col -->
                    </div>
                    <!-- end row -->

                    <div class="m-h-50"></div>

                    <div class="row">
                        <div class="col-md-12">

                            @php
                                $purchaseProducts = collect($purchase->purchaseProducts);
                                $medicineProducts = $purchaseProducts->filter(fn($item) => $item->product->is_medicine == 1);
                                $regularProducts = $purchaseProducts->filter(fn($item) => $item->product->is_medicine != 1);
                            @endphp

                            @if ($regularProducts->count())
                                <div class="">
                                    <table class=" table m-t-30">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th style="width: 60%">Item</th>
                                            <th>Batch Id</th>
                                            <th>Quantity</th>
                                            <th>Unit Cost</th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($regularProducts as $product)
                                            <tr>
                                                <td>{{ $loop->index + 1 }}</td>
                                                <td>
                                                    {{ $product->product->product_name }}
                                                </td>
                                                <td>{{ $product->batch_id }}</td>
                                                <td>{{ $product->quantity }}</td>
                                                <td>{{ $product->purchase_price }}</td>
                                                <td>{{ $product->purchase_price * $product->quantity }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    </table>
                                </div>
                            @endif


                            @if ($medicineProducts->count())
                                <div class="">
                                    <table class=" table m-t-30">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th style="width: 36%">Medicine Item</th>
                                            <th>Batch Id</th>
                                            <th>Expiry Date</th>
                                            <th>Box Pattern</th>
                                            <th>Box Qty</th>
                                            <th>Quantity</th>
                                            <th>Unit Cost</th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($medicineProducts as $product)
                                            <tr>
                                                <td>{{ $loop->index + 1 }}</td>
                                                <td>
                                                    {{ $product->product->product_name }}
                                                </td>
                                                <td>{{ $product->batch_id }}</td>
                                                <td>{{ $product->expiry_date }}</td>
                                                <td>{{ $product->boxPattern->name . ' (' . $product->boxPattern->quantity . ')' }}
                                                </td>
                                                <td>{{ $product->box_pattern_quantity }}</td>
                                                <td>{{ $product->quantity }}</td>
                                                <td>{{ $product->purchase_price }}</td>
                                                <td>{{ $product->purchase_price * $product->box_pattern_quantity }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xl-6 col-6">
                            <div class="clearfix m-t-40">
                                <h5 class="small text-inverse font-600">PAYMENT Details</h5>
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th scope="col">Sl</th>
                                            <th scope="col">Payment Method</th>
                                            <th scope="col">Payment By</th>
                                            <th scope="col">Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($contact_payments as $payment)
                                            <tr>
                                                <td>{{ $loop->index + 1 }}</td>
                                                <td>{{ $payment->pay_by }}</td>
                                                <td>
                                                    @if ($payment->pay_by == 'Mobile Banking')
                                                        {{ isset($payment->account_id) ? $payment->account->mobile_number : '' }}
                                                    @endif

                                                    @if ($payment->pay_by == 'Card')
                                                        {{ isset($payment->account_id) ? $payment->account->card_number : '' }}
                                                    @endif

                                                    @if ($payment->pay_by == 'Bank Account')
                                                        {{ isset($payment->account_id) ? $payment->account->bank_account_number : '' }}
                                                    @endif
                                                    @if ($payment->pay_by == 'Cash')
                                                        Cash
                                                    @endif
                                                </td>
                                                <td>{{ $payment->paying_amount }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>

                            </div>
                        </div>
                        <div class="col-xl-3 col-6 offset-md-3">
                            <h3 class="text-right">{{ $purchase->total }} BDT</h3>
                        </div>
                    </div>
                    <hr>
                    <div class="d-print-none">
                        <div class="pull-right">
                            <a href="javascript:window.print()" class="btn btn-inverse waves-effect waves-light"><i
                                    class="fa fa-print"></i></a>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>


@endsection

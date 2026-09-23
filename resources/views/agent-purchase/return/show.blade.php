@extends('layouts.dashboard')
@section('title', 'Invoice Purchase Return')
@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="card-box mt-4" style="border-top: 0px;">
                <div class="panel-body">
                    <div class="clearfix">
                        <div class="pull-left">
                            <img src="/{{ $purchaseReturn->business->invoice_logo ?? json_decode($purchaseReturn->business->logo) }}"
                                alt="{{ $purchaseReturn->business->invoice_name ?? $purchaseReturn->business->name }}"
                                class="logo" alt="" height="60px">
                        </div>
                        <div class="pull-right">
                            <h4>Invoice #{{ $purchaseReturn->id }} <br>
                                <strong>{{ $purchaseReturn->date }}</strong>
                            </h4>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-12">

                            <div class="pull-left m-t-30">
                                <address>
                                    <strong>{{ $purchaseReturn->supplier->name }}</strong>
                                    <br>
                                    {{ $purchaseReturn->supplier->zip }},{{ $purchaseReturn->supplier->city }},{{ $purchaseReturn->supplier->country }}
                                    <br>
                                    <abbr title="Phone">P:</abbr> (+880) {{ $purchaseReturn->supplier->mobile }}
                                </address>
                            </div>
                            <div class="pull-right m-t-30">
                                <p><strong>Return Date: </strong> {{ $purchaseReturn->date }}</p>
                                <p class="m-t-10"><strong>Return Status: </strong> <span
                                        class="label label-pink">Return</span></p>
                            </div>
                        </div><!-- end col -->
                    </div>
                    <!-- end row -->

                    <div class="m-h-50"></div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="">
                            <table class=" table m-t-30">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th style="width: 60%">Item</th>
                                        <th>Quantity</th>
                                        <th>Unit Cost</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($purchaseReturn->purchaseReturnProducts as $product)
                                        <tr>
                                            <td>{{ $loop->index + 1 }}</td>
                                            <td>
                                                {{ $product->product->product_name }} {{ $product->model?->model_no }}
                                            </td>
                                            <td>{{ $product->quantity }}</td>
                                            <td>{{ $product->price }}</td>
                                            <td>{{ $product->price * $product->quantity }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                </table>
                            </div>
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
                                        <tr>
                                            <td>1</td>
                                            <td>{{ $purchaseReturn->contactPayment->pay_by }}</td>
                                            <td>
                                                @if ($purchaseReturn->contactPayment->pay_by == 'Mobile Banking')
                                                    {{ isset($purchaseReturn->contactPayment->account_id) ? $purchaseReturn->contactPayment->account->mobile_number : '' }}
                                                @endif

                                                @if ($purchaseReturn->contactPayment->pay_by == 'Card')
                                                    {{ isset($purchaseReturn->contactPayment->account_id) ? $purchaseReturn->contactPayment->account->card_number : '' }}
                                                @endif

                                                @if ($purchaseReturn->contactPayment->pay_by == 'Bank Account')
                                                    {{ isset($purchaseReturn->contactPayment->account_id) ? $purchaseReturn->contactPayment->account->bank_account_number : '' }}
                                                @endif
                                                @if ($purchaseReturn->contactPayment->pay_by == 'Cash')
                                                    Cash
                                                @endif
                                            </td>
                                            <td>{{ $purchaseReturn->contactPayment->paying_amount }}</td>
                                        </tr>
                                    </tbody>
                                </table>

                            </div>
                        </div>
                        <div class="col-xl-3 col-6 offset-md-3">
                            <h3 class="text-right">{{ $purchaseReturn->total }} BDT</h3>
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

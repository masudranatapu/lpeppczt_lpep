@extends('layouts.dashboard')

@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="card-box mt-4" style="border-top: 0px;">
                <div class="panel-body">
                    <div class="clearfix">
                        <div class="pull-left">
                            <img src="/{{ currentBranch()->invoice_logo ?? json_decode(currentBranch()->logo) }}"
                                alt="{{ currentBranch()->invoice_name ?? currentBranch()->name }}" class="logo"
                                alt="" height="60px">
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
                                    <strong>{{ $purchase->customer->name }}</strong><br>
                                    <abbr title="Phone">Mobile:</abbr> {{ $purchase->customer->mobile }}<br>
                                    <abbr title="Phone">Email:</abbr> {{ $purchase->customer->email }}
                                </address>
                            </div>
                            <div class="pull-right m-t-30">
                                <p><strong>Purchase Date: </strong> {{ $purchase->purchase_date }}</p>
                            </div>
                        </div><!-- end col -->
                    </div>
                    <!-- end row -->

                    <div class="m-h-50"></div>

                    <div class="row">
                        <div class="col-md-12">

                            @php
                                $purchaseProducts = collect($purchase->purchaseProducts);
                            @endphp

                            @if ($purchaseProducts->count())
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
                                        @foreach ($purchaseProducts as $product)
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
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xl-6 col-6">
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

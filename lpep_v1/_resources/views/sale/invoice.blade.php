@extends('layouts.dashboard')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card-box">
                <div class="panel-body">
                    <div class="clearfix">
                        <div class="pull-left">
                            <img src="/{{ currentBranch()->invoice_logo }}"
                                alt="{{ currentBranch()->invoice_name ?? currentBranch()->name }}" class="logo"
                                alt="" height="60px">
                        </div>
                        <div class="pull-right">
                            <h4>Invoice #{{ date('Y') . $sale->id }} <br>
                                <strong>{{ $sale->sale_date }}</strong>
                            </h4>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-12">

                            <div class="pull-left m-t-30">
                                <address>
                                    <strong>{{ $sale->customer_name ??  $sale->customer->name }}</strong><br>
                                    {{ $sale->customer->zip }},{{ $sale->customer->city }},{{ $sale->customer->country }}<br>
                                    <abbr title="Phone">P:</abbr> (+880) {{ $sale->customer_phone ?? $sale->customer->mobile }}
                                </address>
                            </div>
                            <div class="pull-right m-t-30">
                                <p><strong>Order Date: </strong> {{ $sale->sale_date }}</p>
                                <p class="m-t-10"><strong>Order Status: </strong> <span
                                        class="label label-pink">Delivered</span></p>
                                <p class="m-t-10"><strong>Order ID: </strong> #{{ date('Y') . $sale->id }}</p>
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
                                            <th>Item</th>
                                            <th>Quantity</th>
                                            <th>Unit Cost</th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($sale->saleProducts as $product)
                                            <tr>
                                                <td>{{ $loop->index + 1 }}</td>
                                                <td>{{ $product->product->product_name }}</td>
                                                <td id="qty{{ $loop->index + 1 }}" data-qty="{{ $product->qty }}"
                                                    class='qty'>{{ $product->qty }}
                                                    {{ $product->product->unit->short_name }}</td>
                                                <td>
                                                    <input type='text' value="{{ $product->price }}"
                                                        oninput="price({{ $loop->index + 1 }})"
                                                        id="price{{ $loop->index + 1 }}" class="price"
                                                        style="border: 0px;">
                                                </td>
                                                <td id="totalPriceInvoice{{ $loop->index + 1 }}">
                                                    {{ $product->price * $product->qty }}</td>
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
                                        @foreach (\App\Models\ContactPayment::where('contact_id', $sale->contact_id)->where('sale_id', $sale->id)->get() as $payment)
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
                            <p class="text-right">
                                <b>Sub-total:</b> {{ $sale->sub_total }}
                            </p>

                            <p class="text-right" id="discountinvoice" data-discount='{{ $sale->discount_amount }}'>
                                <b>Discount:</b> {{ $sale->discount_amount }}
                            </p>

                            <p class="text-right" id="vatinvoice" data-vat="{{ $sale->vat }}">
                                <b>VAT:</b> {{ $sale->vat }}
                            </p>

                            <hr>
                            <h3 class="text-right" id="allTotal">{{ $sale->total_amount }} BDT</h3>
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
@section('script')
    <script>
        function price(id) {
            let price = $(`#price${id}`).val()
            let qty = $(`#qty${id}`).data('qty')
            let discountinvoice = $("#discountinvoice").data('discount')
            let vatinvoice = $("#vatinvoice").data('vat')

            let invoicetotalprice = parseInt(price) * parseInt(qty)
            $(`#totalPriceInvoice${id}`).text(invoicetotalprice)
            // console.log(total())
            $("#finalTotalInvoice").val(total())
            $("#allTotal").text((parseInt(total()) - parseInt(discountinvoice)) + parseInt(vatinvoice))
        }

        function total() {
            var sum = 0;
            $('.price').each(function() {
                var price = $(this);
                var q = price.closest('tr').find('.qty').data('qty');
                sum += parseInt(price.val()) * parseInt(q);
            });
            return sum;
        }
    </script>
@endsection

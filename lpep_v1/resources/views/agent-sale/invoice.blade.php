@extends('layouts.dashboard')

@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="card-box">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-12">

                            <div class="pull-left m-t-30">
                                <address>

                                    <strong>
                                        @if ($sale->customer_name || $sale->customer_phone )
                                            {{ $sale->customer_name }}
                                        @else
                                        {{ $sale->customer->name }} <br>
                                        @endif
                                    </strong> <br>

                                    @if ($sale->customer_name || $sale->customer_phone )
                                        <abbr title="Phone">Mobile:</abbr> {{ $sale->customer_phone }}
                                    @else
                                        <abbr title="Phone">Mobile:</abbr> {{ $sale->customer->mobile }}
                                    @endif
                                    <br>

                                    @if ($sale->customer?->email)
                                        <abbr title="Phone">Email:</abbr> {{ $sale->customer->email ?? '' }}
                                    @endif

                                </address>
                            </div>
                            <div class="pull-right m-t-30">
                                <strong>Order Date: </strong> {{ $sale->sale_date }} <br>
                                <strong>Order Status: </strong>
                                <span class="label label-pink">Delivered</span> <br>
                                <strong>Order ID: </strong> #{{ $sale->invoice_no }}
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
                                                class='qty'>{{ $product->qty }} x
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

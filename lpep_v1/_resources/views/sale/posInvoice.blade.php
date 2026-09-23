<!DOCTYPE html>
<html lang="en" dir="ltr">

<section class="content">

    <div class="box">
        <div class="box-body">
            <div class="alert alert-info mb-0">
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12">
            <div class="box box-info">
                <div class="box-body">

                    <div id="invoice" class="row ng-scope" ng-controller="InvoiceViewController">
                        <div class="col-xs-12 col-sm-8 col-md-6 col-sm-offset-2 col-md-offset-3">
                            <style id="styles" type="text/css">
                                /*Common CSS*/
                                .receipt-template {
                                    width: 302px;
                                    margin: 0 auto;
                                }

                                .receipt-template .text-small {
                                    font-size: 10px;
                                }

                                .receipt-template .block {
                                    display: block;
                                }

                                .receipt-template .inline-block {
                                    display: inline-block;
                                }

                                .receipt-template .bold {
                                    font-weight: 700;
                                }

                                .receipt-template .italic {
                                    font-style: italic;
                                }

                                .receipt-template .align-right {
                                    text-align: right;
                                }

                                .receipt-template .align-center {
                                    text-align: center;
                                }

                                .receipt-template .main-title {
                                    font-size: 14px;
                                    font-weight: 700;
                                    text-align: center;
                                    margin: 10px 0 5px 0;
                                    padding: 0;
                                }

                                .receipt-template .heading {
                                    position: relation;
                                }

                                .receipt-template .title {
                                    font-size: 16px;
                                    font-weight: 700;
                                    margin: 10px 0 5px 0;
                                }

                                .receipt-template .sub-title {
                                    font-size: 12px;
                                    font-weight: 700;
                                    margin: 10px 0 5px 0;
                                }

                                .receipt-template table {
                                    width: 100%;
                                }

                                .receipt-template td,
                                .receipt-template th {
                                    font-size: 12px;
                                }

                                .receipt-template .info-area {
                                    font-size: 12px;
                                    line-height: 1.222;
                                }

                                .receipt-template .listing-area {
                                    line-height: 1.222;
                                }

                                .receipt-template .listing-area table {}

                                .receipt-template .listing-area table thead tr {
                                    border-top: 1px solid #000;
                                    border-bottom: 1px solid #000;
                                    font-weight: 700;
                                }

                                .receipt-template .listing-area table tbody tr {
                                    border-top: 1px dashed #000;
                                    border-bottom: 1px dashed #000;
                                }

                                .receipt-template .listing-area table tbody tr:last-child {
                                    border-bottom: none;
                                }

                                .receipt-template .listing-area table td {
                                    vertical-align: top;
                                }

                                .receipt-template .info-area table {}

                                .receipt-template .info-area table thead tr {
                                    border-top: 1px solid #000;
                                    border-bottom: 1px solid #000;
                                }

                                /*Receipt Heading*/
                                .receipt-template .receipt-header {
                                    text-align: center;
                                }

                                .receipt-template .receipt-header .logo-area {
                                    width: 120px;
                                    height: 60px;
                                    margin: 0 auto;
                                }

                                .receipt-template .receipt-header .logo-area img.logo {
                                    display: inline-block;
                                    max-width: 100%;
                                    max-height: 100%;
                                }

                                .receipt-template .receipt-header .address-area {
                                    margin-bottom: 5px;
                                    line-height: 1;
                                }

                                .receipt-template .receipt-header .info {
                                    font-size: 12px;
                                }

                                .receipt-template .receipt-header .store-name {
                                    font-size: 24px;
                                    font-weight: 700;
                                    margin: 0;
                                    padding: 0;
                                }


                                /*Invoice Info Area*/
                                .receipt-template .invoice-info-area {}

                                /*Customer Customer Area*/
                                .receipt-template .customer-area {
                                    margin-top: 10px;
                                }

                                /*Calculation Area*/
                                .receipt-template .calculation-area {
                                    border-top: 2px solid #000;
                                    font-weight: bold;
                                }

                                .receipt-template .calculation-area table td {
                                    text-align: right;
                                }

                                .receipt-template .calculation-area table td:nth-child(2) {
                                    border-bottom: 1px dashed #000;
                                }

                                /*Item Listing*/
                                .receipt-template .item-list table tr {}

                                /*Barcode Area*/
                                .receipt-template .barcode-area {
                                    margin-top: 10px;
                                    text-align: center;
                                }

                                .receipt-template .barcode-area img {
                                    max-width: 100%;
                                    display: inline-block;
                                }

                                /*Footer Area*/
                                .receipt-template .footer-area {
                                    line-height: 1.222;
                                    font-size: 10px;
                                }

                                /*Media Query*/
                                @media print {
                                    .receipt-template {
                                        width: 100%;
                                    }
                                }

                                @media all and (max-width: 215px) {}
                            </style>
                            <section class="receipt-template">

                                <header class="receipt-header">
                                    @if (currentBranch()->invoice_logo)
                                        <div class="logo-area">
                                            <img src="/{{ currentBranch()->invoice_logo }}"
                                                alt="{{ currentBranch()->name }}" class="logo" alt=""
                                                height="60px">
                                        </div>
                                    @endif
                                    <h2 class="store-name" style="font-family:VT323;">
                                        {{ currentBranch()->invoice_name ?? currentBranch()->name }}</h2>
                                    <div class="address-area">
                                        {{-- <span class="info address">Address: {{ currentBranch()->state }}</span>
                                        <br> --}}
                                        <div class="block">
                                            <span class="info phone">Mobile:
                                                {{ currentBranch()->mobile }}</span>,<br>
                                            <span class="info email">Email: {{ currentBranch()->email }}</span>
                                        </div>
                                    </div>
                                </header>

                                <section class="info-area">
                                    <table>
                                        <tbody>
                                            <tr>
                                                <td class="w-30"><span>Invoice ID:</span></td>
                                                <td>{{ date('Y') . $sale->id }}</td>
                                            </tr>
                                            <tr>
                                                <td class="w-30"><span>Date:</span></td>
                                                <td>{{ date('d M Y h:i A', strtotime($sale->created_at)) }}</td>
                                            </tr>
                                            <tr>
                                                <td class="w-30">Customer Name:</td>
                                                <td>{{ $sale->customer_name ?? $sale->customer->name }}</td>
                                            </tr>
                                            <tr>
                                                <td class="w-30">Phone:</td>
                                                <td>{{ $sale->customer_phone ?? $sale->customer->mobile }}</td>
                                            </tr>

                                            @if ($sale->customer->name != 'Guest')
                                                @if ($sale->customer->city)
                                                    <tr>
                                                        <td class="w-30">Address:</td>
                                                        <td>{{ $sale->customer->city }}</td>
                                                    </tr>
                                                @endif
                                            @endif

                                        </tbody>
                                    </table>
                                </section>
                                <h4 class="main-title">INVOICE</h4>

                                <section class="listing-area item-list">
                                    @php
                                        $saleProducts = collect($sale->saleProducts);
                                    @endphp

                                    <table>
                                        <thead>
                                            <tr>
                                                <td class="w-10 text-center">Sl.</td>
                                                <td class="w-40 text-center">Name</td>
                                                <td class="w-15 text-center">Qty</td>
                                                <td class="w-15 text-right">Price</td>
                                                <td class="w-30 text-right" style="text-align: right">Amount</td>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($saleProducts as $sp)
                                                <tr>
                                                    <td>{{ $loop->index + 1 }}</td>
                                                    <td>{{ $sp->product->product_name }}</td>
                                                    <td>{{ $sp->qty }} x
                                                        {{ $sp->product->unit->short_name }}</td>
                                                    <td>{{ $sp->price }}</td>
                                                    <td style="text-align: right">{{ $sp->total_price }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </section>

                                <section class="info-area calculation-area">
                                    <table>
                                        <tbody>
                                            <tr>
                                                <td class="w-70">Total Amt:</td>
                                                <td>{{ $sale->sub_total }}</td>
                                            </tr>
                                            <tr>
                                                <td class="w-70">Order Tax:</td>
                                                <td>{{ $sale->vat }}</td>
                                            </tr>

                                            @if ($business_setting->deliverycharge)
                                                <tr>
                                                    <td class="w-70">Delivery Charge:</td>
                                                    <td>{{ $sale->deliverycharge }}</td>
                                                </tr>
                                            @endif

                                            <tr>
                                                <td class="w-70">Discount:</td>
                                                <td>{{ $sale->discount_amount ? $sale->discount_amount : 0.0 }}</td>
                                            </tr>
                                            <tr>
                                                <td class="w-70">Amount Paid:</td>
                                                <td>{{ $sale->paying_amount }}</td>
                                            </tr>
                                            <tr>
                                                <td class="w-70">Due:</td>
                                                <td>{{ $sale->total_amount - $sale->paying_amount }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </section>


                                <section class="listing-area payment-list">
                                    <div class="heading">
                                        <h2 class="sub-title">Payments</h2>
                                    </div>
                                    <table>
                                        <thead>
                                            <tr>
                                                <td class="w-10 text-center">Sl.</td>
                                                <td class="w-50 text-center">Payment Method</td>
                                                <td class="w-20 text-right">Amount</td>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($contact_payments as $payment)
                                                <tr>
                                                    <td>{{ $loop->index + 1 }}</td>
                                                    <td>{{ $payment->pay_by }}</td>
                                                    <td>{{ $payment->paying_amount }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </section>


                                <section class="info-area barcode-area">
                                    {!! DNS1D::getBarcodeSVG(date('Y') . $sale->id, 'C128', 1.5, 24, 'black', false) !!}
                                </section>

                                <section class="info-area align-center footer-area">
                                    <span class="block bold">Thank you for choosing us!</span>
                                    <span class="block bold">&copy; Powered by LPEP Renewable Energy Bangladesh Ltd.</span>
                                </section>

                            </section>
                            <div class="table-responsive footer-actions">
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</section>

<script>
    window.print();
</script>

</html>

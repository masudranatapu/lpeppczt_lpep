<div class="modal-body">

    <div class="row invoice-info">
        <div class="col-sm-4 invoice-col">
            Business:
            <address>
                <strong>{{ $return->business->name }}</strong><br>
                {{ $return->business->state }},{{ $return->business->city }},{{ $return->business->country }}<br>
                Mobile: {{ $return->business->mobile }}
            </address>
        </div>

        <div class="col-sm-4 invoice-col">
            Customer:
            <address>
                <strong>{{ $return->customer->business_name }}</strong>
                {{ $return->customer->name }}<br>
                Mobile: {{ $return->customer->mobile }}
            </address>
        </div>

        <div class="col-sm-4 invoice-col">
            <b>Invoice No:</b> #{{ $return->invoice_no }}<br>
            <b>Date:</b> {{ $return->return_date }}<br>
        </div>
    </div>
    <hr style="margin-top: -6px;"><br>

    <div class="row">
        <div class="col-sm-12 col-xs-12">
            <div class="table-responsive text-center">
                <table class="table text-center bg-secondary text-white">
                    <thead class="">
                        <tr style=" background: #2dce89;">
                        <th>#</th>
                        <th>Product Name</th>
                        <th class="text-right">Return Quantity</th>
                        <th class="text-right">Unit Price</th>
                        <th class="text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($return->returnProducts as $product)
                            <tr>
                                <td>{{ $loop->index + 1 }}</td>
                                <td>{{ $product->product->product_name }}</td>
                                <td>{{ $product->qty }}</td>
                                <td>{{ $product->price }}</td>
                                <td>{{ $product->total_price }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <br>
    <br>

    <div class="row">
        <div class="col-sm-12 col-xs-12">

        </div>
        <div class="col-md-6 col-sm-12 col-xs-12">

        </div>
        <div class="col-md-6 col-sm-12 col-xs-12">
            <div class="table-responsive">
                <table class="table">
                    <tbody>
                        <tr>
                            <th>Total Amount: </th>
                            <td></td>
                            <td><span class="display_currency pull-right" data-currency_symbol="true">৳
                                    {{ $return->total_amount }}</span></td>
                        </tr>
                        <tr>
                            <th>Total Paying: </th>
                            <td></td>
                            <td><span class="display_currency pull-right" data-currency_symbol="true">৳
                                    {{ $return->paying_amount }}</span></td>
                        </tr>
                        <tr>
                            <th>Total Due: </th>
                            <td></td>
                            <td><span class="display_currency pull-right" data-currency_symbol="true">৳
                                    {{ $return->total_amount - $return->paying_amount }}</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
<div class="modal-footer">
    <button type="button" class="btn btn-danger no-print" data-dismiss="modal">Close</button>
</div>

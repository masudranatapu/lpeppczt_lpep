<div class="modal-body">

    <div class="row invoice-info">

        <div class="col-sm-8 invoice-col">
            <address>
                <b>Customer</b>: {{ $purchase->customer->name }}<br>
                <b>Mobile</b>: {{ $purchase->customer->mobile }}<br>
                <b>Email</b>: {{ $purchase->customer->email }}
            </address>
        </div>

        <div class="col-sm-4 invoice-col">
            <b>Invoice No:</b> #{{ $purchase->invoice_no }}<br>
            <b>Date:</b> {{ $purchase->purchase_date }}<br>
            <b>Created at: </b> {{ $purchase->created_at->format('Y-m-d h:i:s a') }}<br>
        </div>
    </div>
    <hr><br>

    <div class="row">
        <div class="col-sm-12 col-xs-12">
            @php
                $purchaseProducts = collect($purchase->purchaseProducts);
            @endphp

            @if ($purchaseProducts->count())
                <div class="table-responsive text-center">
                    <table class="table text-center bg-secondary text-white">
                        <thead class="">
                            <tr style=" background: #2dce89;">
                            <th style="width: 5px">{{ __('page.purchase.19') }}</th>
                            <th style="width: 20%;">{{ __('page.purchase')[8] }}</th>
                            <th style="width: 10%;">{{ 'Batch Id' }}</th>
                            <th style="width: 15%;">{{ __('page.purchase')[9] }}</th>
                            <th style="width: 15%;">{{ __('page.purchase')[10] }}</th>
                            <th style="width: 15%;">{{ __('page.purchase')[11] }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($purchaseProducts as $product)
                                <tr>
                                    <td>{{ $loop->index + 1 }}</td>
                                    <td>{{ $product->product->product_name }}</td>
                                    <td>{{ $product->batch_id }}</td>
                                    <td>{{ $product->quantity }}</td>
                                    <td>{{ $product->purchase_price }}</td>
                                    <td>{{ $product->total_price }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
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
                                    {{ $purchase->total }}</span></td>
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

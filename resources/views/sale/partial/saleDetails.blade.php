<div class="modal-body">

    <div class="row invoice-info">

        <div class="col-sm-6 invoice-col">
            Customer:
            <address>
                @if ($sale->customer_name || $sale->customer_phone )
                {{ $sale->customer_name }} <br>
                <small>({{ $sale->customer_phone }})</small>
            @else
            <strong>{{ $sale->customer->business_name }}</strong>
            {{ $sale->customer->name }}<br>
            Mobile: {{ $sale->customer->mobile }}
            @endif

            </address>
        </div>

        <div class="col-sm-6 invoice-col">
            <b>Invoice No:</b> #{{ date('Y') . $sale->id }}<br>
            <b>Date:</b> {{ $sale->sale_date }}<br>
            <b>{{ __('page.user_log.0') }}</b>: {{ optional($sale->user)->name }} <br>
            <b>{{ __('page.user_log.1') }}</b>: {{ $sale->created_at->format('Y-m-d H:i:s') }}
        </div>
    </div>
    <hr style="margin-top: 10px;"><br>

    <div class="row">
        <div class="col-sm-12 col-xs-12">
            <div class="table-responsive text-center">
                <table class="table text-center bg-secondary text-white">
                    <thead class="">
                        <tr style=" background: #2dce89;">
                        <th>#</th>
                        <th>Product Name</th>
                        <th>Quantity</th>
                        <th>Unit Price</th>
                        <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($sale->saleProducts as $product)
                            <tr>
                                <td>{{ $loop->index + 1 }}</td>
                                <td>{{ $product->product->product_name . ($product->product_model_id ? ' (' . $product->model->model_no . ')' : '') }}
                                </td>
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
        {{-- <div class="col-md-6 col-sm-12 col-xs-12">

        </div> --}}
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="table-responsive">
                <table class="table">
                    <tbody>
                        <tr>
                            <th>Total Amount: </th>
                            <td></td>
                            <td><span class="display_currency pull-right" data-currency_symbol="true">৳
                                    {{ $sale->total_amount }}</span></td>
                        </tr>
                        <tr>
                            <th>Total Paying: </th>
                            <td></td>
                            <td><span class="display_currency pull-right" data-currency_symbol="true">৳
                                    {{ $sale->paying_amount }}</span></td>
                        </tr>
                        <tr>
                            <th>Total Due: </th>
                            <td></td>
                            <td><span class="display_currency pull-right" data-currency_symbol="true">৳
                                    {{ $sale->total_amount - $sale->paying_amount }}</span></td>
                        </tr>

                        <tr>
                            <th>Pay Amount: </th>
                            <td></td>
                            <td>
                                <span class="display_currency pull-right" data-currency_symbol="true">
                                     <input type="text" class="form-control" id="dueAmount" value="{{ $sale->total_amount - $sale->paying_amount }}" placeholder="current due amount">

                                     <button class="btn btn-sm btn-danger" id="updateDueAmountBtn" type="submit">Update</button>
                                </span>
                            </td>
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
<script>
    $('#updateDueAmountBtn').on('click', function() {
        var newDueAmount = $('#dueAmount').val();
        // Perform AJAX request to update the due amount in the backend
        $.ajax({
            url: '/update-due-amount', // Replace with your actual endpoint
            method: 'POST',
            data: {
                sale_id: {{ $sale->id }},
                due_amount: newDueAmount,
                _token: '{{ csrf_token() }}' // Include CSRF token for security
            },
            success: function(response) {
                alert('Due amount updated successfully!');
                window.location.reload();
            },
            error: function(xhr) {
                alert('An error occurred while updating the due amount.');
            }
        });
    });
</script>
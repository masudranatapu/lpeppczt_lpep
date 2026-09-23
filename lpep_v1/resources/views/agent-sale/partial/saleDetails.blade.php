<div class="modal-body">
    <div class="row invoice-info">

        <div class="col-sm-6 invoice-col">
            Customer:
            <address>
                @if ($sale?->customer_name || $sale?->customer_phone )
                {{ $sale?->customer_name }} <br>
                <small>({{ $sale->customer_phone }})</small>
            @else
            <strong>{{ $sale?->customer?->business_name }}</strong>
            {{ $sale?->customer?->name }}<br>
            Mobile: {{ $sale?->customer?->mobile }}
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
                                <td>{{ $product?->product?->product_name . ($product?->product_model_id ? ' (' . $product?->model?->model_no . ')' : '') }}
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
    <div class="card shadow-sm border-0">
        <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Payment Summary</h5>
        
        </div>

        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table table-borderless align-middle mb-0">
                    <tbody>
                        <tr>
                            <th>Total Amount</th>
                            <td class="text-end fw-semibold text-dark">
                                ৳ {{ number_format($sale->total_amount, 2) }}
                            </td>
                        </tr>
                        <tr>
                            <th>Total Paying</th>
                            <td class="text-end fw-semibold text-success">
                                ৳ {{ number_format($sale->paying_amount, 2) }}
                            </td>
                        </tr>
                        <tr>
                            <th>Total Due</th>
                            <td class="text-end fw-semibold text-danger">
                                ৳ {{ number_format($sale->total_amount - $sale->paying_amount, 2) }}
                            </td>
                        </tr>

                        @if ($sale->total_amount - $sale->paying_amount > 0)
                            <tr class="border-top">
                                <th colspan="2" class="pt-3">
                                    <form id="updateDueForm" class="d-flex flex-column flex-md-row gap-2 align-items-center justify-content-between mt-3">
                                        <div class="flex-grow-1">
                                            <label for="dueAmount" class="form-label mb-1 fw-bold">Pay Amount</label>
                                            <input type="number" step="0.01" class="form-control" id="dueAmount"
                                                value="{{ $sale->total_amount - $sale->paying_amount }}"
                                                placeholder="Enter payment amount">
                                        </div>

                                        <div>
                                            <label for="dueDate" class="form-label mb-1 fw-bold">Due Date</label>
                                            <input type="date" class="form-control" name="due_date" id="dueDate">
                                        </div>

                                        <input type="hidden" name="sale_id" id="sale_id" value="{{ $sale->id }}">

                                        <div class="text-end mt-3 mt-md-0">
                                            <button class="btn btn-success px-4" id="updateDueAmountBtn" type="submit">
                                                <i class="bi bi-check-circle"></i> Update
                                            </button>
                                        </div>
                                    </form>
                                </th>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
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
        var dueDate = $('#dueDate').val();
        var saleId = $('#sale_id').val();
        // Perform AJAX request to update the due amount in the backend
        $.ajax({
            url: '{{ route("sale.customer.due.clear") }}', // Replace with your actual endpoint
            method: 'POST',
            data: {
                sale_id: {{ $sale->id }},
                due_amount: newDueAmount,
                due_date: dueDate,
                _token: '{{ csrf_token() }}' // Include CSRF token for security
            },
            success: function(response) {
                alert('Due amount updated successfully!');
                // Optionally, you can refresh the modal or update the UI accordingly
                window.location.reload();
            },
            error: function(xhr) {
                alert('An error occurred while updating the due amount.');
            }
        });
    });
</script>

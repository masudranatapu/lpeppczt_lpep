<div class="modal-body">
    <div class="row invoice-info">
        <div class="col-sm-4 invoice-col">
            <b>Agent: </b>
            {{ $transfer->agent->name }}<br>
        </div>

        <div class="col-sm-4 invoice-col">
            <b>Date: </b> {{ $transfer->date }}<br>
        </div>
    </div>

    <hr><br>

    <div class="row">
        <div class="col-sm-12 col-xs-12">
            <div class="table-responsive text-center">
                <table class="table text-center bg-secondary text-white">
                    <thead class="">
                        <tr style=" background: #2dce89;">
                        <th>#</th>
                        <th>Batch</th>
                        <th>Product Name</th>
                        <th>Quantity</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($transfer->details as $detail)
                            <tr>
                                <td>{{ $loop->index + 1 }}</td>
                                <td>{{ $detail->agentPurchaseProduct->batch_id }}</td>
                                <td>{{ $detail->product->product_name }}
                                </td>
                                <td>{{ $detail->quantity }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-danger no-print" data-dismiss="modal">Close</button>
    </div>

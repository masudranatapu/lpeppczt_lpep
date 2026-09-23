@php
$customer_due_amount = round($contact->total_sale - ($contact->total_normal_paid_amount + $contact->total_sale_paid_amount), 2);
$business_due_amount = round($contact->total_sale_return - $contact->total_sale_return_paid_amount, 2);
@endphp
<div class="text-center" style="margin-top: -16px">
    <h4 style="font-size: 29px">Customer Payment</h4>
    <hr>
</div>

<div style="margin-top: -5px">
    <form method="POST" action="{{ route('contact.payment.store') }}">
        @csrf
        <input type="hidden" name="id" value="{{ $contact->id }}">
        <div class="row">
            <div class="col-md-6">
                <div class="well">
                    <strong>Name: </strong>{{ $contact->name }}<br>
                    <strong>Mobile: </strong>{{ $contact->mobile }}<br>
                    <strong>Address: </strong>{{ $contact->address }}<br>
                </div>
            </div>
            <div class="col-md-6">
                <div class="well">
                    <strong>Total Sale: </strong>{{ $contact->total_sale }} BDT</span><br>
                    <strong>Total Paid: </strong> {{ $contact->total_sale_paid_amount }} BDT</span><br>
                    <strong>Customer Due: </strong> {{ $customer_due_amount }} BDT</span><br>
                    <strong>Business Due: </strong> {{ $business_due_amount }} BDT</span><br>
                    <strong>Receiveable Amount: </strong> {{ $customer_due_amount - $business_due_amount }}
                    BDT</span><br>
                </div>
            </div>
        </div>

        <div class="mt-2">
            <div class="form-group">
                <label for="">Amount</label>
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-money"></i>
                    </span>
                    <input class="form-control input_number valid" required="" placeholder="Amount" name="paying_amount"
                        type="text" value="0.00" id="paying_amount" aria-required="true" aria-invalid="false">
                </div>
            </div>

            <div class="form-group">
                <label for="">Paying Date</label>
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                    </span>
                    <input class="form-control input_number valid" required="" name="paying_date" type="date"
                        value="<?php echo date('Y-m-d'); ?>" id="paying_date" aria-required="true" aria-invalid="false">
                </div>
            </div>

            <div class="form-group">
                <label for="">Paying By</label>
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-money"></i>
                    </span>
                    <select class="form-control" required name="pay_by" id="pay_by">
                        <option value="Cash" selected>Cash</option>
                        <option value="Mobile Banking">Mobile Banking</option>
                        <option value="Card">Card</option>
                        <option value="Bank Account">Bank Account</option>
                    </select>
                </div>

                <div id="account_list">

                </div>
            </div>
        </div>
        @php
            $is_receive_amount = $customer_due_amount - $business_due_amount > 0;
        @endphp
        @if ($is_receive_amount)
            <input type="hidden" name="money_sign" value="1">
        @else
            <input type="hidden" name="money_sign" value="-1">
        @endif
        <div class="text-right mt-4">
            <button class="btn btn-info" type="submit" id="">
                @if ($is_receive_amount)
                    Receive
                @else
                    Pay
                @endif
            </button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>

    </form>
</div>

<h5>Supplier Payment</h5>
<hr>
<div>

    <form method="POST" action="{{ route('contact.payment.store') }}">
        @csrf

        <input type="hidden" name="id" value="{{ $supplier->id }}">

        <div class="row">
            <div class="col-md-6">
                <div class="well">
                    <strong>Supplier: </strong>{{ $supplier->name }}<br>
                    <strong>Business: </strong> {{ $supplier->supplier_business_name }} <br><br>
                </div>
            </div>
            <div class="col-md-6">
                <div class="well">
                    <strong>Total Purchase: </strong><span class="display_currency" data-currency_symbol="true">
                            @if ($supplier->type == "supplier")
                                {{ $supplier->purchase->sum('total') }}
                            @else
                                {{ $supplier->sale->sum('total_amount') }}
                            @endif
                        BDT</span><br>
                    <strong>Total Paid: </strong><span class="display_currency" data-currency_symbol="true">
                            @if ($supplier->type == "supplier")
                                {{ $supplier->contactPayment->sum('paying_amount') }}
                            @else
                                {{ $supplier->contactPayment->sum('paying_amount') }}
                            @endif


                        BDT</span><br>
                    <strong>Total Purchase Due: </strong><span class="display_currency" data-currency_symbol="true">
                            @if ($supplier->type == "supplier")
                                {{ $supplier->purchase->sum('total') - $supplier->contactPayment->sum('paying_amount') }}</span><br>
                            @else
                                {{ $supplier->sale->sum('total_amount') - $supplier->contactPayment->sum('paying_amount') }}
                            @endif
                </div>
            </div>
        </div>

        <div class="mt-5">

            <div class="form-group">
                <label for="">Amount</label>
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-money"></i>
                    </span>
                    <input class="form-control input_number valid" required="" placeholder="Amount" name="paying_amount" type="text" value="0.00" id="paying_amount" aria-required="true" aria-invalid="false">
                </div>
            </div>

            <div class="form-group">
                <label for="">Paying Date</label>
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                    </span>
                    <input class="form-control input_number valid" required="" name="paying_date" type="date" value="<?php echo date('Y-m-d');?>" id="paying_date" aria-required="true" aria-invalid="false">
                </div>
            </div>

            <div class="form-group">
                <label for="">Paying By</label>
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-money"></i>
                    </span>
                    <select class="form-control" required name="pay_by">
                        <option value="">select</option>
                        <option value="bKash">bKash</option>
                        <option value="bKash">Cash</option>
                        <option value="Rocket">Rocket</option>
                        <option value="Nagad">Nagad</option>
                        <option value="Card">Card</option>
                        <option value="Bank">Bank</option>
                    </select>
                </div>
            </div>



        </div>

        <div class="text-right mt-4">
            <button class="btn btn-info" type="submit" id="">Pay</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>

    </form>



</div>
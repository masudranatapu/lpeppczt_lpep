<h5>Add New Income </h5>
<hr>
<div>

    <form>
        <div class="form-group">
            <label for="">Date</label>
            <input type="date" name="income_date" value="<?php echo date('Y-m-d'); ?>" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="">Income Type</label>
            <select name="income_type_id" class="form-control" id="" required>
                <option value="">select</option>
                @foreach ($income_types as $incmtype)
                    <option value="{{ $incmtype->id }}">{{ $incmtype->income_type }}</option>
                @endforeach
            </select>
        </div>

        <div class="">
            <div class=" form-group">
                <div>
                    <label for="" class="mt-2">Payment Type</label>
                </div>
                <div>
                    <select name="payment_type" id="pay_by" class="form-control" required="">
                        <option value="Cash">Cash</option>
                        <option value="Mobile Banking">Mobile Banking</option>
                        <option value="Card">Card</option>
                        <option value="Bank Account">Bank Account</option>
                    </select>
                </div>
            </div>
        </div>

        <div id="account_info" class="form-group">

        </div>

        <div class="form-group">
            <label for="">Amount</label>
            <input type="number" name="amount" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="">Note</label>
            <textarea name="note" rows="3" class="form-control"></textarea>
        </div>

        <div class="text-right">
            <button class="btn btn-success" type="submit" id="submit">Save</button>
            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
        </div>

    </form>

</div>

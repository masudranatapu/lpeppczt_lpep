<div class="from-group row">
    <div class="col-md-6">
        <label for="">Card Type</label>
        <select name="card_type" id="" class="form-control" required>
            <option value="">select</option>
            <option value="Master Card">Master Card</option>
            <option value="Visa Card">Visa Card</option>
            <option value="Credit Card">Credit Card</option>
            <option value="Debit Card">Debit Card</option>
            <option value="Prepid Card">Prepid Card</option>
        </select>
    </div>
    <div class="col-md-6">
        <label for="">Bank Name</label>
        <select name="bank_list_id" id="bank_list_id" class="form-control select3" required>
            <option value="">Select</option>
            @foreach ($bank_lists as $bank)
                <option value="{{ $bank->id }}">{{ $bank->bank_name }}</option>
            @endforeach
        </select>
    </div>
</div>


<div class="from-group row mt-3">
    <div class="col-md-6">
        <label for="">Card Holder Name</label>
        <input type="text" name="card_holder_name" class="form-control" placeholder="Card Holder Name" required>
    </div>
    <div class="col-md-6">
        <label for="">Card Number</label>
        <input type="text" name="card_number" class="form-control" placeholder="Card Number" required>
    </div>
</div>


<div class="form-group row mt-3">
    <div class="col-md-6">
        <label for="">Valid Thru Month</label>
        <select name="valid_thru_month" id="" class="form-control" required>
            <option value="" selected>Select</option>
            <option value="1">January</option>
            <option value="2">February</option> 
            <option value="3">March</option> 
            <option value="4">April</option> 
            <option value="5">May</option> 
            <option value="6">June</option> 
            <option value="7">July</option> 
            <option value="8">August</option> 
            <option value="9">September</option> 
            <option value="10">October</option> 
            <option value="11">November</option> 
            <option value="12">December</option>
        </select>
    </div>
    <div class="col-md-6">
        <label for="">Valid Thru Year</label>
        <input type="number" name="valid_thru_year" class="form-control" minlength="2021" required>
    </div>
</div>

<div class="from-group row mt-3">
    <div class="col-md-12">
        <label for="">CVV Code</label>
        <input type="text" name="cvv_code" class="form-control" required>
    </div>
    
</div>

<div class="text-right mt-3">
    <button class="btn btn-success" type="submit">Save</button>
</div>
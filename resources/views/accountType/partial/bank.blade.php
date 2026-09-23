<div class="form-group row">
    <div class="col-md-6">
        <label for="">Bank Name</label>
        <select name="bank_list_id" id="bank_list_id" class="form-control select3" required>
            <option value="">Select</option>
            @foreach ($bank_lists as $bank)
                <option value="{{ $bank->id }}">{{ $bank->bank_name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-6">
        <label for="">Bank Account Type</label>
        <input type="text" name="bank_account_type" class="form-control" placeholder="Bank Account Type" required>
    </div>
    
</div>


<div class="from-group row mt-3">
    <div class="col-md-6">
        <label for="">Bank Account Name</label>
        <input type="text" name="bank_account_name" class="form-control" placeholder="Bank Account Name" required>
    </div>
    <div class="col-md-6">
        <label for="">Bank Account Number</label>
        <input type="text" name="bank_account_number" class="form-control" placeholder="Bank Account Number" required>
    </div>
</div>


<div class="from-group row mt-3">
    <div class="col-md-12">
        <label for="">Bank Account Branch</label>
        <input type="text" name="bank_account_branch" class="form-control" placeholder="Bank Account Branch" required>
    </div>
</div>

<div class="text-right mt-3">
    <button class="btn btn-success" type="submit">Save</button>
</div>
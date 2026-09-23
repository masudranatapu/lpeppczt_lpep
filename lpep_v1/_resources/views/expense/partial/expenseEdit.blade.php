<h5>Update Expense </h5>
<hr>
<div>

    <form>
        <input type="hidden" name="id" value="{{ $expense->id }}">
        <div class="form-group">
            <label for="">Date</label>
            <input type="date" name="expanse_date" value="{{ $expense->expanse_date }}" class="form-control"
                required>
        </div>

        <div class="form-group">
            <label for="">Expense Type</label>
            <select name="expense_type_id" class="form-control" id="" required>
                <option value="">select</option>
                @foreach ($expense_types as $expense_type)
                    <option value="{{ $expense_type->id }}"
                        {{ $expense_type->id == $expense->expense_type_id ? 'selected' : '' }}>
                        {{ $expense_type->expense_type }}</option>
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
                    <option value="Cash" {{ $expense->pay_by == 'Cash' ? 'selected' : '' }}>Cash</option>
                    <option value="Mobile Banking" {{ $expense->pay_by == 'Mobile Banking' ? 'selected' : '' }}>
                        Mobile Banking</option>
                    <option value="Card" {{ $expense->pay_by == 'Card' ? 'selected' : '' }}>Card</option>
                    <option value="Bank Account" {{ $expense->pay_by == 'Bank Account' ? 'selected' : '' }}>Bank
                        Account</option>
                </select>
            </div>
        </div>
</div>

<div id="account_info" class="form-group">
    <div class="mt-4">
        <div class="form-group">
            <div class="">
                        @if ($expense->pay_by == 'Mobile Banking')
                            <select name="
                account_id" id="" class="form-control select3" required="">
                @foreach ($data as $account)
                    <option value="{{ $account->id }}"
                        {{ $account->id == $expense->account_id ? 'selected' : '' }}>
                        {{ $account->mobile_number }} ({{ $account->mobile_bank_name }})</option>
                @endforeach
                </select>
            @elseif($expense->pay_by == "Card")
                <select name="account_id" id="" class="form-control select3" required="">
                    @foreach ($data as $account)
                        <option value="{{ $account->id }}"
                            {{ $account->id == $expense->account_id ? 'selected' : '' }}>
                            {{ $account->card_number }}
                        </option>
                    @endforeach
                </select>
            @elseif($expense->pay_by == "Bank Account")
                <select name="account_id" id="" class="form-control select3" required="">
                    @foreach ($data as $account)
                        <option value="{{ $account->id }}"
                            {{ $account->id == $expense->account_id ? 'selected' : '' }}>
                            {{ $account->bank_account_number }} ({{ $account->bank->bank_name }})</option>
                    @endforeach
                </select>
                @endif
            </div>
        </div>
    </div>

</div>

<div class="form-group">
    <label for="">Amount</label>
    <input type="number" name="amount" value="{{ $expense->amount }}" class="form-control" required>
</div>

<div class="form-group">
    <label for="">Note</label>
    <textarea name="note" rows="3" class="form-control">{{ $expense->note }}</textarea>
</div>

<div class="text-right">
    <button class="btn btn-success" type="submit" id="submitUpdate">Save</button>
    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
</div>

</form>

</div>

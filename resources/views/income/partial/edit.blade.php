<h5>Update Income </h5>
<hr>
<div>

    <form>
        <input type="hidden" name="id" value="{{ $income->id }}">
        <div class="form-group">
            <label for="">Date</label>
            <input type="date" name="income_date" value="{{ $income->income_date }}" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="">Income Type</label>
            <select name="income_type_id" class="form-control" id="" required>
                <option value="">select</option>
                @foreach ($income_types as $inctype)
                    <option value="{{ $inctype->id }}" {{ $inctype->id == $income->income_type_id ? 'selected' : '' }}>
                        {{ $inctype->income_type }}</option>
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
                        <option value="Cash" {{ $income->pay_by == 'Cash' ? 'selected' : '' }}>Cash</option>
                        <option value="Mobile Banking" {{ $income->pay_by == 'Mobile Banking' ? 'selected' : '' }}>
                            Mobile Banking</option>
                        <option value="Card" {{ $income->pay_by == 'Card' ? 'selected' : '' }}>Card</option>
                        <option value="Bank Account" {{ $income->pay_by == 'Bank Account' ? 'selected' : '' }}>Bank
                            Account</option>
                    </select>
                </div>
            </div>
        </div>

        <div id="account_info" class="form-group">
            <div class="mt-4">
                <div class="form-group">
                    <div class="">
                        @if ($income->pay_by == 'Mobile Banking')
                            <select name="
                account_id" id="" class="form-control select3"
                                required="">
                                @foreach ($data as $account)
                                    <option value="{{ $account->id }}"
                                        {{ $account->id == $income->account_id ? 'selected' : '' }}>
                                        {{ $account->mobile_number }} ({{ $account->mobile_bank_name }})</option>
                                @endforeach
                            </select>
                        @elseif($income->pay_by == 'Card')
                            <select name="account_id" id="" class="form-control select3" required="">
                                @foreach ($data as $account)
                                    <option value="{{ $account->id }}"
                                        {{ $account->id == $income->account_id ? 'selected' : '' }}>
                                        {{ $account->card_number }}
                                    </option>
                                @endforeach
                            </select>
                        @elseif($income->pay_by == 'Bank Account')
                            <select name="account_id" id="" class="form-control select3" required="">
                                @foreach ($data as $account)
                                    <option value="{{ $account->id }}"
                                        {{ $account->id == $income->account_id ? 'selected' : '' }}>
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
            <input type="number" name="amount" value="{{ $income->amount }}" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="">Note</label>
            <textarea name="note" rows="3" class="form-control">{{ $income->note }}</textarea>
        </div>

        <div class="text-right">
            <button class="btn btn-success" type="submit" id="submitUpdate">Save</button>
            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
        </div>

    </form>

</div>

<div class="mt-4">
    <div class="form-group row">
        <div class="col-md-4">
        </div>
        <div class="col-md-8">
            @if ($account_type == "Mobile Banking")
                <select name="account_id" id="" class="form-control select3" required="">
                    @foreach ($data as $account)
                        <option value="{{ $account->id }}">{{ $account->mobile_number }} ({{ $account->mobile_bank_name }})</option>
                    @endforeach
                </select>
            @elseif($account_type == "Card")
                <select name="account_id" id="" class="form-control select3" required="">
                    @foreach ($data as $account)
                        <option value="{{ $account->id }}">{{ $account->card_number }}</option>
                    @endforeach
                </select>
            @elseif($account_type == "Bank Account")
                <select name="account_id" id="" class="form-control select3" required="" >
                    @foreach ($data as $account)
                        <option value="{{ $account->id }}">{{ $account->bank_account_number }} ({{ $account->bank->bank_name }})</option>
                    @endforeach
                </select>
            @elseif($account_type == "Cash")
                <select name="account_id" id="" class="form-control" required="" style="" >
                    @foreach ($data as $account)
                        <option value="{{ $account->id }}">{{ $account->account_type}}</option>
                    @endforeach
                </select>
            @endif
        </div>
    </div>
</div>

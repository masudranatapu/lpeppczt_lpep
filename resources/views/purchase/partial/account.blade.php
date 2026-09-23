<th class="col-md-7 text-right">  </th>
<th class="col-md-5 text-left">
    @if ($account_type == "Mobile Banking")
        <select name="account_id" id="" class="form-control select3" required="" style="padding-right:6px;height:34px;margin-top: 6px;border-radius: 3px;width: 185px;text-align: right; border: 1px solid #bbbbbb;">
            @foreach ($data as $account)
                <option value="{{ $account->id }}">{{ $account->mobile_number }} ({{ $account->mobile_bank_name }})</option>
            @endforeach
        </select>
    @elseif($account_type == "Card") 
        <select name="account_id" id="" class="form-control select3" required="" style="padding-right:6px;height:34px;margin-top: 6px;border-radius: 3px;width: 185px;text-align: right; border: 1px solid #bbbbbb;">
            @foreach ($data as $account)
                <option value="{{ $account->id }}">{{ $account->card_number }}</option>
            @endforeach
        </select>
    @elseif($account_type == "Bank Account") 
        <select name="account_id" id="" class="form-control select3" required="" style="padding-right:6px;height:34px;margin-top: 6px;border-radius: 3px;width: 185px;text-align: right; border: 1px solid #bbbbbb;">
            @foreach ($data as $account)
                <option value="{{ $account->id }}">{{ $account->bank_account_number }} ({{ $account->bank->bank_name }})</option>
            @endforeach
        </select>
    @elseif($account_type == "Cash")
        <select name="account_id" id="" class="form-control" required="" style="padding-right:6px;height:34px;margin-top: 6px;border-radius: 3px;width: 185px;text-align: right; border: 1px solid #bbbbbb;display: none;" >
            @foreach ($data as $account)
                <option value="{{ $account->id }}">{{ $account->id }}</option>
            @endforeach
        </select>
    @endif
    
</th>
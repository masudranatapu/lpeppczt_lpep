{{-- <label for="">{{ $account_info }}</label> --}}
@if ($account_type == "Mobile Banking")
    <select name="account_id" id="" class="form-control select3" required="">
        @foreach ($data as $account)
            <option value="{{ $account->id }}">{{ $account->mobile_number }} ({{ $account->mobile_bank_name }})</option>
        @endforeach
    </select>
    <input type="text" name="balance" value="0" readonly class="form-control mt-3">
@elseif($account_type == "Card")  
    <select name="account_id" id="" class="form-control select3" required="" >
        @foreach ($data as $account)
            <option value="{{ $account->id }}">{{ $account->card_number }}</option>
        @endforeach
    </select>
    <input type="text" name="balance" value="0" readonly class="form-control mt-3">
@elseif($account_type == "Bank Account") 
    <select name="account_id" id="" class="form-control select3" required="" >
        @foreach ($data as $account)
            <option value="{{ $account->id }}">{{ $account->bank_account_number }} ({{ $account->bank->bank_name }})</option>
        @endforeach
    </select>
    <input type="text" name="balance" value="0" readonly class="form-control mt-3">
@endif


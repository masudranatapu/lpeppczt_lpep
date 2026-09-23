{{-- <label for=""></label> --}}
@if ($account_type == "Mobile Banking")
    <select name="account_id" id="account_id" class="form-control select3" required="">
        <option value="">select</option>
        @foreach ($data as $account)
            <option value="{{ $account->id }}">{{ $account->mobile_number }} ({{ $account->mobile_bank_name }})</option>
        @endforeach
    </select>
    <div id="currentBalance"></div>
    {{-- <input type="text" name="balance" value="0" readonly class="form-control mt-3"> --}}
@elseif($account_type == "Card")  
    <select name="account_id" id="account_id" class="form-control select3" required="" >
        <option value="">select</option>
        @foreach ($data as $account)
            <option value="{{ $account->id }}">{{ $account->card_number }}</option>
        @endforeach
    </select>
    <div id="currentBalance"></div>
    {{-- <input type="text" name="balance" value="0" readonly class="form-control mt-3"> --}}
@elseif($account_type == "Bank Account") 
    <select name="account_id" id="account_id" class="form-control select3" required="" >
        <option value="">select</option>
        @foreach ($data as $account)
            <option value="{{ $account->id }}">{{ $account->bank_account_number }} ({{ $account->bank->bank_name }})</option>
        @endforeach
    </select> 
    <div id="currentBalance"></div>
{{-- @elseif($account_type == "Cash")
    <input type="text" name="balance" value="{{ $balance }}" readonly class="form-control mt-3"> --}}

@endif


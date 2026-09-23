@if ($type)
    <div class="form-group mt-3">
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
        @endif
    </div>
@else
    <td colspan="3" style="width: 65%"></td>
    <td colspan="" style="width: 20%"></td>
    <td colspan="" style="width: 15%">
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
        @endif
    </td>
@endif
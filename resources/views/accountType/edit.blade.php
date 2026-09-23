@extends('layouts.dashboard')
@section('content')

    <div class="row">

        <div class="col-md-10 m-auto">
            <div class="card-box mt-4">
                <h4 class="header-title m-t-0 m-b-30 mb-4">Edit Account</h4>

                <form method="POST" action="{{ route('account.type.update') }}"
                    class="">
                @csrf
                <input type=" hidden" value="{{ $account->id }}"
                    name="id">
                    @php
                        $business = \App\Models\Business::findOrFail(1);
                    @endphp
                    <div class="form-group row">
                        <div class="col-md-6">
                            <label for="">Account Type</label>
                            <select name="account_type" id="balance_type" class="form-control" required>
                                <option value="">Select</option>
                                <option value="Mobile Banking"
                                    {{ $account->account_type == 'Mobile Banking' ? 'selected' : '' }}>Mobile Banking
                                </option>
                                <option value="Card" {{ $account->account_type == 'Card' ? 'selected' : '' }}>Card
                                </option>
                                <option value="Bank Account"
                                    {{ $account->account_type == 'Bank Account' ? 'selected' : '' }}>Bank Account</option>
                            </select>
                        </div>
                    </div>

                    <div id="account_info">

                        @if ($account->account_type == 'Mobile Banking')
                            <div class="form-group row">
                                <div class="col-md-6">
                                    <label for="">Mobile Bank Name</label>
                                    <select name="mobile_bank_name" id="mobile_bank_name" class="form-control" required>
                                        <option value="">Select</option>
                                        <option value="bKash"
                                            {{ $account->mobile_bank_name == 'bKash' ? 'selected' : '' }}>bKash</option>
                                        <option value="Rocket"
                                            {{ $account->mobile_bank_name == 'Rocket' ? 'selected' : '' }}>Rocket</option>
                                        <option value="Nagad"
                                            {{ $account->mobile_bank_name == 'Nagad' ? 'selected' : '' }}>Nagad</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="">Mobile Number</label>
                                    <input type="text" name="mobile_number" value="{{ $account->mobile_number }}"
                                        class="form-control" placeholder="Mobile Number" required>
                                </div>
                            </div>

                            <div class="text-right">
                                <button class="btn btn-success" type="submit">Save</button>
                            </div>

                        @elseif($account->account_type == "Card")
                            <div class="from-group row">
                                <div class="col-md-6">
                                    <label for="">Card Type</label>
                                    <select name="card_type" id="" class="form-control" required>
                                        <option value="">select</option>
                                        <option value="Master Card"
                                            {{ $account->card_type == 'Master Card' ? 'selected' : '' }}>Master Card
                                        </option>
                                        <option value="Visa Card"
                                            {{ $account->card_type == 'Visa Card' ? 'selected' : '' }}>Visa Card</option>
                                        <option value="Credit Card"
                                            {{ $account->card_type == 'Credit Card' ? 'selected' : '' }}>Credit Card
                                        </option>
                                        <option value="Debit Card"
                                            {{ $account->card_type == 'Debit Card' ? 'selected' : '' }}>Debit Card
                                        </option>
                                        <option value="Prepid Card"
                                            {{ $account->card_type == 'Prepid Card' ? 'selected' : '' }}>Prepid Card
                                        </option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="">Bank Name</label>
                                    <select name="bank_list_id" id="bank_list_id" class="form-control select2" required>
                                        <option value="">Select</option>
                                        @foreach ($bank_lists as $bank)
                                            <option value="{{ $bank->id }}"
                                                {{ $bank->id == $account->bank_list_id ? 'selected' : '' }}>
                                                {{ $bank->bank_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>


                            <div class="from-group row mt-3">
                                <div class="col-md-6">
                                    <label for="">Card Holder Name</label>
                                    <input type="text" name="card_holder_name" value="{{ $account->card_holder_name }}"
                                        class="form-control" placeholder="Card Holder Name" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="">Card Number</label>
                                    <input type="text" name="card_number" value="{{ $account->card_number }}"
                                        class="form-control" placeholder="Card Number" required>
                                </div>
                            </div>


                            <div class="form-group row mt-3">
                                <div class="col-md-6">
                                    <label for="">Valid Thru Month</label>
                                    <select name="valid_thru_month" id="" class="form-control" required>
                                        <option value="" selected>Select</option>
                                        <option value="1" {{ $account->valid_thru_month == 1 ? 'selected' : '' }}>January
                                        </option>
                                        <option value="2" {{ $account->valid_thru_month == 2 ? 'selected' : '' }}>
                                            February</option>
                                        <option value="3" {{ $account->valid_thru_month == 3 ? 'selected' : '' }}>March
                                        </option>
                                        <option value="4" {{ $account->valid_thru_month == 4 ? 'selected' : '' }}>April
                                        </option>
                                        <option value="5" {{ $account->valid_thru_month == 5 ? 'selected' : '' }}>May
                                        </option>
                                        <option value="6" {{ $account->valid_thru_month == 6 ? 'selected' : '' }}>June
                                        </option>
                                        <option value="7" {{ $account->valid_thru_month == 7 ? 'selected' : '' }}>July
                                        </option>
                                        <option value="8" {{ $account->valid_thru_month == 8 ? 'selected' : '' }}>August
                                        </option>
                                        <option value="9" {{ $account->valid_thru_month == 9 ? 'selected' : '' }}>
                                            September</option>
                                        <option value="10" {{ $account->valid_thru_month == 10 ? 'selected' : '' }}>
                                            October</option>
                                        <option value="11" {{ $account->valid_thru_month == 11 ? 'selected' : '' }}>
                                            November</option>
                                        <option value="12" {{ $account->valid_thru_month == 12 ? 'selected' : '' }}>
                                            December</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="">Valid Thru Year</label>
                                    <input type="number" name="valid_thru_year" value="{{ $account->valid_thru_year }}"
                                        class="form-control" minlength="2021" required>
                                </div>
                            </div>

                            <div class="from-group row mt-3">
                                <div class="col-md-12">
                                    <label for="">CVV Code</label>
                                    <input type="text" name="cvv_code" value="{{ $account->cvv_code }}"
                                        class="form-control" required>
                                </div>

                            </div>

                            <div class="text-right mt-3">
                                <button class="btn btn-success" type="submit">Save</button>
                            </div>

                        @elseif($account->account_type == "Bank Account")

                            <div class="form-group row">
                                <div class="col-md-6">
                                    <label for="">Bank Name</label>
                                    <select name="bank_list_id" id="bank_list_id" class="form-control select2" required>
                                        <option value="">Select</option>
                                        @foreach ($bank_lists as $bank)
                                            <option value="{{ $bank->id }}"
                                                {{ $bank->id == $account->bank_list_id ? 'selected' : '' }}>
                                                {{ $bank->bank_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="">Bank Account Type</label>
                                    <input type="text" name="bank_account_type" value="{{ $account->bank_account_type }}"
                                        class="form-control" placeholder="Bank Account Type" required>
                                </div>

                            </div>


                            <div class="from-group row mt-3">
                                <div class="col-md-6">
                                    <label for="">Bank Account Name</label>
                                    <input type="text" name="bank_account_name" value="{{ $account->bank_account_name }}"
                                        class="form-control" placeholder="Bank Account Name" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="">Bank Account Number</label>
                                    <input type="text" name="bank_account_number"
                                        value="{{ $account->bank_account_number }}" class="form-control"
                                        placeholder="Bank Account Number" required>
                                </div>
                            </div>

                            <div class="from-group row mt-3">
                                <div class="col-md-12">
                                    <label for="">Bank Account Branch</label>
                                    <input type="text" name="bank_account_branch"
                                        value="{{ $account->bank_account_branch }}" class="form-control"
                                        placeholder="Bank Account Branch" required>
                                </div>
                            </div>

                            <div class="text-right mt-3">
                                <button class="btn btn-success" type="submit">Save</button>
                            </div>

                        @endif

                    </div>


                </form>
            </div>
        </div>


    </div>

@endsection

@section('script')
    <script>
        $("body").on('change', "#balance_type", function() {
            let type = $(this).val()
            let _token = "{{ csrf_token() }}"
            $.post("{{ route('account.type.check') }}", {
                _token: _token,
                type: type
            }, function(data) {
                $("#account_info").html(data)
            })
        })
    </script>
@endsection

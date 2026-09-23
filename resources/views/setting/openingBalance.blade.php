@extends('layouts.dashboard')
@section('content')

    <div class="row">

        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-4">
            <div class="small-box bg-green whitecolor mt-3">
                <div class="inner">
                    <h4><span class="count-number">{{ $deposit_total - $withdraw_total }}</span></h4>
                    <p>{{ __('page.openblance')[0] }}</p>
                </div>
                <div class="icon">
                    <i class="fa fa-money"></i>
                </div>
            </div>
        </div>

        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-4">
            <div class="small-box bg-pase whitecolor mt-3">
                <div class="inner">
                    <h4><span class="count-number">{{ $deposit_total }}</span></h4>
                    <p>{{ __('page.openblance')[1] }}</p>
                </div>
                <div class="icon">
                    <i class="fa fa-money"></i>
                </div>
            </div>
        </div>

        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-4">
            <div class="small-box bg-bringal whitecolor mt-3">
                <div class="inner">
                    <h4><span class="count-number">{{ $withdraw_total }}</span></h4>
                    <p>{{ __('page.openblance')[2] }}</p>
                </div>
                <div class="icon">
                    <i class="fa fa-money"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="row">

        <div class="col-md-5">


            @if (isset($type))
                @if ($type == 'deposit')
                    <div class="card-box">
                        <h4 class="header-title m-t-0 m-b-30 mb-4">Diposit Edit Balance</h4>

                        <form method="POST" action="{{ route('deposit.update') }}"
                            class="">
                        @csrf
                        <input type=" hidden" name="id"
                            value="{{ $editDeposit->id }}" id="editId">
                            <div class="form-group">
                                <label for="">Balance Type</label>
                                <select name="balance_type" id="" class="form-control" required readonly>
                                    <option value="1" selected>Deposit</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="">Account Type</label>
                                <select name="pay_by" id="pay_by" class="form-control" required="">
                                    <option value="" selected>Select</option>
                                    <option value="Cash"
                                        {{ $editDeposit->account->account_type == 'Cash' ? 'selected' : '' }}>Cash
                                    </option>
                                    <option value="Mobile Banking"
                                        {{ $editDeposit->account->account_type == 'Mobile Banking' ? 'selected' : '' }}>
                                        Mobile Banking</option>
                                    <option value="Card"
                                        {{ $editDeposit->account->account_type == 'Card' ? 'selected' : '' }}>Card
                                    </option>
                                    <option value="Bank Account"
                                        {{ $editDeposit->account->account_type == 'Bank Account' ? 'selected' : '' }}>
                                        Bank Account</option>
                                </select>
                            </div>

                            <div id="account_info" class="form-group">
                                <label for=""></label>
                                @if ($editDeposit->account->account_type == 'Mobile Banking')
                                    <select name="account_id" id="" class="form-control select2" required="">
                                        @foreach ($data as $account)
                                            <option value="{{ $account->id }}"
                                                {{ $account->id == $editDeposit->account_id ? 'selected' : '' }}>
                                                {{ $account->mobile_number }} ({{ $account->mobile_bank_name }})
                                            </option>
                                        @endforeach
                                    </select>
                                @elseif($editDeposit->account->account_type == "Card")
                                    <select name="account_id" id="" class="form-control select2" required="">
                                        @foreach ($data as $account)
                                            <option value="{{ $account->id }}">{{ $account->card_number }}</option>
                                        @endforeach
                                    </select>
                                @elseif($editDeposit->account->account_type == "Bank Account")
                                    <select name="account_id" id="" class="form-control select2" required="">
                                        @foreach ($data as $account)
                                            <option value="{{ $account->id }}">{{ $account->bank_account_number }}
                                                ({{ $account->bank->bank_name }})</option>
                                        @endforeach
                                    </select>
                                @endif

                            </div>

                            <div class="form-group">
                                <label for="">Amount</label>
                                <input type="text" class="form-control" value="{{ $editDeposit->amount }}"
                                    name="amount" required placeholder="Amount">
                                @error('amount')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="">Remark</label>
                                <textarea name="note" rows="2" class="form-control">{{ $editDeposit->note }}</textarea>
                            </div>

                            <div class="text-right">
                                <button class="btn btn-success" type="submit">Save</button>
                            </div>

                        </form>
                    </div>
                @endif

                @if ($type == 'withdraw')
                    <div class="card-box">
                        <h4 class="header-title m-t-0 m-b-30 mb-4">Diposit Edit Balance</h4>

                        <form method="POST" action="{{ route('withdraw.update') }}"
                            class="">
                        @csrf
                        <input type=" hidden" name="id"
                            value="{{ $editWithdraw->id }}" id="editId">
                            <div class="form-group">
                                <label for="">Balance Type</label>
                                <select name="balance_type" id="" class="form-control" required readonly>
                                    <option value="2" selected>Withdraw</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="">Account Type</label>
                                <select name="pay_by" id="pay_by" class="form-control" required="">
                                    <option value="" selected>Select</option>
                                    <option value="Cash"
                                        {{ $editWithdraw->account->account_type == 'Cash' ? 'selected' : '' }}>Cash
                                    </option>
                                    <option value="Mobile Banking"
                                        {{ $editWithdraw->account->account_type == 'Mobile Banking' ? 'selected' : '' }}>
                                        Mobile Banking</option>
                                    <option value="Card"
                                        {{ $editWithdraw->account->account_type == 'Card' ? 'selected' : '' }}>Card
                                    </option>
                                    <option value="Bank Account"
                                        {{ $editWithdraw->account->account_type == 'Bank Account' ? 'selected' : '' }}>
                                        Bank Account</option>
                                </select>
                            </div>

                            <div id="account_info" class="form-group">
                                <label for=""></label>
                                @if ($editWithdraw->account->account_type == 'Mobile Banking')
                                    <select name="account_id" id="" class="form-control select2" required="">
                                        @foreach ($data as $account)
                                            <option value="{{ $account->id }}"
                                                {{ $account->id == $editWithdraw->account_id ? 'selected' : '' }}>
                                                {{ $account->mobile_number }} ({{ $account->mobile_bank_name }})
                                            </option>
                                        @endforeach
                                    </select>
                                @elseif($editWithdraw->account->account_type == "Card")
                                    <select name="account_id" id="" class="form-control select2" required="">
                                        @foreach ($data as $account)
                                            <option value="{{ $account->id }}"
                                                {{ $account->id == $editWithdraw->account_id ? 'selected' : '' }}>
                                                {{ $account->card_number }}</option>
                                        @endforeach
                                    </select>
                                @elseif($editWithdraw->account->account_type == "Bank Account")
                                    <select name="account_id" id="" class="form-control select2" required="">
                                        @foreach ($data as $account)
                                            <option value="{{ $account->id }}"
                                                {{ $account->id == $editWithdraw->account_id ? 'selected' : '' }}>
                                                {{ $account->bank_account_number }} ({{ $account->bank->bank_name }})
                                            </option>
                                        @endforeach
                                    </select>
                                @endif

                            </div>

                            <div class="form-group">
                                <label for="">Amount</label>
                                <input type="text" class="form-control" value="{{ $editWithdraw->amount }}"
                                    name="amount" required placeholder="Amount">
                                @error('amount')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="">Remark</label>
                                <textarea name="note" rows="2"
                                    class="form-control">{{ $editWithdraw->note }}</textarea>
                            </div>

                            <div class="text-right">
                                <button class="btn btn-success" type="submit">Save</button>
                            </div>

                        </form>
                    </div>
                @endif

            @else
                <div class="card-box">
                    <h4 class="header-title m-t-0 m-b-30 mb-4">{{ __('page.openblance')[3] }}</h4>

                    <form method="POST" action="{{ route('setting.opening.balance.store') }}"
                        class="">
                    @csrf
                    <div class=" form-group">
                        <label for="">{{ __('page.openblance')[4] }}</label>
                        <select name="balance_type" id="" class="form-control" required>
                            <option value="1">Deposit</option>
                            <option value="2">Withdraw</option>
                        </select>
                </div>

                <div class="form-group">
                    <label for="">{{ __('page.openblance')[6] }}</label>
                    <select name="pay_by" id="pay_by" class="form-control" required="">
                        <option value="" selected>Select</option>
                        <option value="Cash">Cash</option>
                        <option value="Mobile Banking">Mobile Banking</option>
                        <option value="Card">Card</option>
                        <option value="Bank Account">Bank Account</option>
                    </select>
                </div>

                <div id="account_info" class="form-group"></div>

                <div class="form-group">
                    <label for="">{{ __('page.openblance')[7] }}</label>
                    <input type="text" class="form-control" name="amount" required placeholder="Amount">
                    @error('amount')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="">{{ __('page.openblance')[8] }}</label>
                    <textarea name="note" rows="2" class="form-control"></textarea>
                </div>

                <div class="text-right">
                    <button class="btn btn-success" type="submit">Save</button>
                </div>

                </form>
        </div>
        @endif




    </div>

    <div class="col-xl-7">

        <ul class="nav nav-tabs">
            <li class="nav-item">
                <a href="#home1" class="btn btn-success ml-4" data-toggle="tab" aria-expanded="false"
                    class="nav-link active">
                    {{ __('page.openblance')[9] }}
                </a>
            </li>
            <li class="nav-item">
                <a href="#profile1" class="btn btn-info ml-2" data-toggle="tab" aria-expanded="true" class="nav-link">
                    {{ __('page.openblance')[10] }}
                </a>
            </li>
        </ul>

        <div class="tab-content">
            <div role="tabpanel" class="tab-pane fade active show" id="home1">
                <div class="card-box">
                    <h4 class="header-title m-t-0 m-b-30 mb-4">{{ __('page.openblance')[11] }}</h4>

                    <table id="datatable" class="table table-bordered mt-4" cellspacing="0" width="100%">
                        <thead class="theme-primary text-white">
                            <tr style="height: 30px;">
                                <th style="padding: 4px 7px; margin:0px; width: 10%;"> {{ __('page.openblance')[13] }}
                                </th>
                                <th style="padding: 4px 7px; margin:0px; width: 50%;"> {{ __('page.openblance')[14] }}
                                </th>
                                <th style="padding: 4px 7px; margin:0px; width: 40%;"> {{ __('page.openblance')[15] }}
                                </th>
                                <th style="padding: 4px 7px; margin:0px; width: 40%;"> {{ __('page.openblance')[16] }}
                                </th>
                            </tr>
                        </thead>
                        <tbody>

                            @foreach ($deposits as $deposit)
                                <tr style="height: 30px;">
                                    <td style="padding: 4px 7px; margin:0px; width: 10%;"> {{ $loop->index + 1 }} </td>
                                    <td style="padding: 4px 7px; margin:0px; width: 50%;">
                                        {{ $deposit->business->name }} </td>
                                    <td style="padding: 4px 7px; margin:0px; width: 40%;"> {{ $deposit->amount }} </td>
                                    <td style="padding: 4px 7px; margin:0px; width: 40%;">
                                        <div class="btn-group btn-sm">
                                            <a href="{{ route('deposit.edit', $deposit->id) }}"
                                                class="btn btn-sm btn-success">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <a href="{{ route('deposit.delete', $deposit->id) }}"
                                                class="btn btn-sm btn-danger" id="delete">
                                                <i class="fa fa-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach

                        </tbody>
                    </table>
                </div>
            </div>
            <div role="tabpanel" class="tab-pane fade" id="profile1">
                <div class="card-box">
                    <h4 class="header-title m-t-0 m-b-30 mb-4">{{ __('page.openblance')[12] }}</h4>

                    <table id="datatable" class="table table-bordered mt-4" cellspacing="0" width="100%">
                        <thead class="theme-primary text-white">
                            <tr style="height: 30px;">
                                <th style="padding: 4px 7px; margin:0px; width: 10%;"> {{ __('page.openblance')[13] }}
                                </th>
                                <th style="padding: 4px 7px; margin:0px; width: 50%;"> {{ __('page.openblance')[14] }}
                                </th>
                                <th style="padding: 4px 7px; margin:0px; width: 40%;"> {{ __('page.openblance')[15] }}
                                </th>
                                <th style="padding: 4px 7px; margin:0px; width: 40%;"> {{ __('page.openblance')[16] }}
                                </th>
                            </tr>
                        </thead>
                        <tbody>

                            @foreach ($withdraws as $withdraw)
                                <tr style="height: 30px;">
                                    <td style="padding: 4px 7px; margin:0px; width: 10%;"> {{ $loop->index + 1 }} </td>
                                    <td style="padding: 4px 7px; margin:0px; width: 50%;">
                                        {{ $withdraw->business->name }} </td>
                                    <td style="padding: 4px 7px; margin:0px; width: 40%;"> {{ $withdraw->amount }} </td>
                                    <td style="padding: 4px 7px; margin:0px; width: 40%;">
                                        <div class="btn-group btn-sm">
                                            <a href="{{ route('withdraw.edit', $withdraw->id) }}"
                                                class="btn btn-sm btn-success">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <a href="{{ route('withdraw.delete', $withdraw->id) }}"
                                                class="btn btn-sm btn-danger" id="delete">
                                                <i class="fa fa-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach

                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    </div>

@endsection

@section('script')
    <script>
        $("body").on('change', "#pay_by", function() {
            // purchase.payment.account
            let account_type = $(this).val()
            let _token = "{{ csrf_token() }}"
            if (account_type == "Cash") {
                $.post("{{ route('setting.payment.account.cash') }}", {
                    _token: _token,
                    account_type: account_type,
                }, function(data) {
                    $("#account_info").html(data)
                })
            } else {
                $.post("{{ route('setting.payment.account') }}", {
                    _token: _token,
                    account_type: account_type,
                }, function(data) {
                    $("#account_info").html(data)
                    $(".select3").select2();
                })
            }

        })

        $('body').on('change', "#account_id", function() {
            let account_id = $(this).val()
            let _token = "{{ csrf_token() }}"
            $.post("{{ route('account.blance.check') }}", {
                _token: _token,
                account_id: account_id
            }, function(data) {
                $("#currentBalance").html(data)
            })
        })
    </script>
@endsection

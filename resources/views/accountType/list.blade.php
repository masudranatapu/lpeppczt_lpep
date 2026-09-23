@extends('layouts.dashboard')
@section('content')

    <div class="row">

        <div class="col-md-12">
            <div class="card-box mt-4">
                <h4 class="header-title m-t-0 m-b-30 mb-4">Mobile Banking Accounts</h4>

                <table id="" class="table table-bordered mt-4" cellspacing="0" width="100%">
                    <thead class="theme-primary text-white">
                        <tr style="height: 30px;">
                            <th style="padding: 4px 7px; margin:0px; width: 10%;"> Sl </th>
                            <th style="padding: 4px 7px; margin:0px; width: 30%;"> Business Branch </th>
                            <th style="padding: 4px 7px; margin:0px; width: 30%;"> Mobile Bank Name </th>
                            <th style="padding: 4px 7px; margin:0px; width: 30%;"> Mobile Number </th>
                            <th style="padding: 4px 7px; margin:0px; width: 30%;"> Amount </th>
                            <th style="padding: 4px 7px; margin:0px; width: 30%;"> Action </th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $business = currentBranch();
                        @endphp

                        @foreach ($lists as $list)
                            @if ($list->account_type == 'Mobile Banking')
                                <tr style="height: 30px;">
                                    <td style="padding: 4px 7px; margin:0px; width: 10%;"> {{ $loop->index + 1 }} </td>
                                    <td style="padding: 4px 7px; margin:0px; width: 30%;"> {{ $business->name }}
                                    </td>
                                    <td style="padding: 4px 7px; margin:0px; width: 30%;"> {{ $list->mobile_bank_name }}
                                    </td>
                                    <td style="padding: 4px 7px; margin:0px; width: 30%;"> {{ $list->mobile_number }}
                                    </td>
                                    <td>
                                        {{ accountBalance($list->id) }}
                                    </td>
                                    <td style="padding: 4px 7px; margin:0px; width: 30%;">
                                        <div class="btn-group btn-sm">
                                            <a href="{{ route('account.type.edit', $list->id) }}"
                                                class="btn btn-sm btn-success">Edit</a>
                                            @if ($list->status == 1)
                                                <a href="{{ route('account.type.deactive', $list->id) }}"
                                                    class="btn btn-sm btn-warning">Deactive</a>
                                            @else
                                                <a href="{{ route('account.type.active', $list->id) }}"
                                                    class="btn btn-sm btn-danger">Active</a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        @endforeach

                    </tbody>
                </table>
            </div>
        </div>


        <div class="col-md-12">
            <div class="card-box mt-4">
                <h4 class="header-title m-t-0 m-b-30 mb-4">Banking Accounts</h4>

                <table id="" class="table table-bordered mt-4" cellspacing="0" width="100%">
                    <thead class="theme-primary text-white">
                        <tr style="height: 30px;">
                            <th style="padding: 4px 7px; margin:0px;"> Sl </th>
                            <th style="padding: 4px 7px; margin:0px;"> Business Branch </th>
                            <th style="padding: 4px 7px; margin:0px;"> Bank Name </th>
                            <th style="padding: 4px 7px; margin:0px;"> Bank Account Type </th>
                            <th style="padding: 4px 7px; margin:0px;"> Bank Account Name </th>
                            <th style="padding: 4px 7px; margin:0px;"> Bank Account Number </th>
                            <th style="padding: 4px 7px; margin:0px;"> Bank Account Branch </th>
                            <th style="padding: 4px 7px; margin:0px;"> Amount </th>
                            <th style="padding: 4px 7px; margin:0px;"> Action </th>
                        </tr>
                    </thead>
                    <tbody>

                        @foreach ($lists as $list)
                            @if ($list->account_type == 'Bank Account')
                                <tr style="height: 30px;">
                                    <td style="padding: 4px 7px; margin:0px;"> {{ $loop->index + 1 }} </td>
                                    <td style="padding: 4px 7px; margin:0px;"> {{ $list->business->name }} </td>
                                    <td style="padding: 4px 7px; margin:0px;"> {{ $list->bank->bank_name }} </td>
                                    <td style="padding: 4px 7px; margin:0px;"> {{ $list->bank_account_type }} </td>
                                    <td style="padding: 4px 7px; margin:0px;"> {{ $list->bank_account_name }} </td>
                                    <td style="padding: 4px 7px; margin:0px;"> {{ $list->bank_account_number }} </td>
                                    <td style="padding: 4px 7px; margin:0px;"> {{ $list->bank_account_branch }} </td>
                                    <td>
                                        {{ accountBalance($list->id) }}
                                    </td>
                                    <td style="padding: 4px 7px; margin:0px;">
                                        <div class="btn-group btn-sm">
                                            <a href="{{ route('account.type.edit', $list->id) }}"
                                                class="btn btn-sm btn-success">Edit</a>
                                            @if ($list->status == 1)
                                                <a href="{{ route('account.type.deactive', $list->id) }}"
                                                    class="btn btn-sm btn-warning">Deactive</a>
                                            @else
                                                <a href="{{ route('account.type.active', $list->id) }}"
                                                    class="btn btn-sm btn-danger">Active</a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        @endforeach

                    </tbody>
                </table>
            </div>
        </div>


        <div class="col-md-12">
            <div class="card-box mt-4">
                <h4 class="header-title m-t-0 m-b-30 mb-4">Card Accounts</h4>

                <table id="" class="table table-bordered mt-4" cellspacing="0" width="100%">
                    <thead class="theme-primary text-white">
                        <tr style="height: 30px;">
                            <th style="padding: 4px 7px; margin:0px;"> Sl </th>
                            <th style="padding: 4px 7px; margin:0px;"> Business Branch </th>
                            <th style="padding: 4px 7px; margin:0px;"> Card Type </th>
                            <th style="padding: 4px 7px; margin:0px;"> Bank Name </th>
                            <th style="padding: 4px 7px; margin:0px;"> Card Holder Name </th>
                            <th style="padding: 4px 7px; margin:0px;"> Card Number </th>
                            <th style="padding: 4px 7px; margin:0px;"> Valid Thru Month </th>
                            <th style="padding: 4px 7px; margin:0px;"> Valid Thru Year </th>
                            <th style="padding: 4px 7px; margin:0px;"> CVV Code </th>
                            <th>Amount</th>
                            <th style="padding: 4px 7px; margin:0px;"> Action </th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $month = ['Junuary', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
                        @endphp
                        @foreach ($lists as $list)
                            @if ($list->account_type == 'Card')
                                <tr style="height: 30px;">
                                    <td style="padding: 4px 7px; margin:0px;"> {{ $loop->index + 1 }} </td>
                                    <td style="padding: 4px 7px; margin:0px;"> {{ $list->business->name }} </td>
                                    <td style="padding: 4px 7px; margin:0px;"> {{ $list->card_type }} </td>
                                    <td style="padding: 4px 7px; margin:0px;"> {{ $list->bank->bank_name }} </td>
                                    <td style="padding: 4px 7px; margin:0px;"> {{ $list->card_holder_name }} </td>
                                    <td style="padding: 4px 7px; margin:0px;"> {{ $list->card_number }} </td>
                                    <td style="padding: 4px 7px; margin:0px;"> {{ $month[$list->valid_thru_month - 1] }}
                                    </td>
                                    <td style="padding: 4px 7px; margin:0px;"> {{ $list->valid_thru_year }} </td>
                                    <td style="padding: 4px 7px; margin:0px;"> {{ $list->cvv_code }} </td>
                                    <td>
                                        {{ accountBalance($list->id) }}
                                    </td>
                                    <td style="padding: 4px 7px; margin:0px;">
                                        <div class="btn-group btn-sm">
                                            <a href="{{ route('account.type.edit', $list->id) }}"
                                                class="btn btn-sm btn-success">Edit</a>
                                            @if ($list->status == 1)
                                                <a href="{{ route('account.type.deactive', $list->id) }}"
                                                    class="btn btn-sm btn-warning">Deactive</a>
                                            @else
                                                <a href="{{ route('account.type.active', $list->id) }}"
                                                    class="btn btn-sm btn-danger">Active</a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        @endforeach

                    </tbody>
                </table>
            </div>
        </div>

    </div>

@endsection

@extends('layouts.dashboard')

@section('content')
    <style>
        #savePurchase:disabled {
            background: red !important;
        }

        input.qty::-webkit-outer-spin-button,
        input.qty::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        input.qty[type=number] {
            -moz-appearance: textfield;
        }

    </style>

    <div class="row">
        <div class="col-12">
            <div class="mt-4">
                <h4>{{ __('page.purchase')[0] }}</h4>
            </div>

            <form id="purchaseForm" action="{{ route('purchase.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="card-box table-responsive mt-4">
                    <div class="row">

                        @if (isRole(ROLE_AGENT))
                            <input type="hidden" name="product_origin" value="customer">
                        @else
                            <input type="hidden" name="product_origin" value="supplier">
                        @endif

                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="contact_id">
                                    @if (isRole(ROLE_AGENT))
                                        {{ __('page.customer.0') }}
                                    @else
                                        {{ __('page.purchase_vendor_name') }}
                                    @endif
                                    <i class="fa fa-info-circle text-info hover-q no-print "></i>
                                </label>
                                <select class="form-control select2 supplier-id" required name="contact_id">
                                    @foreach ($contacts as $contact)
                                        <option value="{{ $contact->id }}">{{ $contact->name }}
                                            ({{ $contact->mobile }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="invoice_no">{{ __('page.purchase')[2] }}</label>
                                <input type="text" class="form-control" name="invoice_no" value="{{ $invoice_id }}"
                                    id="invoice_no" placeholder="Invoice No">
                            </div>
                        </div>

                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="purchase_date">{{ __('page.purchase')[3] }}<i
                                        class="fa fa-info-circle text-info hover-q no-print "></i></label>
                                <input type="date" class="form-control" name="purchase_date" placeholder="Purchase Date"
                                    value="<?php echo date('Y-m-d'); ?>" required>
                            </div>
                        </div>

                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="barcode_type">{{ __('page.purchase')[5] }}</label>
                                <input type="file" class="form-control" name="attachment">
                            </div>
                        </div>


                        <div class="col-md-12">
                            <label for="">{{ __('page.purchase')[6] }}</label>
                            <textarea name="note" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                </div>

                <div class="card-box table-responsive mt-4">

                    <div class="row">
                        <div class="col-md-8 offset-md-2">
                            <div class="form-group">
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-search"></i>
                                    </span>

                                    <input class="form-control prevent" id="search_product"
                                        placeholder="{{ __('page.purchase')[7] }}" autofocus name="search_product"
                                        type="text">

                                    <div id="showSearchProducts" style="display: none">

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="row">

                        <div class="col-sm-12">
                            <div class="table-responsive" style="display: none" id="regular-purchase">
                                <table class="table table-condensed table-bordered text-center">
                                    <thead>
                                        <tr class="theme-primary text-white">
                                            <th style="width: 10%;">{{ __('page.purchase')[8] }}</th>
                                            <th style="width: 10%;">{{ 'Batch Id' }}</th>
                                            <th style="width: 10%;">{{ __('page.purchase')[9] }}</th>
                                            <th style="width: 10%;">{{ __('page.purchase')[10] }}</th>
                                            <th style="width: 10%;">{{ __('page.purchase.17') }}</th>
                                            <th style="width: 10%;">{{ __('page.purchase.18') }}</th>
                                            <th style="width: 10%;">{{ __('page.purchase')[11] }}</th>
                                            <th style="width: 10%;"><i class="fa fa-trash" aria-hidden="true"></i></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="col-sm-12">
                            <div class="table-responsive" id="medicine-purchase" style="display: none">
                                <table class="table table-condensed table-bordered text-center">
                                    <thead>
                                        <tr class="theme-primary text-white">
                                            <th style="width: 10%;">{{ __('page.purchase')[8] }}</th>
                                            <th style="width: 10%;">{{ 'Batch Id' }}</th>
                                            <th style="width: 10%;">{{ 'Expiry Date' }}</th>
                                            <th style="width: 10%;">{{ 'Box Pattern' }}</th>
                                            <th style="width: 10%;">{{ 'Box Qty' }}</th>
                                            <th style="width: 10%;">{{ __('page.purchase')[9] }}</th>
                                            <th style="width: 10%;">{{ __('page.purchase')[10] }}</th>
                                            <th style="width: 10%;">{{ __('page.purchase.17') }}</th>
                                            <th style="width: 10%;">{{ __('page.purchase.18') }}</th>
                                            <th style="width: 10%;">{{ __('page.purchase')[11] }}</th>
                                            <th style="width: 10%;"><i class="fa fa-trash" aria-hidden="true"></i></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="col-md-12 mt-3">
                            <table class="col-md-12">
                                <tbody>
                                    <tr class="hide">
                                        <th class="col-md-7 text-right">{{ __('page.purchase')[12] }}:</th>
                                        <td class="col-md-5">
                                            <input type="text"
                                                style="padding-right:6px;height:34px;margin-top: 6px;border-radius: 3px;width: 185px;text-align: right; border: 1px solid #b3afaf; background-color: #e6e6e6;"
                                                id="total" name="total" value="0" readonly>
                                        </td>
                                    </tr>

                                    <tr>
                                        <th class="col-md-7 text-right"> {{ __('page.paymenttype') }} </th>
                                        <th class="col-md-5 text-left">
                                            <select name="pay_by" id="pay_by" class="form-control" required=""
                                                style="padding-right:6px;height:34px;margin-top: 6px;border-radius: 3px;width: 185px;text-align: right; border: 1px solid #bbbbbb;">
                                                <option value="">Select</option>
                                                <option value="Cash">Cash</option>
                                                <option value="Mobile Banking">Mobile Banking</option>
                                                <option value="Card">Card</option>
                                                <option value="Bank Account">Bank Account</option>
                                            </select>
                                        </th>
                                    </tr>

                                    <tr id="account_info">
                                    </tr>

                                    <tr>
                                        <th class="col-md-7 text-right"> {{ __('page.payingamount') }} </th>
                                        <th class="col-md-5 text-left">
                                            <input type="text"
                                                style="padding-right:6px;height:34px;margin-top: 6px;border-radius: 3px;width: 185px;text-align: right; border: 1px solid #b3afaf;"
                                                oninput="due()" name="paying_amount" id="paying_amount" value="0"
                                                autocomplete="off">
                                        </th>
                                    </tr>

                                    <tr>
                                        <th class="col-md-7 text-right">{{ __('page.due') }}:</th>
                                        <td class="col-md-5 text-left">
                                            <input type="text"
                                                style="padding-right:6px;height:34px;margin-top: 6px;border-radius: 3px;width: 185px;text-align: right; border: 1px solid #b3afaf; background-color: #e6e6e6;"
                                                id="total_due" value="0" readonly>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="text-right mt-4">
                        <button type="submit" id="savePurchase"
                            class="btn btn-primary">{{ __('page.purchase')[16] }}</button>
                    </div>
                </div>

            </form>

        </div>
    </div>
    <!-- end row -->


@endsection

@section('script')
    <script>
        const CSRF_TOKEN = "{{ csrf_token() }}";
        const ROUTE_PURCHASE_SEARCH = "{{ route('purchase.search') }}";
        const ROUTE_PURCHASE_PAYMENT_ACCOUNT = "{{ route('purchase.payment.account') }}";
        const ROUTE_DOES_BATCH_ID_EXIST = "{{ route('purchase.does-batch-id-exist') }}";
        const PRODUCTS = [];
        const USER_ID = {{ auth()->id() }};
        const BOX_PATTERNS = JSON.parse('{!! $box_patterns !!}');
    </script>

    <script src="/js/purchase.js?v=0.9"></script>
@endsection

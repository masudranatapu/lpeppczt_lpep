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
                        <div class="col-md-4">
                            <div class="row">
                                <div class="col-md-12">
                                    @include('inc.appCustomer')
                                </div>

                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="invoice_no">{{ __('page.purchase')[2] }}</label>
                                        <input type="text" class="form-control" name="invoice_no"
                                            value="{{ $invoice_id }}" id="invoice_no" placeholder="Invoice No">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="invoice_no">Total Loan</label>
                                        <input type="text" class="form-control" id="total-loan" value="0" disabled>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="purchase_date">{{ __('page.purchase')[3] }}<i
                                                class="fa fa-info-circle text-info hover-q no-print "></i></label>
                                        <input type="date" class="form-control" name="purchase_date"
                                            placeholder="Purchase Date" value="<?php echo date('Y-m-d'); ?>" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label for="">{{ __('page.purchase')[6] }}</label>
                            <textarea name="note" class="form-control" style="height: 74%"></textarea>
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
                                            <th style="width: 10%;">{{ 'Batch' }}</th>
                                            <th style="width: 10%;">{{ __('page.purchase')[9] }}</th>
                                            <th style="width: 10%;">{{ __('page.purchase')[10] }}</th>
                                            <th style="width: 10%;">{{ __('page.purchase')[11] }}</th>
                                            <th style="width: 10%;"><i class="fa fa-trash" aria-hidden="true"></i></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>


                    <div class="row">
                        <div class="col-md-6"></div>
                        <div class="col-md-6 text-right">
                            <label class="text-right">{{ __('page.purchase')[12] }}:</label>
                            <input type="text"
                                style="padding-right:6px;height:34px;margin-top: 6px;border-radius: 3px;width: 185px;text-align: right; border: 1px solid #b3afaf; background-color: #e6e6e6;"
                                id="total" name="total" value="0" readonly>
                        </div>
                        <div class="col-md-12">
                            <div class="text-right mt-4">
                                <button type="submit" id="savePurchase"
                                    class="btn btn-primary">{{ __('page.purchase')[16] }}</button>
                            </div>
                        </div>
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
        const ROUTE_CUSTOMER_TOTAL_LOANS = "{{ route('customer-points.total-loans', 0) }}";
        const ROUTE_DOES_BATCH_ID_EXIST = "{{ route('purchase.does-batch-id-exist') }}";
        const PRODUCTS = [];
    </script>

    <script src="/js/purchaseAgent.js?v=0.1"></script>
@endsection

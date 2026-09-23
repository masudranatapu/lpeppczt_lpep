@extends('layouts.pos_layout')

@section('pos')
    <link rel="stylesheet" href="/css/pos.css?v=0.3">

    <div class="row">
        <div class="col-md-10"></div>
        <div class="col-md-2">
        </div>
    </div>

    <br>

    <div class="row">
        <div class="col-xl-5">
            <div class="card-box project-box ml-2" style="background: none !important; box-shadow: none; height: 600px;">

                <!-- category list start -->
                <div class="row">
                    <div class="col-md-6">
                        <select name="" class="form-control select2" id="get_category_id">
                            <option value="all">All Categories</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->category_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <select name="" class="form-control select2" id="get_brand_id">
                            <option value="all">All Brand</option>
                            @foreach ($brands as $brand)
                                <option value="{{ $brand->id }}">{{ $brand->brand_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- category list end -->
                <div id="product-show-scroll" class="slimscrollleft mt-4" style="padding-right: 10px">

                    <div class="row" id="filter_product_list">

                        @include('sale.partial.filterProduct')

                    </div>

                </div>

            </div>
        </div><!-- end col-->


        <div class="col-xl-7">
            <div class="card-box project-box" style="box-shadow: none; border-radius: 0px;height: 650px;">

                <div class="project-sort">
                    <div class="project-sort-item">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <div class="input-group">
                                        <span class="input-group-addon">
                                            <i class="fa fa-barcode"></i>
                                        </span>
                                        <input class="form-control" id="search_product"
                                            placeholder="Enter Product name / SKU / Scan bar code" autofocus
                                            name="search_product" type="text">
                                        <div id="showSearchProducts" style="display: none">

                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <div class="input-group">
                                        <span class="input-group-addon">
                                            <i class="fa fa-user"></i>
                                        </span>
                                        <select name="contact_id" class="form-control select2" id="contact_id">
                                            <option value="">Guest Customer</option>
                                        </select>
                                        <span class="input-group-btn" style="margin-left: 1px">
                                            <button style="cursor: pointer;" id="addNewCustomer" type="button"
                                                class="btn btn-default bg-white btn-flat add_new_customer"><i
                                                    class="fa fa-plus-circle text-primary fa-lg"></i></button>
                                        </span>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <!-- cart Section start -->
                <div class="mt-1" id="table-scroller">
                    <table class="table table-bordered m-0">
                        <thead class="text-center" style="background: #00a65a">
                            <tr style="height: 25px; color: #fff;">
                                <th style="padding:4px 0px; margin:0px; width: 30%;">Name</th>
                                <th style="padding:4px 0px; margin:0px; width: 15%;">Batch</th>
                                <th style="padding:4px 0px; margin:0px; width: 15%;">Quantity</th>
                                <th style="padding:4px 0px; margin:0px; width: 15%;">Price</th>
                                <th style="padding:4px 0px; margin:0px; width: 15%;">Total</th>
                                <th style="padding:4px 0px; margin:0px; width: 5%;">
                                    <i class="fa fa-trash"></i>
                                </th>
                            </tr>
                        </thead>
                        <tbody id="myCartList"></tbody>
                    </table>
                </div>
            </div>
        </div><!-- end col-->




    </div>


    <footerup style="position: fixed;color: #fff;left:0;bottom: 70px; width: 100%">

        <div class="row">

            <div class="col-md-5 col-sm-12">

            </div>
            <div class="col-md-7 col-sm-12">
                <table id="totalTable" style="width:100%; float:right; padding:5px; color:#000; background: #FFF;">
                    <tbody>
                        <tr>
                            <td style="padding: 5px 10px;border-top: 1px solid #DDD;">Items</td>
                            <td class="text-right"
                                style="padding: 5px 10px;font-size: 14px; font-weight:bold;border-top: 1px solid #DDD;">
                                <span id="titems">0</span>
                            </td>
                            <td style="padding: 5px 10px;border-top: 1px solid #DDD;">Total</td>
                            <td class="text-right"
                                style="padding: 5px 10px;font-size: 14px; font-weight:bold;border-top: 1px solid #DDD;">
                                <span id="total">0</span>
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 5px 10px;">
                                VAT/SD/Tax
                            </td>
                            <td class="text-right" style="padding: 5px 10px;font-size: 14px; font-weight:bold;">
                                <span id="ttax2">0</span>
                            </td>
                            <td style="padding: 5px 10px;">Discount
                                <i data-toggle="modal" data-target="#discountModal" style="cursor: pointer;"
                                    class="fa fa-edit"></i>
                            </td>
                            <td class="text-right" style="padding: 5px 10px;font-weight:bold;">
                                <span id="tds">(0.00) 0.00</span>
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 5px 10px;  font-weight:bold; background:#6a1b9a; color:#FFF;"
                                colspan="2">
                                Total Payable
                                <span id="payable_amount"></span>
                            </td>
                            <td class="text-right"
                                style="padding:5px 10px 5px 10px; font-size: 14px; font-weight:bold; background:#6a1b9a; color:#FFF;"
                                colspan="2">
                                <span id="gtotal">0</span>
                            </td>
                        </tr>
                    </tbody>

                </table>
            </div>
        </div>
    </footerup>


    <footer style="position: fixed;color: #fff;left:0;bottom: 0;background:#6a1b9a;width: 100%">

        <div class="row">
            <div class="col-md-2 col-3">
                <a href="{{ route('home') }}" class="btn btn-block"
                    style="border-radius: 0px !important; background: #ff4081; font-size: 28px;font-weight: 900;color: #fff">
                    <i class="fa fa-backward fa-lg mt-3"></i>
                </a>
            </div>
            <div class="col-md-6 col-9">
                <div class="text-center">
                    <h3 style="font-size: 28px;font-weight: 900;margin-top: 20px;">Total : <span id="finalTotal"> </span>
                        BDT </h3>
                </div>
            </div>
            <div class="col-md-2 col-6 ">
                <button class="btn btn-block h-100" onclick="cancel()"
                    style="border-radius: 0px !important; background: #f31250; font-size: 20px;font-weight: 900;color: #fff">Cancel</button>
            </div>
            <div class="col-md-2 col-6 ">
                <button class="btn btn-block h-100 open-payment-modal"
                    style="border-radius: 0px !important; background: #00a65a; font-size: 20px;font-weight: 900;color: #fff">Payment
                </button>
            </div>
        </div>
    </footer>



    {{-- payment Modal --}}
    <div class="modal fade payment-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form method="POST" action="{{ route('sale.store') }}">
                    @csrf

                    <input type="hidden" name="customer_id" id="customer_id" value="" required>
                    <div class="row">
                        <div class="col-md-4">

                            <div class="table-responsive">
                                <table class="table table-bordered table-striped table-condensed">
                                    <tbody>
                                        <!-- ngRepeat: items in itemArray -->
                                        <tr ng-repeat="items in itemArray" class="ng-scope">
                                            <td class="text-center w-10 ng-binding">


                                            </td>
                                            <td class="w-70 ng-binding">Payment Details</td>
                                            <td class="text-right w-20 ng-binding"></td>
                                        </tr><!-- end ngRepeat: items in itemArray -->
                                    </tbody>
                                    <tfoot>

                                        <tr>
                                            <th class="text-right w-60" colspan="2">
                                                Subtotal
                                            </th>
                                            <input type="hidden" name="sub_total" value="" autocomplete="off">
                                            <td class="text-right w-40 ng-binding" id="sub_totalModal">0</td>
                                        </tr>
                                        <tr>
                                            <th class="text-right w-60 ng-binding" colspan="2">
                                                Discount
                                            </th>
                                            <input type="hidden" name="discount_amount" value="0"
                                                autocomplete="off">
                                            <td class="text-right w-40 ng-binding" id="discount_amountModal">0.00</td>
                                        </tr>

                                        <tr>
                                            <th class="text-right w-60" colspan="2">
                                                VAT/SD/Tax </th>
                                            <input type="hidden" name="vat" value="0" autocomplete="off">
                                            <td class="text-right w-40 ng-binding" id="vatModal">0.00</td>
                                        </tr>

                                        <tr>
                                            <th class="text-right w-60" colspan="2">
                                                Total Amount<small class="ng-binding">(<span id="itemModal">0</span>
                                                    items)</small>
                                            </th>
                                            <input type="hidden" name="total_amount" value="0" autocomplete="off">
                                            <td class="text-right w-40 ng-binding" id="total_amountModal">0.00</td>
                                        </tr>

                                        <tr>
                                            <th class="text-right w-60" colspan="2">
                                                Paid Amount</th>
                                            <td class="text-right w-40 ng-binding" id="paing_amountModal">00.00</td>
                                        </tr>

                                        <tr>
                                            <th class="text-right w-60" colspan="2">
                                                Due Amount
                                            </th>
                                            <td class="text-right w-40 ng-binding" id="due_amountModal">00.00</td>
                                        </tr>

                                    </tfoot>
                                </table>
                            </div>

                        </div>

                        <div class="col-md-8">
                            <div class="row">
                                <div class="col-md-12">
                                    <button type="button" class="btn btn-block"
                                        style="background:#7a8882;color: #fff;font-weight: 900;font-size: 18px;">Total
                                        Amount: <span style="color: #ffd400;font-size: 22px;"
                                            id="total_amountModal2">00.00</span> BDT</button>
                                </div>
                            </div>

                            <div class="custom-scroll">

                                <div class="customer-section">
                                    <div class="mt-2">
                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="" class="mt-2">Customer Name</label>
                                            </div>
                                            <div class="col-md-8">
                                                <input type="text" class="form-control" name="customer_name">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mt-2">
                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="" class="mt-2">Customer Phone</label>
                                            </div>
                                            <div class="col-md-8">
                                                <input type="text" class="form-control" name="customer_phone">
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                <div class="mt-2">
                                    <div class="form-group row">
                                        <div class="col-md-4">
                                            <label for="" class="mt-2">Cash</label>
                                        </div>
                                        <div class="col-md-8">
                                            <input type="text" class="form-control" name="paying_amount"
                                                value="00.00">
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-2">
                                    <div class="form-group row">
                                        <div class="col-md-4">
                                            <label for="" class="mt-2">Payment Type</label>
                                        </div>
                                        <div class="col-md-8">
                                            <select name="payment_type" id="pay_by" class="form-control"
                                                required="">
                                                <option value="Cash">Cash</option>
                                                <option value="Mobile Banking">Mobile Banking</option>
                                                <option value="Card">Card</option>
                                                <option value="Bank Account">Bank Account</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div id="account_info">

                                </div>

                                @if ($business_setting->deliverycharge)
                                    <div class="mt-2">
                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="" class="mt-2"> Delivery Charge </label>
                                            </div>
                                            <div class="col-md-8">
                                                <input type="text" class="form-control" name="deliverycharge"
                                                    id="deliverycharge" value="0">
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                @if ($business_setting->preorder)
                                    <div class="form-group row">
                                        <div class="col-md-4">
                                            <label for="" class="mt-2">Order Status</label>
                                        </div>
                                        <div class="col-md-8">
                                            <select name="order_status" id="order_status" class="form-control"
                                                required="">
                                                <option value="Regular Order">Regular Order</option>
                                                <option value="Pre Order">Pre Order</option>
                                            </select>
                                        </div>
                                    </div>
                                @endif

                                {{-- <div class="mt-4">
                                    <div class="form-group row">
                                        <div class="col-md-4">
                                            <label for="" class="mt-2"> Due</label>
                                        </div>
                                        <div class="col-md-8">
                                            <input type="text" class="form-control" name="total_due"
                                                id="due_amountModal" readonly value="0">
                                        </div>
                                    </div>
                                </div> --}}

                                <div class="mt-2">

                                    <div class="row">
                                        <div class="col-md-6 mt-4">
                                            <button type="button"
                                                style="background: #f31250;font-size: 20px;font-weight: 600;color: #fff"
                                                class="btn btn-block close-payment-modal">Cancel <span
                                                    style="font-size: 14px;color: #f7e5e5;">[Esc]</span></button>
                                        </div>
                                        <div class="col-md-6 mt-4">
                                            <button type="submit" class="btn btn-block"
                                                style="background: #00a65a;font-size: 20px;font-weight: 600;color:#fff"enabled="enabled">Checkout</button>
                                        </div>
                                    </div>

                                </div>
                            </div>

                        </div>


                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Customer Modal Show --}}
    <div class="modal fade bd-example-modal-lg" id="contactModal" role="dialog" aria-labelledby="myLargeModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div id="modalcontent" class="modal-content" style="padding: 0 !important">

            </div>
        </div>
    </div>

    {{-- Discount Modal --}}
    <!-- Modal -->
    <div class="modal fade" id="discountModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-body">

                    <div class="form-group">
                        <label for="">Discount</label>
                        <div class="input-group">
                            <div class="input-group-btn">
                                <select name="discount_type" id="discount_type"
                                    class="btn waves-effect waves-light btn-primary">
                                    <option value="1" selected>Amount (৳)</option>
                                    <option value="2">Percentage (%)</option>
                                </select>
                            </div>
                            <input type="text" id="discount_total_amount" value="0" name="discount_total_amount"
                                class="form-control" placeholder="Discount">

                        </div>
                    </div>

                    <div class="text-right mt-4">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="button" id="discountSave" class="btn btn-primary">Save changes</button>
                    </div>
                    {{-- </form> --}}
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        const CSRF_TOKEN = "{{ csrf_token() }}";
        const SESSION_SALE_ID = "{{ session('sale_id') }}";
        const ROUTE_CONTACT_CUSTOMER_CREATE = "{{ route('contact.customer.create') }}";
        const ROUTE_CONTACT_CUSTOMER_JSON = "{{ route('contact.customer.json') }}";
        const ROUTE_SALE_CART = "{{ route('sale.cart') }}";
        const ROUTE_CART_LIST = "{{ route('cart.list') }}";
        const ROUTE_SALE_CHANGE_PRICE = "{{ route('sale.change.price') }}";
        const ROUTE_CART_REMOVE = "{{ route('cart.remove') }}";
        const ROUTE_CART_INCREMENT = "{{ route('cart.increment') }}";
        const ROUTE_CART_DECREMENT = "{{ route('cart.decrement') }}";
        const ROUTE_CONTACT_GROUP_CHECK = "{{ route('contact.group.check') }}";
        const ROUTE_POS_CLEAR = "{{ route('pos.clear') }}";
        const ROUTE_CART_QUANTITY_UPDATE = "{{ route('cart.qty.update') }}";
        const ROUTE_SALE_PAYMENT_ACCOUNT = "{{ route('sale.payment.account') }}";
        const ROUTE_FILTER_PRODUCT_CATEGORY = "{{ route('filter.product.category') }}";
        const ROUTE_FILTER_PRODUCT_BRNAD = "{{ route('filter.product.brand') }}";
        const ROUTE_CONTACT_STORE = "{{ route('contact.store') }}";
        const ROUTE_CART_CHANGE_BATCH_ID = "{{ route('cart.change-batch-id') }}";
    </script>

    <script src="/js/pos.js?v=0.3"></script>
@endsection

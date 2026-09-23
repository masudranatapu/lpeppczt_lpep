@extends('layouts.dashboard')
@section('title', 'Purchase Return')
@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="card-box table-responsive mt-4" style="border-top: 3px solid #2aa9b9;">
                <div class="row">
                    <div class="col-md-6">
                        <h4 class="m-t-0 header-title mb-5"><b>Purchase Return</b></h4>
                    </div>
                </div>

                <form action="{{ route('purchase-return.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="purchase_id" value="{{ $purchase->id }}">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group row">
                                <div class="col-md-4 mt-2">
                                    <label for="">Supplier Name</label>
                                </div>
                                <div class="col-md-8">
                                    <input type="hidden" name="supplier_id" value="{{ $purchase->contact_id }}">
                                    <input type="text" class="form-control" value="{{ $purchase->supplier->name }}"
                                        readonly>
                                    @error('supplier_id')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group row">
                                <div class="col-md-4 mt-2">
                                    <label for="">Return Date</label>
                                </div>
                                <div class="col-md-8">
                                    <input type="date" name="return_date" value="<?php echo date('Y-m-d'); ?>"
                                        class="form-control">
                                    @error('return_date')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group row">
                                <div class="col-md-4 mt-2">
                                    <label for="">Return Type</label>
                                </div>
                                <div class="col-md-8">
                                    <select name="return_type_id" id="" class="form-control select2" required>
                                        <option value="">select return type</option>
                                        @foreach ($purchase_return_types as $type)
                                            <option value="{{ $type->id }}">{{ $type->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('return_type_id')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">

                            @php
                                $purchaseProducts = collect($purchase->purchaseProducts);
                                $regularProducts = $purchaseProducts->whereNull('box_pattern_id');
                                $medicineProducts = $purchaseProducts->whereNotNull('box_pattern_id');
                            @endphp

                            @if ($regularProducts->count())
                                <div class="table-responsive mt-5">
                                    <table class="table table-bordered table-hover" id="purchase_entry_table">
                                        <thead>
                                            <tr>
                                                <th style="width: 30%" class="text-center">Product Name</th>
                                                <th style="width: 15%" class="text-center">Price</th>
                                                <th style="width: 20%" class="text-center">Purchase Quantity </th>
                                                <th style="width: 20%" class="text-center">Return Quantity </th>
                                                <th style="width: 15%" class="text-center">Return Subtotal</th>
                                            </tr>
                                        </thead>
                                        <tbody id="myTableData">
                                            @foreach ($regularProducts as $product)
                                                <tr>
                                                    <td>
                                                        {{ $product->product->product_name }}
                                                        <input type="hidden" value="{{ $product->id }}"
                                                            name="purchase_product_id[]">
                                                        <input type="hidden" value="{{ $product->product->id }}"
                                                            name="product_id[{{ $product->id }}]">
                                                    </td>
                                                    <td>
                                                        <input type="number" class="form-control"
                                                            oninput="returnPrice({{ $product->id }},{{ $product->quantity }})"
                                                            step="0.2" value="{{ $product->purchase_price }}"
                                                            id="price{{ $product->id }}"
                                                            name="purchase_price[{{ $product->id }}]"
                                                            style="height: 30px;margin-top: 6px;border-radius: 3px;width: 140px;text-align: right; border: 1px solid #b3afaf;">
                                                    </td>
                                                    <td class="text-center">
                                                        <span>{{ $product->quantity }}</span>
                                                        <input type="hidden" value="{{ $product->quantity }}"
                                                            id="purchaseQuantity{{ $product->id }}"
                                                            name="purchase_quantity[{{ $product->id }}]">
                                                    </td>
                                                    <td>
                                                        <input type="number" step="0.1" min="0"
                                                            max="{{ $product->quantity }}"
                                                            data-stock_quantity="{{ getCurrentStockQuantityFromProduct($product->product) }}"
                                                            oninput="returnQuantity({{ $product->id }},{{ $product->quantity }})"
                                                            class="returnqty" data-return_id="{{ $product->id }}"
                                                            id="return_quantity{{ $product->id }}" value="0"
                                                            name="return_quantity[{{ $product->id }}]"
                                                            class="form-control"
                                                            style="height: 30px;margin-top: 6px;border-radius: 3px;width: 140px;text-align: right; border: 1px solid #b3afaf;">
                                                    </td>
                                                    <td>
                                                        <span id="returnTotal{{ $product->id }}">0</span>
                                                        <input type="hidden" name="total[{{ $product->id }}]"
                                                            class="returnprice returnTotalPice{{ $product->id }}"
                                                            value="0">
                                                    </td>
                                                </tr>
                                            @endforeach

                                        <tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="3" style="width: 65%"></td>
                                                <td colspan="" style="width: 20%"><b>Purchse Return Amount:</b></td>
                                                <td colspan="" style="width: 15%">
                                                    <input type="text"
                                                        style="height: 30px;margin-top: 6px;border-radius: 3px;width: 140px;text-align: right; border: 1px solid #b3afaf;"
                                                        name="sale_return_amount" value="0" readonly>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="3" style="width: 65%"></td>
                                                <td colspan="" style="width: 20%"><b>Receive Return Amount:</b></td>
                                                <td colspan="" style="width: 15%">
                                                    <input type="text"
                                                        style="height: 30px;margin-top: 6px;border-radius: 3px;width: 140px;text-align: right; border: 1px solid #b3afaf;"
                                                        name="pay_return_amount" value="0">
                                                </td>
                                            </tr>

                                            <tr>
                                                <td colspan="3" style="width: 65%"></td>
                                                <td colspan="" style="width: 20%"><b>Receive By:</b></td>
                                                <td colspan="" style="width: 15%">
                                                    <select name="pay_by" id="pay_by"
                                                        style="height: 30px;margin-top: 6px;border-radius: 3px;width: 140px;text-align: right; border: 1px solid #b3afaf;">
                                                        <option value="Cash" selected>Cash</option>
                                                        <option value="Mobile Banking">Mobile Banking</option>
                                                        <option value="Card">Card</option>
                                                        <option value="Bank Account">Bank Account</option>
                                                    </select>
                                                </td>
                                            </tr>

                                            <tr id="account_list">

                                            </tr>

                                        </tfoot>

                                    </table>

                                </div>
                            @endif


                            @if ($medicineProducts->count())
                                <div class="table-responsive mt-5">
                                    <table class="table table-bordered table-hover" id="purchase_entry_table">
                                        <thead>
                                            <tr>
                                                <th style="width: 30%" class="text-center">Medicine Product</th>
                                                <th style="width: 15%" class="text-center">Price</th>
                                                <th style="width: 20%" class="text-center">Box Pattern</th>
                                                <th style="width: 20%" class="text-center">Box Qty</th>
                                                <th style="width: 20%" class="text-center">Quantity </th>
                                                <th style="width: 15%" class="text-center">Return Subtotal</th>
                                            </tr>
                                        </thead>
                                        <tbody id="myTableData">
                                            @foreach ($medicineProducts as $product)
                                                <tr>
                                                    <td>
                                                        {{ $product->product->product_name }}
                                                        <input type="hidden" value="{{ $product->id }}"
                                                            name="purchase_product_id[]">
                                                        <input type="hidden" value="{{ $product->product->id }}"
                                                            name="product_id[{{ $product->id }}]">
                                                    </td>
                                                    <td>
                                                        <input type="number"
                                                            oninput="returnPrice({{ $product->id }},{{ $product->quantity }})"
                                                            step="0.2" value="{{ $product->purchase_price }}"
                                                            id="price{{ $product->id }}"
                                                            name="purchase_price[{{ $product->id }}]"
                                                            style="height: 30px;margin-top: 6px;border-radius: 3px;width: 140px;text-align: right; border: 1px solid #b3afaf;">
                                                    </td>
                                                    <td>
                                                        {{ $product->boxPattern->name . ' (' . $product->boxPattern->quantity . ')' }}
                                                    </td>
                                                    <td class="text-center">
                                                        <span>{{ $product->quantity }}</span>
                                                        <input type="hidden" value="{{ $product->quantity }}"
                                                            id="purchaseQuantity{{ $product->id }}"
                                                            name="purchase_quantity[{{ $product->id }}]">
                                                    </td>
                                                    <td>
                                                        <input type="number" step="0.1" min="0"
                                                            max="{{ $product->quantity }}"
                                                            data-stock_quantity="{{ getCurrentStockQuantityFromProduct($product->product) }}"
                                                            oninput="returnQuantity({{ $product->id }},{{ $product->quantity }})"
                                                            class="returnqty" data-return_id="{{ $product->id }}"
                                                            id="return_quantity{{ $product->id }}" value="0"
                                                            name="return_quantity[{{ $product->id }}]"
                                                            style="height: 30px;margin-top: 6px;border-radius: 3px;width: 140px;text-align: right; border: 1px solid #b3afaf;">
                                                    </td>
                                                    <td>
                                                        <span id="returnTotal{{ $product->id }}">0</span>
                                                        <input type="hidden" name="total[{{ $product->id }}]"
                                                            class="returnprice returnTotalPice{{ $product->id }}"
                                                            value="0">
                                                    </td>
                                                </tr>
                                            @endforeach

                                        <tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="3" style="width: 65%"></td>
                                                <td colspan="" style="width: 20%"><b>Purchse Return Amount:</b></td>
                                                <td colspan="" style="width: 15%">
                                                    <input type="text"
                                                        style="height: 30px;margin-top: 6px;border-radius: 3px;width: 140px;text-align: right; border: 1px solid #b3afaf;"
                                                        name="sale_return_amount" value="0" readonly>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="3" style="width: 65%"></td>
                                                <td colspan="" style="width: 20%"><b>Receive Return Amount:</b></td>
                                                <td colspan="" style="width: 15%">
                                                    <input type="text"
                                                        style="height: 30px;margin-top: 6px;border-radius: 3px;width: 140px;text-align: right; border: 1px solid #b3afaf;"
                                                        name="pay_return_amount" class="form-control" value="0">
                                                </td>
                                            </tr>

                                            <tr>
                                                <td colspan="3" style="width: 65%"></td>
                                                <td colspan="" style="width: 20%"><b>Receive By:</b></td>
                                                <td colspan="" style="width: 15%">
                                                    <select name="pay_by" id="pay_by"
                                                        style="height: 30px;margin-top: 6px;border-radius: 3px;width: 140px;text-align: right; border: 1px solid #b3afaf;">
                                                        <option value="Cash" selected>Cash</option>
                                                        <option value="Mobile Banking">Mobile Banking</option>
                                                        <option value="Card">Card</option>
                                                        <option value="Bank Account">Bank Account</option>
                                                    </select>
                                                </td>
                                            </tr>

                                            <tr id="account_list">

                                            </tr>

                                        </tfoot>

                                    </table>

                                </div>
                            @endif

                            <div class="text-right mt-4">
                                <button type="submit" class="btn btn-success">Save</button>
                            </div>
                        </div>
                </form>

            </div>
        </div>
    </div>


@endsection
@section('script')
    <script>
        const TOKEN = "{{ csrf_token() }}";
        const URL_PAYMENT_ACCOUNT = "{{ route('returnsale.payment.account') }}";
    </script>
    <script src="{{ asset('js/purchase-return.js') }}?v=0.4"></script>
@endsection

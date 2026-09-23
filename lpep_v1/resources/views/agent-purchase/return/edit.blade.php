@extends('layouts.dashboard')
@section('title', 'Edit Purchase Return')
@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="card-box table-responsive mt-4" style="border-top: 3px solid #2aa9b9;">
                <div class="row">
                    <div class="col-md-6">
                        <h4 class="m-t-0 header-title mb-5"><b>Edit Purchase Return</b></h4>
                    </div>
                </div>

                <form action="{{ route('purchase-return.update', $purchaseReturn->id) }}" method="POST">
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
                                    <input type="date" name="return_date" value="{{ $purchaseReturn->date }}"
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
                                            <option value="{{ $type->id }}"
                                                {{ $type->id == $purchaseReturn->purchase_return_type_id ? 'selected' : '' }}>
                                                {{ $type->name }}</option>
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

                            <div class="table-responsive mt-5">
                                <table class="table table-bordered table-hover" id="purchase_entry_table">
                                    <thead>
                                        <tr>
                                            <th style="width: 30%" class="text-center">Product Name (Model) </th>
                                            <th style="width: 15%" class="text-center">Price</th>
                                            <th style="width: 20%" class="text-center">Purchase Quantity </th>
                                            <th style="width: 20%" class="text-center">Return Quantity </th>
                                            <th style="width: 15%" class="text-center">Return Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody id="myTableData">
                                        @foreach ($purchase->purchaseProducts as $product)
                                            @if ($product->models->count())
                                                @foreach ($product->models as $item)
                                                    <tr style="background: {{ !$item->is_available ? '#fad9d9' : '' }}">
                                                        <td>
                                                            {{ $product->product->product_name }}
                                                            ({{ $item->model_no }})
                                                            <input type="hidden" value="{{ $product->id }}"
                                                                name="purchase_product_id[]">
                                                            <input type="hidden" value="{{ $product->product->id }}"
                                                                name="product_id[{{ $product->id }}]">
                                                            <input type="hidden" value="{{ $item->id }}"
                                                                name="product_model_id[{{ $product->id }}]">
                                                        </td>
                                                        <td>
                                                            <input type="number"
                                                                oninput="returnPrice({{ $product->id }},1,{{ $item->id }})"
                                                                step="0.2" value="{{ $product->purchase_price }}"
                                                                id="price{{ $product->id }}-{{ $item->id }}"
                                                                name="purchase_price[{{ $product->id }}]"
                                                                {{ $item->is_available == false ? 'readonly' : '' }}
                                                                style="height: 30px;margin-top: 6px;border-radius: 3px;width: 140px;text-align: right; border: 1px solid #b3afaf;">
                                                        </td>
                                                        <td class="text-center">
                                                            <span>1</span>
                                                            <input type="hidden" value="1"
                                                                id="purchaseQuantity{{ $product->id }}"
                                                                name="purchase_quantity[{{ $product->id }}]">
                                                        </td>
                                                        <td>

                                                            <input type="number"
                                                                onchange="returnQuantity({{ $product->id }},1,{{ $item->id }})"
                                                                step="0.1" min="0" max="1"
                                                                value="{{ $item->is_available ? optional($item->purchaseReturnProduct)->quantity ?? 0 : 0 }}"
                                                                class="returnqty"
                                                                {{ $item->is_available == false ? 'readonly' : '' }}
                                                                data-return_id="{{ $product->id }}"
                                                                id="return_quantity{{ $product->id }}-{{ $item->id }}"
                                                                name="return_quantity[{{ $product->id }}-{{ $item->id }}]"
                                                                style="height: 30px;margin-top: 6px;border-radius: 3px;width: 140px;text-align: right; border: 1px solid #b3afaf;">
                                                        </td>
                                                        <span>
                                                            <span
                                                                id="returnTotal{{ $product->id }}-{{ $item->id }}">{{ optional($item->purchaseReturnProduct)->total ?? '0' }}</span>
                                                            <input type="hidden"
                                                                name="total[{{ $product->id }}-{{ $item->id }}]"
                                                                class="returnprice returnTotalPice{{ $product->id }}-{{ $item->id }}"
                                                                value="{{ optional($item->purchaseReturnProduct)->total ?? 0 }}">
                                                            </td>
                                                    </tr>
                                                @endforeach
                                            @else
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
                                                    <td class="text-center">
                                                        <span>{{ $product->quantity }}</span>
                                                        <input type="hidden" value="{{ $product->quantity }}"
                                                            id="purchaseQuantity{{ $product->id }}"
                                                            name="purchase_quantity[{{ $product->id }}]">
                                                    </td>
                                                    <td>
                                                        <input type="number" step="0.1" min="0"
                                                            max="{{ $product->quantity }}"
                                                            data-stock_quantity="{{ getCurrentStockQuantityFromProduc($product) }}"
                                                            oninput="returnQuantity({{ $product->id }},{{ $product->quantity }})"
                                                            class="returnqty" data-return_id="{{ $product->id }}"
                                                            id="return_quantity{{ $product->id }}"
                                                            value="{{ optional($product->purchaseReturnProduct)->quantity ?? 0 }}"
                                                            name="return_quantity[{{ $product->id }}]"
                                                            style="height: 30px;margin-top: 6px;border-radius: 3px;width: 140px;text-align: right; border: 1px solid #b3afaf;">
                                                    </td>
                                                    <td>
                                                        <span
                                                            id="returnTotal{{ $product->id }}">{{ optional($product->purchaseReturnProduct)->total ?? '0' }}</span>
                                                        <input type="hidden" name="total[{{ $product->id }}]"
                                                            class="returnprice returnTotalPice{{ $product->id }}"
                                                            value="{{ optional($product->purchaseReturnProduct)->total ?? 0 }}">
                                                    </td>
                                                </tr>
                                            @endif
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
                                                    name="pay_return_amount"
                                                    value="{{ $purchaseReturn->contactPayment->paying_amount }}">
                                            </td>
                                        </tr>

                                        <tr>
                                            <td colspan="3" style="width: 65%"></td>
                                            <td colspan="" style="width: 20%"><b>Receive By:</b></td>
                                            <td colspan="" style="width: 15%">
                                                <select name="pay_by" id="pay_by"
                                                    style="height: 30px;margin-top: 6px;border-radius: 3px;width: 140px;text-align: right; border: 1px solid #b3afaf;">
                                                    <option value="Cash"
                                                        {{ $purchaseReturn->contactPayment->pay_by == 'Cash' ? 'selected' : '' }}>
                                                        Cash</option>
                                                    <option value="Mobile Banking"
                                                        {{ $purchaseReturn->contactPayment->pay_by == 'Mobile Banking' ? 'selected' : '' }}>
                                                        Mobile Banking</option>
                                                    <option value="Card"
                                                        {{ $purchaseReturn->contactPayment->pay_by == 'Card' ? 'selected' : '' }}>
                                                        Card</option>
                                                    <option value="Bank Account"
                                                        {{ $purchaseReturn->contactPayment->pay_by == 'Bank Account' ? 'selected' : '' }}>
                                                        Bank Account</option>
                                                </select>
                                            </td>
                                        </tr>
                                        <tr id="account_list">

                                        </tr>
                                    </tfoot>

                                </table>

                            </div>

                            <div class="text-right mt-4">
                                <button type="submit" class="btn btn-success">Update</button>
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
        const IS_RETURN_LIST_CALL = false;
        const URL_PAYMENT_ACCOUNT = "{{ route('returnsale.payment.account') }}";
        const SELECTED_ACCOUNT = "{{ $purchaseReturn->contactPayment->account_id }}";
    </script>
    <script src="{{ asset('js/purchase-return.js') }}?v=0.4"></script>
    <script>
        total();
        account("{{ $purchaseReturn->contactPayment->pay_by }}", SELECTED_ACCOUNT);
    </script>
@endsection

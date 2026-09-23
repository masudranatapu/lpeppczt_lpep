@extends('layouts.dashboard')

@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="card-box table-responsive mt-4" style="border-top: 3px solid #2aa9b9;">
                <div class="row">
                    <div class="col-md-6">
                        <h4 class="m-t-0 header-title mb-5"><b>Edit Return Sale</b></h4>
                    </div>
                </div>

                <form action="{{ route('return.sale.update') }}" method="POST">
                    @csrf
                    <input type="hidden" name="return_sale_id" value="{{ $return->id }}">
                    <input type="hidden" name="sale_id" value="{{ $return->sale_id }}">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row">
                                <div class="col-md-4">
                                    <label for="">Customer Name</label>
                                </div>
                                <div class="col-md-6">
                                    <input type="hidden" name="customer_id" value="{{ $return->contact_id }}">
                                    <input type="text" class="form-control" value="{{ $return->customer->name }}"
                                        readonly>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group row">
                                <div class="col-md-4">
                                    <label for="">Invoice No</label>
                                </div>
                                <div class="col-md-6">
                                    <input type="text" name="invoice_no" value="{{ date('Y') . $return->sale_id }}"
                                        class="form-control" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group row">
                                <div class="col-md-4">
                                    <label for="">Sale Date</label>
                                </div>
                                <div class="col-md-6">
                                    <input type="date" name="sale_date" value="{{ $return->sale->sale_date }}"
                                        class="form-control" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group row">
                                <div class="col-md-4">
                                    <label for="">Return Date</label>
                                </div>
                                <div class="col-md-6">
                                    <input type="date" name="return_date" value="{{ $return->return_date }}"
                                        class="form-control">
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
                                            <th style="width: 30%" class="text-center">Product Name </th>
                                            <th style="width: 15%" class="text-center"> Unit Price </th>
                                            <th style="width: 20%" class="text-center">Sell Quantity </th>
                                            <th style="width: 20%" class="text-center">Return Quantity </th>
                                            <th style="width: 15%" class="text-center">Return Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody id="myTableData">
                                        @foreach ($return->returnProducts as $product)
                                            <tr>
                                                <td>
                                                    {{ $product->product->product_name }}
                                                    <input type="hidden" value="{{ $product->id }}"
                                                        name="return_product[]">
                                                    <input type="hidden" value="{{ $product->product_model_id }}"
                                                        name="product_model_id[{{ $product->id }}]">
                                                </td>
                                                <td>
                                                    <span>{{ $product->price }}</span>
                                                    <input type="hidden" value="{{ $product->price }}"
                                                        id="unitPrice{{ $product->id }}"
                                                        name="product_price[{{ $product->id }}]">
                                                </td>
                                                <td>
                                                    @php
                                                        $sale_product_qty = \App\Models\SaleProduct::where('sale_id', $return->sale_id)
                                                            ->where('product_id', $product->product->id)
                                                            ->first();
                                                    @endphp
                                                    <span>{{ $sale_product_qty->qty }}</span>
                                                    <input type="hidden" value="{{ $sale_product_qty->qty }}"
                                                        id="saleQty{{ $product->id }}"
                                                        name="saleQty[{{ $product->id }}]">
                                                </td>
                                                <td>
                                                    <input type="text"
                                                        style="height: 30px;margin-top: 6px;border-radius: 3px;width: 140px;text-align: right; border: 1px solid #b3afaf;"
                                                        class="returnqty" data-sale_id="{{ $product->id }}"
                                                        id="returnQty{{ $product->id }}" value="{{ $product->qty }}"
                                                        name="return_qty[{{ $product->id }}]">
                                                </td>
                                                <td>
                                                    <span
                                                        id="returnTotal{{ $product->id }}">{{ $product->total_price }}</span>
                                                    <input type="hidden" name="returnTotalPice[{{ $product->id }}]"
                                                        class="returnprice returnTotalPice{{ $product->id }}"
                                                        value="{{ $product->total_price }}">
                                                </td>
                                            </tr>
                                        @endforeach

                                    <tbody>

                                    </tbody>
                                    <tfoot>

                                        <tr>
                                            <td colspan="3"></td>
                                            <td colspan=""><b>Sale Return Amount:</b></td>
                                            <td colspan="">
                                                <input type="text"
                                                    style="height: 30px;margin-top: 6px;border-radius: 3px;width: 140px;text-align: right; border: 1px solid #b3afaf;"
                                                    name="sale_return_amount" value="{{ $return->total_amount }}"
                                                    readonly>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="3"></td>
                                            <td colspan=""><b>Pay Return Amount:</b></td>
                                            <td colspan="">
                                                <input type="text"
                                                    style="height: 30px;margin-top: 6px;border-radius: 3px;width: 140px;text-align: right; border: 1px solid #b3afaf;"
                                                    name="pay_return_amount" value="{{ $return->paying_amount }}">
                                            </td>
                                        </tr>

                                        <tr>
                                            <td colspan="3"></td>
                                            <td colspan=""><b>Pay By:</b></td>
                                            <td colspan="">
                                                @php
                                                    $retrun_payment = \App\Models\ContactPayment::where('sale_return_id', $return->id)->first();
                                                @endphp
                                                <input type="hidden" name="contact_payment_id"
                                                    value="{{ $retrun_payment->id }}">
                                                <select name="pay_by" id=""
                                                    style="height: 30px;margin-top: 6px;border-radius: 3px;width: 140px;text-align: right; border: 1px solid #b3afaf;">
                                                    <option value="cash"
                                                        {{ $retrun_payment->pay_by == 'cash' ? 'selected' : '' }}>Cash
                                                    </option>
                                                    <option value="bKash"
                                                        {{ $retrun_payment->pay_by == 'bKash' ? 'selected' : '' }}>bKash
                                                    </option>
                                                    <option value="rocket"
                                                        {{ $retrun_payment->pay_by == 'rocket' ? 'selected' : '' }}>
                                                        Rocket
                                                    </option>
                                                    <option value="nagad"
                                                        {{ $retrun_payment->pay_by == 'nagad' ? 'selected' : '' }}>Nagad
                                                    </option>
                                                    <option value="card"
                                                        {{ $retrun_payment->pay_by == 'card' ? 'selected' : '' }}>Card
                                                    </option>
                                                    <option value="bank"
                                                        {{ $retrun_payment->pay_by == 'bank' ? 'selected' : '' }}>Bank
                                                    </option>
                                                </select>
                                            </td>
                                        </tr>

                                    </tfoot>

                                </table>

                            </div>

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
        $('body').on('change', ".returnqty", function() {
            let data_id = $(this).data('sale_id')
            let return_qty = $(`#returnQty${data_id}`).val()
            let qty = $(`#saleQty${data_id}`).val()
            // alert(qty)
            let unit_price = $(`#unitPrice${data_id}`).val()

            if (qty < return_qty) {
                toastr.error("Return Quantity Must Be Smaller Or Same Than Sell Quantity")
                $(this).val('')
            } else {
                $(`#returnTotal${data_id}`).text(unit_price * return_qty)
                $(`.returnTotalPice${data_id}`).val(unit_price * return_qty)
                total()
            }
        })

        function total() {
            var sum = 0;
            $('.returnprice').each(function() {
                var price = $(this);
                sum += parseInt(price.val());
            });
            $("input[name=sale_return_amount]").val(sum)
        }
    </script>
@endsection

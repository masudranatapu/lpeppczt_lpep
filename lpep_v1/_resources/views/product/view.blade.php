<div class="modal-body">

    <div class="row">
        <div class="col-sm-9 col-md-9">
            <div class="row">
                <div class="col-sm-6 invoice-col">
                    <b>Barcode:</b>{{ $product->barcode }}<br>
                    <b>Unit: </b>{{ $product->unit->actual_name }} ({{ $product->unit->short_name }})<br>
                    <b>{{ __('page.user_log.0') }}: </b>{{ optional($product->createdBy)->name }} <br>
                    <b>{{ __('page.user_log.1') }}: </b>{{ $product->created_at?->format('Y-m-d H:i:s') }}
                </div>

                <div class="col-sm-6 invoice-col">
                    <b>Brand: </b>{{ isset($product->brand->brand_name) ? $product->brand->brand_name : 'Null' }}<br>
                    <b>Category:
                    </b>{{ isset($product->category->category_name) ? $product->category->category_name : 'Null' }}<br>
                    @if (!isRole(ROLE_AGENT))
                        <b>Alert quantity: </b>{{ $product->alert_quantity }} <br>
                    @endif
                    <b>{{ __('page.user_log.2') }}: </b>{{ optional($product->updatedBy)->name }} <br>
                    <b>{{ __('page.user_log.3') }}: </b>{{ $product->updated_at?->format('Y-m-d H:i:s') }}
                </div>
                <div class="clearfix"></div>
                <br>
                <div class="col-sm-12">
                </div>
            </div>
        </div>

        <div class="col-sm-3 col-md-3">
            <div class="thumbnail">
                <img src="{{ checkImage($product->image) }}" class="img-fluid" alt="Product image">
            </div>
        </div>
    </div>

    <br>
    <br>

    <div class="row">
        <div class="col-md-12">
            <strong>Product Stock Details</strong>
        </div>
        <div class="col-md-12" id="view_product_stock_details" data-product_id="82">
            <div class="row">
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table class="table table-condensed bg-gray">
                            <thead>
                                <tr class="bg-success">
                                    <th>Per Unit Price</th>
                                    <th>Current stock</th>
                                    <th>Total unit sold</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>{{ $product->discount_selling_price }}</td>
                                    <td>{{ getCurrentStockQuantityFromProduct($product) }}</td>
                                    <td>{{ '0' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default no-print" data-dismiss="modal">Close</button>
</div>

@foreach ($sellCarts ?? [] as $item)
    @php
        $collection = collect([]);
            collect($item->product->purchaseProduct)
                ->sortByDesc('expiry_date')
                ->each(function($item) use($collection) {
                    $collection->push([
                    'id' => $item->id,
                    'type' => "purchase",
                    'available_quantity' => $item->available_quantity,
                    'batch_id' => $item->batch_id
                ]);
            });
            collect($item->product->agentStockTransferDetails)
                ->each(function($item) use($collection) {
                    $collection->push([
                    'id' => $item->id,
                    'type' => "stockTransfer",
                    'available_quantity' => $item->available_quantity,
                    'batch_id' => $item->agentPurchaseProduct->batch_id
                ]);

            });

        $maxQuantity = $collection->sum('available_quantity') + 0;
    @endphp

    <tr data-sc-id="{{ $item->id }}">
        <td style="padding:4px 0px; line-height:2; margin:0px; font-size: 13px;font-weight:500;padding-left:10px;">
            {{ $item->product->product_name }} ({{ $item->product->barcode }})
        </td>

        <td class="text-center" class="tx-right tx-medium tx-inverse td-style">
            @if ($collection->count())
                <select class="form-control batch_id" name="batch_id" style="height: 25px; padding: 0px" required>
                    @foreach ($collection as  $purchaseProduct)
                        @php
                            $purchaseProduct = (object) $purchaseProduct;
                        @endphp
                        <option value="{{ $purchaseProduct->batch_id }}"
                            data-quantity="{{ $purchaseProduct->available_quantity }}"
                            data-type="{{ $purchaseProduct->type }}"
                            data-purchase-product-id="{{ $purchaseProduct->id }}"
                            {{ $purchaseProduct->batch_id == $item->batch_id || (!$item->batch_id && $loop->iteration == 1) ? 'selected' : '' }}>
                            {{ $purchaseProduct->batch_id }} ({{ $purchaseProduct->available_quantity }})
                        </option>
                    @endforeach
                </select>
            @else
                <span style="color: red">Batch Not Found!</span>
            @endif
        </td>

        <td class="text-center" class="tx-right tx-medium tx-inverse td-style">
            <div class="cart-info quantity" style="display: flex">
                <div class="btn-decrement" onClick="decrementQuantity({{ $item->id }})">-</div>
                <input class="input-quantity qty" id="inputQuantity-{{ $item->id }}"
                    data-is-decimal="{{ $item->product->unit->is_decimal }}" onchange="changeQuantity(this)"
                    value="{{ $item->qty }}" data-max="{{ $maxQuantity }}" data-id="{{ $item->id }}"
                    autocomplete="off">
                <div class="btn-increment" onClick="incrementQuantity({{ $item->id }})">+</div>
            </div>
        </td>

        <td class="text-center" class="tx-right tx-medium tx-inverse td-style" id="price-{{ $item->id }}">
            <input type="text" value="{{ $item->price }}" onchange="editPrice({{ $item->id }})"
                id="editPricing{{ $item->id }}" style="border: 0px;text-align: center;">
        </td>

        <td class="text-center" class="tx-right tx-medium tx-inverse td-style" id="total_price-{{ $item->id }}">
            {{ $item->total_price }} </td>

        <td class="text-center" class="tx-right tx-medium tx-danger td-style">
            <a onclick="removeCart({{ $item->id }})" style="border: none" class="text-danger"><i
                    class="fa fa-trash"></i></a>
        </td>
    </tr>
@endforeach

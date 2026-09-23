@foreach ($cartItems ?? [] as $item)
    @php
        // Keep only details that have a purchaseProduct (avoid nulls)
        $stockTransferDetails = collect($item->product->stockTransferDetails ?? [])
            ->filter(fn($d) => optional($d->purchaseProduct)->expiry_date)
            ->sortBy(function ($d) {
                // Sort by expiry date; push missing/invalid to the end
                return \Carbon\Carbon::parse(optional($d->purchaseProduct)->expiry_date ?? '2100-12-31');
            })
            ->values();

        // Pick the selected detail (or first available)
        $selectedDetail = $stockTransferDetails->firstWhere('id', $item->stock_transfer_detail_id)
            ?? $stockTransferDetails->first();

        $maxBatchQuantity = optional($selectedDetail)->available_quantity ?? 0;
    @endphp

    <tr data-sc-id="{{ $item->id }}">
        <td style="padding:4px 0; line-height:2; margin:0; font-size:13px; font-weight:500; padding-left:10px;">
            {{ $item->product->product_name }} ({{ $item->product->barcode }})
        </td>

        <td class="text-center tx-right tx-medium tx-inverse td-style">
            @if ($stockTransferDetails->count())
                <select class="form-control batch_id" name="stock_transfer_detail_id"
                        style="height:25px; padding:0" required>
                    @foreach ($stockTransferDetails as $detail)
                        @php
                            $pp = $detail->purchaseProduct; // may be null but filtered above
                        @endphp
                        <option value="{{ $detail->id }}"
                                data-quantity="{{ $detail->available_quantity }}"
                                data-purchase-product-id="{{ $pp?->id }}"
                                {{ ($detail->id == $item->stock_transfer_detail_id) || 
                                   (!$item->stock_transfer_detail_id && $loop->first) ? 'selected' : '' }}>
                            {{ $pp?->batch_id ?? 'N/A' }} ({{ $detail->available_quantity }})
                        </option>
                    @endforeach
                </select>
            @else
                <span style="color:red">Batch Not Found!</span>
            @endif
        </td>

        <td class="text-center tx-right tx-medium tx-inverse td-style">
            <div class="cart-info quantity" style="display:flex">
                <div class="btn-decrement" onClick="decrementQuantity({{ $item->id }})">-</div>
                <input class="input-quantity qty"
                       id="inputQuantity-{{ $item->id }}"
                       data-is-decimal="{{ $item->product->unit->is_decimal }}"
                       onchange="changeQuantity(this)"
                       value="{{ $item->qty }}"
                       data-max="{{ $maxBatchQuantity }}"
                       data-id="{{ $item->id }}"
                       autocomplete="off">
                <div class="btn-increment" onClick="incrementQuantity({{ $item->id }})">+</div>
            </div>
        </td>

        <td class="text-center tx-right tx-medium tx-inverse td-style" id="price-{{ $item->id }}">
            <input type="text" value="{{ $item->price }}"
                   onchange="editPrice({{ $item->id }})"
                   id="editPricing{{ $item->id }}"
                   style="border:0; text-align:center;">
        </td>

        <td class="text-center tx-right tx-medium tx-inverse td-style" id="total_price-{{ $item->id }}">
            {{ $item->total_price }}
        </td>

        <td class="text-center tx-right tx-medium tx-danger td-style">
            <a onclick="removeCart({{ $item->id }})" style="border:none" class="text-danger">
                <i class="fa fa-trash"></i>
            </a>
        </td>
    </tr>
@endforeach

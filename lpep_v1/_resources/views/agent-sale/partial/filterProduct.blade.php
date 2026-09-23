@foreach ($products as $product)
    @php
        $stockQuantity = getCurrentStockQuantityFromProduct($product);
    @endphp

    <div style="cursor: pointer;" class="col-md-3" @if ($stockQuantity > 0) onclick="product_add('{{ $product->id }}', '')" @endif>
        <div class="card text-center mb-3" style="height: 152px;display: block;cursor: pointer;">
            <img class="card-img-top img-fluid" src="{{ checkImage($product->image) }}" alt="Card image cap"
                id="product-picture" style="margin-top: 10px">
            <div class="card-body" style="padding-left: 10px; padding-right: 10px">
                <h6 style="margin-top: -4px;cursor: pointer;" class="text-center">
                    {{ \Str::limit($product->product_name, 12) }}
                </h6>
                @if ($stockQuantity > 0)
                    <span class="text-success"
                        style="cursor: pointer;font-size: 13px;">Available({{ $stockQuantity }})</span>
                @else
                    <span class="text-danger" style="cursor: pointer;">Stock Out</span>
                @endif
            </div>
        </div>
    </div>
@endforeach

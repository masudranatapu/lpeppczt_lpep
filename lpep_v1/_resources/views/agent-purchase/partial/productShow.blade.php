
<datalist id="colorList" >
    @foreach ($products as $product)
        <option class="selectProduct" data-id="{{ $product->id }}" value="{{ $product->product_name }}"></option>
    @endforeach
</datalist>
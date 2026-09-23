<datalist id="colorList">
    @foreach ($products as $product)
        <option value="{{ $product->product_name }}"></option>
    @endforeach
</datalist>
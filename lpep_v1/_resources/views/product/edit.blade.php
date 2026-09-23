@extends('layouts.dashboard')

@section('content')

    <div class="row">
        <div class="col-12">
            <div class="mt-4">
                <h4>Edit Product</h4>
            </div>

            <form action="{{ route('product.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" value="{{ $product->id }}" name="id">
                <div class="card-box table-responsive mt-4" style="border-top: 3px solid #67dd31;">
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="name">Product Name<i
                                        class="fa fa-info-circle text-info hover-q no-print"></i></label>
                                <input class="form-control" required placeholder="Product Name" name="name"
                                    value="{{ $product->product_name }}" type="text" id="name">
                            </div>
                        </div>

                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="sku">Barcode:</label> <i
                                    class="fa fa-info-circle text-info hover-q no-print "></i>
                                <input class="form-control" placeholder="Barcode" name="sku"
                                    value="{{ $product->barcode }}" type="text" id="barcode">
                                <span class="text-danger" id="error_barcode"></span>
                            </div>
                        </div>

                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="barcode_type">Unit<i class="fa fa-info-circle text-info hover-q no-print "
                                        aria-hidden="true" data-container="body" data-toggle="popover"
                                        data-placement="auto bottom"
                                        data-content="Locations where product will be available." data-html="true"
                                        data-trigger="hover"></i></label>
                                <select class="form-control select2" required name="unit_id">
                                    <option value="">select</option>
                                    @foreach ($units as $unit)
                                        <option value="{{ $unit->id }}"
                                            {{ $unit->id == $product->unit_id ? 'selected' : '' }}>
                                            {{ $unit->actual_name }}
                                            ({{ $unit->short_name }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-sm-4 ">
                            <div class="form-group">
                                <label for="category_id">Category<i
                                        class="fa fa-info-circle text-info hover-q no-print"></i></label>
                                <select class="form-control select2" id="category_id" name="category_id" required>
                                    <option selected="selected" value="">Please Select</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}"
                                            {{ $category->id == $product->category_id ? 'selected' : '' }}>
                                            {{ $category->category_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-sm-4 ">
                            <div class="form-group">
                                <label for="sub_category_id">{{ __('page.manufacturer') }}:</label>
                                <select class="form-control select2" id="brand_id" name="brand_id">
                                    <option selected="selected" value="">Please Select</option>
                                    @foreach ($brands as $brand)
                                        <option value="{{ $brand->id }}"
                                            {{ $brand->id == $product->brand_id ? 'selected' : '' }}>
                                            {{ $brand->brand_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="">Alert quantity</label>
                                <input type="number" name="alert_quantity" value="{{ $product->alert_quantity }}"
                                    class="form-control" id="alert_quantity" placeholder="Alert Quantity">
                            </div>
                        </div>


                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="">Selling Price <i
                                        class="fa fa-info-circle text-info hover-q no-print"></i></label>
                                <input type="text" name="selling_price" class="form-control" id="selling_price"
                                    value="{{ $product->selling_price }}" placeholder="Selling Price" required>
                            </div>
                        </div>

                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for=""> Picture </label>
                                <input type="file" name="picture" class="form-control" id="picture" placeholder="Pictue">
                            </div>
                        </div>

                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="vat_group_id">VAT/SD Group</label>
                                <select class="form-control" name="vat_group_id">
                                    <option value="">select</option>
                                    @foreach ($vat_groups as $vat)
                                        <option value="{{ $vat->id }}"
                                            {{ $vat->id == $product->vat_group_id ? 'selected' : '' }}>
                                            {{ $vat->vat_group_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="vat_group_id">Discount Type</label>
                                <select class="form-control" name="discount_type">
                                    <option value="1">Amount</option>
                                    <option value="2">Percenteage</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="other_price">Discount</label>
                                <input type="text" name="discount" value="{{ $product->discount_price }}"
                                    class="form-control" id="discount" placeholder="Discount">
                            </div>
                        </div>

                        <div class="col-sm-4 mt-4">
                            <div class="form-group mt-2">
                                <label for="is_medicine" style="user-select: none">
                                    <input id="is_medicine" type="checkbox" name="is_medicine"
                                        {{ $product->is_medicine ? 'checked' : '' }}>
                                    {{ __('page.productadd')[17] }}
                                </label>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label for=""> Product Description</label>
                                <textarea name="product_description" rows="1" class="form-control"
                                    id="">{{ $product->product_description }}</textarea>
                            </div>
                        </div>

                    </div>

                    <div class="text-right">
                        <div class="btn-group">
                            <button type="submit" class="btn btn-primary">Update</button>
                        </div>
                    </div>
                </div>

            </form>

        </div>
    </div>
    <!-- end row -->



@endsection
@section('script')
    <script>
        function validation(field_name) {
            let value = $(`#${field_name}`).val()
            let _token = "{{ csrf_token() }}"
            $.post("{{ route('product.validation') }}", {
                _token: _token,
                field_name: field_name,
                value: value
            }, function(data) {
                $(`#error_${field_name}`).text(data)
            })
        }
    </script>
@endsection

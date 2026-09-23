@extends('layouts.dashboard')
@section('title', '| Add Product')
@section('content')

    <div class="row">
        <div class="col-12">
            <div class="mt-4">
                <h4>{{ __('page.productadd')[0] }}</h4>
            </div>

            <form id="productFrom" action="{{ route('product.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="card-box table-responsive mt-4" style="border-top: 3px solid #67dd31;">
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="name">{{ __('page.productadd')[1] }}<i
                                        class="fa fa-info-circle text-info hover-q no-print " aria-hidden="true"
                                        data-container="body" data-toggle="popover" data-placement="auto bottom"
                                        data-html="true" data-trigger="hover"></i></label>
                                <input class="form-control" required placeholder="{{ __('page.productadd')[1] }}"
                                    name="name" type="text" id="name" autocomplete="off">
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="sku">{{ __('page.productadd')[2] }}:</label> <i
                                    class="fa fa-info-circle text-info hover-q no-print "></i>
                                <input class="form-control" value="{{ $barcode }}"
                                    placeholder="{{ __('page.productadd')[2] }}" name="sku" type="text" id="barcode">
                                <span class="text-danger" id="error_barcode"></span>
                            </div>
                        </div>

                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="barcode_type">{{ __('page.productadd')[3] }}<i
                                        class="fa fa-info-circle text-info hover-q no-print " aria-hidden="true"
                                        data-container="body" data-toggle="popover" data-placement="auto bottom"
                                        data-content="Locations where product will be available." data-html="true"
                                        data-trigger="hover"></i></label>
                                <select class="form-control select2" required name="unit_id">
                                    <option value="">select</option>
                                    @foreach ($units as $unit)
                                        <option value="{{ $unit->id }}">{{ $unit->actual_name }}
                                            ({{ $unit->short_name }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-sm-4 ">
                            <div class="form-group">
                                <label for="category_id">{{ __('page.productadd')[4] }}<i
                                        class="fa fa-info-circle text-info hover-q no-print"></i></label>
                                <select class="form-control select2" id="category_id" name="category_id" required>
                                    <option selected="selected" value="">Please Select</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->category_name }}</option>
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
                                        <option value="{{ $brand->id }}">{{ $brand->brand_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="">{{ __('page.productadd')[7] }}</label>
                                <input type="number" name="alert_quantity" class="form-control" id="alert_quantity"
                                    placeholder="Alert Quantity" autocomplete="off">
                            </div>
                        </div>


                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="">{{ __('page.productadd')[10] }} (BDT)<i
                                        class="fa fa-info-circle text-info hover-q no-print " aria-hidden="true"
                                        data-container="body" data-toggle="popover" data-placement="auto bottom"
                                        data-content="Locations where product will be available." data-html="true"
                                        data-trigger="hover"></i></label>
                                <input type="text" name="selling_price" class="form-control" id="selling_price"
                                    placeholder="Selling Price (BDT)" required autocomplete="off">
                            </div>
                        </div>

                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for=""> {{ __('page.productadd')[11] }} </label>
                                <input type="file" name="picture" class="form-control" id="selling_price"
                                    placeholder="Picture">
                            </div>
                        </div>

                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="vat_group_id">{{ __('page.productadd')[12] }}</label>
                                <select class="form-control" name="vat_group_id">
                                    <option value="">select</option>
                                    @foreach ($vat_groups as $vat)
                                        <option value="{{ $vat->id }}">{{ $vat->vat_group_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>


                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="vat_group_id">{{ __('page.productadd')[14] }}</label>
                                <select class="form-control" name="discount_type">
                                    <option value="1">Amount</option>
                                    <option value="2">Percenteage</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="other_price">{{ __('page.productadd')[13] }}</label>
                                <input type="text" name="discount" class="form-control" id="discount"
                                    placeholder="Discount" autocomplete="off">
                            </div>
                        </div>

                        <div class="col-sm-4 mt-4">
                            <div class="form-group mt-2">
                                <label for="is_medicine" style="user-select: none">
                                    <input id="is_medicine" type="checkbox" name="is_medicine" value="1">
                                    {{ __('page.productadd')[17] }}
                                </label>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label for=""> {{ __('page.productadd')[15] }}</label>
                                <textarea name="product_description" rows="1" class="form-control" id=""></textarea>
                            </div>
                        </div>

                    </div>

                    <div class="text-right">
                        <div class="btn-group">
                            <button type="submit" id="saveProduct"
                                class="btn btn-primary">{{ __('page.productadd')[16] }}</button>
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

        let shouldSubmit = false;
        $("#productFrom").on('submit', function(e) {
            if (!shouldSubmit) {
                console.log('o')
                e.preventDefault();
                shouldSubmit = true
                document.getElementById('productFrom').submit();
                $("#saveProduct").attr("disabled", true);
            }
        });
    </script>
@endsection

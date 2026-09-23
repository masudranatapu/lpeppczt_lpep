@extends('layouts.dashboard')

@section('content')

    <div class="row">
        <div class="col-12">
            <div class="mt-4">
                <h4>{{ __('page.printbarcode')[0] }}</h4>
            </div>

            <div class="card-box table-responsive mt-4" style="border-top: 3px solid #296bc2;">
                <div class="row">
                    <div class="col-md-8 offset-md-2">
                        <div class="form-group">
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-search"></i>
                                </span>
                                <input class="form-control" id="search_product"
                                    placeholder="{{ __('page.printbarcode')[1] }}" autofocus name="search_product"
                                    type="text">

                                <div id="showSearchProducts" style="display: none">

                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <form action="{{ route('barcode.print') }}" method="POST">
                    @csrf
                    <div class="row">

                        <div class="col-sm-12">
                            <div class="table-responsive">
                                <table class="table table-condensed table-bordered text-center table-striped"
                                    id="purchase_entry_table">
                                    <thead>
                                        <tr class="bg-success text-white">
                                            <th style="width: 50%;">{{ __('page.printbarcode')[2] }}</th>
                                            <th style="width: 40%;">{{ __('page.printbarcode')[3] }}</th>
                                            <th style="width: 10%;"><i class="fa fa-trash" aria-hidden="true"></i></th>
                                        </tr>
                                    </thead>
                                    <tbody id="mytab1">

                                    </tbody>
                                </table>
                            </div>
                            <hr />
                        </div>


                        <div class="form-group">
                            <div class="mt-3">
                                <label class="custom-control">
                                    <span class="custom-control-description"><b>Print : </b></span>
                                </label>
                                <label class="custom-control custom-checkbox">
                                    <input type="radio" class="custom-control-input" name="action" value="product_name"
                                        checked="">
                                    <span class="custom-control-indicator"></span>
                                    <span class="custom-control-description">Product Name</span>
                                </label>
                                <label class="custom-control custom-checkbox">
                                    <input type="radio" class="custom-control-input" name="action" value="category">
                                    <span class="custom-control-indicator"></span>
                                    <span class="custom-control-description">Category Name</span>
                                </label>
                            </div>
                        </div>

                    </div>

                    <div class="text-right">
                        <button type="submit" class="btn btn-primary">{{ __('page.printbarcode')[6] }}</button>
                    </div>
                </form>
            </div>


        </div>
    </div>
    <!-- end row -->



@endsection
@section('script')

    <script>
        let timer = null;
        let addedItemList = [];

        $("#search_product").autocomplete({
            source: function(request, response) {
                $.ajax({
                    url: "/autocomplete/barcode",
                    dataType: "json",
                    data: {
                        q: request.term
                    },
                    success: function(data) {
                        response($.map(data, function(item) {
                            if (data.length == 1) {
                                if (!addedItemList.filter(i => i.product_id == item
                                        .product_id).length) {
                                    addRow(item)
                                    addedItemList.push({
                                        product_id: item.product_id,
                                    });
                                }
                            } else {
                                return {
                                    label: item.product_name,
                                    value: item.barcode,
                                    product_id: item.product_id,
                                    product_name: item.product_name,
                                };
                            }

                        }));
                    }
                });
            },
            minLength: 3,
            select: function(event, ui) {
                const item = ui.item;
                if (!addedItemList.filter(i => i.product_id == item.product_id).length) {
                    addRow(item)
                    addedItemList.push({
                        product_id: item.product_id,
                    });
                }
                return false;
            },
            open: function() {
                $(this).removeClass("ui-corner-all").addClass("ui-corner-top");
            },
            close: function() {
                $(this).removeClass("ui-corner-top").addClass("ui-corner-all");
            }
        });

        function addRow(item) {
            var markup = `<tr>
                        <td style="padding:0px; margin:0px; vertical-align: middle">
                            <input type="hidden" value="${item.product_id}" class="product_id" name="product_id[]">
                            ${item.product_name}
                        </td>
                        <td style="padding:0px; margin:0px; vertical-align: middle">
                            <input style="width: 150px; margin: auto;height:25px; text-align: center;" type="text" class="form-control qty" value="1" name="qty[]">
                        </td>
                        <td style="padding:0px; margin:0px; vertical-align: middle">
                            <button id="DeleteButton" type="button" class="btn btn-sm btn-danger"><i class="fa fa-trash" aria-hidden="true"></i></button>
                        </td>
                    </tr>`
            $("#search_product").val('')
            $("#mytab1").append(markup);
        }


        $("#purchase_entry_table").on("click", "#DeleteButton", function() {
            const tr = $(this).closest("tr");
            const productId = tr.find('input.product_id').val();
            addedItemList = addedItemList.filter(i => i.product_id == productId);
            tr.remove();
        });
    </script>

@endsection

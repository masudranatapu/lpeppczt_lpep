@extends('layouts.dashboard')
@section('title', 'Products')
@section('content')
    <style>
        .sticky-table-header.fixed-solution {
            height: 41px !important;
        }

    </style>
    <div class="row">
        <div class="col-12">
            <div class="card-box table-responsive mt-4" style="border-top: 3px solid #2aa9b9;">
                <div class="row">
                    <div class="col-md-6">
                        <h4 class="m-t-0 header-title mb-5"><b>{{ __('page.product')[0] }}</b></h4>
                    </div>
                    <div class="col-md-6 text-right">
                        @if (permission('p2'))
                            <a href="{{ route('product.create') }}" class="btn btn-primary waves-effect waves-light m-b-5"
                                id="addNew"> <i class="fa fa-plus-square m-r-5"></i>
                                <span>{{ __('page.product')[1] }}</span> </a>
                        @endif
                    </div>
                </div>

                <div class="table-rep-plugin">
                    <div class="table-responsive" id="tablefixed">
                        <table id="data-table" class="table table-bordered table-hover mt-0" cellspacing="0" width="100%">
                            <thead class="theme-primary text-white">
                                <tr>
                                    <th>{{ __('page.product')[2] }}</th>
                                    {{-- <th>{{ __('page.product')[3] }}</th> --}}
                                    <th style="width: 10%;">{{ __('page.product_name') }}</th>
                                    {{-- <th>{{ __('page.product')[4] }}</th> --}}
                                    <th>{{ __('page.product')[7] }}</th>
                                    <th>{{ __('page.product')[8] }}</th>
                                    <th>{{ __('page.product')[9] }}</th>
                                    <th>{{ __('page.product')[10] }}</th>
                                    <th>{{ __('page.product')[11] }}</th>
                                    <th>{{ __('page.product')[12] }}</th>
                                    <th>{{ __('page.product')[13] }}</th>
                                    <th>{{ __('page.product')[14] }}</th>
                                </tr>
                            </thead>
                            <tbody>



                            </tbody>
                        </table>
                    </div>
                </div>
                {{-- <div>
                {{ $products->appends(request()->input())->links() }}
            </div> --}}

            </div>
        </div>
    </div>
    <!-- end row -->


    <!-- Business Modal Start -->
    <div class="modal fade bd-example-modal-xl" id="unitModal" tabindex="-1" role="dialog"
        aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" style="width: 100%">
            <div class="modal-content" id="modalcontent">

            </div>
        </div>
    </div>
    <!-- Business Modal End -->


    <div class="modal fade" id="barcodePrint" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content" id="barcodedata">

            </div>
        </div>
    </div>



@endsection

@section('script')
    <script>
        $(function() {
            $('#tablefixed').responsiveTable({
                addFocusBtn: false
            });
        });
    </script>
    <script>
        function view(id) {
            $.get(`/product/${id}/view`, function(data) {
                $('#modalcontent').html(data)
                $("#unitModal").modal('show')
            });
        }

        function printBarcode(id) {
            /* html */
            let modalBody = `
            <div class="modal-body">
                <h4 style="margin-top: -20px;">Print Barcode</h4>
                <form action="{{ route('barcode.print') }}" method="post" class="mt-4">
                    @csrf
                    <input type="hidden" value="${id}" name="product_id[]" required>
                    <div class="form-group">
                        <label>Barcode Quantity</label>
                        <input type="number" name="qty[]" class="form-control" value="1" required>
                    </div>
                    <div class="d-flex justify-content-start">
                        <div class="form-group">
                            <div class="mt-1">
                                <label class="custom-control">
                                    <span class="custom-control-description"><b>Print : </b></span>
                                </label>
                                <label class="custom-control custom-checkbox">
                                    <input type="radio" class="custom-control-input" name="action" value="product_name" checked="">
                                    <span class="custom-control-indicator"></span>
                                    <span class="custom-control-description">Product Name</span>
                                </label>
                                <label class="custom-control custom-checkbox">
                                    <input type="radio" class="custom-control-input" name="action" value="category" checked="">
                                    <span class="custom-control-indicator"></span>
                                    <span class="custom-control-description">Category Name</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="button" class="btn btn-sm btn-danger" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-sm btn-primary ml-2">Print</button>
                    </div>
                </form>
            </div>
            `
            $('#barcodedata').html(modalBody)
            $("#barcodePrint").modal('show')
        }

        $('#data-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('product.index') }}",
            columns: [{
                    "data": 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'product_name',
                    name: 'product_name'
                },
                {
                    data: 'discount_price',
                    name: 'discount_price'
                },
                {
                    data: 'discount_selling_price',
                    name: 'discount_selling_price'
                },
                {
                    data: 'category.category_name',
                    name: 'category.category_name'
                },
                {
                    data: 'brand',
                    name: 'brand'
                },
                {
                    data: 'barcode',
                    name: 'barcode'
                },
                {
                    data: 'vat',
                    name: 'vat'
                },
                {
                    data: 'status',
                    name: 'status'
                },

                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: true
                },
            ]
        });
    </script>

@endsection

@extends('layouts.dashboard')
@section('content')
    <link rel="stylesheet" href="/css/remove-number-arrows.css">
    <form action="{{ route('agent-stock-transfer.store') }}" method="post">
        @csrf
        <div class="row">
            <div class="col-md-12">

                <div class="card-box table-responsive mt-4">
                    <div class="row">
                        <div class="col-md-8 offset-md-2">

                            <div class="form-group">
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-search"></i>
                                    </span>

                                    <input class="form-control prevent" id="search_product"
                                        placeholder="{{ __('page.purchase')[7] }}" autofocus name="search_product"
                                        type="text">

                                    <div id="showSearchProducts" style="display: none">

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">

                        <div class="col-md-8 offset-md-2">
                            <div class="table-responsive">
                                <table class="table table-condensed table-bordered" id="purchase_entry_table">
                                    <thead>
                                        <tr class="theme-primary text-white">
                                            <th style="width: 36%;">{{ __('page.purchase')[8] }}</th>
                                            <th style="width: 27%;">{{ 'Stock Quantity' }}</th>
                                            <th style="width: 10%"><i class="fa fa-trash" aria-hidden="true"></i></th>
                                        </tr>
                                    </thead>
                                    <tbody id="mytab1">

                                    </tbody>
                                </table>
                            </div>
                            <button type="submit" class="btn btn-primary float-right" style="cursor: pointer">Transfer Stock</button>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@section('script')
    <script>
        const CSRF_TOKEN = "{{ csrf_token() }}";
        const ROUTE_TRANSFER_AUTOCOMPLETE = "{{ route('autocomplete.agent-transfer') }}";
        const PRODUCTS = [];
    </script>

    <script src="/js/agent-stock-transfer.js?v=0.3"></script>
@endsection

@extends('layouts.dashboard')
@section('title', 'Purchase Return List')
@section('content')

    <div class="row">
        <div class="col-12">
            <div class="card table-responsive mt-4" style="border-top: 3px solid #2aa9b9;">
                <div class="card-header">
                    <h4 class="header-title"><b>{{ __('sidebar.purchase_return_list') }}</b></h4>
                </div>
                <div class="card-body">
                    <table id="data-table" class="table table-striped table-bordered mt-4" cellspacing="0" width="100%">
                        <thead class="theme-primary text-white">
                            <tr>
                                <th> {{ __('page.sl') }} </th>
                                <th> {{ __('page.invoice') }} </th>
                                <th> {{ __('page.return_date') }} </th>
                                <th> {{ __('page.return_type_name') }} </th>
                                <th> {{ __('page.business') }} </th>
                                <th> {{ __('page.return_by') }} </th>
                                <th> {{ __('page.updated_by') }} </th>
                                <th>{{ __('page.action') }}</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- end row -->


@endsection
@section('script')
    <script>
        const TOKEN = "{{ csrf_token() }}";
        const IS_RETURN_LIST_CALL = true;
        const URL_RETURN_LIST = "{{ route('purchase-return.index') }}";
        const URL_PURCHASE_RETURN_DELETE = "{{ route('purchase-return.destroy') }}";
    </script>
<script src="{{ asset('js/purchase-return.js') }}?v=0.4" @endsection

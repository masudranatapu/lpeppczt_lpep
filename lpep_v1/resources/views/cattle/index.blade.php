@extends('layouts.dashboard')

@section('content')

    <div class="row">
        <div class="col-12">
            <div class="card-box table-responsive mt-4" style="border-top: 3px solid #2aa9b9;">
                <div class="row">
                    <div class="col-md-6">
                        <h4 class="m-t-0 header-title mb-5"><b>{{ __('page.cattle')[0] }}</b></h4>
                    </div>
                    <div class="col-md-6 text-right">
                        @if (permission('uni2'))
                            <button class="btn btn-primary waves-effect waves-light m-b-5" id="addNew"><i
                                    class="fa fa-plus-square m-r-5"></i> <span>{{ __('page.cattle')[1] }}</span>
                            </button>
                        @endif
                    </div>
                </div>

                <table id="data-table" class="table table-striped table-bordered mt-4" cellspacing="0" width="100%">
                    <thead class="theme-primary text-white">
                        <tr>
                            <th>{{ __('page.cattle')[2] }}</th>
                            <th>{{ __('page.cattle')[4] }}</th>
                            <th>{{ __('page.cattle')[5] }}</th>
                            <th>{{ __('page.cattle')[6] }}</th>
                            <th>{{ __('page.cattle')[20] }}</th>
                        </tr>
                    </thead>

                </table>
            </div>
        </div>
    </div>
    <!-- end row -->

    <!-- Modal Start -->
    <div class="modal fade bd-example-modal-xl" id="tableModal" role="dialog" aria-labelledby="myLargeModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" style="width: 100%">
            <div class="modal-content" id="modalContent" style="padding: 0px !important">

            </div>
        </div>
    </div>
    <!-- Modal End -->


@endsection
@section('script')
    <script>
        const CSRF_TOKEN = "{{ csrf_token() }}";
        const ROUTE_CATTLE_JSON_ALL = "{{ route('cattle.json.all') }}";
        const ROUTE_CATTLE_CREATE = "{{ route('cattle.create') }}";
        const ROUTE_CATTLE_STORE = "{{ route('cattle.store') }}";
        const ROUTE_CATTLE_UPDATE = "{{ route('cattle.update', '#id') }}";
        const ROUTE_CATTLE_EDIT = "{{ route('cattle.edit', '#id') }}";
        const ROUTE_CATTLE_DESTROY = "{{ route('cattle.destroy', '#id') }}";
        const ROUTE_FARMS_FIND_BY_CUSTOMER = "{{ route('farms.json.findByCustomer', '#id') }}";
    </script>

    <script src="/js/cattle.js?v=0.1"></script>
@endsection

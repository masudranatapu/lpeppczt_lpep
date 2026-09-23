@extends('layouts.dashboard')

@section('content')

    <div class="row">
        <div class="col-12">
            <div class="card-box table-responsive mt-4" style="border-top: 3px solid #2aa9b9;">
                <div class="row">
                    <div class="col-md-6">
                        <h4 class="m-t-0 header-title mb-5"><b>{{ __('page.calf')[0] }}</b></h4>
                    </div>
                    <div class="col-md-6 text-right">
                        @if (permission('uni2'))
                            <button class="btn btn-primary waves-effect waves-light m-b-5" id="addNew"><i
                                    class="fa fa-plus-square m-r-5"></i> <span>{{ __('page.calf')[1] }}</span>
                            </button>
                        @endif
                    </div>
                </div>

                <table id="data-table" class="table table-striped table-bordered mt-4" cellspacing="0" width="100%">
                    <thead class="theme-primary text-white">
                        <tr>
                            <th>{{ __('page.calf')[2] }}</th>
                            <th>{{ __('page.calf')[4] }}</th>
                            <th>{{ __('page.calf')[5] }}</th>
                            <th>{{ __('page.calf')[6] }}</th>
                            <th>{{ __('page.calf')[16] }}</th>
                            <th>{{ __('page.calf')[12] }}</th>
                        </tr>
                    </thead>

                </table>
            </div>
        </div>
    </div>
    <!-- end row -->

    <!-- Modal Start -->
    <div class="modal fade bd-example-modal-xl" id="tableModal" tabindex="-1" role="dialog"
        aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" style="width: 100%">
            <div class="modal-content" id="modalContent">

            </div>
        </div>
    </div>
    <!-- Modal End -->


@endsection
@section('script')
    <script>
        const AUTH_ID = '{{ auth()->id() }}';
        const TOKEN = "{{ csrf_token() }}";
        const URL_CALVES_JSON = "{{ route('calves.json.all') }}";
        const URL_CALVES_CREATE = "{{ route('calves.create') }}";
        const URL_CALVES_STORE = "{{ route('calves.store') }}";
        const URL_CALVES_UPDATE = "{{ route('calves.update', '#id') }}";
        const URL_CALVES_EDIT = "{{ route('calves.edit', '#id') }}";
        const URL_CALVES_DELETE = "{{ route('calves.destroy', '#id') }}";
        const URL_ARMS_JSON_FIND_BY_CUSTOMER = "{{ route('farms.json.findByCustomer', '#id') }}";
        const URL_CATTLE_JSON_FIND_BY_FARM = "{{ route('cattle.json.findByFarm', '#id') }}";
    </script>
    <script src="{!! asset('js/calves.js') !!}?v=0.1"></script>
@endsection

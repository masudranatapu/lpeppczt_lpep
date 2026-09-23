@extends('layouts.dashboard')
@section('title', ' Purchase Damage Type')
@section('content')

    <div class="row">
        <div class="col-12">
            <div class="card-box table-responsive mt-4" style="border-top: 3px solid #2aa9b9;">
                <div class="row">
                    <div class="col-md-6">
                        <h4 class="m-t-0 header-title mb-5"><b>{{ __('sidebar.purchase_return_type') }}</b></h4>
                    </div>
                    <div class="col-md-6 text-right">
                        @if (permission('ex2'))
                            <button class="btn btn-primary waves-effect waves-light m-b-5" id="addNew"> <i
                                    class="fa fa-plus-square m-r-5"></i> <span>{{ __('page.add_new_return_type') }}</span>
                            </button>
                        @endif
                    </div>
                </div>

                <table id="data-table" class="table table-striped table-bordered mt-4" cellspacing="0" width="100%">
                    <thead class="theme-primary text-white">
                        <tr>
                            <th> {{ __('page.sl') }} </th>
                            <th> {{ __('page.return_type_name') }} </th>
                            <th> {{ __('page.business') }} </th>
                            <th> {{ __('page.created_by') }} </th>
                            <th> {{ __('page.updated_by') }} </th>
                            <th>{{ __('page.action') }}</th>
                        </tr>
                    </thead>

                </table>
            </div>
        </div>
    </div>
    <!-- end row -->

    <!-- Business Modal Start -->
    <div class="modal fade bd-example-modal-xl" id="unitModal" tabindex="-1" role="dialog"
        aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog" style="width: 100%">
            <div class="modal-content" id="modalcontent">

            </div>
        </div>
    </div>
    <!-- Business Modal End -->

@endsection
@section('script')
    <script>
        const URL_DAMAGE_TYPE_LIST = "{{ route('damage-type.index') }}";
        const URL_DAMAGE_TYPE_CREATE = "{{ route('damage-type.create') }}";
        const URL_DAMAGE_TYPE_STORE = "{{ route('damage-type.store') }}";
        const URL_DAMAGE_TYPE_EDIT = "{{ route('damage-type.edit', 100) }}";
        const URL_DAMAGE_TYPE_UPDATE = "{{ route('damage-type.update', 100) }}";
        const URL_DAMAGE_TYPE_DELETE = "{{ route('purchase.damage-type.delete', 100) }}";
    </script>
<script src="{{ asset('js/damage-type.js') }}?v=0.2" @endsection

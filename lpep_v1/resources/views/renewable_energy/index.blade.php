@extends('layouts.dashboard')

@section('content')
<style>
    .dt-buttons{
        float: none !important;
        text-align: center !important;
    }
</style>
    <div class="row">
        <div class="col-12">
            <div class="card-box table-responsive mt-4" style="border-top: 3px solid #2aa9b9;">
                <div class="row">
                    <div class="col-md-6">
                        <h4 class="m-t-0 header-title"><b>Renewable Energy List</b></h4>
                    </div>
                    <div class="col-md-6 text-right">
                        <button class="btn btn-primary waves-effect waves-light" id="addNew">
                            <i class="fa fa-plus-square m-r-5"></i> Add New
                        </button>
                    </div>
                </div>

                <div class="row mt-4 mb-4">
                    <div class="col-md-12">

                        <form id="filter-form">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Area</label>
                                        <select name="area_id" class="form-control select2" id="filter_area_id">
                                            <option value="">All Area</option>
                                            @foreach($staffs as $staff)
                                                <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Agent</label>
                                        <select name="agent_id" class="form-control select2" id="filter_agent_id">
                                            <option value="">All Agent</option>
                                            @foreach($agents as $agent)
                                                <option value="{{ $agent->id }}">{{ $agent->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Visit Date From</label>
                                        <input type="date" name="start_date" class="form-control" id="filter_start_date">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Visit Date To</label>
                                        <input type="date" name="end_date" class="form-control" id="filter_end_date">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>&nbsp;</label>
                                        <div class="btn-group w-100">
                                            <button type="button" class="btn btn-info" id="filter_btn">Filter</button>
                                            <button type="button" class="btn btn-secondary" id="reset_btn">Reset</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <div id="buttons-container" class="text-center mt-3 mb-3"></div>
                    </div>
                </div>

                <table id="data-table" class="table table-striped table-bordered mt-4" cellspacing="0" width="100%">
                    <thead class="theme-primary text-white">
                        <tr>
                            <th>SL</th>
                            <th>Type</th>
                            <th>Client Name</th>
                            <th>Client Number</th>
                            <th>Visit Date</th>
                            <th>Area</th>
                            <th>Agent</th>
                            <th>Mobile No</th>
                            <th>Division</th>
                            <th>District</th>
                            <th>Upazila</th>
                            <th>Union</th>
                            <th>Livestock Details</th>
                            <th>Village</th>
                            <th>P.O Name</th>
                            <th>Plant Size</th>
                            <th>File</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Remarks</th>
                            <th>Action</th>
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
        const ROUTE_RENEWABLE_ENERGY_JSON_ALL = "{{ route('renewable-energy.json.all') }}";
        const ROUTE_RENEWABLE_ENERGY_CREATE = "{{ route('renewable-energy.create') }}";
        const ROUTE_RENEWABLE_ENERGY_STORE = "{{ route('renewable-energy.store') }}";
        const ROUTE_RENEWABLE_ENERGY_UPDATE = "{{ route('renewable-energy.update', '#id') }}";
        const ROUTE_RENEWABLE_ENERGY_EDIT = "{{ route('renewable-energy.edit', '#id') }}";
        const ROUTE_RENEWABLE_ENERGY_SHOW = "{{ route('renewable-energy.show', '#id') }}";
        const ROUTE_RENEWABLE_ENERGY_DESTROY = "{{ route('renewable-energy.destroy', '#id') }}";
        
        const ROUTE_DISTRICTS_FIND_BY_DIVISION = "{{ route('districts.json.findByDivision', '#id') }}";
        const ROUTE_UPAZILAS_FIND_BY_DISTRICT = "{{ route('upazilas.json.findByDistrict', '#id') }}";
        const ROUTE_UNIONS_FIND_BY_UPAZILA = "{{ route('unions.json.findByUpazila', '#id') }}";
    </script>

    <script src="/js/renewable_energy.js?v=0.1"></script>
@endsection

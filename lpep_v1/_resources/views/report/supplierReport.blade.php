@extends('layouts.dashboard')

@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="card mt-4">
                <div class="card-header">
                    <h4 class="m-t-0 header-title mt-2"><b>{{ __('page.coreport')[10] }}</b></h4>
                </div>
                <div class="card-body">
                    <form action="" method="GET" class="mb-4">
                        <div class="row d-flex justify-content-end">
                            @php
                                $contact_all = \App\Models\Contact::where('type', 'supplier')->get();
                            @endphp
                            <div class="col-md-3">
                                <select name="contact_id" class="form-control select2" id="contact_id">
                                    <option value="">Select</option>
                                    @foreach ($contact_all as $customer)
                                        <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-2">
                                <button class="btn btn-outline-primary btn-block" type="submit" style="cursor: pointer">
                                    <i class="fa fa-search"></i> Search
                                </button>
                            </div>

                        </div>
                    </form>

                    <div class="table-rep-plugin">
                        <div class="table-responsive" id="tablefixed">
                            {!! $dataTable->table(['class' => 'table table-bordered table-hover', 'id' => 'data-table'], true) !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('script')
    <script src="/vendor/datatables/buttons.server-side.js"></script>
    {!! $dataTable->scripts() !!}
@endsection

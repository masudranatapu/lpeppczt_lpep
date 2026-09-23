@extends('layouts.dashboard')
@section('title', 'Products')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card-box table-responsive mt-4" style="border-top: 3px solid #2aa9b9;">
                <div class="row">
                    <div class="col-md-6">
                        <h4 class="m-t-0 header-title mb-5"><b>User Info</b></h4>
                    </div>
                    <div class="col-md-12">
                        <h5>name: {{ $appCustomer->name }}</h5>
                        <h5>phone: {{ $appCustomer->mobile ?? '' }}</h5>
                        <h5>email: {{ $appCustomer->email ?? ''}}</h5>
                    </div>
                </div>
            </div>
        </div>
    </div> 
@endsection
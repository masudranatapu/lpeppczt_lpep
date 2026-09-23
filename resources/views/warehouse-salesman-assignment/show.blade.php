@extends('layouts.dashboard')
@section('title', 'Assignment '.$assignment->invoice_no)
@section('content')
<div class="row"><div class="col-12"><div class="card-box mt-4">@include('warehouse-salesman-assignment.details')</div></div></div>
@endsection

@extends('layouts.dashboard')
@section('title', 'Assign Stock to LSP')
@section('content')
<div class="row"><div class="col-12"><div class="card-box mt-4"><h4>Assign Area Office Stock to LSP</h4><p class="text-muted">Only unassigned Area Office stock can be allocated.</p><form method="POST" action="{{ route('warehouse-salesman-assignments.store') }}">@csrf @include('warehouse-salesman-assignment.form')</form></div></div></div>
@endsection

@extends('layouts.dashboard')
@section('title', 'Edit Assignment')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card-box mt-4">
            <h4>Edit Assignment</h4>
            <form method="POST" action="{{ route('warehouse-salesman-assignments.update', $assignment) }}">
                @csrf
                @method('PUT')
                @include('warehouse-salesman-assignment.form')
            </form>
        </div>
    </div>
</div>
@endsection

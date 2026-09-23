@extends('layouts.dashboard_master')
@section('breadcumb')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <h4 class="page-title float-left">Expense</h4>

            <ol class="breadcrumb float-right">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Expense Edit</li>
            </ol>
            <div class="clearfix"></div>
        </div>
    </div>
</div>
@endsection
@section('content')

<div class="row">
    <div class="col-8 m-auto">
        <div class="card-box table-responsive">
            <h4 class="page-title float-left">Expanse Update</h4>
            <form action="{{ route('expense.update') }}" method="POST">
                @csrf
                <input type="hidden" name="id" value="{{ $expense->id }}">
                <div class="form-group">
                    <label for="">Date</label>
                    <input type="date" value="{{ $expense->expanse_date }}" name="expanse_date" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="">Expense Type</label>
                    <select name="expense_type_id" class="form-control" id="" required>
                        <option value="">select</option>
                        @foreach ($expense_types as $expense_type)
                            <option value="{{ $expense_type->id }}" {{ $expense_type->id == $expense->expense_type_id ? 'selected':'' }}>{{ $expense_type->expense_type }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="">Amount</label>
                    <input type="number" value="{{ $expense->amount }}" name="amount" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="">Note</label>
                    <textarea name="note" rows="3" class="form-control">{{ $expense->note }}</textarea>
                </div>

                <div class="text-center">
                    <button class="btn btn-success" type="submit">Save</button>
                </div>

            </form>
        </div>
    </div>
</div>

@endsection

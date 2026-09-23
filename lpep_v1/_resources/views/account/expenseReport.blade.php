@extends('layouts.dashboard')

@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="card-box table-responsive mt-4" style="border-top: 3px solid #157c34;">
                <div class="row">
                    <div class="col-md-3">
                        <h4 class="m-t-0 header-title mt-2"><b>{{ __('page.expensereport')[0] }}</b></h4>
                    </div>
                    <div class="col-md-9">
                        <form action="" method="GET" class="mb-4">
                            <div class="row">
                                <div class="col-md-12 m-auto row">
                                    <div class="col-md-6">
                                        <div class="input-group">
                                            <input type="date" class="form-control" name="start">
                                            <span class="input-group-addon bg-primary b-0 text-white">to</span>
                                            <input type="date" class="form-control" name="end">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <select name="expense_category" class="form-control" id="">
                                            <option value="">select expense category</option>
                                            @foreach ($expnese_types as $expnese_type)
                                                <option value="{{ $expnese_type->id }}">
                                                    {{ $expnese_type->expnese_type }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <button class="btn btn-info" type="submit">Search</button>
                                    </div>
                                </div>

                            </div>
                        </form>
                    </div>
                </div>



                <table id="datatable-buttons" class="table table-bordered table-hover mt-4" cellspacing="0">
                    <thead class="theme-primary text-white">
                        <tr>
                            <th>{{ __('page.expensereport')[1] }}</th>
                            <th>{{ __('page.expensereport')[2] }}</th>
                            <th>{{ __('page.expensereport')[3] }}</th>
                            <th>{{ __('page.expensereport')[4] }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $business = currentBranch();
                        @endphp

                        @foreach ($expenses as $expense)
                            <tr>
                                <td>{{ $loop->index + 1 }}</td>
                                <td>{{ $expense->expanse_date }}</td>
                                <td>{{ $business->name }}</td>
                                <td>{{ $expense->amount }}</td>
                            </tr>
                        @endforeach

                    </tbody>
                    <tfoot>
                        <tr>
                            <th></th>
                            <th></th>
                            <th>Total</th>
                            <th>{{ $expenses->sum('amount') }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

@endsection

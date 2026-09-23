@extends('layouts.dashboard')

@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="card-box table-responsive mt-4" style="border-top: 3px solid #157c34;">
                <div class="row">
                    <div class="col-md-3">
                        <h4 class="m-t-0 header-title mt-2"><b>{{ __('page.salaryreport')[0] }}</b></h4>
                    </div>
                    <div class="col-md-9">
                        <form action="" method="GET" class="mb-4">
                            <div class="row">
                                <div class="col-md-10 m-auto row">
                                    <div class="col-md-5">
                                        <select name="month" class="form-control" id="">
                                            <option value="January">January</option>
                                            <option value="February">February</option>
                                            <option value="March">March</option>
                                            <option value="April">April</option>
                                            <option value="May">May</option>
                                            <option value="June">June</option>
                                            <option value="July">July</option>
                                            <option value="August">August</option>
                                            <option value="September">September</option>
                                            <option value="October">October</option>
                                            <option value="December">December</option>
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
                            <th>{{ __('page.salaryreport')[1] }}</th>
                            <th>{{ __('page.salaryreport')[2] }}</th>
                            <th>{{ __('page.salaryreport')[3] }}</th>
                            <th>{{ __('page.salaryreport')[4] }}</th>
                            <th>{{ __('page.salaryreport')[5] }}</th>
                            <th>{{ __('page.salaryreport')[6] }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $business = currentBranch();
                        @endphp

                        @foreach ($employees as $employee)
                            <tr>
                                <td>{{ $loop->index + 1 }}</td>
                                <td>{{ $employee->salary_month }}</td>
                                <td>{{ $employee->salary_date }}</td>
                                <td>{{ $business->name }}</td>
                                <td>{{ $employee->employee_name }}</td>
                                <td>{{ $employee->amount }}</td>
                            </tr>
                        @endforeach

                    </tbody>
                </table>
            </div>
        </div>
    </div>

@endsection

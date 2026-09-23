@extends('layouts.dashboard')

@section('content')

<div class="row">
    <div class="col-lg-12">
        <div class="card-box mt-5">
            <h4 class="header-title m-t-0 m-b-30">ALL Employees</h4>
            
            <table class="table table-bordered m-0">
                <thead>
                    <tr>
                        <th>Employee Name</th>
                        <th>Employee Picture</th>
                        <th>Salaray Paid</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($details as $employee)
                        <tr>
                            <td>{{ $employee->employee->employee_name }}</td>
                            <td>
                                <img src="{{ asset(json_decode($employee->employee->picture)) }}" height="40" alt="">
                            </td>
                            <td>
                                {{ $employee->amount }}
                            </td>
                            <td>
                                {{ $employee->salary_date }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div><!-- end col -->
</div>
<!-- end row -->



@endsection
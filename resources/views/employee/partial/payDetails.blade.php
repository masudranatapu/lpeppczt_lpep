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
                                @if ($employee->employee->picture_url)
                                    <img src="{{ $employee->employee->picture_url }}" height="40" width="40"
                                        style="object-fit: cover" alt="{{ $employee->employee->employee_name }}">
                                @else
                                    &mdash;
                                @endif
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

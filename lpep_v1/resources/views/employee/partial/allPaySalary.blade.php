@extends('layouts.dashboard')

@section('content')

<div class="row">
    <div class="col-lg-12">
        <div class="card-box mt-5">
            <h4 class="header-title m-t-0 m-b-30">{{ __('page.employeecreate')[12] }} <small>(Year{{ date('Y') }})</small></h4>
            
            <table class="table table-bordered m-0">
                <thead class="theme-primary text-white">
                    <tr>
                        <th>{{ __('page.employeecreate')[13] }}</th>
                        <th>{{ __('page.employeecreate')[14] }}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        @foreach ($months as $month)
                            <td>{{ $month->salary_month }}</td>
                            <td>
                                <a href="{{ route('salary.month',$month->salary_month) }}" class="btn btn-sm btn-success">Details</a>
                            </td>
                        @endforeach
                    </tr>
                </tbody>
            </table>
        </div>
    </div><!-- end col -->
</div>
<!-- end row -->


@endsection

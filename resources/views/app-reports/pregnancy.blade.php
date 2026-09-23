@extends('layouts.dashboard')

@section('content')

<div class="row">
    <div class="col-md-12">
        <div class="card-box table-responsive mt-4" style="border-top: 3px solid #157c34;">

            <table id="datatable-buttons" class="table table-bordered table-hover mt-4" cellspacing="0">
                <thead class="theme-primary text-white">
                    <tr>
                        <th>{{ __('page.pregnancy')[0] }}</th>
                        <th>{{ __('page.pregnancy')[1] }}</th>
                        <th>{{ __('page.pregnancy')[2] }}</th>
                        <th>{{ __('page.pregnancy')[3] }}</th>
                        <th>{{ __('page.pregnancy')[4] }}</th>
                        <th>{{ __('page.pregnancy')[5] }}</th>
                    </tr>
                </thead>
                <tbody>

                    @foreach($ctlPregnancyExams as $pExam)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $pExam->customer->name }}</td>
                        <td>{{ $pExam->farm->name }}</td>
                        <td>{{ $pExam->cattle->name }}</td>
                        <td>{{ $pExam->is_pregnant ? 'Yes' : 'No' }}</td>
                        <td>{{ $pExam->expected_delivery_date }}</td>
                    </tr>
                    @endforeach

                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
@extends('layouts.dashboard')

@section('content')

<div class="row">
    <div class="col-md-12">
        <div class="card-box table-responsive mt-4" style="border-top: 3px solid #157c34;">

            <table id="datatable-buttons" class="table table-bordered table-hover mt-4" cellspacing="0">
                <thead class="theme-primary text-white">
                    <tr>
                        <th>{{ __('page.disease')[0] }}</th>
                        <th>{{ __('page.disease')[3] }}</th>
                        <th>{{ __('page.disease')[1] }}</th>
                        <th>{{ __('page.disease')[2] }}</th>
                        <th>{{ __('page.disease')[4] }}</th>
                        <th>{{ __('page.disease')[5] }}</th>
                    </tr>
                </thead>
                <tbody>

                    @foreach($ctlDiseaseInfo as $dInfo)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $dInfo->date }}</td>
                        <td>{{ $dInfo->customer->name }}</td>
                        <td>{{ $dInfo->farm->name }}</td>
                        <td>{{ $dInfo->cattle->name }}</td>
                        <td>{{ $dInfo->cattleDisease->name }}</td>
                    </tr>
                    @endforeach

                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
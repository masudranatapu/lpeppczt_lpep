@extends('layouts.dashboard')

@section('content')

<div class="row">
    <div class="col-md-12">
        <div class="card-box table-responsive mt-4" style="border-top: 3px solid #157c34;">

            <table id="datatable-buttons" class="table table-bordered table-hover mt-4" cellspacing="0">
                <thead class="theme-primary text-white">
                    <tr>
                        <th>{{ __('page.vaccine')[0] }}</th>
                        <th>{{ __('page.vaccine')[3] }}</th>
                        <th>{{ __('page.vaccine')[1] }}</th>
                        <th>{{ __('page.vaccine')[2] }}</th>
                        <th>{{ __('page.vaccine')[4] }}</th>
                        <th>{{ __('page.vaccine')[5] }}</th>
                        <th>{{ __('page.vaccine')[6] }}</th>
                    </tr>
                </thead>
                <tbody>

                    @foreach($ctlVaccineInfo as $vInfo)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $vInfo->date }}</td>
                        <td>{{ $vInfo->customer->name }}</td>
                        <td>{{ $vInfo->farm->name }}</td>
                        <td>{{ $vInfo->cattle->name }}</td>
                        <td>{{ $vInfo->cattleDisease->name }}</td>
                        <td>{{ $vInfo->cattleVaccine->name }}</td>
                    </tr>
                    @endforeach

                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
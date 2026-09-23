@extends('layouts.dashboard')

@section('content')

<div class="row">
    <div class="col-md-12">
        <div class="card-box table-responsive mt-4" style="border-top: 3px solid #157c34;">

            <table id="datatable-buttons" class="table table-bordered table-hover mt-4" cellspacing="0">
                <thead class="theme-primary text-white">
                    <tr>
                        <th>{{ __('page.farm')[0] }}</th>
                        <th>{{ __('page.farm')[1] }}</th>
                        <th>{{ __('page.farm')[2] }}</th>
                        <th>{{ __('page.farm')[3] }}</th>
                        <th>{{ __('page.farm')[4] }}</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($farms as $farm)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $farm->customer->name }}</td>
                        <td>{{ $farm->name }}</td>
                        <td>{{ $farm->cattle_count }}</td>
                        <td>{{ $farm->calves_count }}</td>
                    </tr>
                    @endforeach

                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
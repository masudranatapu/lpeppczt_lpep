@extends('layouts.dashboard')

@section('content')

<div class="row">
    <div class="col-md-12">
        <div class="card-box table-responsive mt-4" style="border-top: 3px solid #157c34;">

            <table id="datatable-buttons" class="table table-bordered table-hover mt-4" cellspacing="0">
                <thead class="theme-primary text-white">
                    <tr>
                        <th>{{ __('page.food-consumption')[0] }}</th>
                        <th>{{ __('page.food-consumption')[4] }}</th>
                        <th>{{ __('page.food-consumption')[1] }}</th>
                        <th>{{ __('page.food-consumption')[2] }}</th>
                        <th>{{ __('page.food-consumption')[3] }}</th>
                        <th>{{ __('page.food-consumption')[5] }}</th>
                        <th>{{ __('page.food-consumption')[6] }}</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($ctlFoodConsumptions as $consumption)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $consumption->date }}</td>
                        <td>{{ $consumption->customer->name }}</td>
                        <td>{{ $consumption->farm->name }}</td>
                        <td>{{ $consumption->cattle->name }}</td>
                        <td>{{ $consumption->cattleFood->name }}</td>
                        <td>{{ $consumption->value . ' ' . $consumption->cattleFood->unit }}</td>
                    </tr>
                    @endforeach

                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
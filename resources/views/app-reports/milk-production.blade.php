@extends('layouts.dashboard')

@section('content')

<div class="row">
    <div class="col-md-12">
        <div class="card-box table-responsive mt-4" style="border-top: 3px solid #157c34;">

            <table id="datatable-buttons" class="table table-bordered table-hover mt-4" cellspacing="0">
                <thead class="theme-primary text-white">
                    <tr>
                        <th>{{ __('page.milk-production')[0] }}</th>
                        <th>{{ __('page.milk-production')[3] }}</th>
                        <th>{{ __('page.milk-production')[1] }}</th>
                        <th>{{ __('page.milk-production')[2] }}</th>
                        <th>{{ __('page.milk-production')[4] }}</th>
                        <th>{{ __('page.milk-production')[5] }}</th>
                        <th>{{ __('page.milk-production')[6] }}</th>
                    </tr>
                </thead>
                <tbody>

                    @foreach($ctlMilkProductions as $milkProduction)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $milkProduction->date }}</td>
                        <td>{{ $milkProduction->customer ? $milkProduction->customer->name : null }}</td>
                        <td>{{ $milkProduction->farm->name }}</td>
                        <td>{{ $milkProduction->cattle->name }}</td>
                        <td>{{ $milkProduction->time }}</td>
                        <td>{{ $milkProduction->value }}</td>
                    </tr>
                    @endforeach

                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
@extends('layouts.dashboard')

@section('content')

<div class="row">
    <div class="col-md-12">
        <div class="card-box table-responsive mt-4" style="border-top: 3px solid #157c34;">

            <table id="datatable-buttons" class="table table-bordered table-hover mt-4" cellspacing="0">
                <thead class="theme-primary text-white">
                    <tr>
                        <th>{{ __('page.weight-info')[0] }}</th>
                        <th>{{ __('page.weight-info')[4] }}</th>
                        <th>{{ __('page.weight-info')[1] }}</th>
                        <th>{{ __('page.weight-info')[2] }}</th>
                        <th>{{ __('page.weight-info')[3] }}</th>
                        <th>{{ __('page.weight-info')[5] }}</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($ctlWeightInfos as $info)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $info->date }}</td>
                        <td>{{ $info->customer->name }}</td>
                        <td>{{ $info->farm->name }}</td>
                        <td>{{ $info->cattle->name }}</td>
                        <td>{{ $info->value }}</td>
                    </tr>
                    @endforeach

                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
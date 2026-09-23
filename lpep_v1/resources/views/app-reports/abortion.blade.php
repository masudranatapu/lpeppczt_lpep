@extends('layouts.dashboard')

@section('content')

<div class="row">
    <div class="col-md-12">
        <div class="card-box table-responsive mt-4" style="border-top: 3px solid #157c34;">

            <table id="datatable-buttons" class="table table-bordered table-hover mt-4" cellspacing="0">
                <thead class="theme-primary text-white">
                    <tr>
                        <th>{{ __('page.abortion')[0] }}</th>
                        <th>{{ __('page.abortion')[1] }}</th>
                        <th>{{ __('page.abortion')[2] }}</th>
                        <th>{{ __('page.abortion')[3] }}</th>
                        <th>{{ __('page.abortion')[4] }}</th>
                    </tr>
                </thead>
                <tbody>

                    @foreach($ctlAbortionInfos as $abortionInfo)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $abortionInfo->customer->name }}</td>
                        <td>{{ $abortionInfo->farm->name }}</td>
                        <td>{{ $abortionInfo->cattle->name }}</td>
                        <td>{{ $abortionInfo->date }}</td>
                    </tr>
                    @endforeach

                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
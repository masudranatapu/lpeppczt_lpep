@extends('layouts.dashboard')

@section('content')

<div class="row">
    <div class="col-md-12">
        <div class="card-box table-responsive mt-4" style="border-top: 3px solid #157c34;">

            <table id="datatable-buttons" class="table table-bordered table-hover mt-4" cellspacing="0">
                <thead class="theme-primary text-white">
                    <tr>
                        <th>{{ __('page.impregnation')[0] }}</th>
                        <th>{{ __('page.impregnation')[3] }}</th>
                        <th>{{ __('page.impregnation')[1] }}</th>
                        <th>{{ __('page.impregnation')[2] }}</th>
                        <th>{{ __('page.impregnation')[5] }}</th>
                        <th>{{ __('page.impregnation')[6] }}</th>
                    </tr>
                </thead>
                <tbody>

                    @foreach($impregnations as $impregnation)
                    <tr class="data-row" data-id="{{ $impregnation->id }}">
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $impregnation->pal_date }}</td>
                        <td>{{ $impregnation->customer->name }}</td>
                        <td>{{ $impregnation->farm->name }}</td>
                        <td>{{ $impregnation->cattle->name }}</td>
                        <td>{{ $impregnation->pal_type }}</td>
                    </tr>
                    @endforeach

                </tbody>
            </table>
        </div>
    </div>
</div>

@foreach($impregnations as $impregnation)
<div class="modal" id="app-report-{{ $impregnation->id }}" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Impregnation Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table style="width: 100%;">
                    <tr>
                        <td style="width: 17%; font-weight: bold">{{ __('page.impregnation')[1] }}</td>
                        <td style="width: 32%;">{{ $impregnation->customer->name }}</td>
                        <td style="width: 4%;">&nbsp;</td>
                        <td style="width: 17%; font-weight: bold">{{ __('page.impregnation')[2] }}</td>
                        <td style="width: 32%;">{{ $impregnation->farm->name }}</td>
                    </tr>
                    <tr>
                        <td style="width: 17%; font-weight: bold">{{ __('page.impregnation')[5] }}</td>
                        <td style="width: 32%;">{{ $impregnation->cattle->name }}</td>
                        <td style="width: 4%;">&nbsp;</td>
                        <td style="width: 17%; font-weight: bold">{{ __('page.impregnation')[4] }}</td>
                        <td style="width: 32%;">{{ $impregnation->manualHit->date }}</td>
                    </tr>
                    <tr>
                        <td style="width: 17%; font-weight: bold">{{ __('page.impregnation')[3] }}</td>
                        <td style="width: 32%;">{{ $impregnation->pal_date }}</td>
                        <td style="width: 4%;">&nbsp;</td>
                        <td style="width: 17%; font-weight: bold">{{ __('page.impregnation')[6] }}</td>
                        <td style="width: 32%;">{{ $impregnation->pal_type }}</td>
                    </tr>
                    @if($impregnation->pal_type == 'প্রাকৃতিক')
                        <tr>
                            <td style="width: 17%; font-weight: bold">{{ __('page.impregnation')[7] }}</td>
                            <td style="width: 32%;">{{ $impregnation->palBreed->name }}</td>
                            <td style="width: 4%;">&nbsp;</td>
                            <td style="width: 17%; font-weight: bold">&nbsp;</td>
                            <td style="width: 32%;">&nbsp;</td>
                        </tr>
                    @else
                        <tr>
                            <td style="width: 17%; font-weight: bold">{{ __('page.impregnation')[9] }}</td>
                            <td style="width: 32%;">{{ $impregnation->seedCompany->name }}</td>
                            <td style="width: 4%;">&nbsp;</td>
                            <td style="width: 17%; font-weight: bold">{{ __('page.impregnation')[13] }}</td>
                            <td style="width: 32%;">{{ $impregnation->palBreed->name }}</td>
                        </tr>
                        <tr>
                            <td style="width: 17%; font-weight: bold">{{ __('page.impregnation')[10] }}</td>
                            <td style="width: 32%;">{{ $impregnation->seed_percentage }}</td>
                            <td style="width: 4%;">&nbsp;</td>
                            <td style="width: 17%; font-weight: bold">{{ __('page.impregnation')[11] }}</td>
                            <td style="width: 32%;">{{ $impregnation->straw_number }}</td>
                        </tr>
                        <tr>
                            <td style="width: 17%; font-weight: bold">{{ __('page.impregnation')[12] }}</td>
                            <td style="width: 32%;">{{ $impregnation->worker_info }}</td>
                            <td style="width: 4%;">&nbsp;</td>
                            <td style="width: 17%; font-weight: bold">&nbsp;</td>
                            <td style="width: 32%;">&nbsp;</td>
                        </tr>
                    @endif
                </table>
            </div>
        </div>
    </div>
</div>
@endforeach

@endsection

@section('script')
<script>
    $(document).ready(function () {
        $('tr.data-row').on('click', function () {
            $('#app-report-'+Number($(this).data('id'))).modal('show');
        })
    });
</script>
@endsection
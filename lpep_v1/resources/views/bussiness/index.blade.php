@extends('layouts.dashboard')

@section('content')

    <div class="row">
        <div class="col-12">
            <div class="card-box table-responsive mt-4" style="border-top: 3px solid #00BCD4;">
                <div class="row">
                    <div class="col-md-6">
                        <h4 class="m-t-0 header-title mb-5"><b>{{ __('page.branch')[0] }}</b></h4>
                    </div>
                    <div class="col-md-6 text-right">
                        <button class="btn btn-primary waves-effect waves-light m-b-5" id="addNew"> <i
                                class="fa fa-plus-square m-r-5"></i> <span>{{ __('page.branch')[1] }}</span> </button>
                    </div>
                </div>

                <table id="datatable" class="table table-striped table-bordered mt-4" cellspacing="0" width="100%">
                    <thead class="theme-primary text-white">
                        <tr>
                            <th>{{ __('page.sl') }}</th>
                            <th>{{ __('page.business') }}</th>
                            <th>{{ __('page.logo') }}</th>
                            <th>{{ __('page.start_date') }}</th>
                            <th>{{ __('page.address') }}</th>
                            <th>{{ __('page.zip') }}</th>
                            <th>{{ __('page.country') }}</th>
                            <th>{{ __('page.mobile') }}</th>
                            <th>{{ __('page.account_status') }}</th>
                            <th>{{ __('page.action') }}</th>
                        </tr>
                    </thead>


                    <tbody>
                        @foreach ($businesses as $business)
                            <tr>
                                <td>{{ $loop->index + 1 }}</td>
                                <td>{{ $business->name }}</td>
                                <td>
                                    <img height="40px" src="{{ asset(json_decode($business->logo)) }}" alt="">
                                </td>
                                <td>{{ $business->start_date }}</td>
                                <td>{{ $business->city }}</td>
                                <td>{{ $business->zip }}</td>
                                <td>{{ $business->country }}</td>
                                <td>{{ $business->mobile }}</td>
                                <td>
                                    @if ($business->status == 0)
                                        <span class="badge badge-danger">Disable</span>
                                    @else
                                        <span class="badge badge-success">Active</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a data-id="{{ $business->id }}" id="businessEdit"
                                            class="btn btn-sm btn-info">Edit</a>
                                        @if ($business->status == 0)
                                            <a href="{{ route('business.status', $business->id) }}?status=1"
                                                class="btn btn-sm btn-warning">Enable</a>
                                        @else
                                            <a href="{{ route('business.status', $business->id) }}?status=0"
                                                class="btn btn-sm btn-danger">Disable</a>
                                        @endif

                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- end row -->

    <!-- Business Modal Start -->
    <div class="modal fade bd-example-modal-xl" id="businessModal" tabindex="-1" role="dialog"
        aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" style="width: 100%">
            <div class="modal-content" id="modalcontent">

            </div>
        </div>
    </div>
    <!-- Business Modal End -->

@endsection
@section('script')
    <script>
        $(document).ready(function() {

            $('body').on('click', "#addNew", function() {
                $.get("{{ route('business.create') }}", function(data) {
                    $('#modalcontent').html(data)
                    $("#businessModal").modal('show')
                });

            })

            $('body').on('click', "#businessEdit", function() {
                let id = $(this).data('id')
                $.get(`/business/${id}/edit`, function(data) {
                    $('#modalcontent').html(data)
                    $("#businessModal").modal('show')
                });
            })


        })
    </script>
@endsection

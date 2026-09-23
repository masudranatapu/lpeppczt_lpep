@extends('layouts.dashboard')

@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="card-box table-responsive mt-4" style="border-top: 3px solid #2aa9b9;">
                <div class="row">
                    <div class="col-md-6">
                        <h4 class="m-t-0 header-title mb-5"><b>{{ __('page.salereturn')[0] }}</b></h4>
                    </div>
                </div>

                <table id="datatable-buttons" class="table table-bordered table-hover mt-4" cellspacing="0">
                    <thead class="theme-primary text-white">
                        <tr>
                            <th>{{ __('page.salereturn')[1] }}</th>
                            <th>{{ __('page.salereturn')[2] }}</th>
                            <th>{{ __('page.salereturn')[3] }}</th>
                            <th>{{ __('page.salereturn')[5] }}</th>
                            <th>{{ __('page.salereturn')[6] }}</th>
                            <th>{{ __('page.salereturn')[7] }}</th>
                            <th>{{ __('page.salereturn')[8] }}</th>
                            <th>{{ __('page.salereturn')[9] }}</th>
                            <th>{{ __('page.salereturn')[10] }}</th>
                        </tr>
                    </thead>
                    <tbody>

                        @foreach ($return_sales as $return)
                            <tr>
                                <td>{{ $loop->index + 1 }}</td>
                                <td onclick="viewSaleReturn({{ $return->id }})">{{ $return->return_date }}</td>
                                <td onclick="viewSaleReturn({{ $return->id }})">{{ $return->invoice_no }}</td>
                                <td onclick="viewSaleReturn({{ $return->id }})">{{ $return->customer->name }}</td>
                                <td onclick="viewSaleReturn({{ $return->id }})">{{ $return->total_amount }}</td>
                                <td onclick="viewSaleReturn({{ $return->id }})">{{ $return->paying_amount }}</td>
                                <td onclick="viewSaleReturn({{ $return->id }})">
                                    @if ($return->total_amount - $return->paying_amount == 0)
                                        <span class="badge badge-success">Paid</span>
                                    @else
                                        <span class="badge badge-danger">Due</span>
                                    @endif
                                </td>
                                <td onclick="viewSaleReturn({{ $return->id }})">
                                    @if ($return->total_amount - $return->paying_amount == 0)
                                        0
                                    @else
                                        {{ $return->total_amount - $return->paying_amount }}
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group btn-sm">
                                        <button type="button" class="btn btn-info dropdown-toggle waves-effect btn-sm"
                                            data-toggle="dropdown" aria-expanded="false"> Action <span
                                                class="caret"></span> </button>
                                        <div class="dropdown-menu" x-placement="bottom-start"
                                            style="position: absolute; transform: translate3d(0px, 35px, 0px); top: 0px; left: 0px; will-change: transform;">
                                            <a onclick="viewSaleReturn({{ $return->id }})" href="javascript:void(0);"
                                                class="dropdown-item">View</a>
                                            <a href="{{ route('sale.return.edit', $return->id) }}"
                                                class="dropdown-item">Edit</a>
                                            <a href="{{ route('sale.return.destroy', $return->id) }}"
                                                class="dropdown-item">Delete</a>
                                            <a href="{{ route('sale.return.invoice', $return->id) }}" target="_blank"
                                                class="dropdown-item">Print Invoice</a>
                                        </div>
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
    <div class="modal fade bd-example-modal-xl" id="unitModal" tabindex="-1" role="dialog"
        aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" style="width: 100%">
            <div class="modal-content" id="modalcontent" style="width: 100%">

            </div>
        </div>
    </div>
    <!-- Business Modal End -->

@endsection

@section('script')
    <script>
        function viewSaleReturn(id) {
            $.get(`/sale/return/${id}/details`, function(data) {
                $('#modalcontent').html(data)
                $("#unitModal").modal('show')
            });
        }
    </script>
@endsection

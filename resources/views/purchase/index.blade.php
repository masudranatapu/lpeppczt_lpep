@extends('layouts.dashboard')
@section('title', 'Purchase List')
@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="card-box table-responsive my-4" style="border-top: 3px solid #2aa9b9;">
                <div class="row">
                    <div class="col-md-6">
                        <h4 class="m-t-0 header-title mb-5"><b>{{ __('page.purchasemanage')[0] }}</b></h4>
                    </div>
                    <div class="col-md-6 text-right">
                        @if (permission('pu1'))
                            <a href="{{ route('purchase.create') }}" class="btn btn-primary waves-effect waves-light m-b-5"
                                id=""> <i class="fa fa-plus-square m-r-5"></i>
                                <span>{{ __('page.purchasemanage')[1] }}</span> </a>
                        @endif
                    </div>
                </div>

                <div>
                    <form action="" id="searching">
                        <div class="row justify-content-end">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <input type="date" onchange="purchaseSearch()" name="purchase_date"
                                        value="{{ request('purchase_date') ?? '' }}" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <input type="text" name="search" value="{{ request('search') ?? '' }}"
                                        class="form-control" placeholder="Supplier Name, product name or Invoice">
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="form-group">
                                    <button class="btn btn-success btn-no-border" type="submit">Search</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="table-rep-plugin">
                    <div class="table-responsive" id="tablefixed">
                        <table id="" class="table table-bordered table-hover mt-0" style="margin-bottom: 70px">
                            <thead class="theme-primary text-white">
                                <tr>
                                    <th style="width: 5%">{{ __('page.common.sl') }}</th>
                                    <th style="width: 10%">{{ __('page.common.date') }}</th>
                                    <th style="width: 10%">{{ __('page.product_name') }}</th>
                                    <th style="width: 10%">{{ __('page.common.invoice_no') }}</th>
                                    <th style="width: 10%"> {{ __('page.common.supplier') }}</th>
                                    <th style="width: 10%">{{ __('page.common.total_amount') }}</th>
                                    <th style="width: 10%">{{ __('page.common.paid_amount') }}</th>
                                    <th style="width: 10%">{{ __('page.common.due') }}</th>
                                    <th style="width: 5%">{{ __('page.common.action') }}</th>
                                </tr>
                            </thead>
                            <tbody>

                                @foreach ($purchases as $purchase)
                                    <tr>
                                        <td>{{ $loop->index + 1 }}</td>
                                        <td onclick="viewPurchase({{ $purchase->id }})">
                                            {{ $purchase->purchase_date }}
                                        </td>

                                        <td onclick="viewPurchase({{ $purchase->id }})">
                                            @foreach ($purchase->products as $product)
                                            <span>{{ $product->product_name }}</span>
                                            @endforeach
                                        </td>

                                        <td onclick="viewPurchase({{ $purchase->id }})">{{ $purchase->invoice_no }}
                                        </td>
                                        <td onclick="viewPurchase({{ $purchase->id }})">
                                            {{ $purchase->supplier->name }}</td>
                                        <td onclick="viewPurchase({{ $purchase->id }})">{{ $purchase->total }}</td>
                                        <td onclick="viewPurchase({{ $purchase->id }})">{{ $purchase->total_pay }}
                                        </td>
                                        <td onclick="viewPurchase({{ $purchase->id }})">
                                            {{ number_format($purchase->total - $purchase->total_pay, 2) }}</td>
                                        <td>
                                            <div class="btn-group btn-sm">
                                                <button type="button"
                                                    class="btn btn-info dropdown-toggle waves-effect btn-sm"
                                                    data-toggle="dropdown" aria-expanded="false"> Action <span
                                                        class="caret"></span> </button>
                                                <div class="dropdown-menu" x-placement="bottom-start"
                                                    style="position: absolute; transform: translate3d(0px, 35px, 0px); top: 0px; left: 0px; will-change: transform;">
                                                    @if (permission('pu1'))
                                                        <a onclick="viewPurchase({{ $purchase->id }})"
                                                            href="javascript:void(0);" class="dropdown-item">View</a>
                                                        <a href="{{ route('purchase.invoice', $purchase->id) }}"
                                                            class="dropdown-item">Invoice</a>
                                                    @endif
                                                    @if (permission('pu3'))
                                                        <a href="{{ route('purchase.edit', $purchase->id) }}"
                                                            class="dropdown-item">Edit</a>
                                                        @if ($purchase->purchaseReturn)
                                                            <a href="{{ route('purchase-return.edit', $purchase->purchaseReturn->id) }}"
                                                                class="dropdown-item">Purchase Return</a>
                                                        @else
                                                            <a href="{{ route('purchase.return', $purchase->id) }}"
                                                                class="dropdown-item">Purchase Return</a>
                                                        @endif
                                                    @endif
                                                    @if (permission('pu4'))
                                                        <a href="{{ route('purchase.destroy', $purchase->id) }}"
                                                            class="dropdown-item" id="delete">Delete</a>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="5">
                                        <strong>
                                            Total
                                        </strong>
                                    </td>

                                    <td>
                                        <strong>
                                            {{ $purchases->sum('total') }}
                                        </strong>
                                    </td>
                                    <td>
                                        <strong>
                                            {{ $purchases->sum('total_pay') }}
                                        </strong>
                                    </td>
                                    <td>
                                        <strong>{{ $purchases->sum('total') - $purchases->sum('total_pay') }}</strong>
                                    </td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <div>
                    {{ $purchases->appends(request()->input())->links() }}
                </div>
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
        function viewPurchase(id) {
            $.get(`/purchase/${id}/details`, function(data) {
                $('#modalcontent').html(data)
                $("#unitModal").modal('show')
            });
        }

        function purchaseSearch() {
            $('#searching').submit();
        }

        $(function() {
            $("input[type='date']").datepicker({
                autoclose: true,
                todayHighlight: true,
                dateFormat: 'yyyy-MM-dd',
                format: 'yyyy-mm-dd',
            })
        });
    </script>
    <script>
        $(function() {
            $('#tablefixed').responsiveTable({
                addFocusBtn: false
            });
        });
    </script>
@endsection

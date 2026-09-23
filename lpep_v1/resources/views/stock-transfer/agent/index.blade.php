@extends('layouts.dashboard')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card-box table-responsive mt-4" style="border-top: 3px solid #157c34;">
                <h4 class="m-t-0 header-title mb-2">
                    <b>Stock Transfer History</b>
                    <a href="{{ route('agent-stock-transfer.create') }}" class="btn btn-primary float-right">Make
                        Transfer</a>
                </h4>


                <form action="" method="get">
                    <div class="row" style="margin-top: 25px; margin-bottom: -10px">
                        @if (!isRole(ROLE_AGENT))
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="agent_id">{{ __('page.common.agent') }}</label>
                                    <select name="agent_id" id="agent_id" class="form-control">
                                        <option value="">Select Agent</option>
                                        @foreach (getCachedAgents() as $agent)
                                            <option value="{{ $agent->id }}"
                                                {{ request('agent_id') == $agent->id ? 'selected' : '' }}>
                                                {{ $agent->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        @endif
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="date">{{ __('page.stock_transfer.date') }}</label>
                                <input type="text" class="form-control datepicker px-2" name="date" placeholder="YYYY-MM-DD"
                                    value="{{ request('date') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="date">{{ 'Search Text' }}</label>
                                <input type="text" class="form-control" name="search" placeholder="Enter search text"
                                    value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="">&nbsp;</label>
                                <button type="submit" class="form-control btn btn-info d-block" style="width: auto"><i
                                        class="fa fa-search"></i> Search</button>
                            </div>
                        </div>
                    </div>
                </form>

                <div class="table-rep-plugin">
                    <div class="table-responsive" id="tablefixed">
                        <table id="" class="table table-bordered table-hover mt-0" cellspacing="0">
                            <thead class="theme-primary text-white">
                                <tr>
                                    <th>{{ __('page.stock_transfer.sl') }}</th>
                                    <th>{{ __('page.stock_transfer.invoice') }}</th>
                                    <th>{{ __('page.stock_transfer.date') }}</th>
                                    <th>{{ __('page.stock_transfer.total_quantity') }}</th>
                                    <th>{{ __('page.stock_transfer.action') }}</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($transfers as $transfer)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $transfer->invoice_no }}</td>
                                        <td>{{ $transfer->date }}</td>
                                        <td>{{ $transfer->total_quantity }}</td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="javascript:void(0);" onclick="view({{ $transfer->id }})"
                                                    class="btn btn-sm btn-info">
                                                    <i class="fa fa-eye"></i>
                                                </a>

                                                {{-- <a href="{{ route('stock-transfer.destroy', $transfer->id) }}"
                                                        id="delete" class="btn btn-sm btn-danger" title="Delete">
                                                        <i class="fa fa-trash"></i>
                                                    </a> --}}
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>

                            <tfoot>
                                <tr>
                                    <th colspan="2"></th>
                                    <th>Total</th>
                                    <th>{{ $totalQuantity }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <div>
                    {{ $transfers->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade bd-example-modal-xl" id="unitModal" tabindex="-1" role="dialog"
        aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" style="width: 100%">
            <div class="modal-content" id="modalcontent" style="width: 100%">

            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(function() {
            $('#tablefixed').responsiveTable({
                addFocusBtn: false
            });
        });

        function view(id) {
            $.get(`/agent-stock-transfer/${id}`, function(data) {
                $('#modalcontent').html(data)
                $("#unitModal").modal('show')
            });
        }
    </script>
@endsection

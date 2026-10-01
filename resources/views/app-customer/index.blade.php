@extends('layouts.dashboard')

@push('css')
<style>
    .beneficiary-table th, .beneficiary-table td {
        white-space: nowrap;
        vertical-align: middle;
    }
    .beneficiary-table tfoot th {
        font-weight: 700;
    }
    .beneficiary-filter .select2-container {
        width: 100% !important;
    }
</style>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card-box mt-4" style="border-top: 3px solid #2aa9b9;">
            <div class="row">
                <div class="col-md-6">
                    <h4 class="m-t-0 header-title mb-4"><b>Beneficiary</b></h4>
                </div>
                <div class="col-md-6 text-right">
                    <a href="{{ route('app-customer.export', request()->except('page', 'per_page')) }}" class="btn btn-success waves-effect waves-light m-b-5">
                        <i class="fa fa-file-excel-o m-r-5"></i>
                        <span>Export Excel</span>
                    </a>
                    <button type="button" class="btn btn-info waves-effect waves-light m-b-5" id="export-selected" disabled>
                        <i class="fa fa-check-square-o m-r-5"></i>
                        <span>Export Selected (<span id="selected-count">0</span>)</span>
                    </button>
                    <button type="button" class="btn btn-light waves-effect m-b-5" id="clear-selected" style="display: none;">Clear Selection</button>
                    <button class="btn btn-primary waves-effect waves-light m-b-5" id="addNew">
                        <i class="fa fa-plus-square m-r-5"></i>
                        <span>{{ __('page.customer')[1] }}</span>
                    </button>
                </div>
            </div>

            <form method="GET" action="{{ route('app-customer.index') }}" class="row beneficiary-filter mb-3" id="filter-form">
                <input type="hidden" name="per_page" value="{{ $perPage }}">
                @if ($isAdmin || Auth::user()?->email == 'faridpur@gmail.com')
                    <div class="col-md-2 mb-2">
                        <label for="staf_filter">Area</label>
                        <select class="form-control" id="staf_filter" name="staf">
                            <option value="">Select Area</option>
                            @foreach($staffs as $staf)
                                <option value="{{ $staf->id }}" {{ (string) request('staf') === (string) $staf->id ? 'selected' : '' }}>{{ $staf->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 mb-2">
                        <label for="agent_filter">Agents</label>
                        <select class="form-control" id="agent_filter" name="agent_id">
                            <option value="">Select Agent</option>
                            @foreach($agents as $agent)
                                <option value="{{ $agent->id }}" {{ (string) request('agent_id') === (string) $agent->id ? 'selected' : '' }}>
                                    {{ $agent->employee_name ?? $agent->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <div class="col-md-2 mb-2">
                    <label for="filter_group_name">Beneficiary Number</label>
                    <select class="form-control" id="filter_group_name" name="group_name">
                        <option value="">Select Beneficiary Number</option>
                        @for($i = 1; $i <= 40; $i++)
                            <option value="{{ $i }}" {{ (string) request('group_name') === (string) $i ? 'selected' : '' }}>{{ $i }}</option>
                        @endfor
                    </select>
                </div>

                <div class="col-md-2 mb-2">
                    <label for="filter_group_number">Group Number</label>
                    <select class="form-control" id="filter_group_number" name="group_number">
                        <option value="">Select Group Number</option>
                        @for ($i=1 ; $i <= 28 ; $i++)
                            @if ($i == 7 || $i == 14 || $i == 15 || $i == 22)
                                @continue
                            @endif
                            <option value="{{ $i }}" {{ (string) request('group_number') === (string) $i ? 'selected' : '' }}>{{ $i }}</option>
                        @endfor
                    </select>
                </div>

                <div class="col-md-2 mb-2">
                    <label for="search">Search</label>
                    <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="Name, mobile, village">
                </div>

                <div class="col-md-2 mb-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary mr-2">Filter</button>
                    <a href="{{ route('app-customer.index') }}" class="btn btn-secondary">Reset</a>
                </div>
            </form>

            <div class="d-flex justify-content-between align-items-center mb-2">
                <div>
                    Show
                    <select id="per_page" class="form-control d-inline-block" style="width: auto;">
                        @foreach($perPageOptions as $option)
                            <option value="{{ $option }}" {{ $perPage === $option ? 'selected' : '' }}>{{ $option === 'all' ? 'All' : $option }}</option>
                        @endforeach
                    </select>
                    entries
                </div>
                <div>Total: <strong>{{ method_exists($customers, 'total') ? $customers->total() : $customers->count() }}</strong> beneficiaries</div>
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-bordered beneficiary-table" width="100%">
                    <thead class="theme-primary text-white">
                        <tr>
                            <th rowspan="2"><input type="checkbox" id="select-all" title="Select all on this page"></th>
                            <th rowspan="2">{{ __('page.common.sl') }}</th>
                            <th rowspan="2">Beneficiary Cell Number</th>
                            <th rowspan="2">Beneficiary Name</th>
                            <th rowspan="2">Beneficiary Number</th>
                            <th rowspan="2">Beneficiary Group Number</th>
                            <th colspan="2" class="text-center">Address</th>
                            <th colspan="5" class="text-center">Beneficiary Livestock Details</th>
                            <th colspan="7" class="text-center">Services</th>
                            <th rowspan="2">{{ __('page.common.action') }}</th>
                        </tr>
                        <tr>
                            <th>Village</th>
                            <th>Unions</th>

                            <th>Cow</th>
                            <th>Bull</th>
                            <th>Bakna</th>
                            <th>Goat</th>
                            <th>Khasi</th>

                            <th>Membership</th>
                            <th>Deworming</th>
                            <th>Bringing</th>
                            <th>Fattening</th>
                            <th>Treatment</th>
                            <th>AI</th>
                            <th>Medicine</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($customers as $customer)
                            <tr>
                                <td><input type="checkbox" class="row-check" value="{{ $customer->id }}"></td>
                                <td>{{ method_exists($customers, 'firstItem') ? $customers->firstItem() + $loop->index : $loop->iteration }}</td>
                                <td>{{ $customer->mobile }}</td>
                                <td>{{ $customer->name }}@if($customer->imported_at) <span class="badge badge-info" title="{{ $customer->importNote() }}" style="cursor: help;">Imported</span>@endif</td>
                                <td>{{ $customer->beneficiary_number }}</td>
                                <td>{{ $customer->group_number }}</td>
                                <td>{{ $customer->village }}</td>
                                <td>{{ $customer->unions }}</td>
                                <td>{{ $customer->cow }}</td>
                                <td>{{ $customer->bull }}</td>
                                <td>{{ $customer->bakna }}</td>
                                <td>{{ $customer->goat }}</td>
                                <td>{{ $customer->khasi }}</td>
                                <td>{{ $customer->membership }}</td>
                                <td>{{ $customer->deworming }}</td>
                                <td>{{ $customer->bringing }}</td>
                                <td>{{ $customer->fattening }}</td>
                                <td>{{ $customer->treatment }}</td>
                                <td>{{ $customer->ai }}</td>
                                <td>{{ $customer->medicine }}</td>
                                <td>
                                    <div class="btn btn-group">
                                        {!! $customer->log() !!}
                                        @if ($isAdmin)
                                            <button type="button" class="btn btn-sm btn-success view-qr" title="QR Code" data-id="{{ $customer->id }}"><i class="fa fa-qrcode"></i></button>
                                        @endif
                                        <button type="button" class="btn btn-sm btn-primary edit-customer" title="Edit" data-id="{{ $customer->id }}"><i class="fa fa-edit"></i></button>
                                        <button type="button" class="btn btn-sm btn-danger delete-customer" title="Delete" data-id="{{ $customer->id }}"><i class="fa fa-trash"></i></button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="21" class="text-center py-4">No beneficiaries found.</td></tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="8" class="text-right">Total</th>
                            <th class="text-center">{{ $totals->cow ?? 0 }}</th>
                            <th class="text-center">{{ $totals->bull ?? 0 }}</th>
                            <th class="text-center">{{ $totals->bakna ?? 0 }}</th>
                            <th class="text-center">{{ $totals->goat ?? 0 }}</th>
                            <th class="text-center">{{ $totals->khasi ?? 0 }}</th>
                            <th colspan="8"></th>
                        </tr>
                    </tfoot>
                </table>
            </div>

            @if(method_exists($customers, 'links'))
                <div class="mt-3">{{ $customers->links() }}</div>
            @endif
        </div>
    </div>
</div>

<form id="export-selected-form" method="POST" action="{{ route('app-customer.export.selected') }}" style="display: none;">
    @csrf
</form>

<div class="modal fade bd-example-modal-xl" id="modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" style="width: 100%;">
        <div class="modal-content" id="modalcontent" style="padding: 0px !important"></div>
    </div>
</div>
@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    function errorMessage(error) {
        return error?.responseJSON?.message ?? error?.responseJSON?.error ?? 'Something went wrong';
    }

    // Bulk selection is kept per browser tab, so it survives paging and filtering.
    var SELECTED_KEY = 'app-customer-selected-ids';

    function loadSelected() {
        try {
            return new Set(JSON.parse(sessionStorage.getItem(SELECTED_KEY) || '[]'));
        } catch (e) {
            return new Set();
        }
    }

    var selectedIds = loadSelected();

    function saveSelected() {
        try {
            sessionStorage.setItem(SELECTED_KEY, JSON.stringify(Array.from(selectedIds)));
        } catch (e) {}
    }

    function refreshSelection() {
        $('.row-check').each(function () {
            this.checked = selectedIds.has(String(this.value));
        });
        var rows = $('.row-check');
        var checkedRows = rows.filter(':checked');
        $('#select-all').prop('checked', rows.length > 0 && checkedRows.length === rows.length);
        $('#select-all').prop('indeterminate', checkedRows.length > 0 && checkedRows.length < rows.length);
        $('#selected-count').text(selectedIds.size);
        $('#export-selected').prop('disabled', selectedIds.size === 0);
        $('#clear-selected').toggle(selectedIds.size > 0);
    }

    $(document).ready(function () {
        $("[data-toggle=popover]").popover();

        refreshSelection();

        $('body').on('change', '.row-check', function () {
            if (this.checked) {
                selectedIds.add(String(this.value));
            } else {
                selectedIds.delete(String(this.value));
            }
            saveSelected();
            refreshSelection();
        });

        $('#select-all').on('change', function () {
            var checked = this.checked;
            $('.row-check').each(function () {
                if (checked) {
                    selectedIds.add(String(this.value));
                } else {
                    selectedIds.delete(String(this.value));
                }
            });
            saveSelected();
            refreshSelection();
        });

        $('#clear-selected').on('click', function () {
            selectedIds.clear();
            saveSelected();
            refreshSelection();
        });

        $('#export-selected').on('click', function () {
            var form = $('#export-selected-form');
            form.find('input[name="ids"]').remove();
            form.append($('<input>', { type: 'hidden', name: 'ids', value: Array.from(selectedIds).join(',') }));
            form.submit();
        });

        $('#filter_group_name').select2({ placeholder: 'Select Beneficiary Number', allowClear: true });
        $('#agent_filter').select2({ placeholder: 'Select Agent', allowClear: true });
        $('#filter_group_number').select2({ placeholder: 'Select Group Number', allowClear: true });
        $('#staf_filter').select2({ placeholder: 'Select Area', allowClear: true });

        $('#filter_group_name, #agent_filter, #filter_group_number, #staf_filter').on('change', function () {
            $('#filter-form').submit();
        });

        $('#per_page').on('change', function () {
            $('#filter-form input[name=per_page]').val($(this).val());
            $('#filter-form').submit();
        });

        $('body').on('click', "#addNew", function() {
            $.get("{{ route('app-customer.create') }}", function(data) {
                $('#modalcontent').html(data);
                $("#modal").modal('show');
            });
        });

        $("body").on("submit","#customer-form",function(e) {
            e.preventDefault();
            let formData = new FormData(this);
            $.ajax({
                url: "{{ route('app-customer.store') }}",
                data: formData,
                method: 'POST',
                dataType: "json",
                processData: false,
                contentType: false,
            })
            .then(function(data) {
                toastr.success(data);
                $("#modal").modal('hide');
                location.reload();
            })
            .catch(error => {
                toastr.error(errorMessage(error));
            });
        });

        $('body').on('click', ".edit-customer", function() {
            let id = $(this).data('id');
            $.get('{{ route('app-customer.edit', '#id') }}'.replace('#id', id), function(data) {
                $('#modalcontent').html(data);
                $("#modal").modal('show');
            });
        });

        $('body').on('click', ".view-qr", function() {
            let id = $(this).data('id');
            $.get('{{ route('customer.qr', '#id') }}'.replace('#id', id), function(data) {
                $('#modalcontent').html(data);
                $("#modal").modal('show');
            });
        });

        $('body').on('submit', "#customer-update", function(e) {
            e.preventDefault();
            $.ajax({
                url: $(this)[0].action,
                data: $(this).serialize(),
                method: 'post'
            })
            .then(function(data) {
                toastr.success(data);
                $("#modal").modal('hide');
                location.reload();
            })
            .catch(error => {
                toastr.error(errorMessage(error));
            });
        });

        $('body').on('click', ".delete-customer", function() {
            let id = $(this).data('id');
            swal({
                title: "Are you Want to Delete?",
                text: "Once Delete, This will be permanently Delete!",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            })
            .then((willDelete) => {
                if (willDelete) {
                    $.ajax({
                        url: "{{ route('app-customer.destroy', '#id') }}".replace('#id', id),
                        data: { _token: "{{ csrf_token() }}" },
                        method: 'DELETE'
                    })
                    .then(function(data) {
                        toastr.success(data);
                        location.reload();
                    })
                    .catch(error => {
                        toastr.error(errorMessage(error));
                    });
                } else {
                    swal("Cancelled", "Your Data Is Safe :)", "error");
                }
            });
        });
    });
</script>
@endsection

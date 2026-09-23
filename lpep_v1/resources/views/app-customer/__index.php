@extends('layouts.dashboard')
@push('css')
    <style>
        #deleteData.disabled {
            pointer-events: none;
            background: #2aa9b9;
        }

    </style>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

@endpush
@section('content')

    <div class="row">
        <div class="col-12">
            <div class="card-box table-responsive mt-4" style="border-top: 3px solid #2aa9b9;">
                <div class="row">
                    <div class="col-md-6">
                        <h4 class="m-t-0 header-title mb-5"><b>Beneficiary</b></h4>
                    </div>
                    <div class="col-md-6 text-right">
                        <button class="btn btn-primary waves-effect waves-light m-b-5" id="addNew"> <i
                                class="fa fa-plus-square m-r-5"></i> <span>{{ __('page.customer')[1] }}</span>
                        </button>
                    </div>
                </div>

                <div class="row mb-3">
                    @if (Auth::user()?->userPermission?->role?->role_name === 'Admin')
                        <div class="col-md-3">
                            <label for="agent_filter">Agents</label>
                            <select class="form-control" id="agent_filter">
                                <option value="">Select Agent</option>
                                @foreach($agents as $agent)
                                    <option value="{{ $agent->id }}">{{ $agent->employee_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif


                    <div class="col-md-3">
                        <label for="filter_group_name">Beneficiary Number</label>
                        <select class="form-control" id="filter_group_name">
                            <option value="">Select {{ __('sidebar.app.beneficiary') }}  Number</option>
                            @for($i = 1; $i <= 40; $i++)
                                <option value="{{ $i }}">{{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="filter_group_number">Group Number</label>
                        {{-- <input type="text" class="form-control" id="filter_group_number" placeholder="Enter Group Number"> --}}
                         <select class="form-control" id="filter_group_number">
                            <option value="">Select Group Number</option>
                            @for ($i=1 ; $i <= 28 ; $i++)
                                @if ($i == 7 || $i == 14 || $i == 15 || $i == 22)
                                    @continue
                                @endif
                                <option value="{{ $i }}">{{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                </div>

                <table id="data-table" class="table table-striped table-bordered mt-4" cellspacing="0" width="100%">
                    <thead class="theme-primary text-white">
                        <tr>
                            <th rowspan="2">{{ __('page.common.sl') }}</th>
                            <th rowspan="2">Beneficiary Cell Number</th>
                            <th rowspan="2">Beneficiary Name</th>
                            {{-- <th>{{ __('page.common.name') }} Benificiary Name</th> --}}
                            {{-- <th>{{ __('page.common.email') }}</th> --}}
                            <th rowspan="2">Group Number</th>
                            {{-- <th>{{ __('page.common.farms') }}</th> --}}
                            {{-- <th>Image</th> --}}
                            <th colspan="5" rowspan="1" class="text-center">Beneficiary Livestock Details</th>
                            <th colspan="7" rowspan="1" class="text-center">Services</th>
                            <th rowspan="2">{{ __('page.common.action') }}</th>
                        </tr>
                        <tr>
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
                </table>
            </div>
        </div>
    </div>
    <!-- end row -->

    <!-- Business Modal Start -->
    <div class="modal fade bd-example-modal-xl" id="modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" style="width: 100%;">
            <div class="modal-content" id="modalcontent" style="padding: 0px !important">

            </div>
        </div>
    </div>
    <!-- Business Modal End -->


@endsection
@section('script')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
    
        var table;
        $(function() {
            table = $('#data-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('app-customer.index') }}",
                    data: function(d) {
                        d.group_name = $('#filter_group_name').val();
                        d.group_number = $('#filter_group_number').val();
                        d.agent_id = $('#agent_filter').val();
                        return d;
                    }
                },
                order: [
                    [0, 'desc']
                ],
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false,
                        width: '6%',
                        render: function (data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    {
                        width: '17%',
                        data: 'mobile',
                        name: 'mobile'
                    },
                    {
                        width: '17%',
                        data: 'name',
                        name: 'name'
                    },
                    /*
                    {
                        width: '17%',
                        data: 'email',
                        name: 'email'
                    },
                    */
                
                    {
                        width: '17%',
                        data: 'group_number',
                        name: 'group Number'
                    },
                    // {
                    //     width: '17%',
                    //     data: 'beneficiary_number',
                    //     name: 'beneficiary_number'
                    // },
                    
                    {
                        width: '14%',
                        data: 'cow',
                        name: 'cow'
                    },
                    {
                        width: '14%',
                        data: 'bull',
                        name: 'bull'
                    },
                    {
                        width: '14%',
                        data: 'bakna',
                        name: 'bakna'
                    },
                    {
                        width: '14%',
                        data: 'goat',
                        name: 'goat'
                    },
                    {
                        width: '14%',
                        data: 'khasi',
                        name: 'khasi'
                    },
                    {
                        width: '14%',
                        data: 'membership',
                        name: 'membership'
                    },
                    {
                        width: '14%',
                        data: 'deworming',
                        name: 'deworming'
                    },
                    {
                        width: '14%',
                        data: 'bringing',
                        name: 'bringing'
                    },
                    {
                        width: '14%',
                        data: 'fattening',
                        name: 'fattening'
                    },
                    {
                        width: '14%',
                        data: 'treatment',
                        name: 'treatment'
                    },
                    {
                        width: '14%',
                        data: 'ai',
                        name: 'ai'
                    },
                    {
                        width: '14%',
                        data: 'medicine',
                        name: 'medicine'
                    },
                    
                    {
                        width: '14%',
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: true
                    },
                ],
                drawCallback: function(settings) {
                    $("[data-toggle=popover]").popover();
                }
            });
        });

        $(document).ready(function() {
            // Initialize select2 for group name filter
            $('#filter_group_name').select2({
                placeholder: 'Select Group',
                allowClear: true,
                ajax: {
                    url: "{{ route('app-customer.index') }}",
                    dataType: 'json',
                    delay: 250,
                    processResults: function(data) {
                        // Get unique group names
                        let groups = [...new Set(data.data.map(item => item.beneficiary_number))];
                        return {
                            results: groups.map(group => ({
                                id: group,
                                text: group
                            }))
                        };
                    },
                    cache: true
                }
            });

            // Add event listeners for filters
            $('#filter_group_name').on('change', function() {
                table.ajax.reload();
            });

            $('#agent_filter').on('change', function() {
                table.ajax.reload();
            });

            $('#agent_filter').select2({
                placeholder: 'Select Agent',
                allowClear: true
            });
            $('#filter_group_number').select2({
                placeholder: 'Select Group Number',
                allowClear: true
            });
            
            var delayTimer;
            $('#filter_group_number').on('change', function() {
                clearTimeout(delayTimer);
                delayTimer = setTimeout(function() {
                    table.ajax.reload();
                }, 500);
            });

            $('body').on('click', "#addNew", function() {
                $.get("{{ route('app-customer.create') }}", function(data) {
                    $('#modalcontent').html(data)
                    $("#modal").modal('show')
                });
            })

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
                    toastr.success(data)
                    const oTable = $('#data-table').dataTable();
                    oTable.fnDraw(false);
                    $("#modal").modal('hide')
                })
                .catch(error => {
                    toastr.error(error.responseJSON.error)
                });
            })

            $('body').on('click', "#viewAppCustomer", function() {
                let id = $(this).data('id')
                $.get('{{ route('app-customer.show', '#id') }}'.replace('#id', id), function(data) {
                    $('#modalcontent').html(data)
                    $("#modal").modal('show')
                });
            });

            $('body').on('click', "#editAppCustomer", function() {
                let id = $(this).data('id')
                $.get('{{ route('app-customer.edit', '#id') }}'.replace('#id', id), function(data) {
                    $('#modalcontent').html(data)
                    $("#modal").modal('show')
                });
            });

            $('body').on('click', "#viewQr", function() {
                let id = $(this).data('id')
                $.get('{{ route('customer.qr', '#id') }}'.replace('#id', id), function(data) {
                    $('#modalcontent').html(data)
                    $("#modal").modal('show')
                });
            })

            $('body').on('submit', "#customer-update", function(e) {
                e.preventDefault();

                $.ajax({
                        url: $(this)[0].action,
                        data: $(this).serialize(),
                        method: 'post'
                    })
                    .then(function(data) {
                        toastr.success(data)
                        const oTable = $('#data-table').dataTable();
                        oTable.fnDraw(false);
                        $("#modal").modal('hide')
                    })
                    .catch(error => {
                        toastr.error(error.responseJSON.error)
                    });
            })

            $('body').on('click', "#deleteData", function() {
                let id = $(this).data('id')

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
                                    url: "{{ route('app-customer.destroy', '#id') }}".replace(
                                        '#id', id),
                                    data: {
                                        _token: "{{ csrf_token() }}"
                                    },
                                    method: 'DELETE'
                                })
                                .then(function(data) {
                                    toastr.success(data)
                                    const oTable = $('#data-table').dataTable();
                                    oTable.fnDraw(false);
                                });
                        } else {
                            swal("Cancelled", "Your Data Is Safe :)", "error");
                        }
                    });
            })
        })
    </script>
    
@endsection

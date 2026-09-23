@extends('layouts.dashboard')

@section('content')

    <div class="row">
        <div class="col-12">
            <div class="card-box table-responsive mt-4" style="border-top: 3px solid #2aa9b9;">
                <div class="row">
                    <div class="col-md-6">
                        <h4 class="m-t-0 header-title mb-5"><b>{{ __('page.customer-point')[0] }}</b> <span
                                class="text-success" style="font-size: 16px">(My Points: <span
                                    id="available-points">{{ $availablePoints }}</span>)</span></h4>
                    </div>
                    <div class="col-md-6 text-right">
                        @if (permission('uni2'))
                            <button class="btn btn-primary waves-effect waves-light m-b-5" id="addNew"
                                {{ $availablePoints <= 0 ? 'disabled' : '' }}>
                                <i class="fa fa-plus-square m-r-5"></i> <span>{{ __('page.customer-point')[1] }}</span>
                            </button>
                        @endif
                    </div>
                </div>

                <table id="data-table" class="table table-striped table-bordered mt-4" cellspacing="0" width="100%">
                    <thead class="theme-primary text-white">
                        <tr>
                            <th>{{ __('page.customer-point')[2] }}</th>
                            <th>{{ __('page.customer-point')[3] }}</th>
                            <th>{{ __('page.customer-point')[4] }}</th>
                            <th>{{ __('page.common.available_amount') }}</th>
                            <th>{{ __('page.customer-point')[5] }}</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
    <!-- end row -->

    <!-- Business Modal Start -->
    <div class="modal fade bd-example-modal-xl" id="tableModal" tabindex="-1" role="dialog"
        aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog" style="width: 100%">
            <div class="modal-content" style="padding: 0px !important" id="modalcontent">

            </div>
        </div>
    </div>
    <!-- Business Modal End -->


@endsection
@section('script')
    <script>
        function toastrResponse(response) {
            if (response.error) {
                toastr.error(response.error);
                return false;
            }
            toastr.success(response.message)
            return true;
        }

        $(document).ready(function() {
            let init = false;

            function getData() {
                $('#data-table').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: "{{ route('customer-points.json.all') }}",
                    order: [
                        [0, 'desc']
                    ],
                    columns: [{
                            width: '8%',
                            data: 'DT_RowIndex',
                            name: 'id',
                            orderable: true,
                            searchable: false
                        },
                        {
                            width: '24%',
                            data: 'customer.name',
                            name: 'customer.name'
                        },
                        {
                            width: '24%',
                            data: 'amount',
                            name: 'amount'
                        },
                        {
                            width: '24%',
                            data: 'available_amount',
                            name: 'available_amount'
                        },
                        {
                            width: '20%',
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: true
                        },
                    ],
                    drawCallback: function(settings) {
                        $("[data-toggle=popover]").popover();
                    }
                }).on('xhr.dt', function(e, settings, json, xhr) {
                    if (!init) {
                        fetchAvailablePoints();
                    }
                });
            }
            getData();

            $('body').on('click', "#addNew", function() {
                $.get("{{ route('customer-points.create') }}", function(data) {
                    $('#modalcontent').html(data)
                    $("#tableModal").modal('show');
                    window.select2Hook('#tableModal');
                });
            })

            $('body').on('click', "#submit", function(e) {
                e.preventDefault();
                let customer_id = $("select[name=app_customer_id]").val();
                let assigned_points = $("input[name=assigned_points]").val();

                $.ajax({
                        url: "{{ route('customer-points.store') }}",
                        data: {
                            _token: "{{ csrf_token() }}",
                            customer_id: customer_id,
                            assigned_points: assigned_points
                        },
                        method: 'POST'
                    })
                    .then(function(data) {
                        if (toastrResponse(data)) {
                            const oTable = $('#data-table').dataTable();
                            oTable.fnDraw(false);
                            $("#tableModal").modal('hide')
                            fetchAvailablePoints();
                        }
                    });
            })

            $('body').on('click', "#submitUpdate", function(e) {
                e.preventDefault();
                let id = $("input[name=id]").val();
                let customer_id = $("select[name=customer_id]").val();
                let assigned_points = $("input[name=assigned_points]").val();

                $.ajax({
                        url: "{{ route('customer-points.update', '#id') }}".replace('#id', id),
                        data: {
                            _token: "{{ csrf_token() }}",
                            user_id: '{{ auth()->id() }}',
                            assigned_points: assigned_points,
                            customer_id: customer_id
                        },
                        method: 'PUT'
                    })
                    .then(function(data) {
                        if (toastrResponse(data)) {
                            const oTable = $('#data-table').dataTable();
                            oTable.fnDraw(false);
                            $("#tableModal").modal('hide')
                            fetchAvailablePoints();
                        }
                    });
            })

            $('body').on('click', "#tableEdit", function() {
                let id = $(this).data('id')

                $.get('{{ route('customer-points.edit', '#id') }}'.replace('#id', id), function(data) {
                    $('#modalcontent').html(data)
                    $("#tableModal").modal('show')
                    window.select2Hook('#tableModal');
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
                                    url: "{{ route('customer-points.destroy', '#id') }}".replace(
                                        '#id', id),
                                    data: {
                                        _token: "{{ csrf_token() }}"
                                    },
                                    method: 'DELETE'
                                })
                                .then(function(data) {
                                    if (toastrResponse(data)) {
                                        const oTable = $('#data-table').dataTable();
                                        oTable.fnDraw(false);
                                        fetchAvailablePoints();
                                    }
                                });
                        } else {
                            swal("Cancelled", "Your Data Is Safe :)", "error");
                        }
                    });
            });

            function fetchAvailablePoints() {
                $.get('{{ route('customer-points.agent-points', auth()->id()) }}', function(res) {
                    $('#available-points').text(res);
                    $('#addNew').attr('disabled', Number(res) <= 0);
                    init = true;
                });
            }
        })
    </script>
@endsection

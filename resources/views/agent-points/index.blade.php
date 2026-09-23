@extends('layouts.dashboard')

@push('css')
    <style>
        #deleteData.disabled {
            pointer-events: none;
            background: #2aa9b9;
        }

    </style>
@endpush

@section('content')

    <div class="row">
        <div class="col-12">
            <div class="card-box table-responsive mt-4" style="border-top: 3px solid #2aa9b9;">
                <div class="row">
                    <div class="col-md-6">
                        <h4 class="m-t-0 header-title mb-5"><b>{{ __('page.agent-point')[0] }}</b></h4>
                    </div>
                    <div class="col-md-6 text-right">
                        <button class="btn btn-primary waves-effect waves-light m-b-5" id="addNew"> <i
                                class="fa fa-plus-square m-r-5"></i> <span>{{ __('page.agent-point')[1] }}</span>
                        </button>
                    </div>
                </div>

                <table id="data-table" class="table table-striped table-bordered mt-4" cellspacing="0" width="100%">
                    <thead class="theme-primary text-white">
                        <tr>
                            <th>{{ __('page.agent-point')[2] }}</th>
                            <th>{{ __('page.common.invoice_no') }}</th>
                            <th>{{ __('page.agent-point')[3] }}</th>
                            <th>{{ __('page.agent-point')[9] }}</th>
                            <th>{{ __('page.agent-point')[5] }}</th>
                        </tr>
                    </thead>

                </table>
            </div>
        </div>
    </div>
    <!-- end row -->

    <!-- Business Modal Start -->
    <div class="modal fade bd-example-modal-xl" id="pointModal" tabindex="-1" role="dialog"
        aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog" style="width: 100%">
            <div class="modal-content" id="modalcontent">

            </div>
        </div>
    </div>
    <!-- Business Modal End -->


@endsection
@section('script')
    <script>
        $(document).ready(function() {

            function getData() {
                $('#data-table').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: "{{ route('agent-points.index') }}",
                    order: [
                        [0, 'desc']
                    ],
                    columns: [{
                            width: '6%',
                            data: 'DT_RowIndex',
                            name: 'id',
                            orderable: true,
                            searchable: false
                        },
                        {
                            width: '17%',
                            data: 'invoice_no',
                            name: 'invoice_no'
                        },
                        {
                            width: '17%',
                            data: 'agent.name',
                            name: 'agent.name'
                        },
                        {
                            width: '17%',
                            data: 'amount',
                            name: 'amount'
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
            }
            getData();

            $('body').on('click', "#addNew", function() {
                $.get("{{ route('agent-points.create') }}", function(data) {
                    $('#modalcontent').html(data)
                    $("#pointModal").modal('show')
                });
            })

            $('body').on('submit', "#agentPointStore", function(e) {
                e.preventDefault();
                $.ajax({
                        url: "{{ route('agent-points.store') }}",
                        data: $(this).serialize(),
                        method: 'POST'
                    })
                    .then(function(data) {
                        toastr.success(data)
                        const oTable = $('#data-table').dataTable();
                        oTable.fnDraw(false);
                        $("#pointModal").modal('hide')
                    })
                    .catch(error => {
                        toastr.error(error.responseJSON.error)
                    });
            })

            $('body').on('click', "#submitUpdate", function(e) {
                e.preventDefault();
                let id = $("input[name=id]").val();
                let agent_id = $("select[name=agent_id]").val();
                let assigned_points = $("input[name=assigned_points]").val();
                let min_points = $("input[name=min_points]").val();

                if (assigned_points < min_points) {
                    toastr.error('Input can not be lower than ' + min_points);
                    return false;
                }

                $.ajax({
                        url: "{{ route('agent-points.update', '#id') }}".replace('#id', id),
                        data: {
                            _token: "{{ csrf_token() }}",
                            user_id: '{{ auth()->id() }}',
                            assigned_points: assigned_points,
                            agent_id: agent_id
                        },
                        method: 'PUT'
                    })
                    .then(function(data) {
                        toastr.success(data)
                        const oTable = $('#data-table').dataTable();
                        oTable.fnDraw(false);
                        $("#pointModal").modal('hide')
                    });
            })

            $('body').on('click', "#tableEdit", function() {
                let id = $(this).data('id')

                $.get('{{ route('agent-points.edit', '#id') }}'.replace('#id', id), function(data) {
                    $('#modalcontent').html(data)
                    $("#pointModal").modal('show')
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
                                    url: "{{ route('agent-points.destroy', '#id') }}".replace(
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

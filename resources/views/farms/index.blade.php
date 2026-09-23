@extends('layouts.dashboard')

@section('content')

    <div class="row">
        <div class="col-12">
            <div class="card-box table-responsive mt-4" style="border-top: 3px solid #2aa9b9;">
                <div class="row">
                    <div class="col-md-6">
                        <h4 class="m-t-0 header-title mb-5"><b>{{ __('page.farm')[0] }}</b></h4>
                    </div>
                    <div class="col-md-6 text-right">
                        @if (permission('uni2'))
                            <button class="btn btn-primary waves-effect waves-light m-b-5" id="addNew"><i
                                    class="fa fa-plus-square m-r-5"></i> <span>{{ __('page.farm')[1] }}</span></button>
                        @endif
                    </div>
                </div>

                <table id="data-table" class="table table-striped table-bordered mt-4" cellspacing="0" width="100%">
                    <thead class="theme-primary text-white">
                        <tr>
                            {{-- <th>{{ __('page.farm')[1] }}</th> --}}
                            <th>{{ __('page.farm')[2] }}</th>
                            <th>{{ __('page.farm')[3] }}</th>
                            <th>{{ __('page.farm')[4] }}</th>
                            <th>{{ __('page.action') }}</th>
                        </tr>
                    </thead>

                </table>
            </div>
        </div>
    </div>
    <!-- end row -->

    <!-- Business Modal Start -->
    <div class="modal fade bd-example-modal-xl" id="tableModal" role="dialog"
        aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog" style="width: 100%">
            <div class="modal-content" style="padding: 0px !important" id="modalContent">

            </div>
        </div>
    </div>
    <!-- Business Modal End -->


@endsection
@section('script')
    <script>
        $(function() {
            $('#data-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('farms.json.all') }}",
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
                        width: '36%',
                        data: 'customer.name',
                        name: 'customer.name'
                    },
                    {
                        width: '36%',
                        data: 'name',
                        name: 'name'
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
            });
        })

        $(document).ready(function() {
            $('body').on('click', "#addNew", function() {
                $.get("{{ route('farms.create') }}", function(data) {
                    $('#modalContent').html(data)
                    $("#tableModal").modal('show')
                    window.select2Hook("#tableModal");
                });
            })

            $('body').on('submit', "#customer-form-store", function(e) {
                e.preventDefault();
                $.ajax({
                        url: $(this)[0].action,
                        data: $(this).serialize(),
                        method: 'POST'
                    })
                    .then(function(data) {
                        toastr.success(data)
                        const oTable = $('#data-table').dataTable();
                        oTable.fnDraw(false);
                        $("#tableModal").modal('hide')
                    })
                    .catch(error => {
                        toastr.error(error.responseJSON.error)
                    });
            })

            $('body').on('click', "#tableEdit", function() {
                let id = $(this).data('id')
                $.get('{{ route('farms.edit', '#id') }}'.replace('#id', id), function(data) {
                    $('#modalContent').html(data)
                    $("#tableModal").modal('show')
                    window.select2Hook("#tableModal");
                });
            })

            $('body').on('submit', "#customer-form-update", function(e) {
                e.preventDefault();
                $.ajax({
                        url: $(this)[0].action,
                        data: $(this).serialize(),
                        method: 'PUT'
                    })
                    .then(function(data) {
                        toastr.success(data)
                        const oTable = $('#data-table').dataTable();
                        oTable.fnDraw(false);
                        $("#tableModal").modal('hide')
                    })
                    .catch(error => {
                        toastr.error(error.responseJSON.error)
                    });;
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
                                    url: "{{ route('farms.destroy', '#id') }}".replace('#id',
                                        id),
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
            });

            $('body').on('change', "select.division_id", function() {
                const id = $(this).val();

                if (id < 1)
                    return 0;

                const districtSelect = $('select.district_id');
                districtSelect.empty();

                $.get('{{ route('districts.json.findByDivision', '#id') }}'.replace('#id', id), function(
                    res) {
                    districtSelect.append(`<option value="">Select</option>`);
                    res.data.forEach(function(item) {
                        districtSelect.append(
                            `<option value="${item.id}">${item.bn_name}</option>`);
                    });
                });
            });

            $('body').on('change', "select.district_id", function() {
                const id = $(this).val();

                if (id < 1)
                    return 0;

                const upazilaSelect = $('select.upazila_id');
                upazilaSelect.empty();

                $.get('{{ route('upazilas.json.findByDistrict', '#id') }}'.replace('#id', id), function(
                    res) {
                    upazilaSelect.append(`<option value="">Select</option>`);
                    res.data.forEach(function(item) {
                        upazilaSelect.append(
                            `<option value="${item.id}">${item.bn_name}</option>`);
                    });
                });
            });

            $('body').on('change', "select.upazila_id", function() {
                const id = $(this).val();

                if (id < 1)
                    return 0;

                const upazilaSelect = $('select.union_id');
                upazilaSelect.empty();

                $.get('{{ route('unions.json.findByUpazila', '#id') }}'.replace('#id', id), function(
                    res) {
                    upazilaSelect.append(`<option value="">Select</option>`);
                    res.data.forEach(function(item) {
                        upazilaSelect.append(
                            `<option value="${item.id}">${item.bn_name}</option>`);
                    });
                });
            });
        })
    </script>
@endsection

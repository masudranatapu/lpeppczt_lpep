@extends('layouts.dashboard')

@section('content')

    <div class="row">
        <div class="col-12">
            <div class="card-box table-responsive mt-4" style="border-top: 3px solid #2aa9b9;">
                <div class="row">
                    <div class="col-md-6">
                        <h4 class="m-t-0 header-title mb-5"><b>{{ __('page.category')[0] }}</b></h4>
                    </div>
                    <div class="col-md-6 text-right">
                        @if (permission('cat1'))
                            <button class="btn btn-primary waves-effect waves-light m-b-5" id="addNew"> <i
                                    class="fa fa-plus-square m-r-5"></i> <span>{{ __('page.category')[1] }}</span>
                            </button>
                        @endif
                    </div>
                </div>

                <table id="data-table" class="table table-striped table-bordered mt-4" cellspacing="0" width="100%">
                    <thead class="theme-primary text-white">
                        <tr>
                            <th>{{ __('page.category')[2] }}</th>
                            <th>{{ __('page.category')[3] }}</th>
                            <th>{{ __('page.category')[4] }}</th>
                        </tr>
                    </thead>

                </table>
            </div>
        </div>
    </div>
    <!-- end row -->

    <!-- Business Modal Start -->
    <div class="modal fade bd-example-modal-xl" id="categoryModal" tabindex="-1" role="dialog"
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
                    ajax: "{{ route('all.category.json') }}",
                    columns: [{
                            "data": 'DT_RowIndex',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'category_name',
                            name: 'category_name'
                        },
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: true
                        },
                    ]
                });
            }
            getData();

            $('body').on('click', "#addNew", function() {
                $.get("{{ route('category.create') }}", function(data) {
                    $('#modalcontent').html(data)
                    $("#categoryModal").modal('show')
                });
            })

            $('body').on('click', "#submit", function(e) {
                e.preventDefault();
                let category_name = $("input[name=category_name]").val();
                let _token = "{{ csrf_token() }}"
                $.post("{{ route('category.store') }}", {
                    _token: _token,
                    category_name: category_name
                }, function(data) {
                    toastr.success(data)
                    var oTable = $('#data-table').dataTable();
                    oTable.fnDraw(false);
                    $("#categoryModal").modal('hide')
                })
            })

            $('body').on('click', "#submitUpdate", function(e) {
                e.preventDefault();
                let id = $("input[name=id]").val();
                let category_name = $("input[name=category_name]").val();
                let _token = "{{ csrf_token() }}"
                $.post("{{ route('category.updated') }}", {
                    _token: _token,
                    id: id,
                    category_name: category_name
                }, function(data) {
                    toastr.success(data)
                    var oTable = $('#data-table').dataTable();
                    oTable.fnDraw(false);
                    $("#categoryModal").modal('hide')
                })
            })

            $('body').on('click', "#categoryEdit", function() {
                let id = $(this).data('id')

                $.get(`/category/${id}/edit`, function(data) {
                    $('#modalcontent').html(data)
                    $("#categoryModal").modal('show')
                });
            })

            $('body').on('click', "#deleteData", function() {
                let id = $(this).data('id')
                let _token = "{{ csrf_token() }}"
                $.post("{{ route('category.status') }}", {
                    _token: _token,
                    id: id
                }, function(data) {
                    toastr.success(data)
                    var oTable = $('#data-table').dataTable();
                    oTable.fnDraw(false);
                })

            })
        })
    </script>
@endsection

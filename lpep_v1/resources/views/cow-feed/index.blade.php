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
                    <h4 class="m-t-0 header-title mb-5"><b>Cow Food</b></h4>
                </div>
                <div class="col-md-6 text-right">
                    <button class="btn btn-primary waves-effect waves-light m-b-5" id="addNew">
                        <i class="fa fa-plus-square m-r-5"></i> <span>Create New Food</span>
                    </button>
                </div>
            </div>

            {{-- Filter --}}
            <div class="row mb-3">
                <div class="col-md-3">
                    <label for="item_list">Feed Item</label>
                    <select class="form-control" id="item_list">
                        <option value="">All Items</option>
                        <option value="Feed">Feed</option>
                        <option value="wheat bran">Wheat Bran</option>
                        <option value="Kura">Kura</option>
                        <option value="Cheata gouar">Cheata Gouar</option>
                        <option value="Grass">Grass</option>
                    </select>
                </div>
            </div>

            {{-- DataTable --}}
            <table id="data-table" class="table table-striped table-bordered mt-4" cellspacing="0" width="100%">
                <thead class="theme-primary text-white">
                    <tr>
                        <th>#</th>
                        <th>Item Name</th>
                        <th>Volume</th>
                        <th>Unit Price</th>
                        <th>Qty</th>
                        <th>Total Price</th>
                        <th>Previous Stock</th>
                        <th>Purchase Date</th>
                        <th>End Date</th>
                        <th style="width:120px;">Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

{{-- Modal --}}
<div class="modal fade bd-example-modal-xl" id="modal" tabindex="-1" role="dialog" aria-labelledby="cowFeedModal" aria-hidden="true">
    <div class="modal-dialog modal-lg" style="width: 100%;">
        <div class="modal-content" id="modalcontent" style="padding: 0 !important"></div>
    </div>
</div>
@endsection

@section('script')
<script>
let table;

$(function () {
    table = $('#data-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('cow-feeds.index') }}",
            data: function (d) {
                d.item_name = $('#item_list').val(); // filter by item_name
            }
        },
        order: [[0, 'desc']],
        columns: [
            {
                data: 'DT_RowIndex',
                name: 'DT_RowIndex',
                orderable: false,
                searchable: false,
                width: '6%',
                render: function (data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            },
            { data: 'item_name', name: 'item_name', defaultContent: '' },
            { data: 'volume', name: 'volume', defaultContent: '' },
            { data: 'unit_price', name: 'unit_price', defaultContent: '' },
            { data: 'qty', name: 'qty', defaultContent: '' },
            { data: 'total_price', name: 'total_price', defaultContent: '' },
            { data: 'previus_stock', name: 'previus_stock', defaultContent: '' },
            { data: 'purchase_date', name: 'purchase_date', defaultContent: '' },
            { data: 'end_date', name: 'end_date', defaultContent: '' },
            {
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false
            },
        ]
    });

    // Filter
    $('#item_list').on('change', function () {
        table.ajax.reload();
    });

    // Create
    $('body').on('click', '#addNew', function () {
        $.get("{{ route('cow-feeds.create') }}", function (html) {
            $('#modalcontent').html(html);
            $('#modal').modal('show');
        });
    });

    // Store
    $('body').on('submit', '#cowfeed-form', function (e) {
        e.preventDefault();
        $.ajax({
            url: "{{ route('cow-feeds.store') }}",
            method: 'POST',
            data: $(this).serialize(),
            dataType: 'json'
        }).then(function (res) {
            toastr.success(res.message ?? 'Created successfully');
            table.ajax.reload(null, false);
            $('#modal').modal('hide');
        }).catch(function (err) {
            toastr.error(err.responseJSON?.message ?? 'Failed to create');
        });
    });

    // View
    $('body').on('click', '#viewCowFeed', function () {
        const id = $(this).data('id');
        $.get("{{ route('cow-feeds.show', ':id') }}".replace(':id', id), function (html) {
            $('#modalcontent').html(html);
            $('#modal').modal('show');
        });
    });

    // Edit
    $('body').on('click', '#editCowFeed', function () {
        const id = $(this).data('id');
        $.get("{{ route('cow-feeds.edit', ':id') }}".replace(':id', id), function (html) {
            $('#modalcontent').html(html);
            $('#modal').modal('show');
        });
    });
    
    $('body').on('click','.update-btn',function(e){
        $(this).addClass('disabled');
    })

    // Update
    $('body').on('submit', '#cowfeed-update', function (e) {
        e.preventDefault();
        $.ajax({
            url: $(this).attr('action'),
            method: 'PUT', // PUT handled with @method('PUT')
            data: $(this).serialize()
        }).then(function (res) {
            toastr.success(res.message ?? 'Updated successfully');
            table.ajax.reload(null, false);
            $('#modal').modal('hide');
        }).catch(function (err) {
            toastr.error(err.responseJSON?.message ?? 'Failed to update');
        });
    });

    // Delete
    $('body').on('click', '#deleteData', function () {
        const id = $(this).data('id');
        swal({
            title: "Are you sure?",
            text: "Once deleted, you cannot recover this record.",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        }).then((willDelete) => {
            if (!willDelete) return;

            $.ajax({
                url: "{{ route('cow-feeds.destroy', ':id') }}".replace(':id', id),
                method: 'POST',
                data: { _method: 'DELETE', _token: "{{ csrf_token() }}" }
            }).then(function (res) {
                toastr.success(res.message ?? 'Deleted successfully');
                table.ajax.reload(null, false);
            }).catch(function () {
                toastr.error('Failed to delete');
            });
        });
    });
});
</script>
@endsection

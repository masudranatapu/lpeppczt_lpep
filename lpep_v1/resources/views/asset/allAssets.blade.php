@extends('layouts.dashboard')

@section('content')


<div class="row">
  <div class="col-12 mt-4">
    <div class="card shadow-sm border-0">
      <div class="card-header d-flex align-items-center justify-content-between border-0 border-top rounded-0 pt-3 pb-3 theme-topbar">
        <h5 class="mb-0 fw-semibold">
          {{ __('page.asset')[0] }}
        </h5>

        <div class="d-flex align-items-center gap-2">
          <!-- Export buttons container (filled by DataTables Buttons) -->
          <div id="exportButtons" class="d-none d-md-inline-flex"></div>

          @if (permission('ex2'))
          <button class="btn btn-primary" id="addNew">
            <i class="fa fa-plus-square me-2"></i>{{ __('page.asset')[1] }}
          </button>
          @endif
        </div>
      </div>

      <div class="card-body">
        <!-- Export buttons for mobile (stacked) -->
        <div class="d-md-none mt-4">
          <div id="exportButtonsMobile" class="d-grid gap-2"></div>
        </div>

        <div class="table-responsive">
          <table id="data-table" class="table table-sm table-striped table-hover align-middle w-100 mb-0">
            <thead class="table-primary sticky-top">
              <tr>
                <th>#SL</th>
                <th>Category</th>
                <th>Date</th>
                <th class="text-end">Amount</th>
                <th class="text-end">Quantity</th>
                <th>Pay By</th>
                <th>Note</th>
                <th class="text-center" style="width: 150px;">Action</th>
              </tr>
            </thead>
            <tfoot class="table-light">
              <tr>
                <th colspan="3" class="text-end">Total Amount:</th>
                <th id="total-amount" class="text-end"></th>
                <th colspan="4"></th>
              </tr>
            </tfoot>
            <tbody>
              {{-- rows go here --}}
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>


 <!-- Business Modal Start -->
    <div class="modal fade bd-example-modal-xl" id="unitModal" tabindex="-1" role="dialog"
        aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog" style="width: 100%">
            <div class="modal-content" id="modalcontent">

            </div>
        </div>
    </div>
    <!-- Business Modal End -->
@endsection

@section('script')
<!-- DataTables & Buttons -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">
<!--<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>-->
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>

<script>
$(document).ready(function () {

  function initDataTable() {
    const fileName = 'Assets_' + new Date().toISOString().slice(0, 10);

    const table = $('#data-table').DataTable({
      processing: true,
      serverSide: true,
      ajax: "{{ route('asset.all') }}",
      lengthMenu: [10, 25, 50, 75, 100],
      pageLength: 25,
      order: [[0, 'asc']],
      columns: [
        { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
        { data: 'asset_category', name: 'asset_category' },
        { data: 'asset_date', name: 'asset_date' },
        { data: 'amount', name: 'amount', className: 'text-end' },
        { data: 'quantity', name: 'quantity', className: 'text-end' },
        { data: 'pay_by', name: 'pay_by' },
        { data: 'note', name: 'note' },
        { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' },
      ],

      // Include 'l' so the length dropdown shows up
      // Keep 'B' so Buttons get created, then we'll move them to #exportButtons
      dom: 'lBfrtip',

      buttons: [
        {
          extend: 'excelHtml5',
          text: '<i class="fa fa-file-excel"></i> Excel',
          className: 'btn btn-success btn-sm',
          title: 'Assets',
          filename: fileName,
          exportOptions: { columns: ':visible:not(:last-child)' },
          footer: true
        },
        {
          extend: 'pdfHtml5',
          text: '<i class="fa fa-file-pdf"></i> PDF',
          className: 'btn btn-danger btn-sm',
          title: 'Assets',
          filename: fileName,
          orientation: 'landscape',
          pageSize: 'A4',
          exportOptions: { columns: ':visible:not(:last-child)' },
          footer: true,
          customize: function (doc) {
            doc.styles.tableHeader.alignment = 'left';
            doc.styles.tableHeader.bold = true;
            // tighten top spacing a bit
            if (doc.content[1] && doc.content[1].table) {
              doc.content[1].margin = [0, 0, 0, 0];
            }
          }
        },
        {
          extend: 'print',
          text: '<i class="fa fa-print"></i> Print',
          className: 'btn btn-primary btn-sm',
          title: 'Assets',
          exportOptions: { columns: ':visible:not(:last-child)' },
          footer: true,
          customize: function (win) {
            $(win.document.body).css('font-size', '12px');
            $(win.document.body).find('table').addClass('compact').css('font-size', 'inherit');
          }
        }
      ],

      drawCallback: function (settings) {
        if (settings.json && typeof settings.json.total_amount !== 'undefined') {
          $('#total-amount').html(settings.json.total_amount);
        }
      }
    });

    // Move export buttons into your custom container
    table.buttons().container().appendTo('#exportButtons');
  }

  // initialize
  initDataTable();

  // ====== CRUD + UI Handlers ======

  // Open create modal
  $('body').on('click', '#addNew', function () {
    $.get("{{ route('asset.create') }}", function (view) {
      $('#modalcontent').html(view);
      $('#unitModal').modal('show');
    });
  });

  // Create submit
  $('body').on('click', '#submit', function (e) {
    e.preventDefault();

    const payload = {
      _token: "{{ csrf_token() }}",
      pay_by: $("select[name=payment_type]").val(),
      expanse_date: $("input[name=expanse_date]").val(),
      amount: $("input[name=amount]").val(),
      quantity: $("input[name=quantity]").val(),
      expense_type_id: $("select[name=expense_type_id]").val(),
      note: $("textarea[name=note]").val(),
      account_id: $("select[name=account_id]").val()
    };

    $.post("{{ route('asset.store') }}", payload, function (msg) {
      toastr.success(msg);
      $('#data-table').DataTable().ajax.reload(null, false);
      $('#unitModal').modal('hide');
    });
  });

  // Update submit
  $('body').on('click', '#submitUpdate', function (e) {
    e.preventDefault();

    const payload = {
      _token: "{{ csrf_token() }}",
      id: $("input[name=id]").val(),
      pay_by: $("select[name=payment_type]").val(),
      expanse_date: $("input[name=expanse_date]").val(),
      amount: $("input[name=amount]").val(),
      quantity: $("input[name=quantity]").val(),
      expense_type_id: $("select[name=expense_type_id]").val(),
      note: $("textarea[name=note]").val(),
      account_id: $("select[name=account_id]").val()
    };

    $.post("{{ route('asset.update') }}", payload, function (msg) {
      toastr.success(msg);
      $('#data-table').DataTable().ajax.reload(null, false);
      $('#unitModal').modal('hide');
    });
  });

  // Edit modal
  $('body').on('click', '#unitEdit', function () {
    const id = $(this).data('id');
    $.get(`/asset/${id}/edit`, function (view) {
      $('#modalcontent').html(view);
      $('#unitModal').modal('show');
    });
  });

  // Delete
  $('body').on('click', '#deleteData', function () {
    const id = $(this).data('id');
    swal({
      title: 'Are you Want to Delete?',
      text: 'Once Delete, This will be permanently Delete!',
      icon: 'warning',
      buttons: true,
      dangerMode: true
    }).then((willDelete) => {
      if (willDelete) {
        $.get(`/asset/${id}/delete`, function (msg) {
          toastr.success(msg);
          $('#data-table').DataTable().ajax.reload(null, false);
        });
      } else {
        swal('Cancelled', 'Your Data Is Safe :)', 'error');
      }
    });
  });

  // Dynamic account list by pay_by
  $('body').on('change', '#pay_by', function () {
    const account_type = $(this).val();
    $.post("{{ route('asset.payment.account') }}", {
      _token: "{{ csrf_token() }}",
      account_type
    }, function (html) {
      $('#account_info').html(html);
      $('.select3').select2();
    });
  });

});
</script>

@endsection

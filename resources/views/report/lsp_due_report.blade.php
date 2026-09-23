@extends('layouts.dashboard')
@section('title', ' | LSP Due Report')

@section('style')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-datepicker@1.10.0/dist/css/bootstrap-datepicker.min.css">
@endsection

@section('content')
<div class="row">
  <div class="col-md-12">
    <div class="card-box table-responsive mt-4">
      <div class="row">
        <div class="col-md-12 d-flex justify-content-between align-items-center flex-wrap gap-2">
          <h4 class="m-t-0 header-title mb-0"><b>{{ __('LSP Due Report') }}</b> <small class="text-muted">({{ $filterLabel }})</small></h4>
          <div class="d-flex gap-2">
            <button type="button" class="btn btn-success" id="btn-export-excel">Export Excel</button>
            <button type="button" class="btn btn-danger" id="btn-export-pdf">Export PDF</button>
            <button type="button" class="btn btn-primary" id="btn-print">Print</button>
          </div>
        </div>

        <div class="col-md-12 mt-3">
          <form method="GET" action="{{ route('report.lsp.due') }}" id="searching">
            <div class="row justify-content-end g-2">
              <div class="col-md-2">
                <select name="salesman_id" class="form-control select2">
                  <option value="">All LSPs</option>
                  @foreach ($salesmen as $salesman)
                    <option value="{{ $salesman->id }}" @selected(($filters['salesman_id'] ?? '') == $salesman->id)>{{ $salesman->name }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-2">
                <select name="product_id" class="form-control select2">
                  <option value="">All Products</option>
                  @foreach ($products as $product)
                    <option value="{{ $product->id }}" @selected(($filters['product_id'] ?? '') == $product->id)>{{ $product->product_name }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-1">
                <select name="per_page" class="form-control">
                  <option value="all" @selected(($filters['per_page'] ?? 'all') === 'all')>All</option>
                  <option value="10" @selected(($filters['per_page'] ?? 'all') === '10')>10</option>
                  <option value="25" @selected(($filters['per_page'] ?? 'all') === '25')>25</option>
                  <option value="50" @selected(($filters['per_page'] ?? 'all') === '50')>50</option>
                  <option value="100" @selected(($filters['per_page'] ?? 'all') === '100')>100</option>
                </select>
              </div>
              <div class="col-md-2 date-filter custom-filter-field">
                <div class="input-daterange input-group" id="date-range">
                  <input type="text" name="from_date" class="form-control datepicker" placeholder="From Date" value="{{ $filters['from_date'] ?? '' }}" autocomplete="off">
                  <span class="input-group-text bg-success text-white">to</span>
                  <input type="text" name="to_date" class="form-control datepicker" placeholder="To Date" value="{{ $filters['to_date'] ?? '' }}" autocomplete="off">
                </div>
              </div>
              <div class="col-md-1 d-grid">
                <button type="submit" class="btn btn-success">Search</button>
              </div>
            </div>
          </form>
        </div>
      </div>

      <div class="table-rep-plugin mt-3" id="print-area">
        <div class="table-responsive">
          <table id="lsp-due-table" class="table table-bordered w-100">
            <thead class="theme-primary text-white">
              <tr>
                <th>#</th>
                <th>Date</th>
                <th>Invoice</th>
                <th>LSP</th>
                <th>Customer</th>
                <th>Products</th>
                <th>Total</th>
                <th>Paid</th>
                <th>Due</th>
                <th>Payment Status</th>
              </tr>
            </thead>
            <tbody>
              @forelse($sales as $sale)
                <tr>
                  <td>{{ $sales->firstItem() + $loop->index }}</td>
                  <td>{{ \Carbon\Carbon::parse($sale->sale_date)->format('d-m-Y') }}</td>
                  <td>{{ $sale->invoice_no }}</td>
                  <td>{{ $sale->salesman?->name ?: '-' }}</td>
                  <td>{{ $sale->customer_name ?: '-' }}<br><small>{{ $sale->customer_phone }}</small></td>
                  <td>
                    @foreach ($sale->items as $item)
                      <span class="d-block">{{ $item->product?->product_name ?: '-' }} ({{ number_format($item->quantity, 2) }})</span>
                    @endforeach
                  </td>
                  <td>{{ number_format($sale->total_amount, 2) }}</td>
                  <td>{{ number_format($sale->paid_amount, 2) }}</td>
                  <td>{{ number_format($sale->due_amount, 2) }}</td>
                  <td>
                    @if ($sale->due_amount > 0)
                      <span class="badge bg-danger">Due</span>
                    @else
                      <span class="badge bg-success">Paid</span>
                    @endif
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="10" class="text-center">No LSP due sales found.</td>
                </tr>
              @endforelse
            </tbody>
            <tfoot>
              <tr>
                <td colspan="6" class="text-end fw-bold">Totals:</td>
                <td class="fw-bold">{{ number_format($sales->sum('total_amount'), 2) }}</td>
                <td class="fw-bold">{{ number_format($sales->sum('paid_amount'), 2) }}</td>
                <td class="fw-bold">{{ number_format($sales->sum('due_amount'), 2) }}</td>
                <td></td>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>

      <div class="text-center">
        {{ $sales->appends(request()->except('page'))->links() }}
      </div>
    </div>
  </div>
</div>
@endsection

@section('script')
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.full.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap-datepicker@1.10.0/dist/js/bootstrap-datepicker.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/jspdf@2.5.1/dist/jspdf.umd.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/jspdf-autotable@3.8.1/dist/jspdf.plugin.autotable.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/print-this@1.15.0/printThis.min.js"></script>

  <script>
    function fileTitle() {
      const d = new Date().toISOString().slice(0, 10);
      return 'LSP_Due_Report_' + d;
    }

    $('#btn-export-excel').on('click', function () {
      const table = document.getElementById('lsp-due-table');
      const wb = XLSX.utils.table_to_book(table, { sheet: 'Report', raw: true });
      XLSX.writeFile(wb, fileTitle() + '.xlsx');
    });

    $('#btn-export-pdf').on('click', function () {
      const { jsPDF } = window.jspdf;
      const doc = new jsPDF({ orientation: 'landscape', unit: 'pt', format: 'a4' });

      doc.setFontSize(14);
      doc.text('LSP Due Report', 40, 30);

      doc.autoTable({
        html: '#lsp-due-table',
        startY: 50,
        styles: { fontSize: 8, cellPadding: 3, overflow: 'linebreak' },
        headStyles: { fillColor: [40, 40, 40] },
        didDrawPage: (data) => {
          const pageSize = doc.internal.pageSize;
          const pageHeight = pageSize.getHeight ? pageSize.getHeight() : pageSize.height;
          doc.setFontSize(9);
          doc.text('Generated: ' + new Date().toLocaleString(), 40, pageHeight - 20);
          const pageCount = doc.internal.getNumberOfPages();
          doc.text('Page ' + doc.internal.getCurrentPageInfo().pageNumber + ' of ' + pageCount, pageSize.getWidth() - 100, pageHeight - 20);
        }
      });

      doc.save(fileTitle() + '.pdf');
    });

    $('#btn-print').on('click', function () {
      $('#print-area').printThis({
        importCSS: true,
        importStyle: true,
        pageTitle: 'LSP Due Report',
      });
    });

    document.addEventListener('DOMContentLoaded', function () {
      document.querySelectorAll('.custom-filter-field').forEach(el => el.style.display = '');

      $('.datepicker').datepicker({ autoclose: true, todayHighlight: true, format: 'yyyy-mm-dd' });
      $('select.select2').select2({ width: '100%' });
    });
  </script>
@endsection

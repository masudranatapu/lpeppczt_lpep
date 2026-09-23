@extends('layouts.dashboard')
@section('title', ' | Sale Due Report')

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
          <h4 class="m-t-0 header-title mb-0"><b>{{ __('Agent Sales Due Report') }}</b></h4>

          <div class="d-flex gap-2">
            <button class="btn btn-success" id="btn-export-excel">Export Excel</button>
            <button class="btn btn-danger" id="btn-export-pdf">Export PDF</button>
            <button class="btn btn-primary" id="btn-print">Print</button>
          </div>
        </div>

        {{-- Filters --}}
        <div class="col-md-12 mt-3">
          <form action="" id="searching">
            <div class="row justify-content-end g-2">
              @if (permission('filterByUser'))
                <div class="col-md-3">
                  <select name="user" class="form-control select2">
                    <option value="">All User</option>
                    @foreach ($agents->unique('id') as $user)
                      <option value="{{ $user->id }}" {{ request('user') == $user->id ? 'selected' : '' }}>
                        {{ $user->name }} (<small>{{ $user->employee_name }}</small>)
                      </option>
                    @endforeach
                  </select>
                </div>
              @endif

              <div class="col-md-3">
                <select name="product" class="form-control select2">
                  <option value="">All Product</option>
                  @foreach ($products->unique('id') as $product)
                    <option value="{{ $product->id }}" {{ request('product') == $product->id ? 'selected' : '' }}>
                      {{ $product->product_name }}
                    </option>
                  @endforeach
                </select>
              </div>

              <div class="col-md-5">
                <div class="">
                    <div class="input-daterange input-group" id="date-range">
                        <input type="text" placeholder="Start Date"
                            class="form-control datepicker startdate" name="start_date"
                            value="{{ request('start_date') ?? '' }}" autocomplete="off">
                        <span class="input-group-addon bg-success b-0 text-white">to</span>
                        <input type="text" placeholder="End Date"
                            class="form-control datepicker enddate" name="end_date"
                            value="{{ request('end_date') ?? '' }}" autocomplete="off">
                    </div>
                </div>
            </div>

              <div class="col-md-1 d-grid">
                <button type="submit" class="btn btn-success">Search</button>
              </div>
            </div>
          </form>
        </div>
      </div>

      {{-- Table --}}
      <div class="table-rep-plugin mt-3" id="print-area">
        <div class="table-responsive">
          <table id="due-sales-table" class="table table-bordered w-100">
            <thead class="theme-primary text-white">
              <tr>
                <th>{{ __('page.sale')[1] }}</th>
                <th>{{ __('page.sale')[2] }}</th>
                <th>{{ __('page.sale')[3] }}</th>
                <th>{{ __('Sector Beneficiary') }}</th>
                <th>{{ __('page.sale')[5] }}</th>
                <th>{{ __('page.sale')[6] }}</th>
                <th>{{ __('page.sale')[7] }}</th>
                <th>{{ __('page.sale')[8] }}</th>
                <th>{{ __('page.sale')[10] }}</th>
                <th>{{ __('Profit') }}</th>
                <th>{{ __('page.sale')[9] }}</th>
                <th>{{ __('Note') }}</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($sales as $sale)
                <tr>
                  <td>{{ $loop->index + 1 }}</td>
                  <td>{{ $sale->sale_date }}</td>
                  <td>{{ date('Y') . $sale->id }}</td>
                  <td>
                    @forelse ($sale->saleProducts as $data)
                      <span>{{ $data->product?->product_name }}@if(!$loop->last),@endif</span>
                    @empty
                    @endforelse
                  </td>
                  <td>
                    @if ($sale->customer_name || $sale->customer_phone)
                      {{ $sale->customer_name }} <br>
                      <small>({{ $sale->customer_phone }})</small>
                    @else
                      {{ $sale->customer?->name }} <br>
                      <small>({{ $sale->customer?->mobile }})</small>
                    @endif
                  </td>
                  <td>{{ $sale->agent->name }}</td>
                  <td>{{ $sale->total_amount }}</td>
                  <td>{{ $sale->paying_amount }}</td>
                  <td>{{ $sale->total_amount - $sale->paying_amount }}</td>
                  <td>{{ $sale->profit_amount }}</td>
                  <td><span class="badge bg-danger">Due</span></td>
                  <td>{{ $sale->note ?? '-' }}</td>
                </tr>
              @endforeach
            </tbody>
            <tfoot>
              <tr>
                <td colspan="6" class="text-end fw-bold">Totals:</td>
                <td class="fw-bold">{{ $sales->sum('total_amount') }}</td>
                <td class="fw-bold">{{ $sales->sum('paying_amount') }}</td>
                <td class="fw-bold">{{ $sales->sum('total_amount') - $sales->sum('paying_amount') }}</td>
                <td class="fw-bold">{{ $total_profit }}</td>
                <td colspan="2"></td>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>

      <div class="text-center">
        {{ $sales->appends(request()->input())->links() }}
      </div>
    </div>
  </div>
</div>
@endsection

@section('script')
  <!--<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>-->
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.full.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap-datepicker@1.10.0/dist/js/bootstrap-datepicker.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/jspdf@2.5.1/dist/jspdf.umd.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/jspdf-autotable@3.8.1/dist/jspdf.plugin.autotable.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/print-this@1.15.0/printThis.min.js"></script>

  <script>
    function fileTitle() {
      const d = new Date().toISOString().slice(0,10);
      return 'Agent_Sales_Due_Report_' + d;
    }

    $('#btn-export-excel').on('click', function () {
      const table = document.getElementById('due-sales-table');
      const wb = XLSX.utils.table_to_book(table, { sheet: 'Report', raw: true });
      XLSX.writeFile(wb, fileTitle() + '.xlsx');
    });

    $('#btn-export-pdf').on('click', function () {
      const { jsPDF } = window.jspdf;
      const doc = new jsPDF({ orientation: 'landscape', unit: 'pt', format: 'a4' });

      doc.setFontSize(14);
      doc.text('Agent Sales Due Report', 40, 30);

      doc.autoTable({
        html: '#due-sales-table',
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
        pageTitle: 'Agent Sales Due Report',
      });
    });

    $(function () {
      $('.datepicker').datepicker({ autoclose: true, todayHighlight: true, format: 'yyyy-mm-dd' });
      $('select.select2').select2({ width: '100%' });
    });
  </script>
@endsection

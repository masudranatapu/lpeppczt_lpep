{{-- resources/views/visit-info/index.blade.php --}}
@extends('layouts.dashboard')

@section('content')
@php use Carbon\Carbon; @endphp

<style>
/* Minimal dropdown styles (no Bootstrap JS needed) */
.btn-group { position: relative; display: inline-block; }
.btn-outline-secondary { border: 1px solid #ced4da; background: #fff; }
.dropdown-menu-custom {
    position: absolute; right: 0; top: 100%;
    min-width: 220px; padding: .5rem 0; margin-top: .25rem;
    background: #fff; border: 1px solid rgba(0,0,0,.15); border-radius: .25rem;
    box-shadow: 0 .5rem 1rem rgba(0,0,0,.15);
    display: none; z-index: 1000;
}
.dropdown-menu-custom.show { display: block; }
.dropdown-item-custom {
    display: block; width: 100%; padding: .375rem 1rem; clear: both;
    color: #212529; text-align: inherit; text-decoration: none; white-space: nowrap;
}
.dropdown-item-custom:hover { background-color: #f8f9fa; }
.dropdown-divider { height: 0; margin: .5rem 0; overflow: hidden; border-top: 1px solid #e9ecef; }
.mr-2 { margin-right: .5rem; }

/* Print styles: কেবল টেবিল দেখাবো */
/* PRINT: show full table across pages */
@media print {
  /* Hide everything except print area */
  body * { visibility: hidden !important; }

  /* Show only the print area */
  #print-area, #print-area * { visibility: visible !important; }

  /* Let it flow across pages — no absolute positioning */
  #print-area { 
    position: static !important; 
    overflow: visible !important; 
  }

  /* Bootstrap wrappers may clip */
  .table-responsive { 
    overflow: visible !important; 
  }
  .card, .card-body { 
    overflow: visible !important; 
  }

  /* Table pagination-friendly rules */
  table { width: 100% !important; border-collapse: collapse !important; }
  thead { display: table-header-group; }   /* repeat header on each page */
  tfoot { display: table-footer-group; }
  tr, img { 
    break-inside: avoid !important; 
    page-break-inside: avoid !important; 
  }

  /* Optional: page size/orientation + margins */
  @page { 
    size: A4 landscape; 
    margin: 12mm; 
  }

  /* Hide buttons/controls in print */
  .no-print { display: none !important; }
}

</style>

<div class="row">
        <div class="col-12">
            <div class="card-box table-responsive mt-4" style="border-top: 3px solid #2aa9b9;">
    <div class="card-header d-flex align-items-center justify-content-between">
      <h3 class="card-title mb-0">Visit Information</h3>

      <div class="card-tools d-flex align-items-center no-print">
        <a href="{{ route('visit-info.create') }}" class="btn btn-primary mr-2">
          <i class="fas fa-plus"></i> Add New Visit
        </a>

        {{-- Excel export (server-side) --}}
        <a class="btn btn-danger mr-2" href="{{ route('visit-info.export.excel', request()->all()) }}">
          <i class="fas fa-file-excel mr-2"></i> Excel (.xlsx)
        </a>

        {{-- PDF export (client-side) --}}
        <button id="btnExportPdf" type="button" class="btn btn-secondary mr-2">
          <i class="fas fa-file-pdf mr-2"></i> PDF (.pdf)
        </button>

        {{-- Print (client-side) --}}
        <button id="btnPrint" type="button" class="btn btn-success">
          <i class="fas fa-print mr-2"></i> Print
        </button>
      </div>
    </div>

    <div class="card-body">
      {{-- Filters --}}
      <form action="{{ route('visit-info.index') }}" method="GET" class="mb-4 no-print">
        <div class="row">
          <div class="col-md-4">
            <div class="form-group">
              <label for="visit_date">Visit Date</label>
              <input type="date" name="visit_date" id="visit_date" class="form-control"
                     value="{{ request('visit_date') }}">
            </div>
          </div>

          @if (!isRole(ROLE_AGENT))
          <div class="col-md-4">
            <div class="form-group">
              <label for="agent_id">Agent</label>
              <select name="agent_id" id="agent_id" class="form-control">
                <option value="">All Agents</option>
                @foreach($agents as $agent)
                  <option value="{{ $agent->id }}" {{ request('agent_id') == $agent->id ? 'selected' : '' }}>
                    {{ $agent->name }}
                  </option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <label for="namenumber">Beneficiary Name or number</label>
              <input type="text" name="namenumber" id="namenumber" class="form-control" placeholder="Beneficiary Name or number">
            </div>
          </div>
          @endif

          <div class="col-md-4 d-flex align-items-end">
            <button type="submit" class="btn btn-primary mr-2">Filter</button>
            <a href="{{ route('visit-info.index') }}" class="btn btn-secondary">Reset</a>
          </div>
        </div>
      </form>
      <div id="print-area">
        <div class="mb-3">
          <h4 class="mb-0">Visit Information</h4>
          <small>
            @php
              $parts = [];
              if(request('visit_date')) $parts[] = 'Visit Date: '.request('visit_date');
              if(request('agent_id')){
                $a = $agents->firstWhere('id', request('agent_id'));
                $parts[] = 'Agent: '.($a->name ?? 'N/A');
              }
              echo implode(' | ', $parts);
            @endphp
            @if(!empty($parts)) <span> | </span> @endif
            <!--Generated at: {{ now()->format('Y-m-d h:i A') }}-->
          </small>
        </div>

        {{-- Table --}}
        <div class="table-responsive">
          <table id="visit-table" class="table table-bordered table-striped align-middle">
            <thead>
            <tr>
              <th>#SL</th>
              <th>Beneficiary Cell Number</th>
              <th>Beneficiary Name</th>
              <th>Beneficiary Group</th>
              <th>Beneficiary Number</th>
              <th>Visit date & Time</th>
              <th>Invoice no</th>
              {{-- <th>Area</th> --}}
              <th>Total Amount</th>
              <th>Fee Types</th>
              <th>Description</th>
              <th class="no-print">Actions</th>
            </tr>
            </thead>
            <tbody>
            @forelse($visits as $key => $visit)
              @php $memos = is_array($visit->memo_no) ? $visit->memo_no : []; @endphp
              <tr>
                <td>{{ $key + 1 }}</td>
                <td>{{ $visit->appCustomer->mobile ?? 'N/A' }}</td>
                <td>{{ $visit->appCustomer->name ?? 'N/A' }}</td>
                <td>{{ $visit->customer_number }}</td>
                <td>{{ $visit->appCustomer->beneficiary_number }}</td>
                <td>{{ $visit->visit_date ? Carbon::parse($visit->visit_date)->format('Y-m-d h:i A') : 'N/A' }}</td>
                <td>
                  @if(count($memos))
                    <ul class="mb-0 pl-3">
                      @foreach($memos as $m)
                        <li>{{ $m }}</li>
                      @endforeach
                    </ul>
                  @else
                    <span class="text-muted">—</span>
                  @endif
                </td>
                {{-- <td>{{ $visit->area->name ?? 'N/A' }}</td> --}}
                <td>{{ number_format($visit->fees->sum('amount'), 2) ?? 'N/A' }}</td>
                <td>
                  @if($visit->fees->isNotEmpty())
                    <ul class="mb-0 pl-3">
                      @foreach($visit->fees as $fee)
                        <li>{{ $fee->fee_type ?? 'N/A' }}: {{ number_format($fee->amount, 2) }}</li>
                      @endforeach
                    </ul>
                  @else
                    <span class="text-muted">No fees</span>
                  @endif
                </td>
                <td>{{ $visit->description ?? 'N/A' }}</td>
                <td class="text-nowrap no-print">
                  <a href="{{ route('visit-info.show', $visit) }}" class="btn btn-sm btn-success" title="View">
                    <i class="fas fa-eye"></i>
                  </a>
                  <a href="{{ route('visit-info.edit', $visit) }}" class="btn btn-sm btn-info" title="Edit">
                    <i class="fas fa-edit"></i>
                  </a>
                  <form action="{{ route('visit-info.destroy', $visit) }}" method="POST"
                        class="d-inline" onsubmit="return confirm('Are you sure?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                      <i class="fas fa-trash"></i>
                    </button>
                  </form>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="11" class="text-center">No visits found</td>
              </tr>
            @endforelse
            </tbody>
          </table>
        </div>
      </div>

      {{-- Pagination --}}
      <div class="mt-4 no-print">
        {{ $visits->links() }}
      </div>
    </div>
  </div>
</div>
<div>
{{-- ======= CDN & Scripts (client-side export) ======= --}}
{{-- jQuery --}}
<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
{{-- jsPDF + autotable --}}
<script src="https://cdn.jsdelivr.net/npm/jspdf@2.5.1/dist/jspdf.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jspdf-autotable@3.8.3/dist/jspdf.plugin.autotable.min.js"></script>

<script>
  (function() {
    // PRINT: শুধুমাত্র #print-area প্রিন্ট হবে (CSS @media print দ্বারা)
    $('#btnPrint').on('click', function () {
      window.print();
    });

    // PDF EXPORT: jsPDF + autoTable; #visit-table থেকে সরাসরি
    $('#btnExportPdf').on('click', function () {
      const { jsPDF } = window.jspdf;

      // Landscape হলে বড় টেবিল ভালো বসে, প্রয়োজন হলে 'p' দিন
      const doc = new jsPDF('l', 'pt', 'a4');

      // Header/title
      const title = 'Visit Information';
      const meta  = (function() {
        const parts = [];
        const visitDate = @json(request('visit_date'));
        const agentId   = @json(request('agent_id'));
        let agentName   = '';
        @if (!isRole(ROLE_AGENT))
          @php
            $jsonAgents = $agents->map(fn($a)=>['id'=>$a->id,'name'=>$a->name])->values();
          @endphp
          const agents = @json($jsonAgents);
          const found  = agents.find(a => String(a.id) === String(agentId));
          agentName = found ? found.name : '';
        @endif
        if (visitDate) parts.push('Visit Date: ' + visitDate);
        if (agentId && agentName) parts.push('Agent: ' + agentName);
        parts.push('Generated at: {{ now()->format('Y-m-d h:i A') }}');
        return parts.join('  |  ');
      })();

      // Title text
      doc.setFontSize(16);
      doc.text(title, 40, 40);
      doc.setFontSize(10);
      doc.text(meta, 40, 58);

      // autoTable from HTML
      doc.autoTable({
          html: '#visit-table',
          startY: 80,
          theme: 'grid', // optional, just to get borders
          styles: {
            fontSize: 8,
            cellPadding: 4,
            overflow: 'linebreak',
            textColor: [0, 0, 0],        // make all text black by default
            lineColor: [200, 200, 200],
            lineWidth: 0.5
          },
          headStyles: {
            fillColor: [240, 240, 240],  // light gray header background
            textColor: [0, 0, 0],        // << IMPORTANT: header text black
            fontStyle: 'bold',
            halign: 'center'             // optional
          },
          alternateRowStyles: {
            fillColor: [250, 250, 250],  // subtle zebra
            textColor: [0, 0, 0]
          },
          columnStyles: {
            0: { cellWidth: 40 },
            5: { cellWidth: 120 },
            6: { cellWidth: 120 },
            7: { cellWidth: 90 },
            8: { cellWidth: 150 },
            10:{ cellWidth: 80 }
          },
          didParseCell: function (data) {
            // hide "Actions" (has .no-print)
            if (data.cell && data.cell.raw) {
              const el = data.cell.raw;
              if (el.classList && el.classList.contains('no-print')) {
                data.cell.text = [''];
              }
            }
          },
          didDrawPage: function () {
            const pageSize  = doc.internal.pageSize;
            const pageW     = pageSize.width  || pageSize.getWidth();
            const pageH     = pageSize.height || pageSize.getHeight();
            doc.setFontSize(9);
            doc.text('Page ' + doc.internal.getNumberOfPages(), pageW - 60, pageH - 20);
          }
        });


      // File name
      const fileNameParts = [];
      fileNameParts.push('visit-info');
      const vd = {{ json_encode(request('visit_date')) }}; if (vd) fileNameParts.push(vd);
      const name = fileNameParts.join('_') + '.pdf';

      doc.save(name);
    });
  })();
</script>

<script>
  $('#btnPrint').on('click', function () {
    const printContents = document.querySelector('#print-area').innerHTML;
    const win = window.open('', '', 'width=1200,height=800');

    // minimal print stylesheet inside the new window
    win.document.write(`
      <html>
        <head>
          <title>Visit Information</title>
          <style>
            @page { size: A4 landscape; margin: 12mm; }
            body { font-family: Arial, sans-serif; }
            table { width: 100%; border-collapse: collapse; }
            th, td { border: 1px solid #ccc; padding: 6px; vertical-align: top; }
            thead { display: table-header-group; }
            tfoot { display: table-footer-group; }
            tr { break-inside: avoid; page-break-inside: avoid; }
          </style>
        </head>
        <body>${printContents}</body>
      </html>
    `);

    win.document.close();
    win.focus();
    // Give the browser a moment to render before printing
    win.onload = function() {
      win.print();
      win.close();
    };
  });
</script>
@endsection

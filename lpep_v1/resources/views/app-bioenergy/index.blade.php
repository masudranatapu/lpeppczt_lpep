@extends('layouts.dashboard')

@push('css')
<style>
    #deleteData.disabled {
        pointer-events: none;
        background: #2aa9b9;
    }
    .dataTables_wrapper .dt-buttons {
        margin-bottom: .75rem;
    }
    tfoot th, tfoot td {
        font-weight: 700;
    }
</style>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">
@endpush

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card-box table-responsive mt-4" style="border-top: 3px solid #2aa9b9;">
            <div class="row">
                <div class="col-md-6">
                    <h4 class="m-t-0 header-title mb-5"><b> Renewable Energy</b></h4>
                </div>
                <div class="col-md-6 text-right">
                    <button class="btn btn-primary waves-effect waves-light m-b-5" id="addNew">
                        <i class="fa fa-plus-square m-r-5"></i>
                        <span>{{ __('page.energy')[1] }}</span>
                    </button>
                </div>
            </div>

            <div class="row mb-3">
                @if (Auth::user()?->userPermission?->role?->role_name === 'Admin' || Auth::user()?->email == 'faridpur@gmail.com')
                    <div class="col-md-3">
                        <label for="agent_filter">Area</label>
                        <select class="form-control" id="staf_filter">
                            <option value="">Select Area</option>
                            {{-- @foreach($staffs as $staf)
                                <option value="{{ $staf->id }}">{{ $staf->name }}</option>
                            @endforeach --}}
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="agent_filter">Agents</label>
                        <select class="form-control" id="agent_filter">
                            <option value="">Select Agent</option>
                           {{-- @foreach($agents as $agent)
                                <option value="{{ $agent->id }}">{{ $agent->employee_name }}</option>
                            @endforeach --}}
                        </select>
                    </div>
                @endif

               
               
            </div>

            <br><br>

            <table id="data-table" class="table table-striped table-bordered mt-5" cellspacing="0" width="100%">
                <thead class="theme-primary text-white">
                    <tr>
                        <th rowspan="2">{{ __('page.common.sl') }}</th>
                        <th rowspan="2">Visit Date</th>
                        <th rowspan="2">Client Name</th>
                        <th rowspan="2">Client Number</th>
                        <th rowspan="2">District</th>
                       
                        <th colspan="5" rowspan="1" class="text-center">Livestock Details</th>
                        <th colspan="2" rowspan="1" class="text-center">Plant Size</th>
                        <th colspan="3" rowspan="1" class="text-center">Plant Start Date</th>
                        <th colspan="3" rowspan="1" class="text-center">Plant End Date</th>
                        <th colspan="4" rowspan="1" class="text-center">P.O Name</th>
                        <th clospan="4" rowspan="1" class="text-center">Plant Condition</th>
                         <th rowspan="2">{{ __('page.common.action') }}</th>

                       
                    </tr>
                    <tr>
                        <th> 01</th>
                        <th> 01-05-1992</th>
                        <th> Rahim </th>
                        <th>00023</th>
                        <th>Upazilla</th>
                        <th>Village</th>
                        <th>Unions</th>

                        <th>Cow</th>
                        <th>Bull</th>
                        <th>Bakna</th>
                        <th>Goat</th>
                        <th>Khasi</th>
                    
                        
                     <td>2.4/4.8 m^3</td>
                    
                    
                        <td>Y-M-D</td>
                   
                   
                        <td>Y-M-D</td>
                    
                    
                        <td>Rafi</td>
                    
                      
                        <td>Good</td>
                

                    <th>  
                        <td>Edit</td>
                        <td>view</td>
                        <td>Delete</td>
                
                    </th>
                        
                        
                     
                        
                    </tr>
                </thead>

                <tfoot>
                    <tr>
                        <th colspan="7" class="text-right">Total</th>
                        <th id="total_cow" class="text-center">0</th>
                        <th id="total_bull" class="text-center">0</th>
                        <th id="total_bakna" class="text-center">0</th>
                        <th id="total_goat" class="text-center">0</th>
                        <th id="total_khasi" class="text-center">0</th>
                        <th colspan="7"></th>
                        <th></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div> 

<div class="modal fade bd-example-modal-xl" id="modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" style="width: 100%;">
        <div class="modal-content" id="modalcontent" style="padding: 0px !important"></div>
    </div>
</div>
@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>

<script>
    var table;

    function setTotals(totals) {
        $('#total_cow').text(totals?.cow ?? 0);
        $('#total_bull').text(totals?.bull ?? 0);
        $('#total_bakna').text(totals?.bakna ?? 0);
        $('#total_goat').text(totals?.goat ?? 0);
        $('#total_khasi').text(totals?.khasi ?? 0);
    }

    $(function () {
        table = $('#data-table').DataTable({
            processing: true,
            serverSide: true,
            // responsive: true,
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'All']],
            pageLength: 10,
            ajax: {
                url: "{{ route('app-customer.index') }}",
                data: function(d) {
                    d.group_name = $('#filter_group_name').val();
                    d.group_number = $('#filter_group_number').val();
                    d.agent_id = $('#agent_filter').val();
                    d.staf = $('#staf_filter').val();
                    return d;
                }
            },
            order: [[0, 'desc']],
            dom: "<'row'<'col-sm-6'l><'col-sm-6 text-right'B>>" + "frtip",
            buttons: [
                {
                    extend: 'excelHtml5',
                    title: 'Beneficiaries',
                    filename: 'beneficiaries',
                    exportOptions: { columns: ':visible:not(:last-child)' }
                },
                {
                    extend: 'pdfHtml5',
                    title: 'Beneficiaries',
                    filename: 'beneficiaries',
                    orientation: 'landscape',
                    pageSize: 'A4',
                    exportOptions: { columns: ':visible:not(:last-child)' }
                },
                {
                    extend: 'print',
                    title: 'Beneficiaries',
                    exportOptions: { columns: ':visible:not(:last-child)' },
                    customize: function (win) {
                        $(win.document.body).css('font-size', '10pt');
                        $(win.document.body).find('table').addClass('compact').css('font-size', 'inherit');
                    }
                }
            ],
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
                { width: '17%', data: 'mobile', name: 'mobile' },
                { width: '17%', data: 'name', name: 'name' },
                { width: '17%', data: 'beneficiary_number', name: 'beneficiary_number' },
                { width: '17%', data: 'group_number', name: 'group_number' },

                { width: '17%', data: 'village', name: 'village' },
                { width: '17%', data: 'unions', name: 'unions' },

                { width: '14%', data: 'cow', name: 'cow' },
                { width: '14%', data: 'bull', name: 'bull' },
                { width: '14%', data: 'bakna', name: 'bakna' },
                { width: '14%', data: 'goat', name: 'goat' },
                { width: '14%', data: 'khasi', name: 'khasi' },

                { width: '14%', data: 'membership', name: 'membership' },
                { width: '14%', data: 'deworming', name: 'deworming' },
                { width: '14%', data: 'bringing', name: 'bringing' },
                { width: '14%', data: 'fattening', name: 'fattening' },
                { width: '14%', data: 'treatment', name: 'treatment' },
                { width: '14%', data: 'ai', name: 'ai' },
                { width: '14%', data: 'medicine', name: 'medicine' },

                { width: '14%', data: 'action', name: 'action', orderable: false, searchable: true }
            ],
            drawCallback: function(settings) {
                $("[data-toggle=popover]").popover();
            }
        });

        $('#data-table').on('xhr.dt', function (e, settings, json, xhr) {
            setTotals(json?.totals);
        });
    });

    $(document).ready(function () {
        $('#filter_group_name').select2({ placeholder: 'Select Beneficiary Number', allowClear: true });
        $('#agent_filter').select2({ placeholder: 'Select Agent', allowClear: true });
        $('#filter_group_number').select2({ placeholder: 'Select Group Number', allowClear: true });
        $('#staf_filter').select2({ placeholder: 'Select Area', allowClear: true });

        $('#filter_group_name, #agent_filter, #filter_group_number, #staf_filter').on('change', function () {
            table.ajax.reload();
        });

        var delayTimer;
        $('#filter_group_number').on('change', function() {
            clearTimeout(delayTimer);
            delayTimer = setTimeout(function() {
                table.ajax.reload();
            }, 300);
        });

        $('body').on('click', "#addNew", function() {
            $.get("{{ route('app-customer.create') }}", function(data) {
                $('#modalcontent').html(data);
                $("#modal").modal('show');
            });
        });

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
                toastr.success(data);
                $('#data-table').DataTable().ajax.reload(null, false);
                $("#modal").modal('hide');
            })
            .catch(error => {
                toastr.error(error.responseJSON.error);
            });
        });

        $('body').on('click', "#viewAppCustomer", function() {
            let id = $(this).data('id');
            $.get('{{ route('app-customer.show', '#id') }}'.replace('#id', id), function(data) {
                $('#modalcontent').html(data);
                $("#modal").modal('show');
            });
        });

        $('body').on('click', "#editAppCustomer", function() {
            let id = $(this).data('id');
            $.get('{{ route('app-customer.edit', '#id') }}'.replace('#id', id), function(data) {
                $('#modalcontent').html(data);
                $("#modal").modal('show');
            });
        });

        $('body').on('click', "#viewQr", function() {
            let id = $(this).data('id');
            $.get('{{ route('customer.qr', '#id') }}'.replace('#id', id), function(data) {
                $('#modalcontent').html(data);
                $("#modal").modal('show');
            });
        });

        $('body').on('submit', "#customer-update", function(e) {
            e.preventDefault();
            $.ajax({
                url: $(this)[0].action,
                data: $(this).serialize(),
                method: 'post'
            })
            .then(function(data) {
                toastr.success(data);
                $('#data-table').DataTable().ajax.reload(null, false);
                $("#modal").modal('hide');
            })
            .catch(error => {
                toastr.error(error.responseJSON.error);
            });
        });

        $('body').on('click', "#deleteData", function() {
            let id = $(this).data('id');
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
                        url: "{{ route('app-customer.destroy', '#id') }}".replace('#id', id),
                        data: { _token: "{{ csrf_token() }}" },
                        method: 'DELETE'
                    })
                    .then(function(data) {
                        toastr.success(data);
                        $('#data-table').DataTable().ajax.reload(null, false);
                    });
                } else {
                    swal("Cancelled", "Your Data Is Safe :)", "error");
                }
            });
        });
    });
</script>
@endsection
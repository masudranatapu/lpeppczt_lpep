<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="modal-title">Renewable Energy Details</h5>
        <div>
            <a href="{{ route('renewable-energy.download-pdf', $entry->id) }}" target="_blank" class="btn btn-sm btn-danger">
                <i class="fa fa-file-pdf"></i> Download PDF
            </a>
            <button type="button" class="btn btn-sm btn-danger" data-dismiss="modal">&times;</button>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered">
                <tr>
                    <th width="30%">Type</th>
                    <td>{{ ucfirst($entry->type) }}</td>
                </tr>
                <tr>
                    <th>Visit Date</th>
                    <td>{{ $entry->visit_date }}</td>
                </tr>
                <tr>
                    <th>Area (Staff)</th>
                    <td>{{ $entry->area->name ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <th>Agent</th>
                    <td>{{ $entry->agent->name ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <th>Client Name</th>
                    <td>{{ $entry->client_name }}</td>
                </tr>
                <tr>
                    <th>Client Number</th>
                    <td>{{ $entry->client_number ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <th>Location</th>
                    <td>
                        {{ $entry->union->name ?? '' }}, 
                        {{ $entry->upazila->name ?? '' }}, 
                        {{ $entry->district->name ?? '' }}
                    </td>
                </tr>
                
                  <tr>
                    <th>Ward No.</th>
                    <td>{{ $entry->ward_number ?? 'N/A' }}</td>
                </tr>
                
                <tr>
                    <th>Village</th>
                    <td>{{ $entry->village ?? 'N/A' }}</td>
                </tr>
                
                
                <tr>
                    <th>P.O Name</th>
                    <td>{{ $entry->po_name ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <th>Plant Size</th>
                    <td>{{ $entry->plant_size }} {{ $entry->plant_size_unit }}</td>
                </tr>
                <tr>
                    <th>Plant Duration</th>
                    <td>{{ $entry->plant_start_date ?? 'N/A' }} to {{ $entry->plant_end_date ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <th>Livestock Details</th>
                    <td>{{ $entry->livestock_details ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <th>Contribution/Condition</th>
                    <td>{{ $entry->contribution_condition ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <th>Remarks</th>
                    <td>{{ $entry->remarks ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <th>Document</th>
                    <td>
                        @if($entry->document)
                            <a href="{{ asset($entry->document) }}" target="_blank" class="btn btn-xs btn-primary">
                                <i class="fa fa-download"></i> Download / View File
                            </a>
                        @else
                            N/A
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>Created By</th>
                    <td>{{ $entry->creator->name ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <th>Created At</th>
                    <td>{{ $entry->created_at }}</td>
                </tr>
            </table>
        </div>
    </div>
    <div class="card-footer text-right">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
    </div>
</div>

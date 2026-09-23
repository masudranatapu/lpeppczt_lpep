<div class="card">
    <div class="card-header">
        <h5 style="margin-bottom: 10px;">Edit Renewable Energy Entry</h5>
    </div>
    <div class="card-body">
        <form id="renewable-energy-form-update" action="{{ route('renewable-energy.update', $entry->id) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Energy Type <span class="text-danger">*</span></label>
                        <select name="type" class="form-control" required>
                            <option value="">Select Type</option>
                            <option value="biogas" {{ $entry->type == 'biogas' ? 'selected' : '' }}>Biogas</option>
                            <option value="solar" {{ $entry->type == 'solar' ? 'selected' : '' }}>Solar</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Visit Date <span class="text-danger">*</span></label>
                        <input type="date" name="visit_date" class="form-control" value="{{ $entry->visit_date }}" required>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Area (Staff) <span class="text-danger">*</span></label>
                        <select name="area_id" class="form-control select2" required>
                            <option value="">Select Staff</option>
                            @foreach($staffs as $staff)
                                <option value="{{ $staff->id }}" {{ $entry->area_id == $staff->id ? 'selected' : '' }}>{{ $staff->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Agent Name</label>
                        <select name="agent_id" class="form-control select2">
                            <option value="">Select Agent</option>
                            @foreach($agents as $agent)
                                <option value="{{ $agent->id }}" {{ $entry->agent_id == $agent->id ? 'selected' : '' }}>{{ $agent->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <hr>
            <h5>Client Information</h5>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Client Name <span class="text-danger">*</span></label>
                        <input type="text" name="client_name" class="form-control" value="{{ $entry->client_name }}" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Client Number</label>
                        <input type="text" name="client_number" class="form-control" value="{{ $entry->client_number }}">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Division</label>
                        <select name="division_id" class="form-control division_id">
                            <option value="">Select</option>
                            @foreach($divisions as $id => $name)
                                <option value="{{ $id }}" {{ $entry->division_id == $id ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>District</label>
                        <select name="district_id" class="form-control district_id">
                            <option value="">Select</option>
                            @foreach($districts as $id => $name)
                                <option value="{{ $id }}" {{ $entry->district_id == $id ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Upazila</label>
                        <select name="upazila_id" class="form-control upazila_id">
                            <option value="">Select</option>
                            @foreach($upazilas as $id => $name)
                                <option value="{{ $id }}" {{ $entry->upazila_id == $id ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Union</label>
                        <select name="union_id" class="form-control union_id">
                            <option value="">Select</option>
                            @foreach($unions as $id => $name)
                                <option value="{{ $id }}" {{ $entry->union_id == $id ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Village</label>
                        <input type="text" name="village" class="form-control" value="{{ $entry->village }}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>P.O Name</label>
                        <input type="text" name="po_name" class="form-control" value="{{ $entry->po_name }}">
                    </div>
                </div>
            </div>

            <hr>
            <h5>Technical Details</h5>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Plant Size</label>
                        <input type="number" step="0.01" name="plant_size" class="form-control" value="{{ $entry->plant_size }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Plant Size Unit</label>
                        <input type="text" name="plant_size_unit" class="form-control" value="{{ $entry->plant_size_unit }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Livestock Details (for Biogas)</label>
                        <textarea name="livestock_details" class="form-control" rows="1">{{ $entry->livestock_details }}</textarea>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Plant Start Date</label>
                        <input type="date" name="plant_start_date" class="form-control" value="{{ $entry->plant_start_date }}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Plant End Date</label>
                        <input type="date" name="plant_end_date" class="form-control" value="{{ $entry->plant_end_date }}">
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label>Plant Contribution / Condition</label>
                <textarea name="contribution_condition" class="form-control" rows="2">{{ $entry->contribution_condition }}</textarea>
            </div>

            <div class="form-group">
                <label>Remarks</label>
                <textarea name="remarks" class="form-control" rows="2">{{ $entry->remarks }}</textarea>
            </div>

            <div class="form-group">
                <label>Document / File (Image, PDF, etc. Max 5MB)</label>
                @if($entry->document)
                    <div class="mb-2">
                        <a href="{{ asset($entry->document) }}" target="_blank" class="btn btn-xs btn-info">
                            <i class="fa fa-eye"></i> View Current File
                        </a>
                    </div>
                @endif
                <input type="file" name="document" class="form-control" accept="image/*,.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.zip,.rar">
            </div>

            <div class="text-right mt-3">
                <button class="btn btn-success" type="submit">Update Entry</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>
        </form>
    </div>
</div>

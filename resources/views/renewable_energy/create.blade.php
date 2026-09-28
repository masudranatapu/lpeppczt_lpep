<div class="card">
    <div class="card-header">
        <h5 style="margin-bottom: 10px;">Add Renewable Energy Entry</h5>
    </div>
    <div class="card-body">
        <form id="renewable-energy-form-store" action="{{ route('renewable-energy.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label>Energy Type <span class="text-danger">*</span></label>
                        <select name="type" class="form-control" required>
                            <option value="">Select Type</option>
                            <option value="biogas">Biogas</option>
                            <option value="solar">Solar</option>
                        </select>
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
                                <option value="{{ $staff->id }}">{{ $staff->name }}</option>
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
                                <option value="{{ $agent->id }}">{{ $agent->name }}</option>
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
                        <input type="text" name="client_name" class="form-control" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Client Number</label>
                        <input type="text" name="client_number" class="form-control">
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
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>District</label>
                        <select name="district_id" class="form-control district_id">
                            <option value="">Select</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Upazila</label>
                        <select name="upazila_id" class="form-control upazila_id">
                            <option value="">Select</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Union</label>
                        <select name="union_id" class="form-control union_id">
                            <option value="">Select</option>
                        </select>
                    </div>
                </div>

                 <div class="col-md-6">
                     <label class="text-xs font-bold uppercase tracking-wide text-slate-500">Ward Number
                        <select name="ward_number" class="mt-1 h-10 w-full rounded-lg border border-slate-300 bg-white px-3 text-sm font-normal normal-case">
                            <option value="">Select Ward Number</option>
                            @foreach(range(1, 20) as $number)<option value="{{ $number }}" @selected((string) old('ward_number') === (string) $number)>{{ $number }}</option>@endforeach
                        </select>
                    </label>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Village</label>
                        <input type="text" name="village" class="form-control">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>P.O Name</label>
                        <input type="text" name="po_name" class="form-control">
                    </div>
                </div>
            </div>

            <hr>
            <h5>Technical Details</h5>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Plant Size</label>
                        <input type="number" step="0.01" name="plant_size" class="form-control">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Plant Size Unit</label>
                        <input type="text" name="plant_size_unit" class="form-control" value="m³">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Livestock Details (for Biogas)</label>
                        <textarea name="livestock_details" class="form-control" rows="1"></textarea>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Plant Start Date</label>
                        <input type="date" name="plant_start_date" class="form-control">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Plant End Date</label>
                        <input type="date" name="plant_end_date" class="form-control">
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label>Plant Contribution / Condition</label>
                <textarea name="contribution_condition" class="form-control" rows="2"></textarea>
            </div>

            <div class="form-group">
                <label>Remarks</label>
                <textarea name="remarks" class="form-control" rows="2"></textarea>
            </div>

            <div class="form-group">
                <label>Document / File (Image, PDF, etc. Max 5MB)</label>
                <input type="file" name="document" class="form-control" accept="image/*,.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.zip,.rar">
            </div>

            <div class="text-right mt-3">
                <button class="btn btn-success" type="submit">Save Entry</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 style="margin-bottom: 20px;">{{ __('page.customer')[1] }}</h5>
    </div>
    <div class="card-body">
        <form id="customer-update" action="{{ route('app-customer.update', $customer->id) }}" method="POST">
            @csrf
            @method("put")
            
            <div class="form-group" style="display:{{ isRole(ROLE_AGENT) ? 'none' : '' }}">
                <label for="">{{ __('page.common.agent') }} <span class="text-danger">*</span></label>
                <select name="agent_id" id="agent_id" class="form-control" required>
                    <option value="">Select</option>
                    @foreach (getCachedAgents() as $agent)
                        <option value="{{ $agent->id }}" {{ $customer->agent_id == $agent->id ? "selected" : "" }}>{{ $agent->name }}</option>
                    @endforeach
                </select>
                @error('agent_id')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="">{{ __('sidebar.app.beneficiary') }}  {{ __('page.common.name') }} <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="name" value="{{ $customer->name }}" placeholder="Enter Name" required>
                @error('name')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="">{{ __('sidebar.app.beneficiary') }}  Cell Number <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="mobile" value="{{ $customer->mobile }}" placeholder="Enter Mobile No." required>
                @error('mobile')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="row">
                    <!-- Group Name Section -->
                <div class="col-md-6 col-sm-12 mb-3">
                        <label for="bgnumber">{{ __('sidebar.app.beneficiary') }}  Number <span class="text-danger">*</span></label>
                        <select name="beneficiary_number" id="bgnumber" class="form-control" required>
                            <option value="">Select {{ __('sidebar.app.beneficiary') }}  Number</option>
                            @for($i = 1; $i <= 40; $i++)
                                <option value="{{ $i }}" {{ $customer->beneficiary_number == $i ? "selected" : "" }}>{{ $i }}</option>
                            @endfor
                        </select>
                    </div>

                <!-- Group Number Section -->
                <div class="col-md-6 col-sm-12 mb-3">
                    <label for="gnumber">Group Number <span class="text-danger">*</span></label>
                    <select name="group_number" id="gnumber" class="form-control" required style="width: 90%;">
                        <option value="">Select Group Number</option>
                        @for($i = 1; $i <= 24; $i++)
                            <option value="{{ $i }}" {{ $customer->group_number == $i ? "selected" : "" }}>{{ $i }}</option>
                        @endfor
                    </select>
                    @error('group_number')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="col-md-6 col-sm-12 mb-3">
                    <label>Village</label>
                    <input type="text" class="form-control" name="village" placeholder="Enter Village" value="{{ $customer->village }}">
                </div>
                <div class="col-md-6 col-sm-12 mb-3">
                    <label>Union</label>
                    <input type="text" class="form-control" name="union" placeholder="Enter Union" value="{{ $customer->unions }}">
                </div>
            </div>
           

            <!-- Livestock Section -->
            <h6 class="mt-4 mb-2 text-primary">Number of Livestock</h6>
            <div class="row">
                <div class="col-md-6 col-sm-12 mb-3">
                    <label>Cow</label>
                    <input type="number" class="form-control" name="cow" value="{{ $customer->cow }}" placeholder="Enter number of cows">
                </div>
                <div class="col-md-6 col-sm-12 mb-3">
                    <label>Bull</label>
                    <input type="number" class="form-control" name="bull" value="{{ $customer->bull }}" placeholder="Enter number of bulls">
                </div>
                <div class="col-md-6 col-sm-12 mb-3">
                    <label>Bakna</label>
                    <input type="number" class="form-control" name="bakna" value="{{ $customer->bakna }}" placeholder="Enter number of bakna">
                </div>
                <div class="col-md-6 col-sm-12 mb-3">
                    <label>Goat</label>
                    <input type="number" class="form-control" name="goat" value="{{ $customer->goat }}" placeholder="Enter number of goats">
                </div>
                <div class="col-md-6 col-sm-12 mb-3">
                    <label>Khasi</label>
                    <input type="number" class="form-control" name="khasi" value="{{ $customer->khasi }}" placeholder="Enter number of khasi">
                </div>
            </div>

            <!-- Memo Number of Services Provided Section -->
            <h6 class="mt-4 mb-2 text-primary">Memo No. of Services Provided</h6>
            <div class="row">
                <div class="col-md-6 col-sm-12 mb-3">
                    <label>Membership</label>
                    <input type="text" class="form-control" name="membership" value="{{ $customer->membership }}" placeholder="Enter text of memberships">
                </div>
                <div class="col-md-6 col-sm-12 mb-3">
                    <label>Deworming / Vaccination</label>
                    <input type="text" class="form-control" name="deworming" value="{{ $customer->deworming }}" placeholder="Enter text of cases">
                </div>
                <div class="col-md-6 col-sm-12 mb-3">
                    <label>Bringing to the Fore</label>
                    <input type="text" class="form-control" name="bringing" value="{{ $customer->bringing }}" placeholder="Enter text of cases">
                </div>
                <div class="col-md-6 col-sm-12 mb-3">
                    <label>Fattening</label>
                    <input type="text" class="form-control" name="fattening" value="{{ $customer->fattening }}" placeholder="Enter text of cases">
                </div>
                <div class="col-md-6 col-sm-12 mb-3">
                    <label>Treatment & Other</label>
                    <input type="text" class="form-control" name="treatment" value="{{ $customer->treatment }}" placeholder="Enter text of treatments">
                </div>
                <div class="col-md-6 col-sm-12 mb-3">
                    <label>AI (Artificial Insemination)</label>
                    <input type="text" class="form-control" name="ai" value="{{ $customer->ai }}" placeholder="Enter text of AI cases">
                </div>
                <div class="col-md-6 col-sm-12 mb-3">
                    <label>Medicine</label>
                    <input type="text" class="form-control" name="medicine" value="{{ $customer->medicine }}" placeholder="Enter text of medicine cases">
                </div>
                {{-- <div class="col-md-6 col-sm-12 mb-3">
                    <label>Feed</label>
                    <input type="text" class="form-control" name="feed" value="{{ $customer->feed }}" placeholder="Enter text of feed cases">
                </div> --}}
            </div>

            <div class="text-right">
                <button class="btn btn-success" type="submit" id="submit">{{ __('page.unit')[6] }}</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">{{ __('page.unit')[7] }}</button>
            </div>

        </form>
    </div>
</div>

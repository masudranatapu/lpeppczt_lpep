<div class="card">
    <div class="card-header">
        <h5 style="margin-bottom: 20px;">{{ __('page.customer')[1] }}</h5>
    </div>
    <div class="card-body">
        <form id="customer-form" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <!-- Existing fields -->
                <div class="col-md-6 col-sm-12 mb-3" style="display:{{ isRole(ROLE_AGENT) ? 'none' : '' }}">
                    <label>{{ __('page.common.agent') }} <span class="text-danger">*</span></label>
                    <select name="agent_id" id="agent_id" class="form-control select2" required>
                        <option value="">Select</option>
                        @foreach (getCachedAgents() as $agent)
                            <option value="{{ $agent->id }}" {{ auth()->id() == $agent->id ? "selected" : '' }}>
                                {{ $agent->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('agent_id')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="col-md-6 col-sm-12 mb-3">
                    <label>{{ __('sidebar.app.beneficiary') }}  {{ __('page.common.name') }} <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="name" placeholder="Enter Name" required>
                </div>

                <div class="col-md-6 col-sm-12 mb-3">
                    <label>{{ __('sidebar.app.beneficiary') }}  Cell Number <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="mobile" placeholder="Enter Mobile No." required>
                </div>

                <div class="col-md-6 col-sm-12 mb-3">
                    <label for="bgnumber">{{ __('sidebar.app.beneficiary') }}  Number <span class="text-danger">*</span></label>
                    <input type="text" name="beneficiary_number" id="bgnumber" class="form-control" required>
                       
                </div>

                <div class="col-md-6 col-sm-12 mb-3">
                    <label for="gnumber">{{ __('sidebar.app.beneficiary') }} Group Number <span class="text-danger">*</span></label>
                    <select name="group_number" id="gnumber" class="form-control" required>
                        <option value="">Select Group Number</option>
                        @for($i = 1; $i <= 28; $i++)
                            @if ($i == 7 || $i == 14 || $i == 15 || $i == 22)
                                @continue
                            @endif
                            <option value="{{ $i }}">{{ $i }}</option>
                        @endfor
                    </select>
                </div>

                <div class="col-md-6 col-sm-12 mb-3">
                    <label>{{ __('page.common.email') }}</label>
                    <input type="email" class="form-control" name="email" placeholder="Enter Email Address">
                </div>

                <div class="col-md-6 col-sm-12 mb-3">
                    <label>{{ __('page.common.password') }}</label>
                    <input type="password" class="form-control" name="password" placeholder="Enter Password">
                </div>
                {{-- <div class="col-md-6 col-sm-12 mb-3">
                    <label>Image</label>
                    <input type="file" class="form-control" name="image" >
                </div> --}}
                <div class="col-md-6 col-sm-12 mb-3">
                    <label>Village</label>
                    <input type="text" class="form-control" name="village" placeholder="Enter village">
                </div>
                <div class="col-md-6 col-sm-12 mb-3">
                    <label>Union</label>
                    <input type="text" class="form-control" name="union" placeholder="Enter Union">
                </div>
            </div>

            <!-- Number of Livestock Section -->
            <h6 class="mt-4 mb-2 text-primary">{{ __('page.livestock.number_of_livestock') }}</h6>
            <div class="row">
                <div class="col-md-6 col-sm-12 mb-3">
                    <label>{{ __('page.livestock.cow') }}</label>
                    <input type="number" class="form-control" name="cow" placeholder="Enter number of cows">
                </div>
                <div class="col-md-6 col-sm-12 mb-3">
                    <label>{{ __('page.livestock.bull') }}</label>
                    <input type="number" class="form-control" name="bull" placeholder="Enter number of bulls">
                </div>
                <div class="col-md-6 col-sm-12 mb-3">
                    <label>{{ __('page.livestock.bakna') }}</label>
                    <input type="number" class="form-control" name="bakna" placeholder="Enter number of bakna">
                </div>
                <div class="col-md-6 col-sm-12 mb-3">
                    <label>{{ __('page.livestock.goat') }}</label>
                    <input type="number" class="form-control" name="goat" placeholder="Enter number of goats">
                </div>
                <div class="col-md-6 col-sm-12 mb-3">
                    <label>{{ __('page.livestock.khasi') }}</label>
                    <input type="number" class="form-control" name="khasi" placeholder="Enter number of khasi">
                </div>
            </div>

            <!-- Memo No. of Services Provided Section -->
            <h6 class="mt-4 mb-2 text-primary">{{ __('page.services.memo_of_services_provided') }}</h6>
            <div class="row">
                <div class="col-md-6 col-sm-12 mb-3">
                    <label>{{ __('page.services.membership') }}</label>
                    <input type="text" class="form-control" name="membership" placeholder="Enter text of memberships">
                </div>
                <div class="col-md-6 col-sm-12 mb-3">
                    <label>{{ __('page.services.deworming') }}</label>
                    <input type="text" class="form-control" name="deworming" placeholder="Enter text of cases">
                </div>
                <div class="col-md-6 col-sm-12 mb-3">
                    <label>{{ __('page.services.bringing') }}</label>
                    <input type="text" class="form-control" name="bringing" placeholder="Enter text of cases">
                </div>
                <div class="col-md-6 col-sm-12 mb-3">
                    <label>{{ __('page.services.fattening') }}</label>
                    <input type="text" class="form-control" name="fattening" placeholder="Enter text of cases">
                </div>
                <div class="col-md-6 col-sm-12 mb-3">
                    <label>{{ __('page.services.treatment') }}</label>
                    <input type="text" class="form-control" name="treatment" placeholder="Enter text of treatments">
                </div>
                <div class="col-md-6 col-sm-12 mb-3">
                    <label>{{ __('page.services.ai') }}</label>
                    <input type="text" class="form-control" name="ai" placeholder="Enter text of AI cases">
                </div>
                <div class="col-md-6 col-sm-12 mb-3">
                    <label>{{ __('page.services.medicine') }}</label>
                    <input type="text" class="form-control" name="medicine" placeholder="Enter text of medicine cases">
                </div>
                
            </div>

            <div class="text-right mt-3">
                <button class="btn btn-success" type="submit">{{ __('page.unit')[6] }}</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">{{ __('page.unit')[7] }}</button>
            </div>
        </form>
    </div>
</div>


<script>
$(document).ready(function() {
    $('#agent_id').select2({ dropdownParent: $('#modal') });
    $('#bgnumber').select2({ dropdownParent: $('#modal') });
    $('#gnumber').select2({ dropdownParent: $('#modal') });
});
</script>

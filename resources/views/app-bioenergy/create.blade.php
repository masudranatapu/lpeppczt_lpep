<div class="card">
    <div class="card-header">
        <!-- <h5 style="margin-bottom: 20px;">{{ __('page.energy')[1] }}</h5> -->
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
                    <label>{{ __('sidebar.app.renewable_energy') }}  {{ __('page.area.name') }} <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="name" placeholder="Enter Area Name" required>
                </div>

                <div class="col-md-6 col-sm-12 mb-3">
                    <label>{{ __('sidebar.app.renewable_energy') }}  Client Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="mobile" placeholder="Enter Clinet Name." required>
                </div>

                <div class="col-md-6 col-sm-12 mb-3">
                    <label for="bgnumber">{{ __('sidebar.app.renewable_energy') }} Client  Number <span class="text-danger">*</span></label>
                    <input type="text" name="client_number" id="bgnumber" class="form-control" required>
                       
                </div>

                <!-- <div class="col-md-6 col-sm-12 mb-3">
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
                </div> -->

          
             
                 <div class="col-md-6 col-sm-12 mb-3">
                    <label>client Image</label>
                    <input type="file" class="form-control" name="image" >
                </div> 

                 <div class="col-md-6 col-sm-12 mb-3">
                    <label>Upazilla</label>
                    <input type="text" class="form-control" name="upazilla" placeholder="Enter upazilla">
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


               <div class="col-md-6 col-sm-12 mb-3">
                    <label for="bgnumber">{{ __('sidebar.app.renewable_energy') }} Plant Size  <span class="text-danger">*</span></label>
                    <input type="text" name="client_number" id="bgnumber" class="form-control" required>
                       
                </div>

                  <div class="col-md-6 col-sm-12 mb-3">
                    <label for="bgnumber">{{ __('sidebar.app.renewable_energy') }} Plant Start Date <span class="text-danger">*</span></label>
                    <input type="text" name="client_number" id="bgnumber" class="form-control" required>
                       
                </div>

                  <div class="col-md-6 col-sm-12 mb-3">
                    <label for="bgnumber">{{ __('sidebar.app.renewable_energy') }} Plant End Date <span class="text-danger">*</span></label>
                    <input type="text" name="client_number" id="bgnumber" class="form-control" required>
                       
                </div>

              

                  <div class="col-md-6 col-sm-12 mb-3">
                    <label>{{ __('sidebar.app.renewable_energy') }}  {{ __('page.organizer_name') }} <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="P.O name" placeholder="Enter P.O  Name" required>
                </div>

                <div class="col-md-6 col-sm-12 mb-3">
                    <label>{{ __('sidebar.app.renewable_energy') }}  {{ __('page. condition') }} <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="P.O name" placeholder="Enter Plant condition  Name" required>
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

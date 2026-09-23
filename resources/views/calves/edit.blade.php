<h5>{{ __('page.calf')[15] }}</h5>
<div>

    <form method="POST" id="calvesEditForm" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="user_id" value="{{auth()->id()}}">
        <input type="hidden" name="calf_id" value="{{$calf->id}}">

        {{-- <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="">{{ __('page.calf')[3] }} <span class="text-danger">*</span></label>
                    <select class="form-control customer_id" name="customer_id" required>
                        <option value="">Select</option>
                        @foreach ($customers as $id => $name)
                        <option value="{{$id}}" {{$calf->customer_id == $id ? 'selected' : ''}}>{{$name}}</option>
                        @endforeach
                    </select>

                    @error('customer_id')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="">{{ __('page.calf')[4] }} <span class="text-danger">*</span></label>
                    <select class="form-control farm_id" name="farm_id" required>
                        <option value="">Select</option>
                        @foreach ($farms as $id => $name)
                        <option value="{{$id}}" {{$calf->farm_id == $id ? 'selected' : ''}}>{{$name}}</option>
                        @endforeach
                    </select>

                    @error('farm_id')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div> --}}

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="">{{ __('page.calf')[16] }} <span class="text-danger">*</span></label>
                    <input type="hidden" class="form-control cattle_id" value="{{ $cattle->id }}" name="cattle_id" required readonly>
                    <input type="hidden" class="form-control farm_id" value="{{ $farm->id }}" name="farm_id" required >
                    <input type="text" class="form-control" value="{{ $cattle->name }}"  required readonly>

                    @error('cattle_id')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="">{{ __('page.calf')[6] }} <span class="text-danger">*</span></label>
                    <input type="text" value="{{$calf->name}}" class="form-control" name="name"
                        placeholder="{{ __('page.calf')[6] }}" required>

                    @error('name')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>


        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="">{{ __('page.calf')[5] }} <span class="text-danger">*</span></label>
                    <input type="text" value="{{$calf->tag}}" class="form-control" name="tag"
                        placeholder="{{ __('page.calf')[5] }}" required>

                    @error('tag')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="">{{ __('page.calf')[7] }} <span class="text-danger">*</span></label>
                    <input type="text" value="{{$calf->birth_date}}" class="form-control datepicker" name="birth_date"
                        placeholder="{{ __('page.calf')[7] }}" required>

                    @error('birth_date')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>


        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="">{{ __('page.calf')[10] }} <span class="text-danger">*</span></label>
                    <input type="text" value="{{$calf->weight}}" class="form-control" name="weight"
                        placeholder="{{ __('page.calf')[10] }}" required>

                    @error('weight')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="">{{ __('page.calf')[8] }} <span class="text-danger">*</span></label>
                    <select class="form-control" name="gender" required>
                        <option value="">Select</option>
                        @foreach (['এঁড়ে বাছুর', 'বকনা'] as $value)
                        <option value="{{$value}}" {{$calf->gender == $value ? 'selected' : '' }}>{{$value}}</option>
                        @endforeach
                    </select>

                    @error('gender')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>


        <div class="row">
            <div class="col-md-6">
                @php
                    $prevIds = collect($calf->birthProblems)->pluck('calf_birth_problem_id')->toArray();
                @endphp
                <div class="form-group">
                    <label for="" style="text-decoration:underline;">{{ __('page.calf')[9] }}:</label>
                    @foreach ($birthProblems as $id => $name)
                        <div>
                            <input type="checkbox" name="calf_birth_problem_ids[]" value="{{$id}}" id="calf_birth_problem_ids_{{$id}}" {{in_array($id, $prevIds) ? 'checked' : ''}}>
                            <label for="calf_birth_problem_ids_{{$id}}">{{$name}}</label>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="">{{ __('page.calf')[11] }}</label>
                    <input type="file" value="{{$calf->weight}}" class="form-control image" name="image"
                        placeholder="{{ __('page.calf')[11] }}" >

                    @error('image')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>

        <div class="text-right">
            <button class="btn btn-success" type="submit" id="submitUpdate">{{ __('page.cattle')[21] }}</button>
            <button type="button" class="btn btn-danger" data-dismiss="modal">{{ __('page.cattle')[22] }}</button>
        </div>

    </form>

</div>

<h5>{{ __('page.calf')[1] }}</h5>
<div>

    <form>
        <input type="hidden" name="user_id" value="{{ auth()->id() }}">

        <div class="row">
            <div class="col-md-6">
                @include('inc.appCustomer')
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="">{{ __('page.calf')[4] }} <span class="text-danger">*</span></label>
                    <select class="form-control farm_id" name="farm_id" required>
                        <option value="">Select</option>
                        @foreach ($farms as $farm)
                            <option value="{{ $farm->id }}">{{ $farm->name }}</option>
                        @endforeach
                    </select>

                    @error('farm_id')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="">{{ __('page.calf')[16] }} <span class="text-danger">*</span></label>
                    <select class="form-control cattle_id" name="cattle_id" required>
                        <option value="">Select</option>
                        @foreach ($cattles as $cattle)
                            <option value="{{ $cattle->id }}">{{ $cattle->name }}</option>
                        @endforeach
                    </select>

                    @error('cattle_id')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="">{{ __('page.calf')[6] }} <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="name" placeholder="{{ __('page.calf')[6] }}"
                        required>

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
                    <input type="text" class="form-control" name="tag" placeholder="{{ __('page.calf')[5] }}"
                        required>

                    @error('tag')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="">{{ __('page.calf')[7] }} <span class="text-danger">*</span></label>
                    <input type="text" class="form-control datepicker" name="birth_date"
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
                    <input type="text" class="form-control" name="weight" placeholder="{{ __('page.calf')[10] }}"
                        required>

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
                            <option value="{{ $value }}">{{ $value }}</option>
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
                <div class="form-group">
                    <label for="" style="text-decoration:underline;">{{ __('page.calf')[9] }}:</label>
                    @foreach ($birthProblems as $id => $name)
                        <div>
                            <input type="checkbox" name="calf_birth_problem_ids[]" value="{{ $id }}"
                                id="calf_birth_problem_ids_{{ $id }}">
                            <label for="calf_birth_problem_ids_{{ $id }}">{{ $name }}</label>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="">{{ __('page.calf')[11] }}</label>
                    <input type="file" class="form-control image" name="image"
                        placeholder="{{ __('page.calf')[11] }}" required>

                    @error('image')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>

        <div class="text-right">
            <button class="btn btn-success" type="submit" id="submit">{{ __('page.cattle')[21] }}</button>
            <button type="button" class="btn btn-danger" data-dismiss="modal">{{ __('page.cattle')[22] }}</button>
        </div>

    </form>

</div>

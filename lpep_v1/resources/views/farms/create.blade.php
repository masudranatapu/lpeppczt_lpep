<div class="card">
    <div class="card-header">
        <h5 style="margin-bottom: 10px;">{{ __('page.farm')[1] }}</h5>
    </div>
    <div class="card-body">
        <div>
            <form id="customer-form-store" action="{{ route('farms.store') }}">
                @csrf
                <input type="hidden" name="user_id" value="{{ auth()->id() }}">
                @include('inc.appCustomer')
                <div class="form-group">
                    <label for="">{{ __('page.farm')[4] }} <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="name" placeholder="{{ __('page.farm')[4] }}"
                        required>

                    @error('name')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="">{{ __('page.farm')[11] }}</label>

                    <select name="division_id" id="division_id" class="form-control division_id">
                        <option value="">Select</option>
                        @foreach ($divisions as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>

                    @error('division_id')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="">{{ __('page.farm')[12] }}</label>

                    <select name="district_id" id="district_id" class="form-control district_id">
                        <option value="">Select</option>
                    </select>

                    @error('district_id')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="">{{ __('page.farm')[13] }}</label>

                    <select name="upazila_id" id="upazila_id" class="form-control upazila_id">
                        <option value="">Select</option>
                    </select>

                    @error('upazila_id')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="">{{ __('page.farm')[14] }}</label>

                    <select name="union_id" id="union_id" class="form-control union_id">
                        <option value="">Select</option>
                    </select>

                    @error('union_id')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="text-right">
                    <button class="btn btn-success" type="submit" id="submit">{{ __('page.farm')[8] }}</button>
                    <button type="button" class="btn btn-danger"
                        data-dismiss="modal">{{ __('page.farm')[9] }}</button>
                </div>

            </form>

        </div>
    </div>
</div>

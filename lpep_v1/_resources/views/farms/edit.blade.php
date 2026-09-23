<div class="card">
    <div class="card-header">
        <h5>{{ __('page.farm')[10] }}</h5>
    </div>
    <div class="card-body">
        <form id="customer-form-update" action="{{ route('farms.update', $farm->id) }}">
            @csrf
            @method('put')
            <input type="hidden" name="id" value="{{ $farm->id }}">

            @include('inc.appCustomer',['customer' => $farm->customer])

            <div class="form-group">
                <label for="">{{ __('page.farm')[4] }} <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="name" placeholder="{{ __('page.farm')[4] }}"
                    value="{{ $farm->name }}" required>

                @error('name')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="">{{ __('page.farm')[11] }}</label>

                <select name="division_id" id="division_id" class="form-control division_id">
                    <option value="">Select</option>
                    @foreach ($divisions as $id => $name)
                        <option value="{{ $id }}" {{ $id == $farm->division_id ? 'selected' : '' }}>
                            {{ $name }}</option>
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
                    @foreach ($districts as $id => $name)
                        <option value="{{ $id }}" {{ $id == $farm->district_id ? 'selected' : '' }}>
                            {{ $name }}</option>
                    @endforeach
                </select>

                @error('district_id')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="">{{ __('page.farm')[13] }}</label>

                <select name="upazila_id" id="upazila_id" class="form-control upazila_id">
                    <option value="">Select</option>
                    @foreach ($upazilas as $id => $name)
                        <option value="{{ $id }}" {{ $id == $farm->upazila_id ? 'selected' : '' }}>
                            {{ $name }}</option>
                    @endforeach
                </select>

                @error('upazila_id')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="">{{ __('page.farm')[14] }}</label>

                <select name="union_id" id="union_id" class="form-control union_id">
                    <option value="">Select</option>
                    @foreach ($unions as $id => $name)
                        <option value="{{ $id }}" {{ $id == $farm->union_id ? 'selected' : '' }}>
                            {{ $name }}</option>
                    @endforeach
                </select>

                @error('union_id')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="text-right">
                <button class="btn btn-success" type="submit" id="submitUpdate">{{ __('page.farm')[8] }}</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">{{ __('page.farm')[9] }}</button>
            </div>

        </form>
    </div>
</div>

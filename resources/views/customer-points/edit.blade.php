<div class="card">
    <div class="card-header">
        <h5 style="margin-bottom: 10px;">{{ __('page.customer-point')[8] }}</h5>
    </div>
    <div class="card-body">
        <form>
            <input type="hidden" name="id" value="{{ $customerPoint->id }}">

            <div class="form-group">
                <label for="">{{ __('page.customer-point')[3] }} <span class="text-danger">*</span></label>

                <select name="customer_id" id="customer_id" class="form-control" required>
                    <option value="">Select</option>
                    @foreach ($customers as $customer)
                        <option {{ $customerPoint->customer_id == $customer->id ? 'selected' : '' }}
                            value="{{ $customer->id }}">
                            {{ $customer->name }}
                        </option>
                    @endforeach
                </select>

                @error('customer_id')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="">{{ __('page.customer-point')[4] }} <span class="text-danger">*</span></label>
                <input type="number" class="form-control" name="assigned_points"
                    placeholder="{{ __('page.customer-point')[4] }}" value="{{ $customerPoint->assigned_points }}"
                    oninput="verifyNumber(this, 0, {{ $availablePoints }})" required>

                @error('assigned_points')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="text-right">
                <button class="btn btn-success" type="submit" id="submitUpdate">{{ __('page.unit')[6] }}</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">{{ __('page.unit')[7] }}</button>
            </div>

        </form>

    </div>
</div>

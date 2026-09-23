<div class="card">
    <div class="card-header">
        <h5 style="margin-bottom: 10px;">{{ __('page.customer-point')[1] }}</h5>
    </div>
    <div class="card-body">
        <form>
            @include('inc.appCustomer')

            <div class="form-group">
                <label for="">{{ __('page.customer-point')[4] }} <span class="text-danger">*</span></label>
                <input type="number" class="form-control" name="assigned_points"
                    placeholder="{{ __('page.customer-point')[4] }}"
                    oninput="verifyNumber(this, 0, {{ $availablePoints }})" required>

                @error('assigned_points')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="text-right">
                <button class="btn btn-success" type="submit" id="submit">{{ __('page.unit')[6] }}</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">{{ __('page.unit')[7] }}</button>
            </div>

        </form>
    </div>
</div>

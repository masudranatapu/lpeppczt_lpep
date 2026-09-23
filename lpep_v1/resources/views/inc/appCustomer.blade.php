<div class="form-group">
    @if (!isset($hideLabel))
        <label for="">{{ __('page.farm')[3] }} <span class="text-danger">*</span></label>
    @endif

    @if (isset($readOnly))
        <input type="text" class="form-control" value="{{ $customer->name }}" disabled>
        <input type="text" class="form-control" name="app_customer_id" id="app_customer_id"
            value="{{ $customer->id }}"readonly>
    @else
        {{-- <select name="app_customer_id" id="app_customer_id" class="form-control select3" required>
            @if (isset($customer) && $customer)
                <option value="{{ $customer->id }}" selected>{{ $customer->name }} ( {{ $customer->mobile }} )
                </option>
            @endif
        </select> --}}

        <div class="input-group">
            <span class="input-group-addon">
                <i class="fa fa-user"></i>
            </span>
            <select name="contact_id" class="form-control select2" id="contact_id">

            </select>
            <span class="input-group-btn" style="margin-left: 1px">
                <button style="cursor: pointer;" id="addNewCustomer" type="button"
                    class="btn btn-default bg-white btn-flat add_new_customer"><i
                        class="fa fa-plus-circle text-primary fa-lg"></i></button>
            </span>
        </div>
    @endif
</div>

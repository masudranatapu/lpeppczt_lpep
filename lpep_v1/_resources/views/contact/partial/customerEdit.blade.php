<div class="card">
    <div class="card-header">
        Edit Customer
    </div>
    <div class="card-body">
        <form>
            <input type="hidden" name="type" value="customer">
            <input type="hidden" name="id" value="{{ $contact->id }}">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="">{{ __('page.contactcreate')[0] }}<span class="text-danger">*</span></label>
                        <input type="text" value="{{ $contact->name }}" class="form-control" name="name"
                            placeholder="Name" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <label for="">{{ __('page.contactcreate')[11] }}</label>
                    <select name="customer_group_id" id="customer_group_id" class="form-control">
                        <option value="0">select</option>
                        @foreach ($customer_groups as $customer_group)
                            <option value="{{ $customer_group->id }}"
                                {{ $contact->customer_group_id == $customer_group->id ? 'selected' : '' }}>
                                {{ $customer_group->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="landline">{{ __('page.contactcreate')[3] }}<span
                                class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-phone"></i>
                            </span>
                            <input class="form-control" value="{{ $contact->mobile }}" placeholder="Mobile"
                                name="mobile" type="text" id="mobile" required>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="email">{{ __('page.contactcreate')[2] }}</label>
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-envelope"></i>
                            </span>
                            <input class="form-control" value="{{ $contact->email }}" placeholder="Email" name="email"
                                type="email" id="email">
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="address">{{ __('page.contactcreate')[16] }}<span
                                class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-map-marker"></i>
                            </span>
                            <input class="form-control" value="{{ $contact->address }}" placeholder="Address"
                                name="address" type="text" id="address" required>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="">{{ __('page.contactcreate')[7] }}</label>
                <textarea name="note" id="note" rows="3" class="form-control">{{ $contact->note }}</textarea>
            </div>

            <div class="text-right">
                <button class="btn btn-info" type="submit"
                    id="submitUpdate">{{ __('page.contactcreate')[10] }}</button>
                <button type="button" class="btn btn-secondary"
                    data-dismiss="modal">{{ __('page.contactcreate')[9] }}</button>
            </div>

        </form>
    </div>
</div>

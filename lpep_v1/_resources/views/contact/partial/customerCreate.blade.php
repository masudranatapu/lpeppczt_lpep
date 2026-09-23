<div class="card">
    <div class="card-header">
        <h4>Add New Customer</h4>
    </div>
    <div class="card-body">
        <div class="row">
            <form id="customerStore" class="col-md-12" action="{{ route('contact.store') }}" method="POST">
                @csrf
                <input type="hidden" name="type" value="customer">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="">{{ __('page.contactcreate')[0] }}<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="name" placeholder="Name" required>
                            @error('name')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="">{{ __('page.contactcreate')[11] }}</label>
                        <select name="customer_group_id" id="customer_group_id" class="form-control">
                            <option value="">select</option>
                            @foreach ($customer_groups as $customer_group)
                                <option value="{{ $customer_group->id }}">{{ $customer_group->name }}</option>
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
                                <input class="form-control" placeholder="Mobile" name="mobile" type="text" id="mobile"
                                    required>
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
                                <input class="form-control" placeholder="Email" name="email" type="email" id="email">
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
                                <input class="form-control" placeholder="Address" name="address" type="text" id="address"
                                    required>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="">{{ __('page.contactcreate')[7] }}</label>
                    <input type="text" class="form-control" name="note" id="note" placeholder="Note">
                </div>

                <div class="text-right">
                    <button class="btn btn-success" type="submit" id="submit">{{ __('page.contactcreate')[8] }}</button>
                    <button type="button" class="btn btn-secondary"
                        data-dismiss="modal">{{ __('page.contactcreate')[9] }}</button>
                </div>

            </form>

        </div>

    </div>
</div>
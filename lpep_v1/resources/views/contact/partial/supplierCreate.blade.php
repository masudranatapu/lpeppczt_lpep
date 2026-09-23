<div class="card">
    <div class="card-header">
        Supplier Create
    </div>
    <div class="card-body">
        <form id="customerStore" action="{{ route('contact.store') }}">
            @csrf
            <input type="hidden" name="type" value="supplier">
            <div class="form-group row">
                <div class="col-md-6">
                    <label for="">{{ __('page.contactcreate')[0] }}<span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="name" placeholder="Name" required>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="supplier_business_name">{{ __('page.contactcreate')[1] }}</label>
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-briefcase"></i>
                            </span>
                            <input class="form-control" placeholder="Business Name" name="supplier_business_name"
                                type="text" id="supplier_business_name">
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
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
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="">{{ __('page.address') }}<span class="text-danger">*</span></label>
                        <input name="address" id="address" rows="2" class="form-control" placeholder="Address" />
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="">{{ __('page.contactcreate')[7] }}</label>
                <textarea name="note" id="note" rows="3" class="form-control" placeholder="note"></textarea>
            </div>

            <div class="text-right">
                <button class="btn btn-success" type="submit" id="submit">{{ __('page.contactcreate')[8] }}</button>
                <button type="button" class="btn btn-secondary"
                    data-dismiss="modal">{{ __('page.contactcreate')[9] }}</button>
            </div>

        </form>
    </div>
</div>

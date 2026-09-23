<h5>Edit Business Branch</h5>
<hr>
<div>

    <form action="{{ route('business.update', $business->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('put')
        <div class="form-group">
            <label for="">Name<span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="name" placeholder="Name" value="{{ $business->name }}"
                required>
        </div>

        <div class="form-group row">
            <div class="col-md-4">
                <label for="">City<span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="city" placeholder="City" value="{{ $business->city }}"
                    required>
            </div>
            <div class="col-md-4">
                <label for="">Zip<span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="zip" placeholder="Zip"
                    value="{{ $business->zip_code }}" required>
            </div>

            <div class="col-md-4">
                <label for="">State<span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="sate" placeholder="State"
                    value="{{ $business->state }}" required>
            </div>
        </div>

        <div class="form-group row">
            <div class="col-md-4">
                <label for="">Country<span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="country" placeholder="Country"
                    value="{{ $business->country }}" required>
            </div>
            <div class="col-md-4">
                <label for="">Mobile<span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="mobile" placeholder="Mobile"
                    value="{{ $business->mobile }}" required>
            </div>
            <div class="col-md-4">
                <label for="">Email</label>
                <input type="email" class="form-control" name="email" placeholder="Email"
                    value="{{ $business->email }}">
            </div>
        </div>

        <div class="form-group row">
            <div class="col-md-4">
                <label for="">Websie</label>
                <input type="text" class="form-control" name="website" placeholder="Website"
                    value="{{ $business->website }}">
            </div>
            <div class="col-md-4">
                <label for="">Logo</label>
                <input type="file" class="form-control" name="logo" placeholder="Logo">
            </div>
            <div class="col-md-4">
                <label for=""></label>
                <div class="mt-3">
                    <label class="custom-control custom-checkbox">
                        <input type="checkbox" name="status" value="1" class="custom-control-input"
                            {{ $business->status == 1 ? 'checked' : '' }}>
                        <span class="custom-control-indicator"></span>
                        <span class="custom-control-description">Is Active</span>
                    </label>
                </div>
            </div>
        </div>

        <div class="text-right">
            <button class="btn btn-success" type="submit">Save</button>
        </div>

    </form>

</div>

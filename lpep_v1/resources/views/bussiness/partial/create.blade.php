<h5>Add New Business Branch</h5>
<hr>
<div>

    <form action="{{ route('business.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="form-group">
            <label for="">Name<span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="name" placeholder="Name" required>
        </div>

        <div class="form-group row">
            <div class="col-md-4">
                <label for="">City<span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="city" placeholder="City" required>
            </div>
            <div class="col-md-4">
                <label for="">Zip<span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="zip" placeholder="Zip" required>
            </div>
            <div class="col-md-4">
                <label for="">State<span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="sate" placeholder="State" required>
            </div>
        </div>

        <div class="form-group row">
            <div class="col-md-4">
                <label for="">Country<span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="country" placeholder="Country" required>
            </div>
            <div class="col-md-4">
                <label for="">Mobile<span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="mobile" placeholder="Mobile" required>
            </div>
            <div class="col-md-4">
                <label for="">Email</label>
                <input type="email" class="form-control" name="email" placeholder="Email">
            </div>
        </div>

        <div class="form-group row">
            <div class="col-md-4">
                <label for="">Websie</label>
                <input type="text" class="form-control" name="website" placeholder="Website">
            </div>
            <div class="col-md-4">
                <label for="">Logo</label>
                <input type="file" class="form-control" name="logo" placeholder="Logo">
            </div>
            <div class="col-md-4">
                <label for=""></label>
                <div class="mt-3">
                    <label class="custom-control custom-checkbox">
                        <input type="checkbox" name="status" value="1" class="custom-control-input">
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

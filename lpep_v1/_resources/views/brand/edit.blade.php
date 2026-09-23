<h5>Update Category</h5>
<hr>
<div>

    <form>
        <input type="hidden" name="id" value="{{ $brand->id }}">
        <div class="form-group">
            <label for="">{{ __('page.brand')[3] }}<span class="text-danger">*</span></label>
            <input type="text" value="{{ $brand->brand_name }}" class="form-control" name="brand_name" placeholder="Brand Name" required>
        </div>
        <div class="text-right">
            <button class="btn btn-success" type="submit" id="submitUpdate">{{ __('page.brand')[5] }}</button>
            <button type="button" class="btn btn-danger" data-dismiss="modal">{{ __('page.brand')[6] }}</button>
        </div>

    </form>

</div>
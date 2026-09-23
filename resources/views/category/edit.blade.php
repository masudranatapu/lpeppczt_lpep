<h5>Update Category</h5>
<hr>
<div>

    <form>
        <input type="hidden" name="id" value="{{ $category->id }}">
        <div class="form-group">
            <label for="">{{ __('page.category')[3] }}<span class="text-danger">*</span></label>
            <input type="text" value="{{ $category->category_name }}" class="form-control" name="category_name" placeholder="Category Name" required>
        </div>
        <div class="text-right">
            <button class="btn btn-success" type="submit" id="submitUpdate">{{ __('page.category')[5] }}</button>
            <button type="button" class="btn btn-danger" data-dismiss="modal">{{ __('page.category')[6] }}</button>
        </div>

    </form>

</div>
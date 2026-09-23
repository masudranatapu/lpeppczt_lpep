<h5>{{ __('page.brand')[1] }}</h5>
<hr>
<div>

    <form>
        <div class="form-group">
            <label for="">{{ __('page.brand')[3] }}<span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="brand_name" placeholder="Brand Name" required>
        </div>
        <div class="text-right">
            <button class="btn btn-success" type="submit" id="submit">{{ __('page.brand')[5] }}</button>
            <button type="button" class="btn btn-danger" data-dismiss="modal">{{ __('page.brand')[6] }}</button>
        </div>

    </form>

</div>
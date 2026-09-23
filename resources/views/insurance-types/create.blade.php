<h5>{{ __('page.insurance-type')[1] }}</h5>
<hr>
<div>

    <form>
        <input type="hidden" name="user_id" value="{{auth()->id()}}">

        <div class="form-group">
            <label for="">{{ __('page.insurance-type')[3] }} <span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="name" placeholder="{{ __('page.insurance-type')[3] }}" required>

            @error('name')
            <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <div class="text-right">
            <button class="btn btn-success" type="submit" id="submit">{{ __('page.insurance-type')[5] }}</button>
            <button type="button" class="btn btn-danger" data-dismiss="modal">{{ __('page.insurance-type')[6] }}</button>
        </div>

    </form>

</div>

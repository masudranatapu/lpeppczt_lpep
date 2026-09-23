<h5>{{ __('page.unit')[1] }}</h5>
<hr>
<div>

    <form>
        <div class="form-group">
            <label for="">{{ __('page.unit')[3] }}<span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="actual_name" placeholder="{{ __('page.unit')[3] }}"
                required>
            @error('actual_name')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="">{{ __('page.unit')[4] }}</label>
            <input type="text" class="form-control" name="short_name" placeholder="{{ __('page.unit')[4] }}">
        </div>

        <div class="form-group">
            <label for="is_decimal" style="user-select: none">
                <input id="is_decimal" name="is_decimal" type="checkbox" placeholder="{{ __('page.unit')[8] }}"
                    style="margin-top: -5px">
                {{ __('page.unit')[8] }}
            </label>
        </div>

        <div class="text-right">
            <button class="btn btn-success" type="submit" id="submit">{{ __('page.unit')[6] }}</button>
            <button type="button" class="btn btn-danger" data-dismiss="modal">{{ __('page.unit')[7] }}</button>
        </div>

    </form>

</div>

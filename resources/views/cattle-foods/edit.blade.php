<h5>{{ __('page.cattle-food')[7] }}</h5>
<hr>
<div>

    <form>
        <input type="hidden" name="id" value="{{$cattleFood->id}}">

        <div class="form-group">
            <label for="">{{ __('page.cattle-food')[3] }} <span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="name" placeholder="{{ __('page.cattle-food')[3] }}" value="{{$cattleFood->name}}" required>

            @error('name')
            <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="">{{ __('page.cattle-food')[8] }} <span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="unit" placeholder="{{ __('page.cattle-food')[8] }}" value="{{$cattleFood->unit}}" required>

            @error('unit')
            <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <div class="text-right">
            <button class="btn btn-success" type="submit" id="submitUpdate">{{ __('page.cattle-food')[5] }}</button>
            <button type="button" class="btn btn-danger" data-dismiss="modal">{{ __('page.cattle-food')[6] }}</button>
        </div>

    </form>

</div>

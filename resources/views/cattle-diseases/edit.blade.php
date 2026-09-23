<h5>{{ __('page.cattle-disease')[7] }}</h5>
<hr>
<div>

    <form>
        <input type="hidden" name="id" value="{{$cattleDisease->id}}">

        <div class="form-group">
            <label for="">{{ __('page.cattle-disease')[3] }} <span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="name" placeholder="{{ __('page.cattle-disease')[3] }}" value="{{$cattleDisease->name}}" required>

            @error('name')
            <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <div class="text-right">
            <button class="btn btn-success" type="submit" id="submitUpdate">{{ __('page.cattle-disease')[5] }}</button>
            <button type="button" class="btn btn-danger" data-dismiss="modal">{{ __('page.cattle-disease')[6] }}</button>
        </div>

    </form>

</div>

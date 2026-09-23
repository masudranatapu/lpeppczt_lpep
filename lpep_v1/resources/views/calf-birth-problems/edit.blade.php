<h5>{{ __('page.calf-birth-problem')[7] }}</h5>
<hr>
<div>

    <form>
        <input type="hidden" name="id" value="{{$calfBirthProblem->id}}">

        <div class="form-group">
            <label for="">{{ __('page.calf-birth-problem')[3] }} <span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="name" placeholder="{{ __('page.calf-birth-problem')[3] }}" value="{{$calfBirthProblem->name}}" required>

            @error('name')
            <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <div class="text-right">
            <button class="btn btn-success" type="submit" id="submitUpdate">{{ __('page.calf-birth-problem')[5] }}</button>
            <button type="button" class="btn btn-danger" data-dismiss="modal">{{ __('page.calf-birth-problem')[6] }}</button>
        </div>

    </form>

</div>

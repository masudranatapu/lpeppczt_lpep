<div class="card">
    <div class="card-header">
        <h5>{{ __('page.seed-company')[1] }}</h5>
    </div>
    <div class="card-body">
        <form>
            <input type="hidden" name="user_id" value="{{auth()->id()}}">

            <div class="form-group">
                <label for="">{{ __('page.seed-company')[3] }} <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="name" placeholder="{{ __('page.seed-company')[3] }}" required>

                @error('name')
                <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="text-right">
                <button class="btn btn-success" type="submit" id="submit">{{ __('page.seed-company')[5] }}</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">{{ __('page.seed-company')[6] }}</button>
            </div>

        </form>
    </div>
</div>
<h5>{{ __('page.insurance-company')[7] }}</h5>
<hr>
<div>

    <form>
        <input type="hidden" name="id" value="{{$insuranceCompany->id}}">

        <div class="form-group">
            <label for="">{{ __('page.insurance-company')[3] }} <span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="name" placeholder="{{ __('page.insurance-company')[3] }}" value="{{$insuranceCompany->name}}" required>

            @error('name')
            <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <div class="text-right">
            <button class="btn btn-success" type="submit" id="submitUpdate">{{ __('page.insurance-company')[5] }}</button>
            <button type="button" class="btn btn-danger" data-dismiss="modal">{{ __('page.insurance-company')[6] }}</button>
        </div>

    </form>

</div>

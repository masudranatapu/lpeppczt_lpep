<h5>{{ __('page.cattle-disease')[7] }}</h5>
<hr>
<div>

    <form>
        <input type="hidden" name="id" value="{{$cattleVaccine->id}}">

        <div class="form-group">
            <label for="">{{ __('page.cattle-vaccine')[8] }} <span class="text-danger">*</span></label>
            <select name="disease_id" required class="form-control">
                <option value="">Select</option>
                @foreach ($diseases as $id => $name)
                    <option value="{{$id}}" {{$id == $cattleVaccine->disease_id ? 'selected' : ''}}>{{$name}}</option>
                @endforeach
            </select>

            @error('disease_id')
            <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="">{{ __('page.cattle-disease')[3] }} <span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="name" placeholder="{{ __('page.cattle-disease')[3] }}" value="{{$cattleVaccine->name}}" required>

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

<h5>{{ __('page.cattle-vaccine')[1] }}</h5>
<hr>
<div>

    <form>
        <input type="hidden" name="user_id" value="{{auth()->id()}}">

        <div class="form-group">
            <label for="">{{ __('page.cattle-vaccine')[8] }} <span class="text-danger">*</span></label>
            <select name="disease_id" required class="form-control">
                <option value="">Select</option>
                @foreach ($diseases as $id => $name)
                    <option value="{{$id}}">{{$name}}</option>
                @endforeach
            </select>

            @error('disease_id')
            <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="">{{ __('page.cattle-vaccine')[3] }} <span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="name" placeholder="{{ __('page.cattle-vaccine')[3] }}" required>

            @error('name')
            <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <div class="text-right">
            <button class="btn btn-success" type="submit" id="submit">{{ __('page.cattle-vaccine')[5] }}</button>
            <button type="button" class="btn btn-danger" data-dismiss="modal">{{ __('page.cattle-vaccine')[6] }}</button>
        </div>

    </form>

</div>

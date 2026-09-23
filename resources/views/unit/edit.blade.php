<h5>Update Unit</h5>
<hr>
<div>

    <form>
        <input type="hidden" name="id" value="{{ $unit->id }}">
        <div class="form-group">
            <label for="">Name<span class="text-danger">*</span></label>
            <input type="text" value="{{ $unit->actual_name }}" class="form-control" name="actual_name"
                placeholder="Name" required>
            @error('actual_name')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="">Short Name <span class="text-danger">*</span></label>
            <input type="text" value="{{ $unit->short_name }}" class="form-control" name="short_name"
                placeholder="Short Name" required>
        </div>

        <div class="form-group">
            <label for="is_decimal" style="user-select: none">
                <input id="is_decimal" {{ $unit->is_decimal ? 'checked' : '' }} name="is_decimal" type="checkbox"
                    placeholder="{{ __('page.unit')[8] }}">
                {{ __('page.unit')[8] }}
            </label>
        </div>

        <div class="text-right">
            <button class="btn btn-success" type="submit" id="submitUpdate">Update</button>
            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
        </div>

    </form>

</div>

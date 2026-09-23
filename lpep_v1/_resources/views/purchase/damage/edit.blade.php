<h5> {{ __('page.edit_return_type') }} </h5>
<hr>
<div style="margin-top: -15px">

    <form id="updateDamageType">
        @csrf
        @method('put')
        <input type="hidden" name="id" value="{{ $type->id }}">
        <div class="form-group">
            <label for="">Name</label>
            <input type="text" name="name" value="{{ $type->name }}" class="form-control" required>
        </div>

        <div class="text-right">
            <button class="btn btn-success" type="submit" id="submit">Update</button>
            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
        </div>

    </form>

</div>

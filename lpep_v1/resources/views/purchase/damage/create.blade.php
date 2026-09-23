<h5> {{ __('page.add_new_return_type') }} </h5>
<hr>
<div style="margin-top: -15px">

    <form id="addDamageType">
        @csrf

        <div class="form-group">
            <label for="">Name</label>
            <input type="text" name="name" class="form-control" required>
        </div>

        <div class="text-right">
            <button class="btn btn-success" type="submit" id="submit">Save</button>
            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
        </div>

    </form>

</div>

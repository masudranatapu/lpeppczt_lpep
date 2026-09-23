<h5>Edit Customer Group</h5>
<hr>
<div>

    <form>
        <input type="hidden" id="id" value="{{ $group->id }}" name="id">
        <div class="form-group">
            <label for="">Name<span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="name" placeholder="Name" value="{{ $group->name }}" required>
        </div>

        <div class="form-group">
            <label for="">Amount <strong>(%)</strong></label>
            <input type="text" class="form-control" name="amount" value="{{ $group->amount }}" placeholder="Amount Percentage">
        </div>
       
        <div class="text-right">
            <button class="btn btn-success" type="submit" id="submitUpdate">UPDATE</button>
        </div>

    </form>

</div>
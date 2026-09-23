<h5>Edit VAT/SD/Tax</h5>
<hr>
<div>

    <form>
        <input type="hidden" name="id" value="{{ $vat->id }}">
        <div class="form-group">
            <label for="">VAT/SD/Tax Group Name<span class="text-danger">*</span></label>
            <input type="text" value="{{ $vat->vat_group_name }}" class="form-control" name="vat_group_name" placeholder="VAT/SD/Tax Group Name" required>
        </div>

        <div class="form-group">
            <label for="">Percent (%)<span class="text-danger">*</span></label>
            <input type="text" value="{{ $vat->vat_percent }}" class="form-control" name="vat_percent" placeholder="Percentage" required>
        </div>

        <div class="text-right">
            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            <button class="btn btn-success" type="submit" id="submitUpdate">Update</button>
        </div>

    </form>

</div>
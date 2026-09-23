<h5>Add New VAT/SD/Tax</h5>
<hr>
<div>

    <form>
        <div class="form-group">
            <label for="">VAT/SD/Tax Group Name<span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="vat_group_name" placeholder="VAT/SD/Tax Group Name" required>
        </div>

        <div class="form-group">
            <label for="">Percent (%)<span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="vat_percent" placeholder="Percentage" required>
        </div>

        <div class="text-right">
            <button class="btn btn-success" type="submit" id="submit">Save</button>
            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
        </div>

    </form>

</div>
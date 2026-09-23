<form id="cowfeed-form">
    @csrf
    <div class="modal-header p-2">
        <h5 class="modal-title">Create Cow Food</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
    </div>

    <div class="modal-body p-4">
        <div class="row">
            <div class="form-group col-md-6 col-sm-12">
                <label>Item Name <span class="text-danger">*</span></label>
                <select name="item_name" class="form-control" required>
                    <option value="">-- Select Item --</option>
                    <option value="Feed">Feed</option>
                    <option value="wheat bran">Wheat Bran</option>
                    <option value="Kura">Kura</option>
                    <option value="Cheata gouar">Cheata Gouar</option>
                    <option value="Grass">Grass</option>
                </select>
            </div>

            <div class="form-group col-md-6 col-sm-12">
                <label>Volume</label>
                <input type="text" name="volume" class="form-control">
            </div>

            <div class="form-group col-md-6 col-sm-12">
                <label>Unit Price</label>
                <input type="number" step="0.01" name="unit_price" id="create_unit_price" class="form-control">
            </div>

            <div class="form-group col-md-6 col-sm-12">
                <label>Quantity</label>
                <input type="number" name="qty" id="create_qty" class="form-control">
            </div>

            <div class="form-group col-md-6 col-sm-12">
                <label>Total Amount</label>
                <input type="number" id="create_total_amount" class="form-control" readonly>
            </div>

            <div class="form-group col-md-6 col-sm-12">
                <label>Previous Stock</label>
                <input type="text" name="previus_stock" class="form-control">
            </div>

            <div class="form-group col-md-6 col-sm-12">
                <label>Purchase Date</label>
                <input type="date" name="purchase_date" class="form-control">
            </div>

            <div class="form-group col-md-6 col-sm-12">
                <label>End Date</label>
                <input type="date" name="end_date" class="form-control">
            </div>

            <div class="form-group col-md-6 col-sm-12">
                <label>Farm</label>
                <select name="farm_id" class="form-control">
                    <option value="">-- Select Farm --</option>
                    @foreach($farms as $farm)
                        <option value="{{ $farm->id }}">{{ $farm->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <div class="modal-footer">
        <button type="submit" class="btn btn-success">Save</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
    </div>
</form>

<script>
document.addEventListener("input", function() {
    let price = parseFloat(document.getElementById("create_unit_price").value) || 0;
    let qty   = parseFloat(document.getElementById("create_qty").value) || 0;
    document.getElementById("create_total_amount").value = (price * qty).toFixed(2);
});
</script>

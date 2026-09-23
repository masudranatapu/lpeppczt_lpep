<form action="{{ route('cow-feeds.update', $cowFeed->id) }}" method="POST">
  @csrf
  @method('PUT')
    <div class="modal-header p-2">
        <h5 class="modal-title">Edit Cow Food</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
    </div>

    <div class="modal-body p-4">
        <div class="row">
            <div class="form-group col-md-6 col-sm-12">
                <label>Item Name <span class="text-danger">*</span></label>
                <select name="item_name" class="form-control" required>
                    <option value="">-- Select Item --</option>
                    <option value="Feed" {{ $cowFeed->item_name == 'Feed' ? 'selected' : '' }}>Feed</option>
                    <option value="wheat bran" {{ $cowFeed->item_name == 'wheat bran' ? 'selected' : '' }}>Wheat Bran</option>
                    <option value="Kura" {{ $cowFeed->item_name == 'Kura' ? 'selected' : '' }}>Kura</option>
                    <option value="Cheata gouar" {{ $cowFeed->item_name == 'Cheata gouar' ? 'selected' : '' }}>Cheata Gouar</option>
                    <option value="Grass" {{ $cowFeed->item_name == 'Grass' ? 'selected' : '' }}>Grass</option>
                </select>
            </div>

            <div class="form-group col-md-6 col-sm-12">
                <label>Volume</label>
                <input type="text" name="volume" class="form-control" value="{{ $cowFeed->volume }}">
            </div>

            <div class="form-group col-md-6 col-sm-12">
                <label>Unit Price</label>
                <input type="number" step="0.01" name="unit_price" id="edit_unit_price" class="form-control" value="{{ $cowFeed->unit_price }}">
            </div>

            <div class="form-group col-md-6 col-sm-12">
                <label>Quantity</label>
                <input type="number" name="qty" id="edit_qty" class="form-control" value="{{ $cowFeed->qty }}">
            </div>

            <div class="form-group col-md-6 col-sm-12">
                <label>Total Amount</label>
                <input type="number" id="edit_total_amount" class="form-control" value="{{ $cowFeed->unit_price * $cowFeed->qty }}" readonly>
            </div>

            <div class="form-group col-md-6 col-sm-12">
                <label>Previous Stock</label>
                <input type="text" name="previus_stock" class="form-control" value="{{ $cowFeed->previus_stock }}">
            </div>

            <div class="form-group col-md-6 col-sm-12">
                <label>Purchase Date</label>
                <input type="date" name="purchase_date" class="form-control"
                    value="{{ $cowFeed->purchase_date ? \Carbon\Carbon::parse($cowFeed->purchase_date)->format('Y-m-d') : '' }}">
            </div>

            <div class="form-group col-md-6 col-sm-12">
                <label>End Date</label>
                <input type="date" name="end_date" class="form-control"
                    value="{{ $cowFeed->end_date ? \Carbon\Carbon::parse($cowFeed->end_date)->format('Y-m-d') : '' }}">
            </div>


            <div class="form-group col-md-6 col-sm-12">
                <label>Farm</label>
                <select name="farm_id" class="form-control">
                    <option value="">-- Select Farm --</option>
                    @foreach($farms as $farm)
                        <option value="{{ $farm->id }}" {{ $cowFeed->farm_id == $farm->id ? 'selected' : '' }}>
                            {{ $farm->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <div class="modal-footer">
        <button type="submit" class="btn btn-primary update-btn">Update</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
    </div>
</form>

<script>
document.addEventListener("input", function() {
    let price = parseFloat(document.getElementById("edit_unit_price").value) || 0;
    let qty   = parseFloat(document.getElementById("edit_qty").value) || 0;
    document.getElementById("edit_total_amount").value = (price * qty).toFixed(2);
});
</script>

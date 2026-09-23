<div class="modal-header p-2">
    <h5 class="modal-title">Cow Feed Details</h5>
    <button type="button" class="close" data-dismiss="modal">&times;</button>
</div>

<div class="modal-body p-4">
    <div class="row">
        <div class="col-md-6 col-sm-12 mb-3">
            <strong>Item Name:</strong>
            <p>{{ $cowFeed->item_name }}</p>
        </div>

        <div class="col-md-6 col-sm-12 mb-3">
            <strong>Volume:</strong>
            <p>{{ $cowFeed->volume ?? '-' }}</p>
        </div>

        <div class="col-md-6 col-sm-12 mb-3">
            <strong>Unit Price:</strong>
            <p>{{ $cowFeed->unit_price ? number_format($cowFeed->unit_price,2) : '-' }}</p>
        </div>

        <div class="col-md-6 col-sm-12 mb-3">
            <strong>Quantity:</strong>
            <p>{{ $cowFeed->qty ?? '-' }}</p>
        </div>

        <div class="col-md-6 col-sm-12 mb-3">
            <strong>Total Amount:</strong>
            <p>
                {{ $cowFeed->unit_price && $cowFeed->qty ? number_format($cowFeed->unit_price * $cowFeed->qty,2) : '-' }}
            </p>
        </div>

        <div class="col-md-6 col-sm-12 mb-3">
            <strong>Previous Stock:</strong>
            <p>{{ $cowFeed->previus_stock ?? '-' }}</p>
        </div>

        <div class="col-md-6 col-sm-12 mb-3">
            <strong>Purchase Date:</strong>
            <p>{{ $cowFeed->purchase_date ? \Carbon\Carbon::parse($cowFeed->purchase_date)->format('d M Y') : '-' }}</p>
        </div>

        <div class="col-md-6 col-sm-12 mb-3">
            <strong>End Date:</strong>
            <p>{{ $cowFeed->end_date ? \Carbon\Carbon::parse($cowFeed->end_date)->format('d M Y') : '-' }}</p>
        </div>

        <div class="col-md-6 col-sm-12 mb-3">
            <strong>Farm:</strong>
            <p>{{ $cowFeed->farm->name ?? '-' }}</p>
        </div>

        <div class="col-md-6 col-sm-12 mb-3">
            <strong>Created By:</strong>
            <p>{{ $cowFeed->creator->name ?? '-' }}</p>
        </div>
    </div>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
</div>

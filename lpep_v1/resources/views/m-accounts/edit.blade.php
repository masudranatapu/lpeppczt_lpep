<h5>{{ __('page.m-account')[7] }}</h5>
<hr>
<div>

    <form>
        <input type="hidden" name="id" value="{{$mAccount->id}}">

        <div class="form-group">
            <label for="">{{ __('page.m-account')[8] }} <span class="text-danger">*</span></label>
            <select class="form-control" name="type" required>
                <option value="">Select</option>
                @foreach (['Income' => 'আয়', 'Expense' => 'ব্যয়'] as $key => $value)
                <option value="{{$key}}" {{$mAccount->type == $key ? 'selected' : ''}}>{{ $value }}</option>
                @endforeach
            </select>

            @error('type')
            <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="">{{ __('page.m-account')[3] }} <span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="name" placeholder="{{ __('page.m-account')[3] }}" value="{{$mAccount->name}}" required>

            @error('name')
            <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="">Date <span class="text-danger">*</span></label>
            <input type="date" class="form-control" name="date" placeholder="Data Select" value="{{ $mAccount->date }}" required>

            @error('date')
            <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="">{{ __('page.m-account')[9] }} <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="unit" name="unit" placeholder="{{ __('page.m-account')[9] }}" value="{{$mAccount->unit}}" required>

            @error('unit')
            <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="">Quantity <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="quantity" name="quantity" placeholder="Quantity" value="{{$mAccount->quantity}}" required>

            @error('quantity')
            <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="">Total Amount<span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="total" name="total_amount" placeholder="Total Amount" value="{{$mAccount->total_amount}}" required readonly>

            @error('total')
            <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <div class="text-right">
            <button class="btn btn-success" type="submit" id="submitUpdate">{{ __('page.m-account')[5] }}</button>
            <button type="button" class="btn btn-danger" data-dismiss="modal">{{ __('page.m-account')[6] }}</button>
        </div>

    </form>

</div>


<script>
    $('#quantity').keyup(function() {
        var quantity = $(this).val();
        var unit =parseInt($('#unit').val());
        var total = quantity * unit;
        $('#total').val(total);
    });
    $('#unit').keyup(function() {
        var quantity = $('#quantity').val();
        var unit = $(this).val();
        var total = quantity * unit;
        $('#total').val(total);
    });
    
</script>
<h5>{{ __('page.m-account')[1] }}</h5>
<hr>
<div>

    <form>
        <input type="hidden" name="user_id" value="{{auth()->id()}}">

        <div class="form-group">
            <label for="">{{ __('page.m-account')[8] }} <span class="text-danger">*</span></label>
            <select class="form-control" name="type" required>
                <option value="">Select</option>
                @foreach (['Income' => 'আয়', 'Expense' => 'ব্যয়'] as $key => $value)
                <option value="{{$key}}">{{$value}}</option>
                @endforeach
            </select>

            @error('type')
            <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="">{{ __('page.m-account')[3] }} <span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="name" placeholder="{{ __('page.m-account')[3] }}" required>

            @error('name')
            <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="">Date <span class="text-danger">*</span></label>
            <input type="date" class="form-control" name="date" placeholder="Data Select" required>

            @error('date')
            <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="">{{ __('page.m-account')[9] }} <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="unit" name="unit" placeholder="{{ __('page.m-account')[9] }}" required>

            @error('unit')
            <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="">Quantity <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="quantity" name="quantity" placeholder="Quantity" required>

            @error('quantity')
            <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="">Total Amount<span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="total" name="total_amount" placeholder="Total Amount" required readonly>

            @error('total')
            <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <div class="text-right">
            <button class="btn btn-success" type="submit" id="submit">{{ __('page.m-account')[5] }}</button>
            <button type="button" class="btn btn-danger" data-dismiss="modal">{{ __('page.m-account')[6] }}</button>
        </div>

    </form>

</div>

<script>
    $('#quantity').keyup(function() {
        var quantity = $(this).val();
        var unit = $('#unit').val();
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
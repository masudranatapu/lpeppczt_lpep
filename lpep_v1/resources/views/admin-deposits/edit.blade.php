<h5 style="margin: 0 !important">{{ __('page.admin_deposit.update_deposit') }}</h5>
<hr>
<div>
    <form id="depositUpdateForm">
        @csrf
        @method("put")
        <input type="hidden" name="user_id" value="{{ auth()->id() }}">
        <input type="hidden" name="id" value="{{ $deposit->id }}">

        <div class="form-group">
            <label for=""> {{ __('page.common.date') }} <span class="text-danger">*</span></label>
            <input type="text" name="date" value="{{ $deposit->date }}" class="form-control datepicker"
                autocomplete="off">
            @error('date')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="">{{ __('page.common.amount') }} <span class="text-danger">*</span></label>
            <input type="number" name="amount" value="{{ $deposit->amount }}" min="{{ $deposit->loan_amount }}"
                class="form-control" step="0.01">
            @error('amount')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="">{{ __('page.common.note') }}</label>
            <textarea name="note" class="form-control" rows="4"> {{ $deposit->note }}</textarea>
            @error('note')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <div class="text-right">
            <button class="btn btn-success" type="submit" id="submit">{{ __('page.unit')[6] }}</button>
            <button type="button" class="btn btn-danger" data-dismiss="modal">{{ __('page.unit')[7] }}</button>
        </div>
    </form>
</div>

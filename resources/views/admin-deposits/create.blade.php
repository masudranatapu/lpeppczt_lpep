<h5 style="margin: 0 !important">{{ __('page.agent-point')[1] }}</h5>
<hr>
<div>
    <form id="depositStoreForm">
        @csrf
        <input type="hidden" name="user_id" value="{{ auth()->id() }}">
        <div class="form-group">
            <label for="">{{ __('page.common.date') }} <span class="text-danger">*</span></label>

            <input type="text" name="date" class="form-control datepicker" autocomplete="off"
                value="{{ date('Y-m-d') }}">

            @error('date')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="">{{ __('page.common.amount') }} <span class="text-danger">*</span></label>

            <input type="number" name="amount" class="form-control" step="0.01">

            @error('amount')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="">{{ __('page.common.note') }}</label>

            <textarea name="note" class="form-control" rows="4"></textarea>

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

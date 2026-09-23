<h5 style="margin-bottom: 20px;">{{ __('page.agent-point')[1] }}</h5>
<div>
    <form id="agentPointStore" method="POST">
        @csrf
        <input type="hidden" name="user_id" value="{{ auth()->id() }}">
        <div class="form-group">
            <label for="">{{ __('page.agent-point')[3] }} <span class="text-danger">*</span></label>
            <select name="agent_id" id="agent_id" class="form-control" required>
                <option value="">Select</option>
                @foreach ($agents as $agent)
                    <option value="{{ $agent->id }}">{{ $agent->name }}</option>
                @endforeach
            </select>

            @error('agent_id')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="">{{ __('page.agent-point')[4] }} <span class="text-danger">*</span></label>
            <input type="number" class="form-control" name="assigned_points"
                placeholder="{{ __('page.agent-point')[4] }}" required>

            @error('assigned_points')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <div class="text-right">
            <button class="btn btn-success" type="submit" id="submit">{{ __('page.unit')[6] }}</button>
            <button type="button" class="btn btn-danger" data-dismiss="modal">{{ __('page.unit')[7] }}</button>
        </div>

    </form>

</div>

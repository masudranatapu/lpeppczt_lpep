@extends('layouts.dashboard')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card-box mt-4">
            <h4 class="mb-3">Edit LSP</h4>
            <form method="POST" action="{{ route('warehouse-salesmen.update', $salesman) }}">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label>Area Office</label>
                    <select name="warehouse_id" class="form-control" required>
                        @foreach($warehouses as $warehouse)
                            <option value="{{ $warehouse->id }}" {{ $salesman->warehouse_id == $warehouse->id ? 'selected' : '' }}>
                                {{ $warehouse->name }} ({{ $warehouse->code }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Name</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $salesman->name) }}" required>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email', $salesman->email) }}" required>
                </div>
                <div class="form-group">
                    <label>Phone</label>
                    <input type="text" name="phone" class="form-control" value="{{ old('phone', $salesman->phone) }}">
                </div>
                <div class="form-group">
                    <label>Address</label>
                    <input type="text" name="address" class="form-control" value="{{ old('address', $salesman->address) }}">
                </div>
                <div class="form-group">
                    <label>New Password</label>
                    <input type="password" name="password" class="form-control" placeholder="Leave blank to keep current password">
                </div>
                <div class="form-group">
                    <label>Status</label>
                    <select name="status" class="form-control">
                        <option value="1" {{ old('status', $salesman->status) == 1 ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ old('status', $salesman->status) == 0 ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Update</button>
                <a href="{{ route('warehouse-salesmen.index') }}" class="btn btn-secondary">Back</a>
            </form>
        </div>
    </div>
</div>
@endsection

@extends('layouts.dashboard')
@section('title', '| Edit Area Office')
@section('content')

    <div class="row">
        <div class="col-12">
            <div class="mt-4">
                <h4>Edit Area Office</h4>
            </div>

            <form id="warehouseForm" action="{{ route('warehouses.update', $warehouse) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="card-box table-responsive mt-4" style="border-top: 3px solid #67dd31;">
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="name">Name</label>
                                <input class="form-control" required placeholder="Name" name="name" type="text"
                                    id="name" value="{{ old('name', $warehouse->name) }}" autocomplete="off">
                            </div>
                        </div>

                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="code">Code</label>
                                <input class="form-control" required placeholder="Code" name="code" type="text"
                                    id="code" value="{{ old('code', $warehouse->code) }}" autocomplete="off">
                            </div>
                        </div>

                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input class="form-control" required placeholder="Email" name="email" type="email"
                                    id="email" value="{{ old('email', $warehouse->email) }}" autocomplete="off">
                            </div>
                        </div>

                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="address">Address</label>
                                <input class="form-control" placeholder="Address" name="address" type="text"
                                    id="address" value="{{ old('address', $warehouse->address) }}" autocomplete="off">
                            </div>
                        </div>

                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="password">Password</label>
                                <input class="form-control" placeholder="New Password" name="password" type="password"
                                    id="password">
                            </div>
                        </div>

                        <div class="col-sm-4">
                            <div class="form-group">
                                <label>Status</label>
                                <select name="status" class="form-control">
                                    <option value="1" {{ old('status', $warehouse->status) == 1 ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ old('status', $warehouse->status) == 0 ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="notes">Notes</label>
                                <textarea name="notes" rows="1" class="form-control" id="notes"
                                    placeholder="Notes">{{ old('notes', $warehouse->notes) }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="text-right">
                        <div class="btn-group">
                            <x-loading-submit type="submit" id="saveWarehouse" class="btn btn-primary" icon="save" loading-text="Updating...">Update</x-loading-submit>
                            <a href="{{ route('warehouses.index') }}" class="btn btn-secondary">Back</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <!-- end row -->

@endsection

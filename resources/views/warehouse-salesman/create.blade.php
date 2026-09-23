@extends('layouts.dashboard')
@section('title', '| Add LSP')
@section('content')

    <div class="row">
        <div class="col-12">
            <div class="mt-4">
                <h4>Add LSP</h4>
            </div>

            <form id="salesmanForm" action="{{ route('warehouse-salesmen.store') }}" method="POST">
                @csrf
                <div class="card-box table-responsive mt-4" style="border-top: 3px solid #67dd31;">
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="warehouse_id">Area Office</label>
                                <select class="form-control" required name="warehouse_id" id="warehouse_id">
                                    <option value="">Select Area Office</option>
                                    @foreach ($warehouses as $warehouse)
                                        <option value="{{ $warehouse->id }}" {{ old('warehouse_id', $selectedWarehouseId) == $warehouse->id ? 'selected' : '' }}>
                                            {{ $warehouse->name }} ({{ $warehouse->code }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="name">Name</label>
                                <input class="form-control" required placeholder="Name" name="name" type="text"
                                    id="name" value="{{ old('name') }}" autocomplete="off">
                            </div>
                        </div>

                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input class="form-control" required placeholder="Email" name="email" type="email"
                                    id="email" value="{{ old('email') }}" autocomplete="off">
                            </div>
                        </div>

                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="phone">Phone</label>
                                <input class="form-control" placeholder="Phone" name="phone" type="text"
                                    id="phone" value="{{ old('phone') }}" autocomplete="off">
                            </div>
                        </div>

                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="address">Address</label>
                                <input class="form-control" placeholder="Address" name="address" type="text"
                                    id="address" value="{{ old('address') }}" autocomplete="off">
                            </div>
                        </div>

                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="password">Password</label>
                                <div class="input-group">
                                    <input class="form-control" required placeholder="Password" name="password"
                                        type="password" id="password">
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-outline-secondary" id="togglePassword" aria-label="Show password">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="text-right">
                        <div class="btn-group">
                            <button type="submit" id="saveSalesman" class="btn btn-primary">Save</button>
                            <a href="{{ route('warehouse-salesmen.index') }}" class="btn btn-secondary">Back</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <!-- end row -->

    <script>
        document.getElementById('togglePassword').addEventListener('click', function () {
            const password = document.getElementById('password');
            const visible = password.type === 'text';
            password.type = visible ? 'password' : 'text';
            this.setAttribute('aria-label', visible ? 'Show password' : 'Hide password');
            this.querySelector('i').className = visible ? 'fas fa-eye' : 'fas fa-eye-slash';
        });
    </script>

@endsection

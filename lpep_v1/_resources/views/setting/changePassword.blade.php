@extends('layouts.dashboard')

@section('content')
    
    <div class="row">
        <div class="col-md-12">
            <div class="card-box table-responsive mt-4">
                <div class="row">
                    <div class="col-md-6">
                        <h4 class="m-t-0 header-title mb-5"><b>Change Password</b></h4>
                    </div>
                </div>
                <div class="col-md-8 m-auto mt-5">
                    <form action="{{ route('change.password.update') }}" method="post" class="resultFind">
                      @csrf
                      <div class="form-group">
                        <label for="roll" class="form-label">Old Password</label>
                        <input type="password" name="old_password" placeholder="Enter Old Password" required class="form-control" id="roll">
                        @error ('old_password')
                          <span class="text-danger">{{ $message }}</span>
                        @enderror
                      </div>
                      <div class="form-group">
                        <label for="new" class="form-label">New Password</label>
                        <input type="password" name="password" placeholder="Enter New Password" class="form-control" id="new">
                        @error ('password')
                          <span class="text-danger">{{ $message }}</span>
                        @enderror
                        @if (session('samePassword'))
                          <span class="text-danger">{{ session('samePassword') }}</span>
                        @endif
                      </div>
                      <div class="form-group">
                        <label for="Confirm" class="form-label">Confirm Password</label>
                        <input type="password" name="password_confirmation" placeholder="Enter Confirm Password" class="form-control" id="Confirm">
                        @error ('password_confirmation')
                          <span class="text-danger">{{ $message }}</span>
                        @enderror
                      </div>
                      
                      <div class="mt-3 float-right">
                        <button type="submit" class="btn btn-outline-warning">Change</button>
                      </div>
        
                    </form>
                </div>
        

            </div>
        </div>
    </div>

@endsection
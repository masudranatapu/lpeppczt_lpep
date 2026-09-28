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
                        <label for="old_password" class="form-label">Old Password</label>
                        <div class="input-group">
                          <input type="password" name="old_password" placeholder="Enter Old Password" required class="form-control" id="old_password">
                          <div class="input-group-append">
                            <button type="button" class="btn btn-light border password-toggle" data-target="old_password" aria-label="Toggle password visibility">
                              <i class="fa fa-eye"></i>
                            </button>
                          </div>
                        </div>
                        @error ('old_password')
                          <span class="text-danger">{{ $message }}</span>
                        @enderror
                      </div>
                      <div class="form-group">
                        <label for="new_password" class="form-label">New Password</label>
                        <div class="input-group">
                          <input type="password" name="password" placeholder="Enter New Password" class="form-control" id="new_password">
                          <div class="input-group-append">
                            <button type="button" class="btn btn-light border password-toggle" data-target="new_password" aria-label="Toggle password visibility">
                              <i class="fa fa-eye"></i>
                            </button>
                          </div>
                        </div>
                        @error ('password')
                          <span class="text-danger">{{ $message }}</span>
                        @enderror
                        @if (session('samePassword'))
                          <span class="text-danger">{{ session('samePassword') }}</span>
                        @endif
                      </div>
                      <div class="form-group">
                        <label for="confirm_password" class="form-label">Confirm Password</label>
                        <div class="input-group">
                          <input type="password" name="password_confirmation" placeholder="Enter Confirm Password" class="form-control" id="confirm_password">
                          <div class="input-group-append">
                            <button type="button" class="btn btn-light border password-toggle" data-target="confirm_password" aria-label="Toggle password visibility">
                              <i class="fa fa-eye"></i>
                            </button>
                          </div>
                        </div>
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

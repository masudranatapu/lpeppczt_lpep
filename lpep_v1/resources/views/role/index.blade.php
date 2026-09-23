@extends('layouts.dashboard')

@section('content')

    <div class="row">
        <div class="col-12">
            <div class="card-box table-responsive mt-4" style="border-top: 3px solid #2aa9b9;">
                <div class="row">
                    <div class="col-md-6">
                        <h4 class="m-t-0 header-title mb-5"><b>{{ __('page.role')[0] }}</b></h4>
                    </div>
                    {{-- <div class="col-md-6 text-right">
                        @if (permission('ro2'))
                            <a href="{{ route('role.create') }}" class="btn btn-primary waves-effect waves-light m-b-5"
                                id="addNew"> <i class="fa fa-plus-square m-r-5"></i> <span>{{ __('page.role')[1] }}</span>
                            </a>
                        @endif
                    </div> --}}
                </div>

                <table id="datatable" class="table table-bordered table-hover mt-4" cellspacing="0" width="100%">
                    <thead class="theme-primary text-white">
                        <tr>
                            <th>{{ __('page.role')[2] }}</th>
                            <th>{{ __('page.role')[3] }}</th>
                            <th>{{ __('page.role')[4] }}</th>
                        </tr>
                    </thead>
                    <tbody>

                        @foreach ($roles as $role)
                            <tr>
                                <td>{{ $loop->index + 1 }}</td>
                                <td>{{ $role->role_name }}</td>
                                <td>
                                    <div class="btn-group">
                                        @if (permission('ro2') && $role->id > 1)
                                            <a href="{{ route('role.edit', $role->id) }}"
                                                class="btn btn-success btn-sm">Edit</a>
                                        @endif
                                        {{-- @if (permission('ro3'))
                                        <a href="{{ route('role.destroy',$role->id) }}" id="delete" class="btn btn-danger btn-sm">Delete</a>
                                    @endif --}}
                                    </div>
                                </td>
                            </tr>
                        @endforeach

                    </tbody>

                </table>

            </div>
        </div>
    </div>

@endsection

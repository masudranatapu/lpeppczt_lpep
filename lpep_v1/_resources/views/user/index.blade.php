@extends('layouts.dashboard')
@section('title', '| User List')
@section('content')

    <div class="row">
        <div class="col-12">
            <div class="card-box table-responsive mt-4" style="border-top: 3px solid #2aa9b9;">
                <div class="row">
                    <div class="col-md-6">
                        <h4 class="m-t-0 header-title mb-4"><b>{{ __('page.user')[0] }}</b></h4>
                    </div>
                    <div class="col-md-6 text-right">
                        @if (permission('u2'))
                            <a href="{{ route('user.create') }}" class="btn btn-primary"><i class="fa fa-plus"></i> Add
                                User</a>
                        @endif
                    </div>
                </div>

                <table id="datatable" class="table table-bordered table-hover mt-4" cellspacing="0" width="100%">
                    <thead class="theme-primary text-white">
                        <tr>
                            <th>{{ __('page.user')[1] }}</th>
                            <th>{{ __('page.user')[2] }}</th>
                            <th>{{ __('page.user')[7] }}</th>
                            <th>{{ __('page.user')[3] }}</th>
                            <th>{{ __('page.phone') }}</th>
                            <th>{{ __('page.user')[4] }}</th>
                            <th>{{ __('page.user')[6] }}</th>
                        </tr>
                    </thead>
                    <tbody>

                        @foreach ($users as $user)
                            <tr>
                                <td>{{ $loop->index + 1 }}</td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->employee_name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->mobile }}</td>
                                <td>{{ $user->userPermission->role->role_name }}</td>
                                <td>
                                    <div class="btn-group">
                                        @if (permission('u3'))
                                            <a href="{{ route('user.edit', $user->id) }}"
                                                class="btn btn-success btn-sm"><i class="fa fa-edit"></i></a>
                                        @endif
                                        @if (permission('u4') && $user->id !== auth()->id())
                                            <a href="{{ route('user.destroy', $user->id) }}" id="delete"
                                                class="btn btn-danger btn-sm"><i class="fa fa-trash"></i></a>
                                        @endif
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

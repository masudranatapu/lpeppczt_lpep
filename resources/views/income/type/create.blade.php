@extends('layouts.dashboard')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="mt-4">
                <h4>{{ isset($type) ? __('Edit Income Type') : __('Create Income Type') }}</h4>
            </div>
            <div class="card-box">
                <div class="row">

                    <a href="{{ route('incomeType.index') }}"
                        class="btn btn-success btn-rounded waves-effect waves-light m-b-5">
                        <i class="fa fa-arrow-left m-r-5"></i>
                        <span>{{ __('Back') }}</span>
                    </a>

                </div>

                <div class="my-5">
                    <form action="{{ isset($type) ? route('incomeType.update', $type->id) : route('incomeType.store') }}"
                        method="post">
                        @csrf
                        @if (isset($type))
                            @method('PUT')
                        @endif

                        <div class="form-group">
                            <input type="text" name="income_type" class="form-control" placeholder="income type"
                                value="{{ old('income_type', isset($type) ? $type->income_type : '') }}">

                            @error('income_type')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <button type="submit" class="btn  btn-primary btn-rounded " style="cursor: pointer;">
                            {{ isset($type) ? 'Update' : 'Submit' }}
                        </button>
                    </form>
                </div>

            </div>

        </div>
    </div>
@endsection

@push('css')
    <style>
        .table th {
            font-size: 12px !important;
        }
    </style>
@endpush

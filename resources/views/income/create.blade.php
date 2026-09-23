@extends('layouts.pos_layout')
@section('pos')
    <div class="row">
        <div class="col-12">

            <div class="card-box">
                <div class="d-flex justify-content-between">
                    <a href="{{ route('income.index') }}" class="btn btn-primary waves-effect waves-light m-b-5">
                        <i class="fa fa-arrow-left m-r-5"></i>
                        <span>{{ __('Back') }}</span>
                    </a>

                    <div class="mt-4">
                        <h4>{{ __('page.income')[1] }}</h4>
                    </div>
                </div>

                <form action="{{ route('income.store') }}" method="POST">
                    @csrf
                    <div class="form-group mb-3">
                        <label for="">{{ __('Date') }}</label>
                        <input type="date" name="income_date" value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}"
                            class="form-control" required>
                    </div>

                    <div class="table-responsive">

                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('Name of Employee') }}</th>
                                    @foreach ($income_types as $incmtype)
                                        <th>{{ $incmtype->income_type }}</th>
                                    @endforeach
                                    <th>{{ __('Note') }}</th>
                                    <th>{{ __('Mark as Off Duty') }}</th>
                                    <th>{{ __('Total') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($users as $user)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            {{ $user->employee_name }} - <small>({{ $user->name }})</small>
                                        </td>
                                        @foreach ($income_types as $incmtype)
                                            <td>
                                                <input type="number"
                                                    name="income_types[{{ $user->id }}][{{ $incmtype->income_type }}]"
                                                    class="form-control" value="0"
                                                    placeholder="{{ $incmtype->income_type }}">
                                            </td>
                                        @endforeach
                                        <td>
                                            <input type="text" name="income_types[{{ $user->id }}][note]"
                                                class="form-control">
                                        </td>
                                        <td>
                                            <input type="checkbox" value="1" name="income_types[{{ $user->id }}][is_absent]"  class="form-control">
                                        </td>
                                        <td>
                                            <input type="number" name="income_types[{{ $user->id }}][total]"
                                                value="0" readonly class="form-control total">
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ count($income_types) + 3 }}">{{ __('No Users Found...!') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="text-right mt-3">
                        <button class="btn btn-success" type="submit">{{ __('Submit') }}</button>
                    </div>
                </form>
            </div>

        </div>
    </div>
@endsection


@push('js')
    <script src="{{ asset('js/income.js') }}"></script>
@endpush


@push('css')
    <style>
        input[type="number"],
        input[type="text"] {
            min-width: 65px;
        }

        .table th {
            font-size: 12px !important;
        }

        .form-control {
            padding: .275rem 0.15rem !important;
        }

        .total {
            min-width: 80px;
        }
    </style>
@endpush

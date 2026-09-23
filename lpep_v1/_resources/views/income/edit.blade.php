@extends('layouts.pos_layout')
@section('pos')
    <div class="row">
        <div class="col-12">

            <div class="card-box">
                <div class="d-flex justify-content-between">
                    <a href="{{ route('income.index') }}" class="btn btn-success waves-effect waves-light m-b-5">
                        <i class="fa fa-arrow-left m-r-5"></i>
                        <span>{{ __('Back') }}</span>
                    </a>

                    <div class="mt-4">
                        <h4>{{ __('page.income')[9] }}</h4>
                    </div>
                </div>

                <form action="{{ route('income.update', $income->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="form-group mb-3">
                        <label for="">{{ __('Date') }}</label>
                        <input type="date" name="income_date" value="{{ $income->income_date }}" class="form-control"
                            required>
                    </div>

                    <div class="table-responsive">

                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('Name of Employee') }}</th>
                                    @foreach ($income->details->first()->income_types ?? [] as $inctypeName => $value)
                                        <th>{{ $inctypeName }}</th>
                                    @endforeach
                                    <th>{{ __('Note') }}</th>
                                    <th>{{ __('Mark as Off Duty') }}</th>
                                    <th>{{ __('Total') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($income->details as $detail)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            {{ $detail->user->employee_name }} -
                                            <small>({{ $detail->user->name }})</small>
                                        </td>
                                        @foreach ($detail->income_types as $incmtypeName => $inctypeVal)
                                            <td>
                                                <input type="number"
                                                    name="income_types[{{ $detail->user->id }}][{{ $incmtypeName }}]"
                                                    class="form-control" value="{{ $inctypeVal }}"
                                                    placeholder="{{ $incmtypeName }}">
                                            </td>
                                        @endforeach
                                        <td>
                                            <input type="text" name="income_types[{{ $detail->user->id }}][note]"
                                                class="form-control" value="{{ $detail->note }}">
                                        </td>

                                        <td>
                                            <input type="checkbox" name="income_types[{{ $detail->user->id }}][is_absent]" class="form-control" value="1" {{ $detail->is_absent ? 'checked' : '' }}>
                                        </td>
                                        <td>
                                            <input type="number" name="income_types[{{ $detail->user->id }}][total]"
                                                value="{{ numFormat($detail->total) }}" readonly
                                                class="form-control total">
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="">{{ __('No Users Found...!') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="text-right mt-3">
                        <button class="btn btn-success" type="submit">{{ __('Update') }}</button>
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

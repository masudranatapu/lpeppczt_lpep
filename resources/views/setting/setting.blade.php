@extends('layouts.dashboard')
@section('title', 'Setting')
@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="card table-responsive mt-4 theme-color-set">
                <div class="card-header">
                    <h4 class="header-title text-center" style="color: {{ $setting->color }};font-size: 27px">
                        <b>{{ __('page.businessSetting')[0] }}</b>
                    </h4>
                </div>
                <div class="card-body">

                    <form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" value="{{ $setting->id }}" name="id">
                        <div class="form-group row">
                            <div class="col-md-6">
                                <label for="">{{ __('page.businessSetting')[1] }}</label>
                                <input type="text" name="name" value="{{ $setting->name }}" class="form-control"
                                    required>
                            </div>
                            <div class="col-md-6">
                                <label for="">{{ __('page.invoice_name') }}</label>
                                <input type="text" name="invoice_name" value="{{ $setting->invoice_name }}"
                                    class="form-control" placeholder="Invoice Name">
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-6">
                                <label for="">{{ __('page.businessSetting')[2] }}</label>
                                <input type="email" name="email" value="{{ $setting->email }}" class="form-control"
                                    required>
                            </div>
                            <div class="col-md-6">
                                <label for="">{{ __('page.businessSetting')[3] }}</label>
                                <input type="text" name="mobile" value="{{ $setting->mobile }}" class="form-control"
                                    required>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-6">
                                <label for="">{{ __('page.businessSetting')[11] }}</label>
                                <input type="color" name="color" value="{{ $setting->color }}" class="form-control"
                                    required>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-6">
                                <label for="">{{ __('page.businessSetting')[12] }}</label>
                                <input type="file" name="logo" class="dropify"
                                    data-default-file="{{ asset(json_decode($setting->logo)) }}" />
                            </div>
                            <div class="col-md-6">
                                <label for="">{{ __('page.invoice_logo') }}</label>
                                <input type="file" name="invoice_logo" class="dropify"
                                    data-default-file="{{ asset($setting->invoice_logo) }}" />
                            </div>
                        </div>


                        <div class="float-right mt-3">
                            <button type="submit" class="btn btn-success">{{ __('page.businessSetting')[13] }}</button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('script')
    <!-- file uploads js -->
    <script src="{!! asset('backend') !!}/plugins/fileuploads/js/dropify.min.js"></script>
    <script type="text/javascript">
        $('.dropify').dropify({
            messages: {
                'default': 'Drag and drop a file here or click',
                'replace': 'Drag and drop or click to replace',
                'remove': 'Remove',
                'error': 'Ooops, something wrong appended.'
            },
            error: {
                'fileSize': 'The file size is too big (1M max).'
            }
        });
    </script>
@endsection

@extends('layouts.dashboard')

@section('content')
    <style>
        .employee-picture-editor {
            display: flex;
            align-items: center;
            gap: 18px;
            padding: 16px;
            border: 1px solid #e1e5e9;
            border-radius: 6px;
            background: #f8fafb;
        }

        .employee-picture-preview {
            display: flex;
            flex: 0 0 120px;
            align-items: center;
            justify-content: center;
            width: 120px;
            height: 120px;
            overflow: hidden;
            border: 2px solid #fff;
            border-radius: 6px;
            background: #e9ecef;
            box-shadow: 0 1px 5px rgba(0, 0, 0, .16);
            color: #6c757d;
            text-align: center;
        }

        .employee-picture-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .employee-picture-preview .picture-placeholder {
            padding: 10px;
            font-size: 13px;
        }

        .employee-picture-controls {
            flex: 1;
            min-width: 0;
        }

        @media (max-width: 575px) {
            .employee-picture-editor {
                align-items: flex-start;
                flex-direction: column;
            }
        }
    </style>

    <div class="row">
        <div class="col-12">
            <div class="card-box table-responsive mt-4" style="border-top: 3px solid #2aa9b9;">
                <div class="row">
                    <div class="col-md-6">
                        <h4 class="m-t-0 header-title mb-5"><b>Update Employee</b></h4>
                    </div>
                    <div class="col-md-6"></div>
                </div>

                <form method="POST" action="{{ route('employee.update', $employee->id) }}" enctype="multipart/form-data">
                    @csrf
                    @method('put')

                    <div class="form-group row">
                        <div class="col-md-6">
                            <label for="">Employee Name<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" value="{{ $employee->employee_name }}"
                                name="employee_name" placeholder="Employee Name" required>
                        </div>
                        <div class="col-md-6">
                            <label for="">Designation<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" value="{{ $employee->designation }}"
                                name="designation" placeholder="Designation" required>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-md-6">
                            <label for="">Email<span class="text-danger">*</span></label>
                            <input type="text" value="{{ $employee->email }}" class="form-control" name="email"
                                placeholder="Email" id="email" required>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-md-6">
                            <label for="">Mobile<span class="text-danger">*</span></label>
                            <input type="text" value="{{ $employee->mobile }}" class="form-control" name="mobile"
                                placeholder="Mobile" id="mobile" required>
                        </div>
                        <div class="col-md-6">
                            <label for="">NID Number<span class="text-danger">*</span></label>
                            <input type="text" value="{{ $employee->nid_number }}" class="form-control"
                                name="nid_number" placeholder="NID Number" id="nid_number" required>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-12">
                            <label for="picture"><strong>Employee Picture</strong></label>
                            <div class="employee-picture-editor">
                                <div class="employee-picture-preview" id="picturePreview">
                                    @if ($employee->picture_url)
                                        <img src="{{ $employee->picture_url }}" id="picturePreviewImage"
                                            alt="{{ $employee->employee_name }}">
                                    @else
                                        <div class="picture-placeholder" id="picturePlaceholder">
                                            <i class="fa fa-user fa-3x mb-2"></i>
                                            <div>No picture available</div>
                                        </div>
                                    @endif
                                </div>
                                <div class="employee-picture-controls">
                                    <label class="btn btn-primary mb-2" for="picture">
                                        <i class="fa fa-camera mr-1"></i>
                                        {{ $employee->picture_url ? 'Change Picture' : 'Upload Picture' }}
                                    </label>
                                    <input type="file" class="d-none" name="picture" id="picture"
                                        accept="image/jpeg,image/png,image/gif,image/webp">
                                    <button type="button" class="btn btn-outline-secondary mb-2 ml-1 d-none"
                                        id="cancelPictureSelection">
                                        Cancel Selection
                                    </button>
                                    <div id="selectedPictureName" class="small text-muted">
                                        JPG, PNG, GIF or WEBP. Maximum size 5 MB.
                                    </div>
                                    <small class="form-text text-muted">
                                        Select a new file only when you want to replace the current picture.
                                    </small>
                                    @error('picture')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-md-6">
                            <label for="">Address<span class="text-danger">*</span></label>
                            <input type="text" value="{{ $employee->address }}" class="form-control" name="address"
                                placeholder="Address" id="address" required>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-md-6">
                            <label for="">Joining Date<span class="text-danger">*</span></label>
                            <input type="date" value="{{ $employee->joing_date }}" class="form-control"
                                name="joing_date" placeholder="Joing Date" required>
                        </div>
                        <div class="col-md-6">
                            <label for="resignation_date">Resignation Date</label>
                            <input type="date" value="{{ old('resignation_date', $employee->resignation_date) }}"
                                class="form-control" name="resignation_date" id="resignation_date">
                            @error('resignation_date')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-md-6">
                            <label for="">Salary<span class="text-danger">*</span></label>
                            <input type="text" value="{{ $employee->salary }}" class="form-control" name="salary"
                                placeholder="Salary" id="salary" required>
                        </div>
                    </div>


                    <div class="text-right mt-4">
                        <button class="btn btn-success" type="submit" id="submit">Save</button>
                    </div>

                </form>

            </div>
        </div>

    </div>

@endsection

@section('script')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const input = document.getElementById('picture');
            const preview = document.getElementById('picturePreview');
            const cancelButton = document.getElementById('cancelPictureSelection');
            const selectedName = document.getElementById('selectedPictureName');
            const originalPreview = preview.innerHTML;
            const helpText = 'JPG, PNG, GIF or WEBP. Maximum size 5 MB.';

            input.addEventListener('change', function () {
                const file = this.files && this.files[0];

                if (!file) {
                    resetSelection();
                    return;
                }

                if (!file.type.startsWith('image/')) {
                    alert('Please select a valid image file.');
                    resetSelection();
                    return;
                }

                if (file.size > 5 * 1024 * 1024) {
                    alert('The picture must not be larger than 5 MB.');
                    resetSelection();
                    return;
                }

                const reader = new FileReader();
                reader.onload = function (event) {
                    preview.innerHTML = '<img src="' + event.target.result + '" alt="New employee picture preview">';
                };
                reader.readAsDataURL(file);

                selectedName.textContent = 'Selected: ' + file.name;
                cancelButton.classList.remove('d-none');
            });

            cancelButton.addEventListener('click', resetSelection);

            function resetSelection() {
                input.value = '';
                preview.innerHTML = originalPreview;
                selectedName.textContent = helpText;
                cancelButton.classList.add('d-none');
            }
        });
    </script>
@endsection

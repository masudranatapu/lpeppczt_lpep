<div class="card">
    <div class="card-header">
        <h5>{{ __('page.cattle')[1] }}</h5>
    </div>
    <div class="card-body">
        <form onsubmit="handleSubmit(event)">
            <input type="hidden" name="user_id" value="{{ auth()->id() }}">
            @sectionTitle(['title' => 'Basic Info'])
            <div class="row">
                <div class="col-md-6">
                    @include('inc.appCustomer')
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="">{{ __('page.cattle')[4] }} <span class="text-danger">*</span></label>
                        <select class="form-control farm_id" name="farm_id" required >
                            <option value="">Select</option>
                        </select>

                        @error('farm_id')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="">{{ __('page.cattle')[5] }} <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="tag" placeholder="{{ __('page.cattle')[5] }}"
                            required>

                        @error('tag')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="">{{ __('page.cattle')[6] }} <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name" placeholder="{{ __('page.cattle')[6] }}"
                            required>

                        @error('name')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="">{{ __('page.cattle')[7] }} <span class="text-danger">*</span></label>
                        <select class="form-control" name="cattle_group_id" required>
                            <option value="">Select</option>
                            @foreach ($cattleGroups as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>

                        @error('cattle_group_id')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="">{{ __('page.cattle')[8] }} <span class="text-danger">*</span></label>
                        <select class="form-control" name="cattle_breed_id" required>
                            <option value="">Select</option>
                            @foreach ($cattleBreeds as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>

                        @error('cattle_breed_id')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="">{{ __('page.cattle')[9] }} <span class="text-danger">*</span></label>
                        <input type="date" class="form-control datepicker" name="birth_date"
                            placeholder="{{ __('page.cattle')[9] }}" required>

                        @error('birth_date')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="">{{ __('page.cattle')[10] }} <span class="text-danger">*</span></label>
                        <input type="text" class="form-control date-picker" name="weight"
                            placeholder="{{ __('page.cattle')[10] }}" required>

                        @error('weight')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="">{{ __('page.cattle')[11] }} <span class="text-danger">*</span></label>
                        <select class="form-control" name="gender" required>
                            <option value="">Select</option>
                            @foreach (['গাভী', 'দামড়া'] as $value)
                                <option value="{{ $value }}">{{ $value }}</option>
                            @endforeach
                        </select>

                        @error('gender')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="">{{ __('page.cattle')[12] }} <span class="text-danger">*</span></label>
                        <select class="form-control" name="health_problem" required>
                            <option value="">Select</option>
                            @foreach (['হ্যা', 'না'] as $value)
                                <option value="{{ $value }}">{{ $value }}
                                </option>
                            @endforeach
                        </select>

                        @error('health_problem')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>



            @sectionTitle(['title' => 'Milk, Reproduction, Disease Info'])

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="">{{ __('page.cattle')[13] }} <span class="text-danger">*</span></label>
                        <select class="form-control" name="avg_milk_production" required>
                            <option value="">Select</option>
                            @foreach (range(1, 34) as $value)
                                <option value="{{ $value }}">
                                    {{ $value }}
                                </option>
                            @endforeach
                        </select>

                        @error('avg_milk_production')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="">{{ __('page.cattle')[14] }} <span class="text-danger">*</span></label>
                        <select class="form-control" name="milk_production_status" required>
                            <option value="">Select</option>
                            @foreach (['হ্যা', 'না'] as $value)
                                <option value="{{ $value }}">
                                    {{ $value }}
                                </option>
                            @endforeach
                        </select>

                        @error('milk_production_status')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="">{{ __('page.cattle')[15] }} <span class="text-danger">*</span></label>
                        <select class="form-control" name="calf_count" required>
                            <option value="">Select</option>
                            @foreach (range(0, 8) as $value)
                                <option value="{{ $value }}">{{ $value }}
                                </option>
                            @endforeach
                        </select>

                        @error('calf_count')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="">{{ __('page.cattle')[16] }} <span class="text-danger">*</span></label>
                        <input type="date" class="form-control datepicker" name="last_calf_birth_date"
                            placeholder="{{ __('page.cattle')[16] }}" required>

                        @error('last_calf_birth_date')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>


            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="">{{ __('page.cattle')[17] }} <span class="text-danger">*</span></label>
                        <select class="form-control" name="genetic_percentage" required>
                            <option value="">Select</option>
                            @foreach ([50, 55, 60, 65, 70, 75, 80, 85, 87.5, 100] as $value)
                                <option value="{{ $value }}">
                                    {{ $value }}
                                </option>
                            @endforeach
                        </select>

                        @error('genetic_percentage')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                </div>
            </div>



            @sectionTitle(['title' => 'Ancestral Disease Info'])

            <div class="row">
                <div class="col-md-12">
                    @foreach ($diseaseHistories as $id => $name)
                        <div>
                            <input type="checkbox" name="disease_history_ids[]" value="{{ $id }}"
                                id="disease_history_ids_{{ $id }}">
                            <label for="disease_history_ids_{{ $id }}">{{ $name }}</label>
                        </div>
                    @endforeach
                </div>
            </div>



            @sectionTitle(['title' => 'Reproductive Health Info'])

            <div class="row">
                <div class="col-md-12">
                    @foreach ($healthInfos as $id => $name)
                        <div>
                            <input type="checkbox" name="health_info_ids[]" value="{{ $id }}"
                                id="health_info_ids_{{ $id }}">
                            <label for="health_info_ids_{{ $id }}">{{ $name }}</label>
                        </div>
                    @endforeach
                </div>
            </div>



            @sectionTitle(['title' => 'Insurance Info'])

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="">{{ __('page.cattle')[18] }} <span class="text-danger">*</span></label>
                        <select class="form-control" name="insurance_company_id" required>
                            <option value="">Select</option>
                            @foreach ($insuranceCompanies as $id => $name)
                                <option value="{{ $id }}">{{ $name }}
                                </option>
                            @endforeach
                        </select>

                        @error('insurance_company_id')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="">{{ __('page.cattle')[19] }} <span class="text-danger">*</span></label>
                        <select class="form-control" name="insurance_type_id" required>
                            <option value="">Select</option>
                            @foreach ($insuranceTypes as $id => $name)
                                <option value="{{ $id }}">{{ $name }}
                                </option>
                            @endforeach
                        </select>

                        @error('insurance_type_id')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="">{{ __('page.cattle')[24] }} <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="insurance_no"
                            placeholder="{{ __('page.cattle')[24] }}" required>

                        @error('insurance_no')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="text-right">
                <button class="btn btn-success" type="submit" id="submit">{{ __('page.cattle')[21] }}</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">{{ __('page.cattle')[22] }}</button>
            </div>

        </form>
    </div>
</div>

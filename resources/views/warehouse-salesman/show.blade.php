@extends('layouts.dashboard')

@section('title', 'LSP Profile')

@section('content')
    <style>
        .salesman-profile-card {
            overflow: hidden;
            border: 0;
            border-radius: 14px;
            box-shadow: 0 10px 30px rgba(23, 32, 51, .10);
        }
        .salesman-profile-cover {
            min-height: 145px;
            padding: 28px;
            background: linear-gradient(135deg, #172033 0%, #24566f 55%, #2aa9b9 100%);
            color: #fff;
        }
        .salesman-profile-cover p { color: rgba(255, 255, 255, .72); }
        .salesman-profile-body { padding: 0 30px 30px; }
        .salesman-avatar {
            display: flex;
            width: 112px;
            height: 112px;
            margin-top: -56px;
            align-items: center;
            justify-content: center;
            border: 6px solid #fff;
            border-radius: 50%;
            background: #eaf7f9;
            box-shadow: 0 8px 22px rgba(23, 32, 51, .14);
            color: #218c9a;
        }
        .salesman-avatar svg { width: 62px; height: 62px; }
        .salesman-profile-heading { display: flex; align-items: flex-start; justify-content: space-between; gap: 20px; }
        .salesman-status {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 11px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
        }
        .salesman-status::before { width: 7px; height: 7px; border-radius: 50%; content: ''; }
        .salesman-status.active { background: #e8f8ef; color: #18864b; }
        .salesman-status.active::before { background: #20a860; }
        .salesman-status.inactive { background: #fff0f0; color: #c53d3d; }
        .salesman-status.inactive::before { background: #dc4c4c; }
        .salesman-info-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px;
            margin-top: 26px;
        }
        .salesman-info-item {
            display: flex;
            min-height: 84px;
            gap: 13px;
            padding: 15px;
            border: 1px solid #e7edf3;
            border-radius: 10px;
            background: #fbfcfe;
        }
        .salesman-info-icon {
            display: flex;
            width: 38px;
            height: 38px;
            flex: 0 0 38px;
            align-items: center;
            justify-content: center;
            border-radius: 9px;
            background: #eaf7f9;
            color: #218c9a;
            font-size: 17px;
        }
        .salesman-info-label {
            margin-bottom: 4px;
            color: #7a8796;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .04em;
            text-transform: uppercase;
        }
        .salesman-info-value { color: #172033; font-size: 14px; font-weight: 600; word-break: break-word; }
        .salesman-info-value a { color: #172033; }
        .salesman-profile-actions { margin-top: 26px; padding-top: 22px; border-top: 1px solid #e7edf3; }
        @media (max-width: 767px) {
            .salesman-profile-cover { min-height: 125px; padding: 22px; }
            .salesman-profile-body { padding: 0 18px 22px; }
            .salesman-profile-heading { display: block; }
            .salesman-profile-heading .salesman-status { margin-top: 14px; }
            .salesman-info-grid { grid-template-columns: 1fr; }
        }
    </style>

    <div class="row justify-content-center">
        <div class="col-xl-9 col-lg-10">
            <div class="card-box salesman-profile-card mt-4 p-0">
                <div class="salesman-profile-cover">
                    <h4 class="text-white mb-1">LSP Profile</h4>
                    <p class="mb-0">Area Office team member information and assignment details</p>
                </div>

                <div class="salesman-profile-body">
                    <div class="salesman-profile-heading">
                        <div>
                            <div class="salesman-avatar" aria-label="Default user avatar">
                                <svg viewBox="0 0 64 64" role="img" aria-hidden="true" fill="none">
                                    <circle cx="32" cy="21" r="12" fill="currentColor" opacity=".92"/>
                                    <path d="M12 55c1.4-12 9.3-19 20-19s18.6 7 20 19" fill="currentColor" opacity=".92"/>
                                </svg>
                            </div>
                            <h3 class="mt-3 mb-1">{{ $salesman->name }}</h3>
                            <div class="text-muted">Area Office LSP <span class="mx-1">&bull;</span> ID #{{ $salesman->id }}</div>
                        </div>
                        <span class="salesman-status {{ $salesman->status ? 'active' : 'inactive' }}">
                            {{ $salesman->status ? 'Active' : 'Inactive' }}
                        </span>
                    </div>

                    <div class="salesman-info-grid">
                        <div class="salesman-info-item">
                            <div class="salesman-info-icon"><i class="fa fa-envelope"></i></div>
                            <div>
                                <div class="salesman-info-label">Email Address</div>
                                <div class="salesman-info-value"><a href="mailto:{{ $salesman->email }}">{{ $salesman->email }}</a></div>
                            </div>
                        </div>

                        <div class="salesman-info-item">
                            <div class="salesman-info-icon"><i class="fa fa-phone"></i></div>
                            <div>
                                <div class="salesman-info-label">Phone Number</div>
                                <div class="salesman-info-value">
                                    @if($salesman->phone)
                                        <a href="tel:{{ $salesman->phone }}">{{ $salesman->phone }}</a>
                                    @else
                                        Not provided
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="salesman-info-item">
                            <div class="salesman-info-icon"><i class="fa fa-building"></i></div>
                            <div>
                                <div class="salesman-info-label">Assigned Area Office</div>
                                <div class="salesman-info-value">
                                    @if($salesman->warehouse)
                                        <a href="{{ route('warehouses.show', $salesman->warehouse) }}">{{ $salesman->warehouse->name }}</a>
                                        @if($salesman->warehouse->code)
                                            <span class="text-muted">({{ $salesman->warehouse->code }})</span>
                                        @endif
                                    @else
                                        Not assigned
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="salesman-info-item">
                            <div class="salesman-info-icon"><i class="fa fa-map-marker"></i></div>
                            <div>
                                <div class="salesman-info-label">Address</div>
                                <div class="salesman-info-value">{{ $salesman->address ?: 'Not provided' }}</div>
                            </div>
                        </div>

                        <div class="salesman-info-item">
                            <div class="salesman-info-icon"><i class="fa fa-user"></i></div>
                            <div>
                                <div class="salesman-info-label">Username</div>
                                <div class="salesman-info-value">{{ $salesman->username ?: 'Not set' }}</div>
                            </div>
                        </div>

                        <div class="salesman-info-item">
                            <div class="salesman-info-icon"><i class="fa fa-calendar"></i></div>
                            <div>
                                <div class="salesman-info-label">Added On</div>
                                <div class="salesman-info-value">{{ $salesman->created_at?->format('d M Y, h:i A') ?: '-' }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="salesman-profile-actions d-flex justify-content-between flex-wrap">
                        <a href="{{ route('warehouse-salesmen.index', $salesman->warehouse_id ? ['warehouse_id' => $salesman->warehouse_id] : []) }}" class="btn btn-secondary mb-2">
                            <i class="fa fa-arrow-left mr-1"></i> Back to LSPs
                        </a>
                        <a href="{{ route('warehouse-salesmen.edit', $salesman) }}" class="btn btn-primary mb-2">
                            <i class="fa fa-pencil mr-1"></i> Edit LSP
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

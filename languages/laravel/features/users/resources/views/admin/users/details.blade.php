@extends('admin.layouts.app')
@section('content')
    <div class="nk-content-body">
        <div class="nk-block-head nk-block-head-sm">
            <div class="nk-block-between">
                <div class="nk-block-head-content">
                    <div class="nk-block-head-sub">
                        <a class="back-to" href="{{ route('admin.users') }}" style="cursor:pointer;">
                            <em class="icon ni ni-arrow-left"></em>
                            <span>Back</span>
                        </a>
                    </div>
                    <h3 class="nk-block-title page-title">User Details</h3>
                    <div class="nk-block-des">
                        <p>{{ $user->name }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="nk-block nk-block-lg mt-3">
            <div class="row g-gs">
                <div class="col-lg-6">
                    <div class="card card-bordered">
                        <div class="card-inner">
                            <h5 class="card-title">Profile</h5>
                            <div class="nk-data data-list">
                                <div class="data-item"><div class="data-col"><span class="data-label">Name</span><span class="data-value">{{ $user->name }}</span></div></div>
                                <div class="data-item"><div class="data-col"><span class="data-label">Email</span><span class="data-value">{{ $user->email ?? 'NA' }}</span></div></div>
                                <div class="data-item"><div class="data-col"><span class="data-label">Phone</span><span class="data-value">{{ $user->phone_number ?? 'NA' }}</span></div></div>
                                <div class="data-item"><div class="data-col"><span class="data-label">Role</span><span class="data-value">{{ optional($user->role)->name ?? 'NA' }}</span></div></div>
                                <div class="data-item"><div class="data-col"><span class="data-label">Status</span><span class="data-value">{{ $user->is_active ? 'Active' : 'Inactive' }}</span></div></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card card-bordered">
                        <div class="card-inner">
                            <h5 class="card-title">Tracking</h5>
                            <div class="nk-data data-list">
                                <div class="data-item"><div class="data-col"><span class="data-label">UTM Source</span><span class="data-value">{{ $user->utm_source ?? 'NA' }}</span></div></div>
                                <div class="data-item"><div class="data-col"><span class="data-label">Device ID</span><span class="data-value">{{ $user->device_id ?? 'NA' }}</span></div></div>
                                <div class="data-item"><div class="data-col"><span class="data-label">Created At</span><span class="data-value">{{ optional($user->created_at)->format('d-m-Y h:i A') ?? 'NA' }}</span></div></div>
                                <div class="data-item"><div class="data-col"><span class="data-label">Updated At</span><span class="data-value">{{ optional($user->updated_at)->format('d-m-Y h:i A') ?? 'NA' }}</span></div></div>
                            </div>
                            <h6 class="mt-4">Device Info</h6>
                            <pre style="max-height: 220px; overflow:auto;">{{ json_encode($user->device_info, JSON_PRETTY_PRINT) ?: '{}' }}</pre>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

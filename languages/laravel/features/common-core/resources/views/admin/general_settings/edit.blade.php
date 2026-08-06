@extends('admin.layouts.app')
@section('content')
    <div class="nk-content-wrap">
        <div class="nk-block-head nk-block-head-sm">
            <div class="nk-block-between">
                <div class="nk-block-head-content">
                    <h3 class="nk-block-title page-title">Edit General Setting</h3>
                    <div class="nk-block-des">
                        <p>Update general setting details</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="nk-block nk-block-lg mt-3">
            <div class="card card-bordered card-preview col-sm-8">
                <div class="card-inner">
                    <form method="POST" action="{{ url('admin/general_settings/edit/1') }}">
                        @csrf
                        <div class="row g-4">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label class="form-label" for="site_title">Site Title</label>
                                    <div class="form-control-wrap">
                                        <input type="text"
                                            class="form-control form-control-lg @error('site_title') is-invalid @enderror"
                                            name="site_title" id="site_title" placeholder="Application title"
                                            value="{{ old('site_title', $data->site_title) }}">
                                        @error('site_title')
                                            <span class="invalid-feedback">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label class="form-label" for="support_email">Support Email</label>
                                    <div class="form-control-wrap">
                                        <input type="email"
                                            class="form-control form-control-lg @error('support_email') is-invalid @enderror"
                                            name="support_email" id="support_email" placeholder="support@example.com"
                                            value="{{ old('support_email', $data->support_email) }}">
                                        @error('support_email')
                                            <span class="invalid-feedback">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label class="form-label" for="support_phone">Support Phone</label>
                                    <div class="form-control-wrap">
                                        <input type="text"
                                            class="form-control form-control-lg @error('support_phone') is-invalid @enderror"
                                            name="support_phone" id="support_phone" placeholder="+1 000 000 0000"
                                            value="{{ old('support_phone', $data->support_phone) }}">
                                        @error('support_phone')
                                            <span class="invalid-feedback">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label class="form-label" for="default_language">Default Language</label>
                                    <div class="form-control-wrap">
                                        <input type="text"
                                            class="form-control form-control-lg @error('default_language') is-invalid @enderror"
                                            name="default_language" id="default_language" placeholder="en"
                                            value="{{ old('default_language', $data->default_language) }}">
                                        @error('default_language')
                                            <span class="invalid-feedback">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label class="form-label" for="date_format">Date Format</label>
                                    <div class="form-control-wrap">
                                        <input type="text"
                                            class="form-control form-control-lg @error('date_format') is-invalid @enderror"
                                            name="date_format" id="date_format" placeholder="Y-m-d"
                                            value="{{ old('date_format', $data->date_format) }}">
                                        @error('date_format')
                                            <span class="invalid-feedback">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label class="form-label" for="time_format">Time Format</label>
                                    <div class="form-control-wrap">
                                        <input type="text"
                                            class="form-control form-control-lg @error('time_format') is-invalid @enderror"
                                            name="time_format" id="time_format" placeholder="H:i"
                                            value="{{ old('time_format', $data->time_format) }}">
                                        @error('time_format')
                                            <span class="invalid-feedback">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label class="form-label" for="items_per_page">Items Per Page</label>
                                    <div class="form-control-wrap">
                                        <input type="number"
                                            class="form-control form-control-lg @error('items_per_page') is-invalid @enderror"
                                            name="items_per_page" id="items_per_page" placeholder="15"
                                            value="{{ old('items_per_page', $data->items_per_page) }}">
                                        @error('items_per_page')
                                            <span class="invalid-feedback">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <button type="submit" class="btn btn-lg btn-primary">Update</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

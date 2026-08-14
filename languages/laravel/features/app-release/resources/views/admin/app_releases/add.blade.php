@extends('admin.layouts.app')
@section('content')
    <div class="nk-content-body">
        <div class="nk-block-head nk-block-head-sm">
            <div class="nk-block-between">
                <div class="nk-block-head-content">
                    <div class="nk-block-head-sub">
                        <a class="back-to" href="{{ route('admin.app_releases') }}" style="cursor:pointer;">
                            <em class="icon ni ni-arrow-left"></em>
                            <span>Back</span>
                        </a>
                    </div>
                    <h3 class="nk-block-title page-title">Add App Release</h3>
                    <div class="nk-block-des">
                        <p>Create a new version release for iOS or Android.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="nk-block nk-block-lg mt-3">
            <div class="card card-bordered card-preview col-sm-8">
                <div class="card-inner">
                    <form method="POST" action="{{ url('admin/app_releases/add') }}">
                        @csrf
                        <div class="row g-4">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label class="form-label" for="platform">Platform</label>
                                    <div class="form-control-wrap">
                                        <select class="form-select js-select2 @error('platform') is-invalid @enderror" data-ui="lg" data-search="on" name="platform" id="platform">
                                            <option value="select_platform" disabled {{ old('platform') ? '' : 'selected' }}>Select Platform</option>
                                            <option value="android" {{ old('platform') == 'android' ? 'selected' : '' }}>Android</option>
                                            <option value="ios" {{ old('platform') == 'ios' ? 'selected' : '' }}>iOS</option>
                                        </select>
                                        @error('platform')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label class="form-label" for="latest_version">Latest Version</label>
                                    <div class="form-control-wrap">
                                        <input type="text" class="form-control form-control-lg @error('latest_version') is-invalid @enderror" name="latest_version" id="latest_version" value="{{ old('latest_version') }}" placeholder="e.g. 1.0.0">
                                        @error('latest_version')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label class="form-label">Force Update</label>
                                    <div class="custom-control-md text-start custom-switch" style="margin-top: 2px;">
                                        <input type="checkbox" class="custom-control-input" id="is_force_update" name="is_force_update" value="1" {{ old('is_force_update') == 1 ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="is_force_update"></label>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label class="form-label" for="release_notes">Release Notes</label>
                                    <div class="form-control-wrap">
                                        <textarea class="form-control form-control-lg @error('release_notes') is-invalid @enderror" name="release_notes" id="release_notes" rows="4" placeholder="What's new in this version?">{{ old('release_notes') }}</textarea>
                                        @error('release_notes')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 mt-4">
                                <div class="form-group">
                                    <button type="submit" class="btn btn-lg btn-primary">Save</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

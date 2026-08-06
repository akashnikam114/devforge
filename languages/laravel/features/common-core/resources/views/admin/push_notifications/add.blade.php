@extends('admin.layouts.app')
@section('content')
    <div class="nk-content-wrap">
        <div class="nk-block-head nk-block-head-sm">
            <div class="nk-block-between">
                <div class="nk-block-head-content">
                    <div class="nk-block-head-sub">
                        <a class="back-to" href="{{ route('admin.notification') }}" style="cursor:pointer;">
                            <em class="icon ni ni-arrow-left"></em>
                            <span>Back</span>
                        </a>
                    </div>
                    <h3 class="nk-block-title page-title">Add Notification</h3>
                    <div class="nk-block-des">
                        <p>Create new notification</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="nk-block nk-block-lg mt-3">
            <div class="card card-bordered card-preview col-sm-8">
                <div class="card-inner">
                    <form method="POST" action="{{ url('admin/push_notifications/add') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="row g-4">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-label" for="image">Image</label>
                                    <div class="form-control-wrap">
                                        <input type="file" class="form-control form-control-lg @error('image') is-invalid @enderror"
                                            name="image" id="image" accept="image/*">
                                        <div class="form-note mt-1">Recommended size: 1024x512px (Max 2MB)</div>
                                        @error('image')
                                            <span class="invalid-feedback">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-label" for="title">Title</label>
                                    <div class="form-control-wrap">
                                        <input type="text" class="form-control form-control-lg @error('title') is-invalid @enderror"
                                            name="title" id="title" value="{{ old('title') }}" placeholder="Enter Title">
                                        @error('title')
                                            <span class="invalid-feedback">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-label" for="description">Description</label>
                                    <div class="form-control-wrap">
                                        <textarea class="form-control form-control-lg @error('description') is-invalid @enderror"
                                            name="description" id="description" rows="3" placeholder="Enter Description">{{ old('description') }}</textarea>
                                        @error('description')
                                            <span class="invalid-feedback">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
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

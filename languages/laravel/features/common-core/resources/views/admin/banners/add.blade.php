@extends('admin.layouts.app')
@section('content')
    <div class="nk-content-wrap">
        <div class="nk-block-head nk-block-head-sm">
            <div class="nk-block-between">
                <div class="nk-block-head-content">
                    <div class="nk-block-head-sub">
                        <a class="back-to" href="{{ route('admin.banners') }}" style="cursor:pointer;">
                            <em class="icon ni ni-arrow-left"></em>
                            <span>Back</span>
                        </a>
                    </div>
                    <h3 class="nk-block-title page-title">Add Banner</h3>
                    <div class="nk-block-des">
                        <p>Create new banner</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="nk-block nk-block-lg mt-3">
            <div class="card card-bordered card-preview col-sm-8">
                <div class="card-inner">
                    <form method="POST" action="{{ url('admin/banners/add') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="row g-4">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label class="form-label" for="image">Image</label>
                                    <div class="form-control-wrap">
                                        <input type="file"
                                            class="form-control form-control-lg @error('image') is-invalid @enderror"
                                            name="image" id="image" accept="image/*">
                                        <div class="form-note mt-1">Recommended size: 1920x600px (Max 2MB)</div>
                                        @error('image')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label class="form-label" for="link_url">Link URL (Optional)</label>
                                    <div class="form-control-wrap">
                                        <input type="url"
                                            class="form-control form-control-lg @error('link_url') is-invalid @enderror"
                                            name="link_url" id="link_url" value="{{ old('link_url') }}"
                                            placeholder="https://example.com">
                                        @error('link_url')
                                            <span class="invalid-feedback" role="alert">
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

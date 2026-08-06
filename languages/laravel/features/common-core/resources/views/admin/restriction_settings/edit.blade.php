@extends('admin.layouts.app')
@section('content')
    <div class="nk-content-wrap">
        <div class="nk-block-head nk-block-head-sm">
            <div class="nk-block-between">
                <div class="nk-block-head-content">
                    <div class="nk-block-head-sub">
                        <a class="back-to" href="{{ route('admin.restriction') }}" style="cursor:pointer;">
                            <em class="icon ni ni-arrow-left"></em>
                            <span>Back</span>
                        </a>
                    </div>
                    <h3 class="nk-block-title page-title">Edit Restriction Setting</h3>
                    <div class="nk-block-des">
                        <p>Update restriction details</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="nk-block nk-block-lg mt-3">
            <div class="card card-bordered card-preview col-sm-8">
                <div class="card-inner">
                    <form method="POST" action="{{ url('admin/restriction_settings/edit') }}/{{ $data->id }}" enctype="multipart/form-data">
                        @csrf
                        <div class="row g-4">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label class="form-label" for="restriction_name">Restriction Name</label>
                                    <div class="form-control-wrap">
                                        <input type="text" class="form-control form-control-lg @error('restriction_name') is-invalid @enderror"
                                            name="restriction_name" id="restriction_name" value="{{ old('restriction_name', $data->restriction_name) }}"
                                            Placeholder="Enter Restriction Name">
                                        @error('restriction_name')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label class="form-label">Restriction Status</label>
                                    <div class="custom-control custom-switch d-block">
                                        <input type="checkbox" class="custom-control-input @error('is_restriction_enabled') is-invalid @enderror" id="is_restriction_enabled" name="is_restriction_enabled"
                                            {{ old('is_restriction_enabled', $data->is_restriction_enabled) == 1 ? 'checked' : '' }} value="1"
                                            onclick="toggleSection('restrictionFields', this)">
                                        <label class="custom-control-label" for="is_restriction_enabled">Enable Restriction</label>
                                        @error('is_restriction_enabled')
                                            <span class="invalid-feedback d-block" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div id="restrictionFields" class="col-12" style="display: {{ old('is_restriction_enabled', $data->is_restriction_enabled) == 1 ? 'block' : 'none' }};">
                                <div class="row g-4">
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label class="form-label" for="image">Image</label>
                                            <div class="form-control-wrap">
                                                <input type="file" class="form-control form-control-lg @error('image') is-invalid @enderror" name="image" id="image" accept="image/*">
                                                <div class="form-note mt-1">Recommended size: Max 2MB. Format: JPG, PNG, WEBP.</div>
                                                @error('image')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                            <div class="form-control-wrap mt-3">
                                                <label class="form-label d-block">Preview Image:</label>
                                                <img id="image-preview" src="{{ ($data->image) ? asset('storage/' . $data->image) : asset('assets/admin/img/default-image.jpeg') }}"
                                                    alt="Image Preview" style="max-width: 100%; max-height: 130px; border-radius: 4px;">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label class="form-label" for="title">Title</label>
                                            <input type="text" class="form-control form-control-lg @error('title') is-invalid @enderror"
                                                name="title" id="title" value="{{ old('title', $data->title) }}" placeholder="e.g., App Under Maintenance">
                                            @error('title')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label class="form-label" for="sub_title">Description</label>
                                            <textarea class="form-control form-control-lg @error('sub_title') is-invalid @enderror"
                                                name="sub_title" id="sub_title" rows="3" Placeholder="Enter Description">{{ old('sub_title', $data->sub_title) }}</textarea>
                                            @error('sub_title')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input @error('is_button_enabled') is-invalid @enderror" id="is_button_enabled" name="is_button_enabled"
                                                    {{ old('is_button_enabled', $data->is_button_enabled) == 1 ? 'checked' : '' }} value="1"
                                                    onclick="toggleSection('buttonFields', this)">
                                                <label class="custom-control-label" for="is_button_enabled"><strong>Enable Action Button</strong></label>
                                                @error('is_button_enabled')
                                                    <span class="invalid-feedback d-block" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div id="buttonFields" class="col-12" style="display: {{ old('is_button_enabled', $data->is_button_enabled) == 1 ? 'block' : 'none' }};">
                                        <div class="row g-4">
                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <label class="form-label" for="url_label">Button Label</label>
                                                    <input type="text" class="form-control form-control-lg @error('url_label') is-invalid @enderror" name="url_label"
                                                        value="{{ old('url_label', $data->url_label) }}" placeholder="e.g., Contact Support">
                                                    @error('url_label')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <label class="form-label" for="redirection_url">Redirection URL</label>
                                                    <input type="url" class="form-control form-control-lg @error('redirection_url') is-invalid @enderror" name="redirection_url"
                                                        value="{{ old('redirection_url', $data->redirection_url) }}" placeholder="https://...">
                                                    @error('redirection_url')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
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

@section('scriptJs')
    <script>
        document.getElementById('image').addEventListener('change', function(event) {
            const input = event.target;
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('image-preview').setAttribute('src', e.target.result);
                };
                reader.readAsDataURL(input.files[0]);
            }
        });

        function toggleSection(id, toggle) {
            const element = document.getElementById(id);
            element.style.display = toggle.checked ? 'block' : 'none';
        }
    </script>
@endsection

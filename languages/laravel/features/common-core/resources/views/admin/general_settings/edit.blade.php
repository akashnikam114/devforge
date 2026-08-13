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
                                    <label class="form-label" for="default_language">Default Language</label>
                                    <div class="form-control-wrap">
                                        <select class="form-select js-select2 @error('default_language') is-invalid @enderror"
                                            name="default_language" id="default_language" data-ui="lg" data-search="on"
                                            data-placeholder="Select Language">
                                            @foreach ([
                                                'en' => 'English',
                                                'hi' => 'Hindi',
                                                'mr' => 'Marathi',
                                                'gu' => 'Gujarati',
                                                'ta' => 'Tamil',
                                                'te' => 'Telugu',
                                                'kn' => 'Kannada',
                                                'ml' => 'Malayalam',
                                                'bn' => 'Bengali',
                                            ] as $value => $label)
                                                <option value="{{ $value }}" {{ old('default_language', $data->default_language) === $value ? 'selected' : '' }}>
                                                    {{ $label }} ({{ $value }})
                                                </option>
                                            @endforeach
                                        </select>
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
                                        <select class="form-select js-select2 @error('date_format') is-invalid @enderror"
                                            name="date_format" id="date_format" data-ui="lg" data-search="off"
                                            data-placeholder="Select Date Format">
                                            @foreach ([
                                                'Y-m-d' => now()->format('Y-m-d'),
                                                'd-m-Y' => now()->format('d-m-Y'),
                                                'm/d/Y' => now()->format('m/d/Y'),
                                                'd/m/Y' => now()->format('d/m/Y'),
                                                'M d, Y' => now()->format('M d, Y'),
                                                'd M Y' => now()->format('d M Y'),
                                            ] as $value => $label)
                                                <option value="{{ $value }}" {{ old('date_format', $data->date_format) === $value ? 'selected' : '' }}>
                                                    {{ $label }} ({{ $value }})
                                                </option>
                                            @endforeach
                                        </select>
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
                                        <select class="form-select js-select2 @error('time_format') is-invalid @enderror"
                                            name="time_format" id="time_format" data-ui="lg" data-search="off"
                                            data-placeholder="Select Time Format">
                                            @foreach ([
                                                'H:i' => now()->format('H:i'),
                                                'h:i A' => now()->format('h:i A'),
                                                'H:i:s' => now()->format('H:i:s'),
                                                'h:i:s A' => now()->format('h:i:s A'),
                                            ] as $value => $label)
                                                <option value="{{ $value }}" {{ old('time_format', $data->time_format) === $value ? 'selected' : '' }}>
                                                    {{ $label }} ({{ $value }})
                                                </option>
                                            @endforeach
                                        </select>
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

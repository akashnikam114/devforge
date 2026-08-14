@extends('admin.layouts.app')
@section('content')
    <div class="nk-content-wrap">
        <div class="nk-block-head nk-block-head-sm">
            <div class="nk-block-between">
                <div class="nk-block-head-content">
                    <h3 class="nk-block-title page-title">Edit Business Setting</h3>
                    <div class="nk-block-des">
                        <p>Update business setting details</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="nk-block nk-block-lg mt-3">
            <div class="card card-bordered card-preview">
                <div class="card-inner">
                    <form method="POST" action="{{ url('admin/business_settings/update') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="row g-4">
                            @foreach ($data as $business)
                                @if($business->key === 'app_logo')
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label class="form-label" for="{{ $business->key }}">
                                                {{ ucwords(str_replace('_', ' ', $business->key)) }}
                                            </label>
                                            <div class="form-control-wrap">
                                                <input type="file"
                                                    class="form-control form-control-lg @error($business->key) is-invalid @enderror"
                                                    name="{{ $business->key }}" id="{{ $business->key }}"
                                                    accept="image/png,image/jpeg,image/jpg,image/webp,image/svg+xml">
                                                @error($business->key)
                                                    <span class="invalid-feedback">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                            <div class="form-control-wrap mt-3">
                                                <img id="app-logo-preview"
                                                    src="{{ $appSetting::getAssetUrl('app_logo', 'assets/admin/images/app-logo.png') }}"
                                                    alt="App Logo Preview" style="max-width: 100%; max-height: 60px;">
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach

                            @foreach ($data as $business)
                                @if(!in_array($business->value, ["true", "false"]) && strlen($business->value) <= 64 && !in_array($business->key, ['app_logo', 'privacy_policy', 'terms_and_conditions']))
                                    @php
                                        $selectOptions = [
                                            'currency_symbol' => [
                                                '₹' => 'INR - ₹',
                                                '$' => 'USD - $',
                                                '€' => 'EUR - €',
                                                '£' => 'GBP - £',
                                                'AED' => 'AED',
                                            ],
                                            'otp_provider' => [
                                                'None' => 'None',
                                                'Firebase' => 'Firebase',
                                                'Twilio' => 'Twilio',
                                                'Msg91' => 'MSG91',
                                                'Custom' => 'Custom',
                                            ],
                                        ];
                                    @endphp
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label class="form-label" for="{{ $business->key }}">
                                                {{ ucwords(str_replace('_', ' ', $business->key)) }}
                                            </label>
                                            <div class="form-control-wrap">
                                                @if(array_key_exists($business->key, $selectOptions))
                                                    <select class="form-select js-select2 @error($business->key) is-invalid @enderror"
                                                        name="{{ $business->key }}" id="{{ $business->key }}" data-ui="lg"
                                                        data-search="off" data-placeholder="Select {{ ucwords(str_replace('_', ' ', $business->key)) }}">
                                                        <option value="select_option" disabled {{ old($business->key, $business->value) ? '' : 'selected' }}>Select {{ ucwords(str_replace('_', ' ', $business->key)) }}</option>
                                                        @foreach ($selectOptions[$business->key] as $value => $label)
                                                            <option value="{{ $value }}" {{ old($business->key, $business->value) === $value ? 'selected' : '' }}>
                                                                {{ $label }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                @else
                                                    <input type="text"
                                                        class="form-control form-control-lg @error($business->key) is-invalid @enderror"
                                                        name="{{ $business->key }}" id="{{ $business->key }}"
                                                        value="{{ old($business->key, $business->value) }}"
                                                        placeholder="{{ ucwords(str_replace('_', ' ', $business->key)) }}">
                                                @endif
                                                @error($business->key)
                                                    <span class="invalid-feedback">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach

                            @foreach ($data as $business)
                                @php
                                    $isRichText = in_array($business->key, ['privacy_policy', 'terms_and_conditions']);
                                    $isLongText = strlen($business->value) > 64;
                                @endphp

                                @if($business->key !== 'app_logo' && !in_array($business->value, ["true", "false"]) && ($isRichText || $isLongText))
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label class="form-label" for="{{ $business->key }}">
                                                {{ ucwords(str_replace('_', ' ', $business->key)) }}
                                            </label>
                                            <div class="form-control-wrap">
                                                @if($isRichText)
                                                    <div class="quill-wrap @error($business->key) is-invalid-quill @enderror">
                                                        <div class="quill-basic" id="editor_{{ $business->key }}" data-placeholder="{{ ucwords(str_replace('_', ' ', $business->key)) }}">
                                                            {!! old($business->key, $business->value) !!}
                                                        </div>
                                                        <input type="hidden" name="{{ $business->key }}" id="input_editor_{{ $business->key }}" value="{{ old($business->key, $business->value) }}">
                                                    </div>
                                                    @error($business->key)
                                                        <span class="invalid-feedback d-block">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                @else
                                                    <textarea
                                                        class="form-control form-control-lg @error($business->key) is-invalid @enderror"
                                                        name="{{ $business->key }}" id="{{ $business->key }}"
                                                        style="height: 120px;" placeholder="{{ ucwords(str_replace('_', ' ', $business->key)) }}">{{ old($business->key, $business->value) }}</textarea>
                                                    @error($business->key)
                                                        <span class="invalid-feedback">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach

                            <div class="col-lg-12">
                                <div class="row g-3">
                                    @foreach ($data as $business)
                                        @if(in_array($business->value, ["true", "false"]))
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <div class="custom-control custom-switch">
                                                        <input type="hidden" name="{{ $business->key }}" value="false">
                                                        <input type="checkbox" class="custom-control-input"
                                                            name="{{ $business->key }}"
                                                            id="{{ $business->key }}" value="true"
                                                            {{ old($business->key, $business->value) === "true" ? 'checked' : '' }}>
                                                        <label class="custom-control-label" for="{{ $business->key }}">
                                                            <strong>{{ ucwords(str_replace('_', ' ', $business->key)) }}</strong>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>

                            <div class="col-12 mt-4">
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
    <script src="{{ asset('assets/admin/js/quill-handler.js') }}"></script>
    <script>
        const appLogoInput = document.getElementById('app_logo');
        if (appLogoInput) {
            appLogoInput.addEventListener('change', function(event) {
                const input = event.target;
                const preview = document.getElementById('app-logo-preview');

                if (preview && input.files && input.files[0]) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        preview.setAttribute('src', e.target.result);
                    };
                    reader.readAsDataURL(input.files[0]);
                }
            });
        }
    </script>
@endsection

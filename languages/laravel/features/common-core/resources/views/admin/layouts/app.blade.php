<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="{{ $appSetting::getBusinessInfo('app_name') }} - Modern application management platform.">
    <title>{{ $appSetting::getBusinessInfo('app_name') }} | Admin Panel</title>
    <link rel="shortcut icon" href="{{ asset('assets/admin/images/favicons/favicon.ico') }}" type="image/x-icon">
    <link rel="icon" href="{{ asset('assets/admin/images/favicons/favicon.ico') }}" type="image/x-icon">
    <link rel="stylesheet" href="{{ asset('assets/admin/css/dashlite.css?ver=3.3.0') }}">
    <link id="skin-default" rel="stylesheet" href="{{ asset('assets/admin/css/theme.css?ver=3.3.0') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/css/editors/quill.css?ver=3.3.0') }}">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.1/jquery.validate.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/additional-methods.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <style>
        :root {
            --app-primary: {{ config('app-ui.primary_color', '#049C9C') }};
            --app-secondary: {{ config('app-ui.secondary_color', '#037a7a') }};
            --app-primary-soft: color-mix(in srgb, var(--app-primary) 10%, white);
        }

        .btn-primary {
            background-color: var(--app-primary) !important;
            border-color: var(--app-primary) !important;
        }

        .text-primary,
        .text-primary-alt,
        .link-primary {
            color: var(--app-primary) !important;
        }

        a {
            transition: color 0.2s ease, background-color 0.2s ease, border-color 0.2s ease;
        }

        a:hover,
        .link-list a:hover,
        .link-list-menu a:hover {
            color: var(--app-primary) !important;
        }

        .bg-primary,
        .badge-primary,
        .badge-dot.bg-primary {
            background-color: var(--app-primary) !important;
        }

        .btn-outline-primary {
            color: var(--app-primary) !important;
            border-color: var(--app-primary) !important;
        }

        .btn-outline-primary:hover,
        .btn-outline-primary:focus {
            color: #ffffff !important;
            background-color: var(--app-primary) !important;
            border-color: var(--app-primary) !important;
        }

        .form-control:focus,
        .form-select:focus,
        .custom-select:focus {
            border-color: var(--app-primary) !important;
            box-shadow: 0 0 0 3px color-mix(in srgb, var(--app-primary) 12%, transparent) !important;
        }

        .custom-control-input:checked ~ .custom-control-label::before {
            background-color: var(--app-primary) !important;
            border-color: var(--app-primary) !important;
        }

        .back-to {
            color: #667692;
            transition: color 0.2s ease;
        }

        .back-to:hover {
            color: var(--app-primary);
        }

        .logo-link {
            display: flex;
            align-items: center;
            min-height: 64px;
        }

        .logo-img {
            max-height: 44px;
            width: auto;
            object-fit: contain;
        }

        .nk-header {
            border-bottom-color: color-mix(in srgb, var(--app-primary) 12%, #e5e9f2);
        }

        .nk-header .nk-nav-toggle:hover,
        .nk-header .nk-quick-nav-icon:hover,
        .nk-header .dropdown-toggle:hover,
        .nk-header .dark-switch:hover,
        .nk-header .dark-switch.active {
            color: var(--app-primary) !important;
        }

        .nk-header .nk-news-icon,
        .nk-header .user-status {
            color: var(--app-primary) !important;
        }

        .nk-header .user-avatar,
        .dropdown-menu .user-avatar {
            color: #ffffff !important;
            background: linear-gradient(135deg, var(--app-primary), var(--app-secondary)) !important;
        }

        .dropdown-menu .dropdown-inner {
            border-color: color-mix(in srgb, var(--app-primary) 10%, #e5e9f2);
        }

        .dropdown-menu .link-list a:hover,
        .dropdown-menu .link-list-menu a:hover,
        .dropdown-menu .link-list-menu a.active {
            color: var(--app-primary) !important;
            background-color: var(--app-primary-soft);
        }

        .dropdown-menu .link-list a:hover .icon,
        .dropdown-menu .link-list-menu a:hover .icon,
        .dropdown-menu .link-list-menu a.active .icon {
            color: var(--app-primary) !important;
        }

        .nk-sidebar {
            border-right-color: color-mix(in srgb, var(--app-primary) 13%, #e5e9f2);
        }

        .nk-sidebar .nk-menu-heading .overline-title {
            color: var(--app-primary) !important;
        }

        .nk-sidebar .nk-menu-link:hover,
        .nk-sidebar .nk-menu-item.active > .nk-menu-link,
        .nk-sidebar .nk-menu-item.current-menu > .nk-menu-link,
        .nk-sidebar .nk-menu-item.has-sub.active > .nk-menu-link {
            color: var(--app-primary) !important;
            background-color: var(--app-primary-soft);
        }

        .nk-sidebar .nk-menu-link:hover .nk-menu-icon,
        .nk-sidebar .nk-menu-item.active > .nk-menu-link .nk-menu-icon,
        .nk-sidebar .nk-menu-item.current-menu > .nk-menu-link .nk-menu-icon,
        .nk-sidebar .nk-menu-item.has-sub.active > .nk-menu-link .nk-menu-icon {
            color: var(--app-primary) !important;
        }

        .nk-sidebar .nk-menu-sub .nk-menu-link:hover,
        .nk-sidebar .nk-menu-sub .nk-menu-item.active > .nk-menu-link {
            color: var(--app-primary) !important;
        }

        .card {
            border-radius: 8px;
        }

        .dashboard-stat-card {
            border-radius: 8px;
            overflow: hidden;
            border-color: color-mix(in srgb, var(--app-primary) 13%, #dbdfea);
            box-shadow: 0 8px 24px rgba(31, 43, 58, 0.06);
        }

        .dashboard-stat-card .card-hint {
            color: var(--app-primary);
        }

        .page-item.active .page-link {
            color: #ffffff !important;
            background-color: var(--app-primary) !important;
            border-color: var(--app-primary) !important;
        }

        .page-link:hover,
        .page-link:focus {
            color: var(--app-primary) !important;
            background-color: var(--app-primary-soft);
            border-color: color-mix(in srgb, var(--app-primary) 24%, #dbdfea);
            box-shadow: none;
        }

        .alert-primary {
            color: color-mix(in srgb, var(--app-primary) 70%, #1f2b3a) !important;
            background-color: var(--app-primary-soft) !important;
            border-color: color-mix(in srgb, var(--app-primary) 22%, #dbdfea) !important;
        }

        .spinner-border.text-primary,
        .spinner-grow.text-primary {
            color: var(--app-primary) !important;
        }

        .select2-container--default .select2-selection--single:focus,
        .select2-container--default.select2-container--focus .select2-selection--single,
        .select2-container--default.select2-container--focus .select2-selection--multiple {
            border-color: var(--app-primary) !important;
            box-shadow: 0 0 0 3px color-mix(in srgb, var(--app-primary) 12%, transparent);
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered,
        .select2-container--default .select2-results__option {
            color: #364a63 !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__placeholder {
            color: #8094ae !important;
        }

        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: var(--app-primary) !important;
            color: #ffffff !important;
        }

        .select2-container--default .select2-results__option[aria-selected="true"] {
            background-color: var(--app-primary-soft) !important;
            color: #1f2b3a !important;
        }

        .select2-container--default .select2-results__option--highlighted[aria-selected="true"] {
            background-color: var(--app-primary) !important;
            color: #ffffff !important;
        }

        .dark-mode .select2-container--default .select2-selection--single .select2-selection__rendered,
        .dark-mode .select2-container--default .select2-results__option {
            color: #dfe9fe !important;
        }

        .dark-mode .select2-container--default .select2-results__option[aria-selected="true"] {
            background-color: color-mix(in srgb, var(--app-primary) 28%, #101924) !important;
            color: #ffffff !important;
        }

        .dark-mode .nk-header,
        .dark-mode .dropdown-menu .dropdown-inner {
            border-color: color-mix(in srgb, var(--app-primary) 24%, #203247);
        }

        .dark-mode .dropdown-menu .link-list a:hover,
        .dark-mode .dropdown-menu .link-list-menu a:hover,
        .dark-mode .dropdown-menu .link-list-menu a.active,
        .dark-mode .page-link:hover,
        .dark-mode .page-link:focus {
            background-color: color-mix(in srgb, var(--app-primary) 24%, #101924) !important;
            color: #ffffff !important;
        }

        .nk-tb-actions {
            display: flex;
            justify-content: flex-start;
            align-items: center;
        }

        .dtr-title {
            display: flex;
            justify-content: flex-start;
            align-items: center;
        }

        .dtr-data {
            display: flex;
            justify-content: flex-start;
            align-items: center;
        }

        table.dataTable td.dt-empty {
            text-align: center !important;
        }

        .nk-tb-actions .dropdown-menu {
            z-index: 1050 !important;
            position: absolute !important;
        }

        .table-responsive {
            overflow: visible !important;
        }

        .nk-tb-actions .dropdown {
            position: static !important;
        }

        div.dt-container div.dt-processing {
            box-shadow: none;
            margin-top: -20px !important;
        }

        #myTable_processing {
            display: none !important;
        }

        .nk-menu-sub .nk-menu-link {
            padding: 0.625rem 20px 0.625rem 20px;
            color: #6e82a5;
            font-family: 'Nunito', sans-serif;
            font-weight: 700;
            font-size: 15px;
            letter-spacing: 0.01em;
            text-transform: none;
            line-height: 1.25rem;
        }

        .has-sub.active > .nk-menu-link > .parent-badge {
            display: none;
        }

        .btn-primary {
            border: none !important;
        }

        #shimmer-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: #f6f7f8;
            background-image: linear-gradient(110deg,
                rgba(246, 247, 248, 0) 20%,
                rgba(237, 238, 241, 0.8) 50%,
                rgba(246, 247, 248, 0) 80%
            );
            background-repeat: no-repeat;
            background-size: 250% 250%;
            display: flex;
            z-index: 9999;
            animation: shimmer 2s infinite linear;
        }

        .dark-mode #shimmer-overlay {
            background: #1a1a2e;
            background-image: linear-gradient(110deg,
                rgba(26, 26, 46, 0) 20%,
                rgba(37, 37, 71, 0.8) 50%,
                rgba(26, 26, 46, 0) 80%
            );
        }

        @keyframes shimmer {
            0% { background-position: -150% -150%; }
            100% { background-position: 150% 150%; }
        }

        .is-invalid-quill .ql-toolbar,
        .is-invalid-quill .ql-container {
            border-color: #e85347 !important;
        }

        .invalid-feedback.d-block {
            display: block !important;
        }

        .quill-basic {
            height: 200px;
        }

        .ql-editor {
            font-size: 15px;
        }
    </style>
    @stack('styles')
</head>

<body class="nk-body bg-white has-sidebar no-touch nk-nio-theme" cz-shortcut-listen="true">
    <div id="shimmer-overlay"></div>

    <div class="nk-app-root">
        <div class="nk-main">
            @include('admin.layouts.flash')
            @include('admin.layouts.sidebar')
            <div class="nk-wrap">
                @include('admin.layouts.header')
                <div class="nk-content nk-content-fluid">
                    <div class="container-xl wide-lg">
                        @yield('content')
                    </div>
                </div>
                @include('admin.layouts.footer')
            </div>
        </div>
    </div>

    <!-- Change Password Modal -->
    <div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="changePasswordModalLabel">Change Password</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('admin.password.change') }}">
                        @csrf
                        <div class="row g-4">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label class="form-label" for="current_password">Current Password</label>
                                    <div class="form-control-wrap">
                                        <a href="#" class="form-icon form-icon-right passcode-switch lg"
                                            data-target="current_password">
                                            <em class="passcode-icon icon-show icon ni ni-eye-off"></em>
                                            <em class="passcode-icon icon-hide icon ni ni-eye"></em>
                                        </a>
                                        <input type="password" class="form-control form-control-lg lock-input @error('current_password') is-invalid @enderror"
                                            id="current_password" name="current_password" value="{{ old('current_password') }}" placeholder="Current Password">
                                        @error('current_password')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label class="form-label" for="new_password">New Password</label>
                                    <div class="form-control-wrap">
                                        <a href="#" class="form-icon form-icon-right passcode-switch lg"
                                            data-target="new_password">
                                            <em class="passcode-icon icon-show icon ni ni-eye-off"></em>
                                            <em class="passcode-icon icon-hide icon ni ni-eye"></em>
                                        </a>
                                        <input type="password" class="form-control form-control-lg lock-input @error('new_password') is-invalid @enderror"
                                            id="new_password" name="new_password" value="{{ old('new_password') }}" placeholder="New Password">
                                        @error('new_password')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label class="form-label" for="new_password_confirmation">Confirm New Password</label>
                                    <div class="form-control-wrap">
                                        <a href="#" class="form-icon form-icon-right passcode-switch lg"
                                            data-target="new_password_confirmation">
                                            <em class="passcode-icon icon-show icon ni ni-eye-off"></em>
                                            <em class="passcode-icon icon-hide icon ni ni-eye"></em>
                                        </a>
                                        <input type="password" class="form-control form-control-lg lock-input @error('new_password_confirmation') is-invalid @enderror"
                                            id="new_password_confirmation" name="new_password_confirmation" value="{{ old('new_password_confirmation') }}" placeholder="Confirm New Password">
                                        @error('new_password_confirmation')
                                            <span class="invalid-feedback" role="alert">
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

    <script src="{{ asset('assets/admin/js/bundle.js?ver=3.3.0') }}"></script>
    <script src="{{ asset('assets/admin/js/scripts.js?ver=3.3.0') }}"></script>
    <script src="{{ asset('assets/admin/js/libs/editors/quill.js?ver=3.3.0') }}"></script>
    <script src="{{ asset('assets/admin/js/editors.js?ver=3.3.0') }}"></script>

    <script type="text/javascript">
        $(window).on('load', function() {
            $('#shimmer-overlay').fadeOut('slow');
        });

        $(document).ready(function() {
            function initializeSelect2Fields($scope, options = {}) {
                if (typeof NioApp === 'undefined' || typeof NioApp.Select2 !== 'function') {
                    return;
                }

                const $selects = $scope.find('.js-select2');

                if ($selects.length === 0) {
                    return;
                }

                $selects.each(function() {
                    const $this = $(this);

                    if ($this.data('select2')) {
                        $this.select2('destroy');
                    }

                    const searchSetting = $this.data('search') === 'on' ? 0 : -1;
                    NioApp.Select2($this, {
                        ...options,
                        minimumResultsForSearch: searchSetting
                    });
                });
            }

            initializeSelect2Fields($(document));

            $('.modal').on('shown.bs.modal', function() {
                const $modal = $(this);
                initializeSelect2Fields($modal, { dropdownParent: $modal });
            });
        });

        $(document).ready(function () {
            $(document).on('init.dt', function (e, settings) {
                let api = new $.fn.dataTable.Api(settings);
                let searchInput = $(api.table().container()).find('.dt-search input, .dataTables_filter input');

                if (searchInput.length) {
                    searchInput.off('.DT keyup input change');

                    searchInput.on('keydown', function (e) {
                        if (e.key === 'Enter' || e.keyCode === 13) {
                            api.search(this.value).draw();
                        }
                    });

                    searchInput.on('input', function () {
                        if (this.value === '') {
                            api.search('').draw();
                        }
                    });
                }
            });
        });

        $(document).ready(function() {
            const $html = $('html');
            const $body = $('body');
            const $darkSwitch = $('.dark-switch');

            function setCookie(name, value, days) {
                let expires = "";
                if (days) {
                    let date = new Date();
                    date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
                    expires = "; expires=" + date.toUTCString();
                }
                document.cookie = name + "=" + (value || "") + expires + "; path=/; SameSite=Lax";
            }

            function getCookie(name) {
                let nameEQ = name + "=";
                let ca = document.cookie.split(';');
                for (let i = 0; i < ca.length; i++) {
                    let c = ca[i];
                    while (c.charAt(0) == ' ') c = c.substring(1, c.length);
                    if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
                }
                return null;
            }

            function applyTheme(isDark) {
                if (isDark === "true" || isDark === true) {
                    $html.addClass('dark-mode');
                    $body.addClass('dark-mode');
                    $darkSwitch.addClass('active');
                } else {
                    $html.removeClass('dark-mode');
                    $body.removeClass('dark-mode');
                    $darkSwitch.removeClass('active');
                }
            }

            const savedTheme = getCookie('darkMode');
            if (savedTheme !== null) {
                applyTheme(savedTheme);
            }

            $darkSwitch.off('click').on('click', function(e) {
                e.preventDefault();
                e.stopImmediatePropagation();

                const isDark = !$body.hasClass('dark-mode');
                applyTheme(isDark);
                setCookie('darkMode', isDark, 365);
            });
        });

        $(document).ajaxStart(function() {
            if (window.isAutoReload) return;

            let table = $('table');
            if(!table.length) return;

            table.find('tbody').css('opacity', '0');
            $('.loadingMessage').remove();
            table.before(`
                <div class="loadingMessage text-center my-3">
                    <span class="spinner-border text-primary" role="status"></span>
                    <h5 class="mt-2">Processing. Please Wait<span class="dots">.</span></h5>
                </div>
            `);

            let dots = 1;
            window.loadingInterval = setInterval(function() {
                dots = dots % 3 + 1;
                $('.loadingMessage .dots').text('.'.repeat(dots));
            }, 500);

            $('.loadingMessage').show();
        });

        $(document).ajaxStop(function() {
            if (window.isAutoReload) {
                window.isAutoReload = false;
                return;
            }

            clearInterval(window.loadingInterval);
            $('table tbody').css('opacity', '1').show();
            $('.loadingMessage').hide();
        });

        setInterval(function() {
            $('.sync-data').each(function() {
                const $currentTable = $(this);

                if ($.fn.DataTable.isDataTable($currentTable)) {
                    window.isAutoReload = true;
                    $currentTable.DataTable().ajax.reload(function() {
                        window.isAutoReload = false;
                        Swal.fire({
                            toast: true,
                            position: 'bottom-end',
                            icon: 'success',
                            title: 'Sync Completed',
                            showConfirmButton: false,
                            timer: 1500,
                            timerProgressBar: true,
                            onOpen: (toast) => {
                                toast.addEventListener('mouseenter', Swal.stopTimer);
                                toast.addEventListener('mouseleave', Swal.resumeTimer);
                            }
                        });
                    }, false);
                }
            });
        }, 10000);
    </script>

    @if (session('changePasswordModal'))
        <script>
            var modalEl = document.getElementById('changePasswordModal');
            if (modalEl) {
                var cpModal = new bootstrap.Modal(modalEl);
                cpModal.show();
            }
        </script>
    @endif
    @yield('scriptJs')
</body>
</html>

<div class="nk-sidebar nk-sidebar-fixed" data-content="sidebarMenu">
    <div class="nk-sidebar-element nk-sidebar-head">
        <div class="nk-sidebar-brand">
            <a href="{{ route('admin.dashboard') }}" class="logo-link nk-sidebar-logo">
                <img class="logo-light logo-img" src="{{ asset('assets/admin/images/app-logo.png') }}" alt="{{ $appSetting::getBusinessInfo('app_name') }} logo">
                <img class="logo-dark logo-img" src="{{ asset('assets/admin/images/app-logo.png') }}" alt="{{ $appSetting::getBusinessInfo('app_name') }} logo">
            </a>
        </div>
        <div class="nk-menu-trigger me-n2">
            <a href="#" class="nk-nav-toggle nk-quick-nav-icon d-xl-none" data-target="sidebarMenu">
                <em class="icon ni ni-arrow-left"></em>
            </a>
        </div>
    </div>
    <div class="nk-sidebar-element">
        <div class="nk-sidebar-body" data-simplebar>
            <div class="nk-sidebar-content">
                <div class="nk-sidebar-menu">
                    <ul class="nk-menu">
                        <li class="nk-menu-heading">
                            <h6 class="overline-title text-primary-alt">Menu</h6>
                        </li>
                        <li class="nk-menu-item">
                            <a href="{{ route('admin.dashboard') }}" class="nk-menu-link">
                                <span class="nk-menu-icon"><em class="icon ni ni-dashboard"></em></span>
                                <span class="nk-menu-text">Dashboard</span>
                            </a>
                        </li>
                        <li class="nk-menu-item">
                            <a href="{{ route('admin.banners') }}" class="nk-menu-link">
                                <span class="nk-menu-icon"><em class="icon ni ni-img"></em></span>
                                <span class="nk-menu-text">Banner</span>
                            </a>
                        </li>
                        <li class="nk-menu-item has-sub">
                            <a href="#" class="nk-menu-link nk-menu-toggle">
                                <span class="nk-menu-icon"><em class="icon ni ni-mobile"></em></span>
                                <span class="nk-menu-text">Notification Center</span>
                            </a>
                            <ul class="nk-menu-sub">
                                <li class="nk-menu-item">
                                    <a href="{{ route('admin.notification') }}" class="nk-menu-link">
                                        <span class="nk-menu-icon"><em class="icon ni ni-bell"></em></span>
                                        <span class="nk-menu-text">Push Notification</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="nk-menu-item has-sub">
                            <a href="#" class="nk-menu-link nk-menu-toggle">
                                <span class="nk-menu-icon"><em class="icon ni ni-setting"></em></span>
                                <span class="nk-menu-text">System Settings</span>
                            </a>
                            <ul class="nk-menu-sub">
                                <li class="nk-menu-item">
                                    <a href="{{ route('admin.restriction_settings') }}" class="nk-menu-link">
                                        <span class="nk-menu-icon"><em class="icon ni ni-security"></em></span>
                                        <span class="nk-menu-text">Restriction Setting</span>
                                    </a>
                                </li>
                                <li class="nk-menu-item">
                                    <a href="{{ url('admin/general_settings/edit/1') }}" class="nk-menu-link">
                                        <span class="nk-menu-icon"><em class="icon ni ni-edit"></em></span>
                                        <span class="nk-menu-text">General Setting</span>
                                    </a>
                                </li>
                                <li class="nk-menu-item">
                                    <a href="{{ url('admin/business_settings/edit') }}" class="nk-menu-link">
                                        <span class="nk-menu-icon"><em class="icon ni ni-edit-alt"></em></span>
                                        <span class="nk-menu-text">Business Setting</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

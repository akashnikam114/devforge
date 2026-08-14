<div class="nk-header nk-header-fixed is-light">
    <div class="container-fluid">
        <div class="nk-header-wrap">
            <div class="nk-menu-trigger d-xl-none ms-n1">
                <a href="#" class="nk-nav-toggle nk-quick-nav-icon" data-target="sidebarMenu">
                    <em class="icon ni ni-menu"></em>
                </a>
            </div>
            <div class="nk-header-brand d-xl-none">
                <a href="{{ route('admin.dashboard') }}" class="logo-link">
                    <img class="logo-light logo-img" src="{{ $appSetting::getAssetUrl('app_logo', 'assets/admin/images/app-logo.png') }}" alt="logo">
                    <img class="logo-dark logo-img" src="{{ $appSetting::getAssetUrl('app_logo', 'assets/admin/images/app-logo.png') }}" alt="logo-dark">
                </a>
            </div>
            <div class="nk-header-news d-none d-xl-block">
                <div class="nk-news-list">
                    <a class="nk-news-item" href="javascript:void(0)">
                        <div class="nk-news-icon"><em class="icon ni ni-card-view"></em></div>
                        <div class="nk-news-text">
                            <p>
                                Welcome to the {{ $appSetting::getBusinessInfo('app_name') }} admin workspace.
                                <span> Manage operations, settings, and content from one place.</span>
                            </p>
                        </div>
                    </a>
                </div>
            </div>
            <div class="nk-header-tools">
                <ul class="nk-quick-nav">
                    <li class="dropdown user-dropdown">
                        <a href="#" class="dropdown-toggle" data-bs-toggle="dropdown">
                            <div class="user-toggle">
                                <div class="user-avatar sm"><em class="icon ni ni-user-alt"></em></div>
                                <div class="user-info d-none d-md-block">
                                    <div class="user-status">{{ Auth::user()?->role?->name ?? 'Administrator' }}</div>
                                    <div class="user-name dropdown-indicator">{{ Auth::user()?->name ?? 'Guest' }}</div>
                                </div>
                            </div>
                        </a>
                        <div class="dropdown-menu dropdown-menu-md dropdown-menu-end dropdown-menu-s1">
                            <div class="dropdown-inner user-card-wrap bg-lighter d-none d-md-block">
                                <div class="user-card">
                                    <div class="user-avatar">
                                        <span>{{ strtoupper(substr(Auth::user()?->name ?? 'GU', 0, 2)) }}</span>
                                    </div>
                                    <div class="user-info">
                                        <span class="lead-text">{{ Auth::user()?->name ?? 'Guest' }}</span>
                                        <span class="sub-text">{{ Auth::user()?->email ?? '' }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="dropdown-inner">
                                <ul class="link-list">
                                    <li>
                                        <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                                            <em class="icon ni ni-lock-alt"></em><span>Change Password</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#" class="dark-switch">
                                            <em class="icon ni ni-moon"></em><span>Dark Mode</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                            <div class="dropdown-inner">
                                <ul class="link-list">
                                    <li>
                                        <a href="javascript:void(0)" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                            <em class="icon ni ni-signout"></em><span>Sign out</span>
                                        </a>
                                        <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" style="display: none;">
                                            @csrf
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

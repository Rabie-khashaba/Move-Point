<!-- This file is no longer needed as the header is now integrated into the main layout -->
<!-- The header functionality has been moved to layouts/app.blade.php for better integration -->
<!--! ================================================================ !-->
<!--! [Start] Header !-->
<!--! ================================================================ !-->
<header class="nxl-header" dir="rtl">
    <div class="header-wrapper">
        <!--! [Start] Header Left !-->
        <div class="header-left d-flex align-items-center gap-4">
            <!--! [Start] nxl-head-mobile-toggler !-->
            <a href="javascript:void(0);" class="nxl-head-mobile-toggler" id="mobile-collapse">
                <div class="hamburger hamburger--arrowturn">
                    <div class="hamburger-box">
                        <div class="hamburger-inner"></div>
                    </div>
                </div>
            </a>
            <!--! [End] nxl-head-mobile-toggler !-->
            
            <!--! [Start] nxl-navigation-toggle !-->
            <div class="nxl-navigation-toggle">
                <a href="javascript:void(0);" id="menu-mini-button">
                    <i class="feather-align-right"></i> <!-- Changed to align right icon for RTL -->
                </a>
                <a href="javascript:void(0);" id="menu-expend-button" style="display: none">
                    <i class="feather-arrow-left"></i> <!-- Changed to left arrow for RTL -->
                </a>
            </div>
            <!--! [End] nxl-navigation-toggle !-->
            
            <!--! [Start] nxl-lavel-mega-menu-toggle !-->
            <div class="nxl-lavel-mega-menu-toggle d-flex d-lg-none">
                <a href="javascript:void(0);" id="nxl-lavel-mega-menu-open">
                    <i class="feather-align-right"></i> <!-- Changed to align right icon for RTL -->
                </a>
            </div>
            <!--! [End] nxl-lavel-mega-menu-toggle !-->
        </div>
        <!--! [End] Header Left !-->
        
        <!--! [Start] Header Right !-->
        <div class="header-right ms-auto">
            <div class="d-flex align-items-center">
                <!-- Search -->
                <div class="dropdown nxl-h-item nxl-header-search">
                    <a href="javascript:void(0);" class="nxl-head-link me-0" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                        <i class="feather-search"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end nxl-h-dropdown nxl-search-dropdown">
                        <div class="input-group search-form">
                            <span class="input-group-text">
                                <i class="feather-search fs-6 text-muted"></i>
                            </span>
                            <input type="text" class="form-control search-input-field" placeholder="ابحث...." /> <!-- Changed placeholder text -->
                            <span class="input-group-text">
                                <button type="button" class="btn-close"></button>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Full Screen -->
                <div class="nxl-h-item d-none d-sm-flex">
                    <div class="full-screen-switcher">
                        <a href="javascript:void(0);" class="nxl-head-link me-0" onclick="$('body').fullScreenHelper('toggle');">
                            <i class="feather-maximize maximize"></i>
                            <i class="feather-minimize minimize"></i>
                        </a>
                    </div>
                </div>
                
                <!-- Dark/Light Theme -->
                <div class="nxl-h-item dark-light-theme">
                    <a href="javascript:void(0);" class="nxl-head-link me-0 dark-button">
                        <i class="feather-moon"></i>
                    </a>
                    <a href="javascript:void(0);" class="nxl-head-link me-0 light-button" style="display: none">
                        <i class="feather-sun"></i>
                    </a>
                </div>

                <!-- Notifications -->
                @can('view_notifications')
                <div class="dropdown nxl-h-item nxl-header-notification">
                    <a href="javascript:void(0);" class="nxl-head-link me-0 position-relative" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                        <i class="feather-bell"></i>
                        <span class="notification-badge" id="header-notification-count" style="display: none;">0</span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end nxl-h-dropdown nxl-notification-dropdown">
                        <div class="dropdown-header d-flex justify-content-between align-items-center">
                            <h6 class="text-dark mb-0">الإشعارات</h6>
                            <div class="d-flex gap-2">
                                <button class="btn btn-sm btn-outline-primary" onclick="markAllAsRead()" title="تحديد الكل كمقروء">
                                    <i class="feather-check"></i>
                                </button>
                                <a href="{{ route('notifications.index') }}" class="btn btn-sm btn-outline-secondary" title="عرض الكل">
                                    <i class="feather-eye"></i>
                                </a>
                            </div>
                        </div>
                        <div class="dropdown-divider"></div>
                        <div id="notification-dropdown-list" class="notification-list">
                            <div class="text-center p-3">
                                <div class="spinner-border spinner-border-sm" role="status">
                                    <span class="visually-hidden">جاري التحميل...</span>
                                </div>
                                <p class="text-muted mt-2 mb-0">جاري تحميل الإشعارات...</p>
                            </div>
                        </div>
                        <div class="dropdown-divider"></div>
                        <div class="text-center p-2">
                            <a href="{{ route('notifications.index') }}" class="btn btn-sm btn-primary w-100">
                                عرض جميع الإشعارات
                            </a>
                        </div>
                    </div>
                </div>
                @endcan
                <div class="dropdown nxl-h-item">
                    <a href="javascript:void(0)" data-bs-toggle="dropdown" role="button" data-bs-auto-close="outside">
                        <img src="{{ asset('assets/images/user.png') }}" alt="صورة المستخدم" class="img-fluid user-avtar me-0" />
                    </a>
                    <div class="dropdown-menu dropdown-menu-end nxl-h-dropdown nxl-user-dropdown">
                        <div class="dropdown-header">
                            <div class="d-flex align-items-center">
                                <img src="{{ asset('assets/images/user.png') }}" alt="صورة المستخدم" class="img-fluid user-avtar" />
                                <div>
                                  <h6 class="text-dark mb-0 small">
                                    {{ Auth::user()->employee->name ?? Auth::user()->name ?? 'مدير النظام' }} 
                                    <span class="badge bg-soft-success text-success ms-1">
                                        {{ ucfirst(Auth::user()->getRoleNames()->first()) }} <!-- Display the first role -->
                                    </span>
                                </h6>


                                    
                                </div>
                            </div>
                        </div>
                        <div class="dropdown-divider"></div>
                        {{--
                        <a href="{{route('profile.show')}}" class="dropdown-item">
                            <i class="feather-user"></i>
                            <span>تفاصيل الملف الشخصي</span> <!-- Translated "Profile Details" -->
                        </a>
                        --}}
                        {{--
                        <a href="javascript:void(0);" class="dropdown-item">
                            <i class="feather-activity"></i>
                            <span>سجل النشاط</span> <!-- Translated "Activity Feed" -->
                        </a>
                        --}}
                        {{--
                        <a href="{{ route('account.settings') }}" class="dropdown-item">
                            <i class="feather-settings"></i>
                            <span>إعدادات الحساب</span> <!-- Translated "Account Settings" -->
                        </a>
                        --}}
                        <div class="dropdown-divider"></div>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="dropdown-item">
                                <i class="feather-log-out"></i>
                                <span>تسجيل الخروج</span> <!-- Translated "Logout" -->
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!--! [End] Header Right !-->
    </div>
</header>
<!--! ================================================================ !-->
<!--! [End] Header !-->
<!--! ================================================================ !-->

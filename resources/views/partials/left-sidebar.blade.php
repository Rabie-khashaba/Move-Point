<!--! ================================================================ !-->
<!--! [Start] Navigation Menu !-->
<!--! ================================================================ !-->
<nav class="nxl-navigation" id="sidebar" dir="rtl">
    <div class="navbar-wrapper">
        <div class="m-header">
            <a href="{{ route('dashboard') }}" class="b-brand">
                <img src="{{ asset('assets/images/logo.webp') }}" alt="" class="logo logo-lg" />
                <img src="{{ asset('assets/images/logo.webp') }}" alt="" class="logo logo-sm" />
            </a>
        </div>
        <div class="navbar-content">
            <ul class="nxl-navbar">
                <li class="nxl-item nxl-caption">
                    <label>التنقل</label>
                </li>
                <!-- Permissions -->
                <!-- Dashboard  -->
                @can('view_dashboard')
                    <li
                        class="nxl-item nxl-hasmenu {{ request()->routeIs('dashboard') || request()->routeIs('dashboards.department7') ? 'active' : '' }}">
                        <a href="javascript:void(0);" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-airplay"></i></span>
                            <span class="nxl-mtext">لوحة التحكم</span>
                            <span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
                        </a>
                        <ul class="nxl-submenu"
                            style="{{ request()->routeIs('dashboard') || request()->routeIs('dashboards.department7') || request()->routeIs('dashboards.my') ? 'display:block;' : '' }}">
                            <li class="nxl-item">
                                <a href="{{ route('dashboard') }}"
                                    class="nxl-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                                    <span class="nxl-micon"><i class="feather-home"></i></span>
                                    <span class="nxl-mtext">اللوحة الأساسية</span>
                                </a>
                            </li>
                            @can('view_sales_dashboards')
                                <li class="nxl-item">
                                    <a href="{{ route('dashboards.department7') }}"
                                        class="nxl-link {{ request()->routeIs('dashboards.department7') ? 'active' : '' }}">
                                        <span class="nxl-micon"><i class="feather-grid"></i></span>
                                        <span class="nxl-mtext">المبيعات</span>
                                    </a>
                                </li>
                            @endcan

                            @can('view_sales_dashboards')
                                <li class="nxl-item">
                                    <a href="{{ route('dashboards.moderation') }}"
                                        class="nxl-link {{ request()->routeIs('dashboards.moderation') ? 'active' : '' }}">
                                        <span class="nxl-micon"><i class="feather-grid"></i></span>
                                        <span class="nxl-mtext">لوحة الموديراتور</span>
                                    </a>
                                </li>
                            @endcan
                            @if(auth()->user()->type == 'employee' && optional(auth()->user()->employee)->department_id == 7)
                                <li class="nxl-item">
                                    <a href="{{ route('dashboards.my') }}"
                                        class="nxl-link {{ request()->routeIs('dashboards.my') ? 'active' : '' }}">
                                        <span class="nxl-micon"><i class="feather-user"></i></span>
                                        <span class="nxl-mtext">لوحتي</span>
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </li>
                @endcan


                @can('view_leads')
                    <li class="nxl-item">
                        <a href="{{ route('leads.create') }}"
                            class="nxl-link {{ request()->routeIs('leads.create') ? 'active' : '' }}">
                            <span class="nxl-micon"><i class="feather-alert-circle"></i></span>
                            <span class="nxl-mtext">اضافه عميل محتمل</span>
                        </a>
                    </li>
                @endcan
                <!-- Leads -->
                @can('view_leads')
                    <li class="nxl-item">
                        <a href="{{ route('leads.index') }}"
                            class="nxl-link {{ request()->routeIs('leads.*') ? 'active' : '' }}">
                            <span class="nxl-micon"><i class="feather-alert-circle"></i></span>
                            <span class="nxl-mtext">العملاء المحتملين</span>
                        </a>
                    </li>
                    <li class="nxl-item">
                        <a href="{{ route('leads.waiting') }}"
                            class="nxl-link {{ request()->routeIs('leads.waiting') ? 'active' : '' }}">
                            <span class="nxl-micon"><i class="feather-clock"></i></span>
                            <span class="nxl-mtext">قائمة انتظار العملاء</span>
                        </a>
                    </li>
                    <li class="nxl-item">
                        <a href="{{ route('leads.search') }}"
                            class="nxl-link {{ request()->routeIs('leads.search') ? 'active' : '' }}">
                            <span class="nxl-micon"><i class="feather-search"></i></span>
                            <span class="nxl-mtext">استعلام</span>
                        </a>
                    </li>
                @endcan
                <!-- Interviews -->
                @can('view_interviews')
                    <li class="nxl-item">
                        <a href="{{ route('interviews.index') }}"
                            class="nxl-link {{ request()->routeIs('interviews.*') ? 'active' : '' }}">
                            <span class="nxl-micon"><i class="feather-calendar"></i></span>
                            <span class="nxl-mtext">المقابلات</span>
                        </a>
                    </li>
                @endcan


                @can('view_trainings')
                    <li class="nxl-item">
                        <a href="{{ route('trainings.index') }}"
                            class="nxl-link {{ request()->routeIs('trainings.*') ? 'active' : '' }}">
                            <span class="nxl-micon"><i class="feather-check-circle"></i></span>
                            <span class="nxl-mtext">تدريبات المندوبين</span>
                        </a>
                    </li>
                @endcan

                <!--represtive-->
                @can('view_representatives')
                    <li class="nxl-item">
                        <a href="{{ route('representatives-not-completed.index') }}"
                            class="nxl-link {{ request()->routeIs('representativesNotCompleted.*') ? 'active' : '' }}">
                            <span class="nxl-micon"><i class="feather-users"></i></span>
                            <span class="nxl-mtext">المندوبين غير مكتملين</span>
                        </a>
                    </li>
                @endcan

                @can('view_representatives')
                    <li class="nxl-item">
                        <a href="{{ route('training_sessions.index') }}"
                            class="nxl-link {{ request()->routeIs('training_sessions.*') ? 'active' : '' }}">
                            <span class="nxl-micon"><i class="feather-users"></i></span>
                            <span class="nxl-mtext">محاضرات التدريب</span>
                        </a>
                    </li>
                @endcan

                @can('view_representatives')
                    <li class="nxl-item">
                        <a href="{{ route('waiting-representatives.index') }}"
                            class="nxl-link {{ request()->routeIs('waiting-representatives.*') ? 'active' : '' }}">
                            <span class="nxl-micon"><i class="feather-users"></i></span>
                            <span class="nxl-mtext">المندوبين المنتظرين</span>
                        </a>
                    </li>
                @endcan


                @can('view_representatives')
                    <li class="nxl-item">
                        <a href="{{ route('work_starts.index') }}"
                            class="nxl-link {{ request()->routeIs('work_starts.*') ? 'active' : '' }}">
                            <span class="nxl-micon"><i class="feather-users"></i></span>
                            <span class="nxl-mtext">بدء العمل</span>
                        </a>
                    </li>
                @endcan


                <!--represtive-->
                @can('view_representatives')
                    <li class="nxl-item">
                        <a href="{{ route('representatives.index') }}"
                            class="nxl-link {{ request()->routeIs('representatives.*') ? 'active' : '' }}">
                            <span class="nxl-micon"><i class="feather-users"></i></span>
                            <span class="nxl-mtext">المندوبين الفعلين</span>
                        </a>
                    </li>
                @endcan

                <!--represtive-->
                @can('view_representatives')
                    <li class="nxl-item">
                        <a href="{{ route('resignation-representatives.index') }}"
                            class="nxl-link {{ request()->routeIs('resignation-representatives.*') ? 'active' : '' }}">
                            <span class="nxl-micon"><i class="feather-users"></i></span>
                            <span class="nxl-mtext">المندوبين المستقيلين </span>
                        </a>
                    </li>
                @endcan
                <!--supervisors-->
                @can('view_supervisors')
                    <li class="nxl-item">
                        <a href="{{ route('supervisors.index') }}"
                            class="nxl-link {{ request()->routeIs('supervisors.*') ? 'active' : '' }}">
                            <span class="nxl-micon"><i class="feather-users"></i></span>
                            <span class="nxl-mtext">المشرفين</span>
                        </a>
                    </li>
                    <li class="nxl-item">
                        <a href="{{ route('supervisor-transfer-logs.index') }}"
                            class="nxl-link {{ request()->routeIs('supervisor-transfer-logs.*') ? 'active' : '' }}">
                            <span class="nxl-micon"><i class="feather-activity"></i></span>
                            <span class="nxl-mtext">سجل نقل المندوبين</span>
                        </a>
                    </li>
                @endcan



                <!-- HR Management -->
                @canany(['view_leave_requests', 'view_advance_requests', 'view_delivery_deposits', 'view_employee_targets', 'view_representative_targets', 'view_salary_records', 'view_work_schedules'])
                    <li
                        class="nxl-item nxl-hasmenu {{ request()->routeIs('leave-requests.*') || request()->routeIs('advance-requests.*') || request()->routeIs('delivery-deposits.*') || request()->routeIs('employee-targets.*') || request()->routeIs('representative-targets.*') || request()->routeIs('salary-records.*') || request()->routeIs('salary-components.*') || request()->routeIs('work-schedules.*') ? 'active' : '' }}">
                        <a href="javascript:void(0);" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-briefcase"></i></span>
                            <span class="nxl-mtext">إدارة الموارد البشرية</span>
                            <span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
                        </a>
                        <ul class="nxl-submenu"
                            style="{{ request()->routeIs('leave-requests.*') || request()->routeIs('advance-requests.*') || request()->routeIs('delivery-deposits.*') || request()->routeIs('employee-targets.*') || request()->routeIs('representative-targets.*') || request()->routeIs('salary-records.*') || request()->routeIs('salary-components.*') || request()->routeIs('work-schedules.*') ? 'display:block;' : '' }}">

                            <!-- Employees -->
                            @can('view_employees')
                                <li class="nxl-item">
                                    <a href="{{ route('employees.index') }}"
                                        class="nxl-link {{ request()->routeIs('employees.*') ? 'active' : '' }}">
                                        <span class="nxl-micon"><i class="feather-users"></i></span>
                                        <span class="nxl-mtext">الموظفون</span>
                                    </a>
                                </li>
                            @endcan
                            @can('view_leave_requests')
                                <li class="nxl-item">
                                    <a href="{{ route('leave-requests.index') }}"
                                        class="nxl-link {{ request()->routeIs('leave-requests.*') ? 'active' : '' }}">
                                        <span class="nxl-micon"><i class="feather-calendar"></i></span>
                                        <span class="nxl-mtext">طلبات الإجازة</span>
                                        <span class="notification-badge" id="leave-requests-count"
                                            style="display: none;">0</span>
                                    </a>
                                </li>
                            @endcan

                            @can('view_work_schedules')
                                <li class="nxl-item">
                                    <a href="{{ route('work-schedules.index') }}"
                                        class="nxl-link {{ request()->routeIs('work-schedules.*') ? 'active' : '' }}">
                                        <span class="nxl-micon"><i class="feather-clock"></i></span>
                                        <span class="nxl-mtext">مواعيد العمل</span>
                                    </a>
                                </li>
                            @endcan

                            @can('view_employee_targets')
                                <li class="nxl-item">
                                    <a href="{{ route('employee-targets.index') }}"
                                        class="nxl-link {{ request()->routeIs('employee-targets.*') ? 'active' : '' }}">
                                        <span class="nxl-micon"><i class="feather-target"></i></span>
                                        <span class="nxl-mtext">مرتب الموظفين</span>
                                    </a>
                                </li>
                            @endcan

                            @can('view_representative_targets')
                                <li class="nxl-item">
                                    <a href="{{ route('representative-targets.index') }}"
                                        class="nxl-link {{ request()->routeIs('representative-targets.*') ? 'active' : '' }}">
                                        <span class="nxl-micon"><i class="feather-users"></i></span>
                                        <span class="nxl-mtext">تحديد تارجت الموظفين</span>
                                    </a>
                                </li>
                            @endcan

                            {{-- @can('view_salary_records')
                            <li class="nxl-item">
                                <a href="{{ route('salary-records.index') }}"
                                    class="nxl-link {{ request()->routeIs('salary-records.*') ? 'active' : '' }}">
                                    <span class="nxl-micon"><i class="feather-dollar-sign"></i></span>
                                    <span class="nxl-mtext">المرتبات</span>
                                </a>
                            </li>
                            @endcan --}}

                            @can('view_salary_records')
                                <li class="nxl-item">
                                    <a href="{{ route('salary-record1.index') }}"
                                        class="nxl-link {{ request()->routeIs('salary-records.*') ? 'active' : '' }}">
                                        <span class="nxl-micon"><i class="feather-dollar-sign"></i></span>
                                        <span class="nxl-mtext">المرتبات</span>
                                    </a>
                                </li>
                            @endcan
                            @can('view_salary_records')
                                <li class="nxl-item">
                                    <a href="{{ route('salary-components.index') }}"
                                        class="nxl-link {{ request()->routeIs('salary-components.*') ? 'active' : '' }}">
                                        <span class="nxl-micon"><i class="feather-pie-chart"></i></span>
                                        <span class="nxl-mtext">الحصومات و المكافآت</span>
                                    </a>
                                </li>
                            @endcan

                            @can('view_advance_requests')
                                <li class="nxl-item">
                                    <a href="{{ route('advance-requests.index') }}"
                                        class="nxl-link {{ request()->routeIs('advance-requests.*') ? 'active' : '' }}">
                                        <span class="nxl-micon"><i class="feather-credit-card"></i></span>
                                        <span class="nxl-mtext">طلبات السلف</span>
                                        <span class="notification-badge" id="advance-requests-count"
                                            style="display: none;">0</span>
                                    </a>
                                </li>
                            @endcan

                            @can('view_advance_requests')
                                <li class="nxl-item">
                                    <a href="{{ route('debts.index') }}"
                                        class="nxl-link {{ request()->routeIs('debts.*') ? 'active' : '' }}">
                                        <span class="nxl-micon"><i class="feather-pie-chart"></i></span>
                                        <span class="nxl-mtext">المديونات</span>
                                    </a>
                                </li>
                            @endcan



                            @can('view_delivery_deposits')
                                <li class="nxl-item">
                                    <a href="{{ route('delivery-deposits.index') }}"
                                        class="nxl-link {{ request()->routeIs('delivery-deposits.*') ? 'active' : '' }}">
                                        <span class="nxl-micon"><i class="feather-truck"></i></span>
                                        <span class="nxl-mtext">اصيلات الإيداع</span>
                                        <span class="notification-badge" id="delivery-deposits-count"
                                            style="display: none;">0</span>
                                    </a>
                                </li>
                            @endcan

                        </ul>
                    </li>
                @endcanany

                @can('view_resignation_requests')
                    <li class="nxl-item nxl-hasmenu {{ request()->routeIs('resignation-requests.*') ? 'active' : '' }}">
                        <a href="javascript:void(0);" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-user-minus"></i></span>
                            <span class="nxl-mtext">الاستقاله</span>
                            <span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
                        </a>
                        <ul class="nxl-submenu"
                            style="{{ request()->routeIs('resignation-requests.*') ? 'display:block;' : '' }}">
                            <li class="nxl-item">
                                <a href="{{ route('resignation-requests.index') }}"
                                    class="nxl-link {{ request()->routeIs('resignation-requests.index') ? 'active' : '' }}">
                                    <span class="nxl-micon"><i class="feather-user-minus"></i></span>
                                    <span class="nxl-mtext">طلبات الاستقالة</span>
                                    <span class="notification-badge" id="resignation-requests-count"
                                        style="display: none;">0</span>
                                </a>
                            </li>
                            <li class="nxl-item">
                                <a href="{{ route('resignation-requests.reports') }}"
                                    class="nxl-link {{ request()->routeIs('resignation-requests.reports') ? 'active' : '' }}">
                                    <span class="nxl-micon"><i class="feather-bar-chart-2"></i></span>
                                    <span class="nxl-mtext">تقرير طلبات الاستقالة</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endcan

                <!-- Password Management -->
                @can('view_passwords')
                    <li class="nxl-item nxl-hasmenu {{ request()->routeIs('passwords.*') ? 'active' : '' }}">
                        <a href="javascript:void(0);" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-lock"></i></span>
                            <span class="nxl-mtext">إدارة كلمات المرور</span>
                            <span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
                        </a>
                        <ul class="nxl-submenu" style="{{ request()->routeIs('passwords.*') ? 'display:block;' : '' }}">
                            <li class="nxl-item">
                                <a href="{{ route('passwords.index') }}"
                                    class="nxl-link {{ request()->routeIs('passwords.index') ? 'active' : '' }}">
                                    <span class="nxl-micon"><i class="feather-key"></i></span>
                                    <span class="nxl-mtext">كلمات المرور</span>
                                </a>
                            </li>

                        </ul>
                    </li>
                @endcan





                <!-- Expenses & Finance -->
                @canany(['view_expense_types', 'view_expenses', 'view_safes', 'view_revenue_reports'])
                    <li
                        class="nxl-item nxl-hasmenu {{ request()->routeIs('expense-types.*') || request()->routeIs('expenses.*') || request()->routeIs('safes.*') || request()->routeIs('reports.revenue') ? 'active' : '' }}">
                        <a href="javascript:void(0);" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-credit-card"></i></span>
                            <span class="nxl-mtext">المالية</span>
                            <span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
                        </a>
                        <ul class="nxl-submenu"
                            style="{{ request()->routeIs('expense-types.*') || request()->routeIs('expenses.*') || request()->routeIs('safes.*') || request()->routeIs('reports.revenue') ? 'display:block;' : '' }}">
                            @can('view_expense_types')
                                <li class="nxl-item">
                                    <a href="{{ route('expense-types.index') }}"
                                        class="nxl-link {{ request()->routeIs('expense-types.*') ? 'active' : '' }}">
                                        <span class="nxl-micon"><i class="feather-tag"></i></span>
                                        <span class="nxl-mtext">أنواع المصروفات</span>
                                    </a>
                                </li>
                            @endcan
                            @can('view_safes')
                                <li class="nxl-item">
                                    <a href="{{ route('safes.index') }}"
                                        class="nxl-link {{ request()->routeIs('safes.*') ? 'active' : '' }}">
                                        <span class="nxl-micon"><i class="feather-lock"></i></span>
                                        <span class="nxl-mtext">الخزن</span>
                                    </a>
                                </li>
                            @endcan
                            @can('view_expenses')
                                <li class="nxl-item">
                                    <a href="{{ route('expenses.index') }}"
                                        class="nxl-link {{ request()->routeIs('expenses.*') ? 'active' : '' }}">
                                        <span class="nxl-micon"><i class="feather-trending-down"></i></span>
                                        <span class="nxl-mtext">المصروفات</span>
                                    </a>
                                </li>
                            @endcan
                            @can('view_revenue_reports')
                                <li class="nxl-item">
                                    <a href="{{ route('reports.revenue') }}"
                                        class="nxl-link {{ request()->routeIs('reports.revenue') ? 'active' : '' }}">
                                        <span class="nxl-micon"><i class="feather-bar-chart-2"></i></span>
                                        <span class="nxl-mtext">تقرير الحركات المالية</span>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcanany


                <li class="nxl-item nxl-hasmenu {{ request()->routeIs('bank-accounts.*') || request()->routeIs('wallet-accounts.*') ? 'active' : '' }}">
                    <a href="javascript:void(0);" class="nxl-link">
                        <span class="nxl-micon"><i class="feather-credit-card"></i></span>
                        <span class="nxl-mtext">الحسابات البنكية</span>
                        <span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
                    </a>
                    <ul class="nxl-submenu" style="{{ request()->routeIs('bank-accounts.*') || request()->routeIs('wallet-accounts.*') ? 'display:block;' : '' }}">
                        <li class="nxl-item">
                            <a href="{{ route('bank-accounts.index') }}"
                                class="nxl-link {{ request()->routeIs('bank-accounts.*') ? 'active' : '' }}">
                                <span class="nxl-mtext">الحسابات البنكية</span>
                            </a>
                        </li>
                        <li class="nxl-item">
                            <a href="{{ route('wallet-accounts.index') }}"
                                class="nxl-link {{ request()->routeIs('wallet-accounts.*') ? 'active' : '' }}">
                                <span class="nxl-mtext">المحافظ الالكترونية</span>
                            </a>
                        </li>
                    </ul>
                </li>

                @can('view_settings')
                    <!-- Settings Menu -->
                    <li
                        class="nxl-item nxl-hasmenu {{ request()->routeIs('companies.*') || request()->routeIs('departments.*') || request()->routeIs('locations.*') || request()->routeIs('governorates.*') || request()->routeIs('sources.*') || request()->routeIs('reasons.*') || request()->routeIs('roles.*') ? 'active' : '' }}">
                        <a href="javascript:void(0);" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-settings"></i></span>
                            <span class="nxl-mtext">الإعدادات</span>
                            <span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
                        </a>
                        <ul class="nxl-submenu"
                            style="{{ request()->routeIs('companies.*') || request()->routeIs('departments.*') || request()->routeIs('locations.*') || request()->routeIs('governorates.*') || request()->routeIs('sources.*') || request()->routeIs('reasons.*') || request()->routeIs('roles.*') ? 'display:block;' : '' }}">

                            @can('view_companies')
                                <li class="nxl-item">
                                    <a href="{{ route('companies.index') }}"
                                        class="nxl-link {{ request()->routeIs('companies.*') ? 'active' : '' }}">
                                        <span class="nxl-micon"><i class="feather-briefcase"></i></span>
                                        <span class="nxl-mtext">الشركات</span>
                                    </a>
                                </li>
                            @endcan

                            @can('view_departments')
                                <li class="nxl-item">
                                    <a href="{{ route('departments.index') }}"
                                        class="nxl-link {{ request()->routeIs('departments.*') ? 'active' : '' }}">
                                        <span class="nxl-micon"><i class="feather-grid"></i></span>
                                        <span class="nxl-mtext">الأقسام</span>
                                    </a>
                                </li>
                            @endcan
                            @can('view_governorates')
                                <li class="nxl-item">
                                    <a href="{{ route('governorates.index') }}"
                                        class="nxl-link {{ request()->routeIs('governorates.*') ? 'active' : '' }}">
                                        <span class="nxl-micon"><i class="feather-map"></i></span>
                                        <span class="nxl-mtext">المحافظات</span>
                                    </a>
                                </li>
                            @endcan

                            @can('view_locations')
                                <li class="nxl-item">
                                    <a href="{{ route('locations.index') }}"
                                        class="nxl-link {{ request()->routeIs('locations.*') ? 'active' : '' }}">
                                        <span class="nxl-micon"><i class="feather-map-pin"></i></span>
                                        <span class="nxl-mtext">المناطق</span>
                                    </a>
                                </li>
                            @endcan


                            <li class="nxl-item">
                                <a href="{{ route('banks.index') }}"
                                    class="nxl-link {{ request()->routeIs('banks.*') ? 'active' : '' }}">
                                    <span class="nxl-micon"><i class="feather-credit-card"></i></span>
                                    <span class="nxl-mtext">البنوك</span>
                                </a>
                            </li>



                            @can('view_sources')
                                <li class="nxl-item">
                                    <a href="{{ route('sources.index') }}"
                                        class="nxl-link {{ request()->routeIs('sources.*') ? 'active' : '' }}">
                                        <span class="nxl-micon"><i class="feather-link"></i></span>
                                        <span class="nxl-mtext">المصادر</span>
                                    </a>
                                </li>
                            @endcan

                            @can('view_reasons')
                                <li class="nxl-item">
                                    <a href="{{ route('reasons.index') }}"
                                        class="nxl-link {{ request()->routeIs('reasons.*') ? 'active' : '' }}">
                                        <span class="nxl-micon"><i class="feather-clipboard"></i></span>
                                        <span class="nxl-mtext">الأسباب</span>
                                    </a>
                                </li>
                            @endcan

                            <li class="nxl-item">
                                <a href="{{ route('devices.index') }}"
                                    class="nxl-link {{ request()->routeIs('devices.*') ? 'active' : '' }}">
                                    <span class="nxl-micon"><i class="feather-clipboard"></i></span>
                                    <span class="nxl-mtext">الموبايل</span>
                                </a>
                            </li>


                            {{--@can('view_messages')
                            <li class="nxl-item">
                                <a href="{{ route('messages.index') }}"
                                    class="nxl-link {{ request()->routeIs('messages.*') ? 'active' : '' }}">
                                    <span class="nxl-micon"><i class="feather-message-square"></i></span>
                                    <span class="nxl-mtext">الرسائل</span>
                                </a>
                            </li>
                            @endcan

                            <!-- WhatsApp Messages -->
                            @can('view_whatsapp_messages')
                            <li class="nxl-item">
                                <a href="{{ route('whatsapp.messages.index') }}"
                                    class="nxl-link {{ request()->routeIs('whatsapp.messages.*') ? 'active' : '' }}">
                                    <span class="nxl-micon"><i class="fab fa-whatsapp"></i></span>
                                    <span class="nxl-mtext">رسائل الواتساب</span>
                                </a>
                            </li>
                            @endcan --}}
                            {{--
                            @can('view_whatsapp_logs')
                            <li class="nxl-item">
                                <a href="{{ route('whatsapp.logs.index') }}"
                                    class="nxl-link {{ request()->routeIs('whatsapp.logs.*') ? 'active' : '' }}">
                                    <span class="nxl-micon"><i class="fas fa-clipboard-list"></i></span>
                                    <span class="nxl-mtext">سجل الواتساب</span>
                                </a>
                            </li>
                            @endcan
                            --}}

                            @can('view_roles')
                                <li class="nxl-item">
                                    <a href="{{ route('roles.index') }}"
                                        class="nxl-link {{ request()->routeIs('roles.*') ? 'active' : '' }}">
                                        <span class="nxl-micon"><i class="feather-shield"></i></span>
                                        <span class="nxl-mtext">الأدوار</span>
                                    </a>
                                </li>
                            @endcan

                            @can('view_notifications')
                                <li class="nxl-item">
                                    <a href="{{ route('notifications.index') }}"
                                        class="nxl-link {{ request()->routeIs('notifications.*') ? 'active' : '' }}">
                                        <span class="nxl-micon"><i class="feather-bell"></i></span>
                                        <span class="nxl-mtext">إشعاراتي</span>
                                        <span class="notification-badge" id="all-notifications-count"
                                            style="display: none;">0</span>
                                    </a>
                                </li>
                            @endcan


                            @can('view_admins')
                                <li class="nxl-item">
                                    <a href="{{ route('admins.index') }}"
                                        class="nxl-link {{ request()->routeIs('admins.*') ? 'active' : '' }}">
                                        <span class="nxl-micon"><i class="feather-shield"></i></span>
                                        <span class="nxl-mtext">المسؤولون</span>
                                    </a>
                                </li>
                            @endcan

                            <li class="nxl-item">
                                <a href="{{ route('advertisers.index') }}"
                                    class="nxl-link {{ request()->routeIs('advertisers.*') ? 'active' : '' }}">
                                    <span class="nxl-micon"><i class="feather-shield"></i></span>
                                    <span class="nxl-mtext">المعلنين</span>
                                </a>
                            </li>

                            @can('view_sliders')
                                <li class="nxl-item">
                                    <a href="{{ route('sliders.index') }}"
                                        class="nxl-link {{ request()->routeIs('sliders.*') ? 'active' : '' }}">
                                        <span class="nxl-micon"><i class="feather-image"></i></span>
                                        <span class="nxl-mtext">السلايدر</span>
                                    </a>
                                </li>
                            @endcan

                        </ul>
                    </li>
                @endcan

                @can('view_settings')
                    <!-- Settings Menu -->
                    <li
                        class="nxl-item nxl-hasmenu {{ request()->routeIs('companies.*') || request()->routeIs('departments.*') || request()->routeIs('locations.*') || request()->routeIs('governorates.*') || request()->routeIs('sources.*') || request()->routeIs('reasons.*') || request()->routeIs('roles.*') ? 'active' : '' }}">
                        <a href="javascript:void(0);" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-message-square"></i></span>
                            <span class="nxl-mtext">الرسائل</span>
                            <span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
                        </a>
                        <ul class="nxl-submenu"
                            style="{{ request()->routeIs('companies.*') || request()->routeIs('departments.*') || request()->routeIs('locations.*') || request()->routeIs('governorates.*') || request()->routeIs('sources.*') || request()->routeIs('reasons.*') || request()->routeIs('roles.*') ? 'display:block;' : '' }}">


                            <!-- WhatsApp Messages -->
                            @can('view_whatsapp_messages')
                                <li class="nxl-item">
                                    <a href="{{ route('whatsapp.messages.index') }}"
                                        class="nxl-link {{ request()->routeIs('whatsapp.messages.*') ? 'active' : '' }}">
                                        <span class="nxl-micon"><i class="fab fa-whatsapp"></i></span>
                                        <span class="nxl-mtext">رسائل الواتساب</span>
                                    </a>
                                </li>
                            @endcan

                            @can('view_messages')
                                <li class="nxl-item">
                                    <a href="{{ route('messages.index') }}"
                                        class="nxl-link {{ request()->routeIs('messages.*') ? 'active' : '' }}">
                                        <span class="nxl-micon"><i class="feather-message-square"></i></span>
                                        <span class="nxl-mtext">رسائل المقابلات</span>
                                    </a>
                                </li>
                            @endcan

                            {{--
                            @can('view_whatsapp_logs')
                            <li class="nxl-item">
                                <a href="{{ route('whatsapp.logs.index') }}"
                                    class="nxl-link {{ request()->routeIs('whatsapp.logs.*') ? 'active' : '' }}">
                                    <span class="nxl-micon"><i class="fas fa-clipboard-list"></i></span>
                                    <span class="nxl-mtext">سجل الواتساب</span>
                                </a>
                            </li>
                            @endcan
                            --}}

                            @can('view_messages')
                                <li class="nxl-item">
                                    <a href="{{ route('messagesTraining.index') }}"
                                        class="nxl-link {{ request()->routeIs('messagesTraining.*') ? 'active' : '' }}">
                                        <span class="nxl-micon"><i class="feather-message-square"></i></span>
                                        <span class="nxl-mtext">رسائل التدريب</span>
                                    </a>
                                </li>
                            @endcan
                            @can('view_messages')
                                <li class="nxl-item">
                                    <a href="{{ route('messagesWorking.index') }}"
                                        class="nxl-link {{ request()->routeIs('messagesWorking.*') ? 'active' : '' }}">
                                        <span class="nxl-micon"><i class="feather-message-square"></i></span>
                                        <span class="nxl-mtext">رسائل المخزن</span>
                                    </a>
                                </li>
                            @endcan

                        </ul>

                    <li class="nxl-item">
                        <a href="{{ route('supports.index') }}"
                            class="nxl-link {{ request()->routeIs('supports.*') ? 'active' : '' }}">
                            <span class="nxl-micon"><i class="feather-users"></i></span>
                            <span class="nxl-mtext"> الدعم </span>
                        </a>
                    </li>


                    </li>
                @endcan

            </ul>

        </div>
    </div>
</nav>
<!--! ================================================================ !-->
<!--! [End] Navigation Menu !-->
<!--! ================================================================ !-->

@extends('layouts.app')

@section('title', 'إدارة العملاء المحتملين')

@section('content')
    <!-- [ page-header ] start -->
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
                <li class="breadcrumb-item">العملاء المحتملين</li>
            </ul>
        </div>
        <div class="page-header-right ms-auto">
            <div class="page-header-right-items">
                <div class="d-flex d-md-none">
                    <a href="javascript:void(0)" class="page-header-right-close-toggle">
                        <i class="feather-arrow-left me-2"></i>
                        <span>رجوع</span>
                    </a>
                </div>
                <div class="d-flex align-items-center gap-2 page-header-right-items-wrapper">
                    <a href="javascript:void(0);" class="btn btn-icon btn-light-brand" data-bs-toggle="collapse" data-bs-target="#collapseOne">
                        <i class="feather-bar-chart"></i>
                    </a>

                    <div class="dropdown">
                        <a class="btn btn-icon btn-light-brand" data-bs-toggle="dropdown" data-bs-offset="0, 10" data-bs-auto-close="outside">
                            <i class="feather-filter"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end">
                            @foreach([
                                'جديد' => 'text-black',
                                'متابعة' => 'text-blue',
                                'غير مهتم' => 'text-red',
                                'مقابلة' => 'text-black',
                                'لم يرد' => 'text-warning',
                                'قديم' => 'text-secondary'
                            ] as $status => $color)
                                <a href="javascript:void(0);" class="dropdown-item" onclick="filterByStatus('{{ $status }}')">
                                    <span class="wd-7 ht-7 bg-{{ $color == 'text-blue' ? 'primary' : ($color == 'text-red' ? 'danger' : ($color == 'text-info' ? 'info' : ($color == 'text-warning' ? 'warning' : ($color == 'text-success' ? 'success' : ($color == 'text-secondary' ? 'secondary' : 'dark'))))) }} rounded-circle d-inline-block me-3"></span>
                                    <span>{{ $status }}</span>
                                </a>
                            @endforeach
                            <a href="javascript:void(0);" class="dropdown-item" onclick="filterByStatus('all')">
                                <span class="wd-7 ht-7 bg-light rounded-circle d-inline-block me-3"></span>
                                <span>الكل</span>
                            </a>
                            <div class="dropdown-divider"></div>

                        </div>
                    </div>
                    <div class="dropdown">
                        <a class="btn btn-icon btn-light-brand" data-bs-toggle="dropdown" data-bs-offset="0, 10" data-bs-auto-close="outside">
                            <i class="feather-paperclip"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <a href="javascript:void(0);" class="dropdown-item">
                                <i class="bi bi-filetype-pdf me-3"></i>
                                <span>PDF</span>
                            </a>
                            <a href="javascript:void(0);" class="dropdown-item">
                                <i class="bi bi-filetype-csv me-3"></i>
                                <span>CSV</span>
                            </a>
                            <a href="javascript:void(0);" class="dropdown-item">
                                <i class="bi bi-filetype-xls me-3"></i>
                                <span>Excel</span>
                            </a>
                        </div>
                    </div>
                    @can('create_leads')
                    <a href="{{ route('leads.create') }}" class="btn btn-primary">
                        <i class="feather-plus me-2"></i>
                        <span>إضافة عميل محتمل</span>
                    </a>
                    @endcan
                </div>
            </div>
            <div class="d-md-none d-flex align-items-center">
                <a href="javascript:void(0)" class="page-header-right-open-toggle">
                    <i class="feather-align-right fs-20"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <!-- <div id="collapseOne" class="accordion-collapse show  collapse page-header-collapse mb-4">
        <div class="accordion-body pb-2">
            <div class="row g-3 justify-content-between flex-nowrap overflow-auto" style="white-space: nowrap;">


                <div class="col-xxl-2 col-md-6 mb-3" style="min-width: 12.5%;">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="avatar-text avatar-xl rounded">
                                        <i class="feather-users"></i>
                                    </div>
                                    <a href="javascript:void(0);" class="fw-bold d-block">
                                        <span class="d-block">إجمالي</span>
                                        <span class="fs-24 fw-bolder d-block" id="totalLeads">{{ $totalLeads }}</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xxl-2 col-md-6 mb-3" style="min-width: 12.5%;">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="avatar-text avatar-xl rounded bg-primary">
                                        <i class="feather-user-check"></i>
                                    </div>
                                    <a href="javascript:void(0);" class="fw-bold d-block text-blue">
                                        <span class="d-block">متابعة</span>
                                        <span class="fs-24 fw-bolder d-block" id="activeLeads">{{ $followUpLeads }}</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xxl-2 col-md-6 mb-3" style="min-width: 12.5%;">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="avatar-text avatar-xl rounded bg-info">
                                        <i class="feather-user-plus"></i>
                                    </div>
                                    <a href="javascript:void(0);" class="fw-bold d-block text-black">
                                        <span class="d-block">مقابلة</span>
                                        <span class="fs-24 fw-bolder d-block" id="qualifiedLeads">{{ $interviewLeads }}</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xxl-2 col-md-6 mb-3" style="min-width: 12.5%;">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="avatar-text avatar-xl rounded bg-danger">
                                        <i class="feather-user-minus"></i>
                                    </div>
                                    <a href="javascript:void(0);" class="fw-bold d-block text-red">
                                        <span class="d-block">غير مهتم</span>
                                        <span class="fs-24 fw-bolder d-block" id="inactiveLe ads">{{ $notInterestedLeads }}</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xxl-2 col-md-6 mb-3" style="min-width: 12.5%;">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="avatar-text avatar-xl rounded bg-warning">
                                        <i class="feather-user-plus"></i>
                                    </div>
                                    <a href="javascript:void(0);" class="fw-bold d-block text-black">
                                        <span class="d-block">لم  يرد</span>
                                        <span class="fs-24 fw-bolder d-block" id="qualifiedLeads">{{ $notRespondedLeads }}</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xxl-2 col-md-6 mb-3" style="min-width: 12.5%;">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="avatar-text avatar-xl rounded bg-success">
                                        <i class="feather-user-minus"></i>
                                    </div>
                                    <a href="javascript:void(0);" class="fw-bold d-block text-red">
                                        <span class="d-block">جديد</span>
                                        <span class="fs-24 fw-bolder d-block" id="inactiveLe ads">{{ $newLeads }}</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xxl-2 col-md-6 mb-3" style="min-width: 12.5%;">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="avatar-text avatar-xl rounded bg-success">
                                        <i class="feather-user-minus"></i>
                                    </div>
                                    <a href="javascript:void(0);" class="fw-bold d-block text-red">
                                        <span class="d-block">جديد</span>
                                        <span class="fs-24 fw-bolder d-block" id="inactiveLe ads">{{ $newLeads }}</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xxl-2 col-md-6 mb-3" style="min-width: 12.5%;">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="avatar-text avatar-xl rounded bg-success">
                                        <i class="feather-user-minus"></i>
                                    </div>
                                    <a href="javascript:void(0);" class="fw-bold d-block text-red">
                                        <span class="d-block">جديد</span>
                                        <span class="fs-24 fw-bolder d-block" id="inactiveLe ads">{{ $newLeads }}</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


            </div>
        </div>
    </div> -->


    <div id="collapseStats" class="accordion-collapse show collapse page-header-collapse mb-4">
        <div class="accordion-body pb-2">
            <label class="fw-bold mb-3 d-block">الإحصائيات:</label>

            <div class="d-flex flex-nowrap overflow-auto gap-3">

                {{-- إجمالي --}}
                <div class="card" style="min-width: 200px;">
                    <div class="card-body">
                        <div class="d-flex align-items-center gap-3">
                            <div class="avatar-text avatar-xl rounded">
                                <i class="feather-users"></i>
                            </div>
                            <div>
                                <span class="d-block fw-bold">إجمالي</span>
                                <span class="fs-24 fw-bolder">{{ $totalLeads }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- متابعة --}}
                <div class="card" style="min-width: 200px;">
                    <div class="card-body">
                        <div class="d-flex align-items-center gap-3">
                            <div class="avatar-text avatar-xl rounded bg-primary text-white">
                                <i class="feather-user-check"></i>
                            </div>
                            <div>
                                <span class="d-block fw-bold">متابعة</span>
                                <span class="fs-24 fw-bolder">{{ $followUpLeads }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- مقابلة --}}
                <div class="card" style="min-width: 200px;">
                    <div class="card-body">
                        <div class="d-flex align-items-center gap-3">
                            <div class="avatar-text avatar-xl rounded bg-info text-white">
                                <i class="feather-user-plus"></i>
                            </div>
                            <div>
                                <span class="d-block fw-bold">مقابلة</span>
                                <span class="fs-24 fw-bolder">{{ $interviewLeads }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- غير مهتم --}}
                <div class="card" style="min-width: 200px;">
                    <div class="card-body">
                        <div class="d-flex align-items-center gap-3">
                            <div class="avatar-text avatar-xl rounded bg-danger text-white">
                                <i class="feather-user-minus"></i>
                            </div>
                            <div>
                                <span class="d-block fw-bold">غير مهتم</span>
                                <span class="fs-24 fw-bolder">{{ $notInterestedLeads }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- لم يرد --}}
                <div class="card" style="min-width: 200px;">
                    <div class="card-body">
                        <div class="d-flex align-items-center gap-3">
                            <div class="avatar-text avatar-xl rounded bg-warning text-white">
                                <i class="feather-phone-off"></i>
                            </div>
                            <div>
                                <span class="d-block fw-bold">لم يرد</span>
                                <span class="fs-24 fw-bolder">{{ $notRespondedLeads }}</span>
                            </div>
                        </div>
                    </div>
                </div>


                {{-- جديد --}}
                <div class="card" style="min-width: 200px;">
                    <div class="card-body">
                        <div class="d-flex align-items-center gap-3">
                            <div class="avatar-text avatar-xl rounded bg-success text-white">
                                <i class="feather-star"></i>
                            </div>

                            <div>
                                <span class="d-block fw-bold">جديد</span>
                                <span class="fs-24 fw-bolder">{{ $newLeads }}</span>
                            </div>
                        </div>
                    </div>
                </div>


                {{-- شفت مسائي --}}
                <div class="card" style="min-width: 200px;">
                    <div class="card-body">
                        <div class="d-flex align-items-center gap-3">
                            <div class="avatar-text avatar-xl rounded bg-secondary text-white">
                                <i class="feather-moon"></i>
                            </div>
                            <div>
                                <span class="d-block fw-bold">شفت مسائي</span>
                                <span class="fs-24 fw-bolder">{{ $nightShiftLeads }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- بدون وسيلة نقل --}}
                <div class="card" style="min-width: 200px;">
                    <div class="card-body">
                        <div class="d-flex align-items-center gap-3">
                            <div class="avatar-text avatar-xl rounded bg-dark text-white">
                                <i class="feather-truck"></i>
                            </div>
                            <div>
                                <span class="d-block fw-bold">بدون وسيلة نقل</span>
                                <span class="fs-24 fw-bolder">{{ $noTransportLeads }}</span>
                            </div>
                        </div>
                    </div>
                </div>


            </div>
        </div>
    </div>




    <!-- Filters Section -->
    <div class="filters-section">
        @if(request('date_from') || request('date_to') || request('status') || request('search') || request('transportation') || request('governorate_id') || request('location_id'))
            <div class="filter-summary">
                <strong>الفلاتر المطبقة:</strong>
                @if(request('search'))
                    <span class="badge">بحث: {{ request('search') }}</span>
                @endif
                @if(request('date_from') || request('date_to'))
                    <span class="badge">التاريخ: {{ request('date_from') ?? 'البداية' }} - {{ request('date_to') ?? 'النهاية' }}</span>
                @endif
                @if(request('status'))
                    <span class="badge">الحالة: {{ request('status') }}</span>
                @endif
                @if(request('transportation'))
                    <span class="badge">وسيلة النقل: {{ request('transportation') === '__none__' ? 'بدون وسيلة نقل' : request('transportation') }}</span>
                @endif
                @if(request('governorate_id'))
                    @php
                        $govName = optional($governorates->firstWhere('id', (int) request('governorate_id')))->name;
                    @endphp
                    <span class="badge">المحافظة: {{ $govName ?? request('governorate_id') }}</span>
                @endif
                @if(request('location_id'))
                    @php
                        $locName = optional($locations->firstWhere('id', (int) request('location_id')))->name;
                    @endphp
                    <span class="badge">المنطقة: {{ $locName ?? request('location_id') }}</span>
                @endif
            </div>
        @endif

        <div class="row g-3">
            <div class="col-md-3">
                <label class="form-label">بحث</label>
                <input type="text" class="form-control {{ request('search') ? 'filter-active' : '' }}" id="searchInput" placeholder="الاسم / الهاتف / الموظف / المحافظة" value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label">من تاريخ</label>
                <input type="date" class="form-control {{ request('date_from') ? 'filter-active' : '' }}" id="dateFrom" value="{{ request('date_from', now()->toDateString()) }}">
            </div>
            <div class="col-md-2">
                <label class="form-label">إلى تاريخ</label>
                <input type="date" class="form-control {{ request('date_to') ? 'filter-active' : '' }}" id="dateTo" value="{{ request('date_to', now()->toDateString()) }}">
            </div>
            <div class="col-md-2">
                <label class="form-label">الحالة</label>
                <select class="form-control {{ request('status') ? 'filter-active' : '' }}" id="statusFilter">
                    <option value="">جميع الحالات</option>
                    <option value="جديد" {{ request('status', 'جديد') == 'جديد' ? 'selected' : '' }}>جديد</option>
                    <option value="متابعة" {{ request('status') == 'متابعة' ? 'selected' : '' }}>متابعة</option>
                    <option value="لم يرد" {{ request('status') == 'لم يرد' ? 'selected' : '' }}>لم يرد</option>
                    <option value="غير مهتم" {{ request('status') == 'غير مهتم' ? 'selected' : '' }}>غير مهتم</option>
                    <option value="مقابلة" {{ request('status') == 'مقابلة' ? 'selected' : '' }}>مقابلة</option>
                    <option value="قديم" {{ request('status') == 'قديم' ? 'selected' : '' }}>قديم</option>
                    <option value="شفت مسائي" {{ request('status') == 'شفت مسائي' ? 'selected' : '' }}>شفت مسائي</option>
                    <option value="بدون وسيلة نقل" {{ request('status') == 'بدون وسيلة نقل' ? 'selected' : '' }}>بدون وسيلة نقل</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">المحافظة</label>
                <select class="form-control {{ request('governorate_id') ? 'filter-active' : '' }}" id="governorateFilter">
                    <option value="">كل المحافظات</option>
                    @foreach($governorates as $governorate)
                        <option value="{{ $governorate->id }}" {{ request('governorate_id') == $governorate->id ? 'selected' : '' }}>
                            {{ $governorate->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <!--<div class="col-md-2">-->
            <!--    <label class="form-label">المنطقة</label>-->
            <!--    <select class="form-control {{ request('location_id') ? 'filter-active' : '' }}" id="locationFilter">-->
            <!--        <option value="">كل المناطق</option>-->
            <!--        @foreach($locations as $location)-->
            <!--            <option value="{{ $location->id }}" {{ request('location_id') == $location->id ? 'selected' : '' }}>-->
            <!--                {{ $location->name }}-->
            <!--            </option>-->
            <!--        @endforeach-->
            <!--    </select>-->
            <!--</div>-->
            <div class="col-md-2">
                <label class="form-label">وسيلة النقل</label>
                <select class="form-control {{ request('transportation') ? 'filter-active' : '' }}" id="transportationFilter">
                    <option value="">كل الوسائل</option>
                    <option value="عربية" {{ request('transportation') == 'عربية' ? 'selected' : '' }}>عربية</option>
                    <option value="موتوسيكل" {{ request('transportation') == 'موتوسيكل' ? 'selected' : '' }}>موتوسيكل</option>
                    <option value="دبابه مفتوحه" {{ request('transportation') == 'دبابه مفتوحه' ? 'selected' : '' }}>دبابه مفتوحه</option>
                    <option value="دبابه مقفولة" {{ request('transportation') == 'دبابه مقفولة' ? 'selected' : '' }}>دبابه مقفولة</option>
                    <option value="__none__" {{ request('transportation') == '__none__' ? 'selected' : '' }}>بدون وسيلة نقل</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">الموظف المعين</label>
                <select name="assigned_to" class="form-control {{ request('assigned_to') ? 'filter-active' : '' }}" id="assignedToFilter">
                    <option value="">جميع الموظفين</option>
                    <option value="0">لم يحدد</option>
                    @foreach(\App\Models\Employee::get() as $employee)
                        <option value="{{ $employee->user_id }}">{{ $employee->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <div class="d-flex gap-2 w-100">
                    <button class="btn btn-primary flex-fill" onclick="applyFilters()">
                        <i class="feather-search me-1"></i>
                        تطبيق الفلتر
                    </button>
                    <button class="btn btn-outline-secondary" onclick="clearFilters()">
                        <i class="feather-refresh-cw me-1"></i>
                        مسح
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Results Summary -->
    <div class="mb-3 d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-3">
            <span class="text-muted">
                <i class="feather-users me-1"></i>
                تم العثور على {{ $leads->total() }} عميل محتمل
                @if($leads->total() > 0)
                    (عرض {{ $leads->firstItem() ?? 0 }} - {{ $leads->lastItem() ?? 0 }})
                @endif
            </span>
        </div>
{{--
        @if(request('date_from') || request('date_to') || request('status') || request('search'))
            <div class="d-flex align-items-center gap-2">
                <span class="text-muted small">فلاتر نشطة</span>
                <button class="btn btn-sm btn-outline-secondary" onclick="clearFilters()">
                    <i class="feather-refresh-cw me-1"></i>
                    مسح الفلاتر
                </button>
            </div>
        @endif

        --}}
    </div>

    @can('view_roles')
        <!-- Bulk Assign -->
        <div class="mb-3 d-flex align-items-center gap-2">
            <select id="bulkAssignEmployee" class="form-control w-auto">
                <option value="">-- تعيين تلقائي (أقل عدد عملاء) --</option>
                @foreach(\App\Models\User::where('type', 'employee')->whereHas('employee', function ($query) {
                    $query->where('department_id', 7)->where('is_active', true);
                })->get() as $user)
                    <option value="{{ $user->id }}">{{ $user->employee?->name ?? $user->name }}</option>
                @endforeach
            </select>
            <button class="btn btn-primary" id="bulkAssignBtn">تعيين العملاء المحددين</button>
        </div>
    @endcan

    <!-- [ Main Content ] start -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover" id="leadList">
                            <thead>
                                <tr>
                                    <th class="wd-30">
                                        <div class="btn-group mb-1">
                                            <div class="custom-control custom-checkbox ms-1">
                                                <input type="checkbox" class="custom-control-input" id="checkAllLead">
                                                <label class="custom-control-label" for="checkAllLead"></label>
                                            </div>
                                        </div>
                                    </th>
                                    <th>العميل</th>
                                    <th>الهاتف</th>
                                    <th>المحافظة</th>
                                    <th>الموظف المعين</th>
                                    <th>موديراتور ومعلن</th>
                                    <th>وسيلة النقل</th>
                                    <th>آخر متابعة</th>
                                    <th>التاريخ</th>
                                    <th>الحالة</th>
                                    <th class="text-end">الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($leads as $lead)
                                    <tr class="single-item" id="lead-{{ $lead->id }}">
                                        <td>
                                            <div class="item-checkbox ms-1">
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input lead-checkbox" id="checkBox_{{ $lead->id }}" value="{{ $lead->id }}">
                                                    <label class="custom-control-label" for="checkBox_{{ $lead->id }}"></label>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <a href="{{ route('leads.show', $lead->id) }}" class="hstack gap-3">
                                                <div>
                                                    <span class="text-truncate-1-line">{{ $lead->name }}</span>
                                                </div>
                                            </a>
                                        </td>
                                        <td><a href="tel:{{ $lead->phone }}">{{ $lead->phone }}</a></td>
                                        <td>{{ $lead->governorate?->name ?? '-' }}</td>

                                        <td>{{ $lead->employee->employee->name ?? '-' }}</td>
                                        <td>
{{--                                            @if($lead->source_id == 22 && $lead->referredBy)--}}
{{--                                                <div class="d-flex align-items-center">--}}
{{--                                                    <div class="avatar-text avatar-sm rounded-circle bg-info me-2">--}}
{{--                                                        <i class="feather-user"></i>--}}
{{--                                                    </div>--}}
{{--                                                    <div>--}}
{{--                                                        <div class="fw-bold">--}}
{{--                                                            @if($lead->referredBy)--}}
{{--                                                                @if($lead->referred_by_type === 'representative' && $lead->referredBy->representative)--}}
{{--                                                                    {{ $lead->referredBy->representative->name }}--}}
{{--                                                                @elseif($lead->referred_by_type === 'supervisor' && $lead->referredBy->supervisor)--}}
{{--                                                                    {{ $lead->referredBy->supervisor->name }}--}}
{{--                                                                @else--}}
{{--                                                                    {{ $lead->referredBy->name }}--}}
{{--                                                                @endif--}}
{{--                                                            @else--}}
{{--                                                                ---}}
{{--                                                            @endif--}}
{{--                                                        </div>--}}
{{--                                                        <small class="text-muted">{{ ucfirst($lead->referred_by_type) }}</small>--}}
{{--                                                    </div>--}}
{{--                                                </div>--}}
{{--                                            @else--}}
{{--                                                <span class="text-muted">-</span>--}}
{{--                                            @endif--}}

                                             <div>
                                                {{ $lead->moderator->name ?? '-' }} <br>
                                                {{ $lead->advertiser->name ?? '-' }}
                                            </div>
                                        </td>
                                        <td>{{ $lead->transportation ?? '-' }}</td>
                                        <td>{{ $lead->lastFollowUp?->notes ?? '-' }}</td>
                                        <td>{{ $lead->created_at->format('Y-m-d, h:iA') }}</td>
                                                                                 <td>
                                             @php
                                                 $statusColors = [
                                                     'جديد' => 'bg-soft-dark text-dark',
                                                     'متابعة' => 'bg-soft-primary text-primary',
                                                     'مقابلة' => 'bg-soft-warning text-warning',
                                                     'غير مهتم' => 'bg-soft-danger text-danger',
                                                     'عمل مقابلة' => 'bg-soft-info text-info',
                                                     'لم يرد' => 'bg-soft-warning text-warning',
                                                     'مفاوضات' => 'bg-soft-warning text-warning',
                                                     'مغلق' => 'bg-soft-success text-success',
                                                     'خسر' => 'bg-soft-danger text-danger',
                                                     'قديم' => 'bg-soft-secondary text-secondary'
                                                 ];
                                                 $statusColor = $statusColors[$lead->status] ?? 'bg-soft-secondary text-secondary';
                                             @endphp
                                             <div class="badge {{ $statusColor }}">{{ $lead->status }}</div>
                                             @if($lead->representative)
                                                 <div class="badge bg-success mt-1">
                                                     <i class="feather feather-user-check me-1"></i>تم التحويل
                                                 </div>
                                             @endif
                                         </td>
                                        <td>
                                            <div class="hstack gap-2 justify-content-end">
                                                @can('view_leads')
                                                <a href="{{ route('leads.show', $lead->id) }}" class="avatar-text avatar-md" title="عرض">
                                                    <i class="feather feather-eye"></i>
                                                </a>
                                                @endcan
                                                @can('create_representatives')
                                                @if($lead->status == 'مقابلة' && !$lead->representative)
                                                <a href="{{ route('representatives.create', ['lead_id' => $lead->id]) }}" class="avatar-text avatar-md text-success" title="إضافة كمندوب">
                                                    <i class="feather feather-user-plus"></i>
                                                </a>


                                                @endif
                                                @endcan

                                                <div class="dropdown">
                                                    <a href="javascript:void(0)" class="avatar-text avatar-md" data-bs-toggle="dropdown" data-bs-offset="0,21">
                                                        <i class="feather feather-more-horizontal"></i>
                                                    </a>
                                                    <ul class="dropdown-menu">
                                                        @can('edit_leads')
                                                        <li>
                                                            <a class="dropdown-item" href="{{ route('leads.edit', $lead->id) }}">
                                                                <i class="feather feather-edit-3 me-3"></i>
                                                                <span>تعديل</span>
                                                            </a>
                                                        </li>
                                                        @endcan
                                                        {{--
                                                        @can('create_representatives')
                                                        @if($lead->status == 'مقابلة' && !$lead->representative)
                                                        <li>
                                                            <a class="dropdown-item" href="{{ route('representatives.create', ['lead_id' => $lead->id]) }}">
                                                                <i class="feather feather-user-plus me-3"></i>
                                                                <span>إضافة كمندوب</span>
                                                            </a>
                                                        </li>
                                                        @endif
                                                        @endcan
                                                        --}}
                                                        @can('view_representatives')
                                                        @if($lead->representative)
                                                        <li>
                                                            <a class="dropdown-item" href="{{ route('representatives.show', $lead->representative->id) }}">
                                                                <i class="feather feather-user-check me-3"></i>
                                                                <span>عرض المندوب</span>
                                                            </a>
                                                        </li>
                                                        @endif
                                                        @endcan
                                                    </ul>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    @if($leads->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $leads->appends(request()->query())->links('pagination::bootstrap-5') }}
        </div>
    @endif

    <!-- Loading Overlay -->
    <div id="loadingOverlay" class="loading-overlay" style="display: none;">
        <div class="loading-content">
            <div class="loading-spinner"></div>
            <div class="loading-text">جاري تعيين العملاء المحتملين...</div>
            <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-3" onclick="hideLoadingOverlay()"></button>
        </div>
    </div>
@endsection

@section('styles')
    <style>
        /* Ensure only one pagination is shown (hide any DataTables UI if injected) */
        .dataTables_wrapper .dataTables_paginate,
        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_info,
        .dataTables_wrapper .dataTables_filter {
            display: none !important;
        }
        .text-blue {
            color: #007bff !important; /* Bootstrap primary blue */
        }
        .text-red {
            color: #dc3545 !important; /* Bootstrap danger red */
        }

        /* Filters section styling */
        .filters-section {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            border: 1px solid #e9ecef;
        }

        .filters-section .form-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 8px;
        }

        .filters-section .form-control {
            border-radius: 6px;
            border: 1px solid #ced4da;
        }

        .filters-section .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }

        /* Active filter indicator */
        .filter-active {
            background-color: #e3f2fd;
            border-color: #2196f3;
        }

        /* Filter summary */
        .filter-summary {
            background: #e8f5e8;
            border: 1px solid #4caf50;
            border-radius: 6px;
            padding: 10px 15px;
            margin-bottom: 15px;
            font-size: 14px;
        }

        .filter-summary .badge {
            background-color: #4caf50;
            color: white;
            margin-right: 5px;
        }

        /* Loading overlay styles */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.7);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }

        .loading-content {
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            text-align: center;
            position: relative;
            min-width: 300px;
        }

        .loading-spinner {
            width: 50px;
            height: 50px;
            border: 4px solid #f3f3f3;
            border-top: 4px solid #007bff;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 20px;
        }

        .loading-text {
            font-size: 16px;
            color: #333;
            margin-bottom: 10px;
        }

        /* Spin animation */
        .spin {
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Button disabled state */
        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        /* Toastr customization */
        .toastr {
            z-index: 10000;
        }

                 /* Status badge styling */
         .badge {
             font-size: 0.875rem;
             font-weight: 500;
             padding: 0.5rem 0.75rem;
             border-radius: 6px;
         }

         /* Soft background colors */
         .bg-soft-primary {
             background-color: rgba(0, 123, 255, 0.1) !important;
         }
         .bg-soft-success {
             background-color: rgba(40, 167, 69, 0.1) !important;
         }
         .bg-soft-warning {
             background-color: rgba(255, 193, 7, 0.1) !important;
         }
         .bg-soft-danger {
             background-color: rgba(220, 53, 69, 0.1) !important;
         }
         .bg-soft-info {
             background-color: rgba(23, 162, 184, 0.1) !important;
         }
         .bg-soft-secondary {
             background-color: rgba(108, 117, 125, 0.1) !important;
         }
         .bg-soft-dark {
             background-color: rgba(52, 58, 64, 0.1) !important;
         }

         /* Text colors */
         .text-primary {
             color: #007bff !important;
         }
         .text-success {
             color: #28a745 !important;
         }
         .text-warning {
             color: #ffc107 !important;
         }
         .text-danger {
             color: #dc3545 !important;
         }
         .text-info {
             color: #17a2b8 !important;
         }
         .text-secondary {
             color: #6c757d !important;
         }
         .text-dark {
             color: #343a40 !important;
         }
        /* Smaller fonts for: العميل (2), الموظف المعين (5), الموديراتور والمعلن (6), آخر متابعة (8), الإجراءات (11) */
        #leadList thead th:nth-child(2),
        #leadList thead th:nth-child(5),
        #leadList thead th:nth-child(6),
        #leadList thead th:nth-child(8),
        #leadList thead th:nth-child(11),
        #leadList tbody td:nth-child(2),
        #leadList tbody td:nth-child(5),
        #leadList tbody td:nth-child(6),
        #leadList tbody td:nth-child(8),
        #leadList tbody td:nth-child(11) {
            font-size: 0.85rem;
        }
        #leadList tbody td:nth-child(2) a,
        #leadList tbody td:nth-child(11) a,
        #leadList tbody td:nth-child(11) .badge {
            font-size: 0.8rem;
        }
    </style>
@endsection

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <script>
        // Configure toastr
        toastr.options = {
            "closeButton": true,
            "debug": false,
            "newestOnTop": false,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "preventDuplicates": false,
            "onclick": null,
            "showDuration": "300",
            "hideDuration": "1000",
            "timeOut": "5000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        };

        // Function to hide loading overlay
        function hideLoadingOverlay() {
            const loadingOverlay = document.getElementById('loadingOverlay');
            if (loadingOverlay) {
                loadingOverlay.style.display = 'none';
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            // Apply default filters if no filters are currently applied
            const urlParams = new URLSearchParams(window.location.search);
            if (!urlParams.has('status') && !urlParams.has('date_from') && !urlParams.has('date_to') && !urlParams.has('search') && !urlParams.has('transportation') && !urlParams.has('assigned_to') && !urlParams.has('governorate_id') && !urlParams.has('location_id')) {
                // Apply default filters: status = 'جديد' and today's date
                const today = new Date().toISOString().split('T')[0];
                const url = new URL(window.location.href);
                url.searchParams.set('status', 'جديد');
                url.searchParams.set('date_from', today);
                url.searchParams.set('date_to', today);
                window.location.href = url.toString();
                return; // Exit early to let the page reload with filters
            }

            // Ensure DataTables (if included globally) does not override Laravel pagination
            try {
                if (window.$ && $.fn && $.fn.DataTable) {
                    if ($.fn.dataTable.isDataTable('#leadList')) {
                        $('#leadList').DataTable().destroy();
                    }
                    // Remove any leftover DT markup
                    const dtWrappers = document.querySelectorAll('.dataTables_wrapper');
                    dtWrappers.forEach(w => w.parentNode && w.parentNode.removeChild(w));
                }
            } catch (e) { /* noop */ }
            // Select all functionality
            const checkAll = document.getElementById('checkAllLead');
            checkAll.addEventListener('change', function () {
                document.querySelectorAll('.lead-checkbox').forEach(cb => cb.checked = checkAll.checked);
            });



            // Bulk assign function
            const bulkAssignBtn = document.getElementById('bulkAssignBtn');
            if (bulkAssignBtn) {
                bulkAssignBtn.addEventListener('click', function() {
                    const employeeId = document.getElementById('bulkAssignEmployee').value;
                    const selectedLeads = Array.from(document.querySelectorAll('.lead-checkbox:checked')).map(cb => cb.value);

                    if (selectedLeads.length === 0) {
                        toastr.error('يرجى اختيار العملاء المحتملين');
                        return;
                    }

                    // Show confirmation for automatic assignment
                    let confirmMessage = 'هل أنت متأكد من تعيين العملاء المحددين؟';
                    if (!employeeId) {
                        confirmMessage = 'سيتم تعيين العملاء تلقائياً إلى الموظف الأقل في عدد العملاء. هل أنت متأكد؟';
                    }

                    if (!confirm(confirmMessage)) {
                        return;
                    }

                    // Show loading overlay
                    const loadingOverlay = document.getElementById('loadingOverlay');
                    if (loadingOverlay) {
                        loadingOverlay.style.display = 'flex';
                        const loadingText = loadingOverlay.querySelector('.loading-text');
                        if (loadingText) {
                            loadingText.textContent = `جاري تعيين ${selectedLeads.length} عميل محتمل...`;
                        }
                    }

                    // Disable button and show loading state
                    bulkAssignBtn.disabled = true;
                    const originalText = bulkAssignBtn.innerHTML;
                    bulkAssignBtn.innerHTML = '<i class="feather-loader spin me-2"></i>جاري التعيين...';

                    // Set timeout to force hide overlay after 10 seconds
                    const overlayTimeout = setTimeout(() => {
                        if (loadingOverlay) {
                            loadingOverlay.style.display = 'none';
                        }
                        bulkAssignBtn.disabled = false;
                        bulkAssignBtn.innerHTML = originalText;
                        toastr.error('انتهت مهلة العملية. يرجى المحاولة مرة أخرى.');
                    }, 10000);

                    console.log('Bulk assigning leads:', selectedLeads, 'to employee:', employeeId || 'auto-assign');

                    fetch(`{{ route('leads.bulkAssign') }}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ employee_id: employeeId, leads: selectedLeads })
                    })
                    .then(res => {
                        clearTimeout(overlayTimeout);
                        if (!res.ok) {
                            throw new Error('فشل الاتصال بالخادم: ' + res.status);
                        }
                        return res.json();
                    })
                    .then(data => {
                        clearTimeout(overlayTimeout);

                        // Clear checkboxes
                        document.querySelectorAll('.lead-checkbox:checked').forEach(cb => cb.checked = false);
                        document.getElementById('checkAllLead').checked = false;

                        // Clear any existing toastr messages
                        toastr.clear();

                        if (data.success) {
                            // Show success message
                            toastr.success(data.message || 'تم تعيين العملاء بنجاح');

                            // Hide overlay after 1 second
                            setTimeout(() => {
                                if (loadingOverlay) {
                                    loadingOverlay.style.display = 'none';
                                }
                                bulkAssignBtn.disabled = false;
                                bulkAssignBtn.innerHTML = originalText;

                                // Show reload message and reload page
                                toastr.info('جاري إعادة تحميل الصفحة...');
                                setTimeout(() => {
                                    location.reload();
                                }, 2000);
                            }, 1000);
                        } else {
                            // Hide overlay and show error
                            if (loadingOverlay) {
                                loadingOverlay.style.display = 'none';
                            }
                            bulkAssignBtn.disabled = false;
                            bulkAssignBtn.innerHTML = originalText;
                            toastr.error(data.message || 'فشل تعيين العملاء');
                        }
                    })
                    .catch(err => {
                        clearTimeout(overlayTimeout);
                        console.error('Bulk assign error:', err);

                        // Hide overlay and show error
                        if (loadingOverlay) {
                            loadingOverlay.style.display = 'none';
                        }
                        bulkAssignBtn.disabled = false;
                        bulkAssignBtn.innerHTML = originalText;
                        toastr.error(err.message || 'حدث خطأ أثناء الاتصال بالخادم');
                    });
                });
            }

            // Filter by status function
            window.filterByStatus = function(status) {
                const url = new URL(window.location.href);
                if (status === 'all') {
                    url.searchParams.delete('status');
                } else {
                    url.searchParams.set('status', status);
                }
                window.location.href = url.toString();
            };

            // Apply filters function
            window.applyFilters = function() {
                const searchTerm = document.getElementById('searchInput').value;
                const dateFrom = document.getElementById('dateFrom').value;
                const dateTo = document.getElementById('dateTo').value;
                const status = document.getElementById('statusFilter').value;
                const governorate_id = document.getElementById('governorateFilter').value;
                const locationEl = document.getElementById('locationFilter');
                const location_id = locationEl ? locationEl.value : '';
                const transportation = document.getElementById('transportationFilter').value;
                const assigned_to = document.getElementById('assignedToFilter').value;

                const url = new URL(window.location.href);

                // Clear existing filters
                url.searchParams.delete('search');
                url.searchParams.delete('date_from');
                url.searchParams.delete('date_to');
                url.searchParams.delete('status');
                url.searchParams.delete('assigned_to');
                url.searchParams.delete('transportation');
                url.searchParams.delete('governorate_id');
                url.searchParams.delete('location_id');

                // Add new filters
                if (searchTerm) {
                    url.searchParams.set('search', searchTerm);
                }
                if (dateFrom) {
                    url.searchParams.set('date_from', dateFrom);
                }
                if (dateTo) {
                    url.searchParams.set('date_to', dateTo);
                }
                if (status) {
                    url.searchParams.set('status', status);
                }
                if (governorate_id) {
                    url.searchParams.set('governorate_id', governorate_id);
                }
                if (location_id) {
                    url.searchParams.set('location_id', location_id);
                }
                if (transportation) {
                    url.searchParams.set('transportation', transportation);
                }
                if (assigned_to) {
                    url.searchParams.set('assigned_to', assigned_to);
                }

                window.location.href = url.toString();
            };

            // Clear filters function
            window.clearFilters = function() {
                document.getElementById('searchInput').value = '';
                document.getElementById('dateFrom').value = '';
                document.getElementById('dateTo').value = '';
                document.getElementById('statusFilter').value = '';
                document.getElementById('governorateFilter').value = '';
                const locationEl = document.getElementById('locationFilter');
                if (locationEl) {
                    locationEl.value = '';
                }
                document.getElementById('transportationFilter').value = '';
                document.getElementById('assignedToFilter').value = '';

                const url = new URL(window.location.href);
                url.searchParams.delete('search');
                url.searchParams.delete('date_from');
                url.searchParams.delete('date_to');
                url.searchParams.delete('status');
                url.searchParams.delete('transportation');
                url.searchParams.delete('governorate_id');
                url.searchParams.delete('location_id');
                url.searchParams.delete('assigned_to');

                window.location.href = url.toString();
            };

            // Filter by date function (for dropdown)
            window.filterByDate = function(date) {
                const url = new URL(window.location.href);
                url.searchParams.set('date_from', date);
                url.searchParams.set('date_to', date);
                window.location.href = url.toString();
            };

            // Add enter key support for date inputs
            document.getElementById('dateFrom').addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    applyFilters();
                }
            });

            document.getElementById('dateTo').addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    applyFilters();
                }
            });

            // Add change event listeners for real-time filtering (optional)
            document.getElementById('statusFilter').addEventListener('change', function() {
                // Uncomment the line below if you want real-time filtering
                // applyFilters();
            });

            document.getElementById('assignedToFilter').addEventListener('change', function() {
                // Uncomment the line below if you want real-time filtering
                // applyFilters();
            });

            // Add date validation
            document.getElementById('dateFrom').addEventListener('change', function() {
                const dateFrom = this.value;
                const dateTo = document.getElementById('dateTo').value;

                if (dateFrom && dateTo && dateFrom > dateTo) {
                    toastr.warning('تاريخ البداية يجب أن يكون قبل تاريخ النهاية');
                    this.value = '';
                }
            });

            document.getElementById('dateTo').addEventListener('change', function() {
                const dateFrom = document.getElementById('dateFrom').value;
                const dateTo = this.value;

                if (dateFrom && dateTo && dateFrom > dateTo) {
                    toastr.warning('تاريخ النهاية يجب أن يكون بعد تاريخ البداية');
                    this.value = '';
                }
            });

            // Add loading state to filter buttons
            window.applyFilters = function() {
                const applyBtn = document.querySelector('button[onclick="applyFilters()"]');
                const originalText = applyBtn.innerHTML;

                applyBtn.disabled = true;
                applyBtn.innerHTML = '<i class="feather-loader spin me-1"></i>جاري التطبيق...';

                // Apply filters after a short delay to show loading state
                setTimeout(() => {
                    const searchTerm = document.getElementById('searchInput').value;
                    const dateFrom = document.getElementById('dateFrom').value;
                    const dateTo = document.getElementById('dateTo').value;
                    const status = document.getElementById('statusFilter').value;
                    const governorate_id = document.getElementById('governorateFilter').value;
                    const locationEl = document.getElementById('locationFilter');
                    const location_id = locationEl ? locationEl.value : '';
                    const transportation = document.getElementById('transportationFilter').value;
                    const assigned_to = document.getElementById('assignedToFilter').value;

                    const url = new URL(window.location.href);

                    // Clear existing filters
                    url.searchParams.delete('search');
                    url.searchParams.delete('date_from');
                    url.searchParams.delete('date_to');
                    url.searchParams.delete('status');
                    url.searchParams.delete('assigned_to');
                    url.searchParams.delete('transportation');
                    url.searchParams.delete('governorate_id');
                    url.searchParams.delete('location_id');
                    url.searchParams.delete('page'); // Reset to first page

                    // Add new filters
                    if (searchTerm) {
                        url.searchParams.set('search', searchTerm);
                    }
                    if (dateFrom) {
                        url.searchParams.set('date_from', dateFrom);
                    }
                    if (dateTo) {
                        url.searchParams.set('date_to', dateTo);
                    }
                    if (status) {
                        url.searchParams.set('status', status);
                    }
                    if (governorate_id) {
                        url.searchParams.set('governorate_id', governorate_id);
                    }
                    if (location_id) {
                        url.searchParams.set('location_id', location_id);
                    }
                    if (transportation) {
                        url.searchParams.set('transportation', transportation);
                    }
                    if (assigned_to) {
                        url.searchParams.set('assigned_to', assigned_to);
                    }

                    window.location.href = url.toString();
                }, 300);
            };
        });
    </script>

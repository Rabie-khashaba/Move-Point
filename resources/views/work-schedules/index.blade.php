@extends('layouts.app')

@section('title', 'مواعيد العمل')

@section('content')
<div class="nxl-content">
    <!-- [ page-header ] start -->
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
                <li class="breadcrumb-item">مواعيد العمل</li>
            </ul>
        </div>
        <div class="page-header-right ms-auto">
            <div class="page-header-right-items">
                <div class="d-flex d-md-none">
                    <a href="javascript:void(0)" class="page-header-right-close-toggle">
                        <i class="feather-arrow-left me-2"></i>
                        <span>الرجوع</span>
                    </a>
                </div>
                <div class="d-flex align-items-center gap-2 page-header-right-items-wrapper">
                    <a href="javascript:void(0);" class="btn btn-icon btn-light-brand" data-bs-toggle="collapse" data-bs-target="#filterCollapse">
                        <i class="feather-filter"></i>
                    </a>
                    @can('create_work_schedules')
                    <a href="{{ route('work-schedules.create') }}" class="btn btn-primary">
                        <i class="feather-plus me-2"></i>
                        <span>إضافة مواعيد عمل</span>
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
    <!-- [ page-header ] end -->

    <!-- Filter Collapse -->
    <div class="collapse" id="filterCollapse">
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('work-schedules.index') }}" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">البحث</label>
                        <input type="text" name="search" class="form-control" placeholder="ابحث عن الموظفين..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">الشيفت</label>
                        <select name="shift" class="form-control">
                            <option value="">جميع الشيفتات</option>
                            <option value="صباحي" {{ request('shift') === 'صباحي' ? 'selected' : '' }}>صباحي</option>
                            <option value="مسائي" {{ request('shift') === 'مسائي' ? 'selected' : '' }}>مسائي</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">الحالة</label>
                        <select name="status" class="form-control">
                            <option value="">جميع الحالات</option>
                            <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>نشط</option>
                            <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>غير نشط</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">تصفية</button>
                        <a href="{{ route('work-schedules.index') }}" class="btn btn-light">مسح</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- [ Main Content ] start -->
    <div class="main-content">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">قائمة مواعيد العمل</h5>
                    </div>
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if($schedules->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>الموظف</th>
                                            <th>الشيفت</th>
                                            <th>وقت البداية</th>
                                            <th>وقت النهاية</th>
                                            <th>أيام العمل</th>
                                            <th>تاريخ التفعيل</th>
                                            <th>الحالة</th>
                                            <th>الإجراءات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($schedules as $schedule)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar avatar-sm me-3">
                                                        <img src="{{ $schedule->employee->profile_photo_url }}" alt="Avatar" class="rounded-circle">
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0">{{ $schedule->employee->name }}</h6>
                                                        <small class="text-muted">{{ $schedule->employee->department->name ?? 'غير محدد' }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $schedule->shift === 'صباحي' ? 'success' : 'warning' }}">
                                                    {{ $schedule->shift }}
                                                </span>
                                            </td>
                                            <td>{{ $schedule->start_time }}</td>
                                            <td>{{ $schedule->end_time }}</td>
                                            <td>
                                                @foreach($schedule->work_days as $day)
                                                    <span class="badge bg-light text-dark me-1">{{ $day }}</span>
                                                @endforeach
                                            </td>
                                            <td>{{ $schedule->effective_date->format('Y-m-d') }}</td>
                                            <td>
                                                <span class="badge bg-{{ $schedule->is_active ? 'success' : 'danger' }}">
                                                    {{ $schedule->is_active ? 'نشط' : 'غير نشط' }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="dropdown">
                                                    <button class="btn btn-sm btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                        <i class="feather-more-horizontal"></i>
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        @can('view_work_schedules')
                                                        <li><a class="dropdown-item" href="{{ route('work-schedules.show', $schedule->id) }}">
                                                            <i class="feather-eye me-2"></i>عرض
                                                        </a></li>
                                                        @endcan
                                                        @can('edit_work_schedules')
                                                        <li><a class="dropdown-item" href="{{ route('work-schedules.edit', $schedule->id) }}">
                                                            <i class="feather-edit me-2"></i>تعديل
                                                        </a></li>
                                                        <li>
                                                            <form action="{{ route('work-schedules.toggle-status', $schedule->id) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                <button type="submit" class="dropdown-item">
                                                                    <i class="feather-{{ $schedule->is_active ? 'pause' : 'play' }} me-2"></i>
                                                                    {{ $schedule->is_active ? 'إلغاء التفعيل' : 'تفعيل' }}
                                                                </button>
                                                            </form>
                                                        </li>
                                                        @endcan
                                                        @can('delete_work_schedules')
                                                        <li>
                                                            <form action="{{ route('work-schedules.destroy', $schedule->id) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="dropdown-item text-danger" onclick="return confirm('هل أنت متأكد من حذف مواعيد العمل هذه؟')">
                                                                    <i class="feather-trash-2 me-2"></i>حذف
                                                                </button>
                                                            </form>
                                                        </li>
                                                        @endcan
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <div class="d-flex justify-content-center mt-4">
                                {{ $schedules->links('pagination::bootstrap-5') }}
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="feather-clock fs-48 text-muted mb-3"></i>
                                <h5 class="text-muted">لا توجد مواعيد عمل</h5>
                                <p class="text-muted">لم يتم إضافة أي مواعيد عمل بعد</p>
                                @can('create_work_schedules')
                                <a href="{{ route('work-schedules.create') }}" class="btn btn-primary">
                                    <i class="feather-plus me-2"></i>إضافة مواعيد عمل
                                </a>
                                @endcan
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- [ Main Content ] end -->
</div>
@endsection

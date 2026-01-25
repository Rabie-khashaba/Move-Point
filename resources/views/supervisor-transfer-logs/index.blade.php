@extends('layouts.app')

@section('title', 'سجل نقل الممثلين')

@section('content')
<div class="nxl-content">
    <!-- [ page-header ] start -->
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="m-b-10">سجل نقل المندوبين</h5>
            </div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
                <li class="breadcrumb-item">سجل نقل المندوبين</li>
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
                    <a href="javascript:void(0);" class="btn btn-icon btn-light-brand" data-bs-toggle="collapse" data-bs-target="#filterCollapse">
                        <i class="feather-filter"></i>
                    </a>
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
                <form method="GET" action="{{ route('supervisor-transfer-logs.index') }}" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">البحث</label>
                        <input type="text" name="search" class="form-control" placeholder="البحث في المندوبين..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">المندوب</label>
                        <select name="representative_id" class="form-control">
                            <option value="">جميع المندوبين</option>
                            @foreach($representatives as $representative)
                                <option value="{{ $representative->id }}" {{ request('representative_id') == $representative->id ? 'selected' : '' }}>
                                    {{ $representative->name }} - {{ $representative->phone }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">المشرف السابق</label>
                        <select name="old_supervisor_id" class="form-control">
                            <option value="">جميع المشرفين</option>
                            @foreach($supervisors as $supervisor)
                                <option value="{{ $supervisor->id }}" {{ request('old_supervisor_id') == $supervisor->id ? 'selected' : '' }}>
                                    {{ $supervisor->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">المشرف الجديد</label>
                        <select name="new_supervisor_id" class="form-control">
                            <option value="">جميع المشرفين</option>
                            @foreach($supervisors as $supervisor)
                                <option value="{{ $supervisor->id }}" {{ request('new_supervisor_id') == $supervisor->id ? 'selected' : '' }}>
                                    {{ $supervisor->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">من تاريخ</label>
                        <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">إلى تاريخ</label>
                        <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                    </div>
                    <div class="col-md-6 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">تصفية</button>
                        <a href="{{ route('supervisor-transfer-logs.index') }}" class="btn btn-light">مسح</a>
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
                        <h5 class="card-title mb-0">سجل نقل المندوبين</h5>
                    </div>
                    <div class="card-body">
                        @if($transferLogs->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>المندوب</th>
                                            <th>المشرف السابق</th>
                                            <th>المشرف الجديد</th>
                                            <th>السبب</th>
                                            <th>تم النقل بواسطة</th>
                                            <th>تاريخ النقل</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($transferLogs as $log)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-text avatar-sm rounded-circle bg-primary me-3">
                                                        <i class="feather-user"></i>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0">{{ $log->representative->name ?? 'غير محدد' }}</h6>
                                                        <small class="text-muted">{{ $log->representative->phone ?? 'غير محدد' }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                @if($log->oldSupervisor)
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-text avatar-sm rounded-circle bg-warning me-2">
                                                            <i class="feather-user-check"></i>
                                                        </div>
                                                        <div>
                                                            <span class="fw-bold">{{ $log->oldSupervisor->name }}</span>
                                                            @if($log->oldSupervisor->location)
                                                                <br><small class="text-muted">{{ $log->oldSupervisor->location->name }}</small>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @else
                                                    <span class="text-muted">غير محدد</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($log->newSupervisor)
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-text avatar-sm rounded-circle bg-success me-2">
                                                            <i class="feather-user-check"></i>
                                                        </div>
                                                        <div>
                                                            <span class="fw-bold">{{ $log->newSupervisor->name }}</span>
                                                            @if($log->newSupervisor->location)
                                                                <br><small class="text-muted">{{ $log->newSupervisor->location->name }}</small>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @else
                                                    <span class="text-muted">غير محدد</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($log->reason)
                                                    <span class="badge bg-info">{{ Str::limit($log->reason, 50) }}</span>
                                                @else
                                                    <span class="text-muted">لا يوجد سبب</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($log->transferredBy)
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-text avatar-sm rounded-circle bg-secondary me-2">
                                                            <i class="feather-user"></i>
                                                        </div>
                                                        <span>{{ $log->transferredBy->name }}</span>
                                                    </div>
                                                @else
                                                    <span class="text-muted">غير محدد</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div>
                                                    <small class="text-muted">{{ $log->created_at->format('d M, Y') }}</small>
                                                    <br>
                                                    <small class="text-muted">{{ $log->created_at->format('H:i') }}</small>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            @if($transferLogs->hasPages())
                                <div class="d-flex justify-content-center mt-4">
                                    {{ $transferLogs->links('pagination::bootstrap-5') }}
                                </div>
                            @endif
                        @else
                            <div class="text-center py-5">
                                <div class="avatar-text avatar-xl mx-auto mb-3">
                                    <i class="feather-activity"></i>
                                </div>
                                <h5>لا توجد سجلات نقل</h5>
                                <p class="text-muted">لم يتم تسجيل أي نقل للمندوبين بعد.</p>
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

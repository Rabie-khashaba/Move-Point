@extends('layouts.app')

@section('title', 'طلبات الإجازة')

@push('styles')
<style>
    .table-responsive {
        overflow: visible !important;
    }
    
    .dropdown-menu {
        position: absolute !important;
        z-index: 1050 !important;
    }
    
    .table td:last-child {
        position: relative;
    }
    
    .dropdown-menu-end {
        right: 0;
        left: auto;
    }
    
    @media (max-width: 768px) {
        .dropdown-menu {
            position: fixed !important;
            top: 50% !important;
            left: 50% !important;
            transform: translate(-50%, -50%) !important;
            width: 90% !important;
            max-width: 300px !important;
        }
    }
</style>
@endpush

@section('content')
<div class="nxl-content">
    <!-- [ page-header ] start -->
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
                <li class="breadcrumb-item">طلبات الإجازة</li>
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
                     @php
                         $mobilePendingCount = $leaves->filter(function($leave) {
                             return $leave->status === 'pending' && $leave->isFromMobileApp();
                         })->count();
                     @endphp
                    @if($mobilePendingCount > 0)
                        <a href="{{ route('leave-requests.index', ['status' => 'pending', 'mobile_only' => '1']) }}" class="btn btn-danger">
                            <i class="feather-alert-circle me-2"></i>
                            <span>طلبات تحتاج موافقة ({{ $mobilePendingCount }})</span>
                        </a>
                    @endif
                    <a href="javascript:void(0);" class="btn btn-icon btn-light-brand" data-bs-toggle="collapse" data-bs-target="#filterCollapse">
                        <i class="feather-filter"></i>
                    </a>
                    @can('create_leave_requests')
                    <a href="{{ route('leave-requests.create') }}" class="btn btn-primary">
                        <i class="feather-plus me-2"></i>
                        <span>إضافة طلب إجازة</span>
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

    <!-- Pending Requests Alert -->
    @php
        $pendingMobileRequests = $leaves->filter(function($leave) {
            return $leave->status === 'pending' && $leave->isFromMobileApp();
        })->count();
    @endphp
    @if($pendingMobileRequests > 0)
    <div class="alert alert-warning alert-dismissible fade show mb-4" role="alert">
        <div class="d-flex align-items-center">
            <i class="feather-alert-triangle me-3 fs-4"></i>
            <div>
                <strong>تنبيه!</strong> يوجد {{ $pendingMobileRequests }} طلب إجازة من التطبيق في انتظار الموافقة أو الرفض.
                <br>
                <small class="text-muted">هذه الطلبات تحتاج إلى مراجعة من الإدارة</small>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- Filter Collapse -->
    <div class="collapse" id="filterCollapse">
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('leave-requests.index') }}" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">البحث</label>
                        <input type="text" name="search" class="form-control" placeholder="ابحث عن الموظفين..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">الحالة</label>
                        <select name="status" class="form-control">
                            <option value="">جميع الحالات</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>في الانتظار</option>
                            <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>تمت الموافقة</option>
                            <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>مرفوض</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">نوع الإجازة</label>
                        <select name="type" class="form-control">
                            <option value="">جميع الأنواع</option>
                            <option value="سنوية" {{ request('type') === 'سنوية' ? 'selected' : '' }}>سنوية</option>
                            <option value="مرضية" {{ request('type') === 'مرضية' ? 'selected' : '' }}>مرضية</option>
                            <option value="طارئة" {{ request('type') === 'طارئة' ? 'selected' : '' }}>طارئة</option>
                            <option value="أخرى" {{ request('type') === 'أخرى' ? 'selected' : '' }}>أخرى</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">من تاريخ</label>
                        <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">إلى تاريخ</label>
                        <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                    </div>
                    <div class="col-md-1 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">تصفية</button>
                        <a href="{{ route('leave-requests.index') }}" class="btn btn-light">مسح</a>
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
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">قائمة طلبات الإجازة</h5>
                                                         @php
                                 $pendingCount = $leaves->where('status', 'pending')->count();
                                 $mobilePendingCount = $leaves->filter(function($leave) {
                                     return $leave->status === 'pending' && $leave->isFromMobileApp();
                                 })->count();
                             @endphp
                            @if($pendingCount > 0)
                                <div class="d-flex gap-2">
                                    @if($mobilePendingCount > 0)
                                        <span class="badge bg-danger">
                                            <i class="feather-alert-circle me-1"></i>
                                            {{ $mobilePendingCount }} يحتاج موافقة
                                        </span>
                                    @endif
                                    <span class="badge bg-warning">
                                        <i class="feather-clock me-1"></i>
                                        {{ $pendingCount }} في الانتظار
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if($leaves->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>مقدم الطلب</th>
                                            <th>نوع الإجازة</th>
                                            <th>تاريخ البداية</th>
                                            <th>تاريخ النهاية</th>
                                            <th>المدة</th>
                                            <th>السبب</th>
                                            <th>الحالة</th>
                                            <th>تاريخ الطلب</th>
                                            <th>الإجراءات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($leaves as $leave)
                                        <tr class="{{ $leave->status === 'pending' && $leave->isFromMobileApp() ? 'table-warning' : '' }}">
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    
                                                    <div>
                                                        <h6 class="mb-0">{{ $leave->requester_name }}</h6>
                                                        <small class="text-muted">
                                                            @if($leave->employee)
                                                                {{ $leave->employee->department->name ?? 'غير محدد' }}
                                                            @elseif($leave->representative)
                                                                مندوب
                                                            @elseif($leave->supervisor)
                                                                مشرف
                                                            @else
                                                                غير محدد
                                                            @endif
                                                        </small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $leave->type === 'سنوية' ? 'success' : ($leave->type === 'مرضية' ? 'warning' : 'info') }}">
                                                    {{ $leave->type }}
                                                </span>
                                            </td>
                                            <td>{{ $leave->start_date->format('Y-m-d') }}</td>
                                            <td>{{ $leave->end_date->format('Y-m-d') }}</td>
                                            <td>{{ $leave->duration }} يوم</td>
                                            <td>
                                                <span class="text-truncate" style="max-width: 150px;" title="{{ $leave->reason }}">
                                                    {{ Str::limit($leave->reason, 50) }}
                                                </span>
                                            </td>
                                                                                         <td>
                                                 @if($leave->status === 'pending')
                                                     <div class="d-flex align-items-center">
                                                         <span class="badge bg-warning me-2">في الانتظار</span>
                                                         @if($leave->isFromMobileApp())
                                                             <span class="badge bg-danger" title="يحتاج موافقة أو رفض">
                                                                 <i class="feather-alert-circle"></i>
                                                             </span>
                                                         @endif
                                                     </div>
                                                 @elseif($leave->status === 'approved')
                                                     <span class="badge bg-success">تمت الموافقة</span>
                                                 @else
                                                     <span class="badge bg-danger">مرفوض</span>
                                                 @endif
                                             </td>
                                            <td>{{ $leave->created_at->format('Y-m-d') }}</td>
                                            <td>
                                                <div class="dropdown position-relative">
                                                    <button class="btn btn-sm btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                        <i class="feather-more-horizontal"></i>
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end" style="z-index: 1050; min-width: 200px;">
                                                        @can('view_leave_requests')
                                                        <li><a class="dropdown-item" href="{{ route('leave-requests.show', $leave->id) }}">
                                                            <i class="feather-eye me-2"></i>عرض
                                                        </a></li>
                                                        @endcan
                                                        
                                                        @if($leave->status === 'pending')
                                                            @can('approve_leave_requests')
                                                            <li>
                                                                <form action="{{ route('leave-requests.approve', $leave->id) }}" method="POST" class="d-inline">
                                                                    @csrf
                                                                    <button type="submit" class="dropdown-item text-success" onclick="return confirm('هل أنت متأكد من الموافقة على هذا الطلب؟')">
                                                                        <i class="feather-check me-2"></i>موافقة
                                                                    </button>
                                                                </form>
                                                            </li>
                                                            <li>
                                                                <a class="dropdown-item text-danger" href="#" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $leave->id }}">
                                                                    <i class="feather-x me-2"></i>رفض
                                                                </a>
                                                            </li>
                                                            @endcan
                                                        @endif

                                                        @can('edit_leave_requests')
                                                        @if($leave->status === 'pending')
                                                        <li><a class="dropdown-item" href="{{ route('leave-requests.edit', $leave->id) }}">
                                                            <i class="feather-edit me-2"></i>تعديل
                                                        </a></li>
                                                        @endif
                                                        @endcan

                                                        @can('delete_leave_requests')
                                                        <li>
                                                            <form action="{{ route('leave-requests.destroy', $leave->id) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="dropdown-item text-danger" onclick="return confirm('هل أنت متأكد من حذف هذا الطلب؟')">
                                                                    <i class="feather-trash-2 me-2"></i>حذف
                                                                </button>
                                                            </form>
                                                        </li>
                                                        @endcan
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>

                                        <!-- Reject Modal -->
                                        @can('approve_leave_requests')
                                        @if($leave->status === 'pending')
                                        <div class="modal fade" id="rejectModal{{ $leave->id }}" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">رفض طلب الإجازة</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <form action="{{ route('leave-requests.reject', $leave->id) }}" method="POST">
                                                        @csrf
                                                        <div class="modal-body">
                                                            <div class="mb-3">
                                                                <label class="form-label">سبب الرفض <span class="text-danger">*</span></label>
                                                                <textarea name="rejection_reason" class="form-control" rows="3" required placeholder="أدخل سبب رفض الطلب..."></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">إلغاء</button>
                                                            <button type="submit" class="btn btn-danger">رفض الطلب</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                        @endif
                                        @endcan
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <div class="d-flex justify-content-center mt-4">
                                {{ $leaves->appends(request()->query())->links('pagination::bootstrap-5') }}
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="feather-calendar fs-48 text-muted mb-3"></i>
                                <h5 class="text-muted">لا توجد طلبات إجازة</h5>
                                <p class="text-muted">لم يتم تقديم أي طلبات إجازة بعد</p>
                                @can('create_leave_requests')
                                <a href="{{ route('leave-requests.create') }}" class="btn btn-primary">
                                    <i class="feather-plus me-2"></i>إضافة طلب إجازة
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

@extends('layouts.app')

@section('title', 'عرض طلب الإجازة')

@section('content')
<div class="nxl-content">
    <!-- [ page-header ] start -->
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
                <li class="breadcrumb-item"><a href="{{ route('leave-requests.index') }}">طلبات الإجازة</a></li>
                <li class="breadcrumb-item">عرض طلب</li>
            </ul>
        </div>
        <div class="page-header-right ms-auto">
            <div class="page-header-right-items">
                @can('edit_leave_requests')
                @if($leave->status === 'pending')
                <a href="{{ route('leave-requests.edit', $leave->id) }}" class="btn btn-warning">
                    <i class="feather-edit me-2"></i>
                    <span>تعديل</span>
                </a>
                @endif
                @endcan
                <a href="{{ route('leave-requests.index') }}" class="btn btn-light">
                    <i class="feather-arrow-left me-2"></i>
                    <span>رجوع</span>
                </a>
            </div>
        </div>
    </div>
    <!-- [ page-header ] end -->

    <!-- [ Main Content ] start -->
    <div class="main-content">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">تفاصيل طلب الإجازة</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="fw-bold">مقدم الطلب:</label>
                                    <p>{{ $leave->requester_name }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="fw-bold">نوع مقدم الطلب:</label>
                                    <p>
                                        @if($leave->employee)
                                            موظف
                                        @elseif($leave->representative)
                                            مندوب
                                        @elseif($leave->supervisor)
                                            مشرف
                                        @else
                                            غير محدد
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="fw-bold">تاريخ البداية:</label>
                                    <p>{{ $leave->start_date->format('Y-m-d') }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="fw-bold">تاريخ النهاية:</label>
                                    <p>{{ $leave->end_date->format('Y-m-d') }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="fw-bold">المدة:</label>
                                    <p>{{ $leave->duration }} يوم</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="fw-bold">نوع الإجازة:</label>
                                    <p>
                                        <span class="badge bg-{{ $leave->type === 'سنوية' ? 'success' : ($leave->type === 'مرضية' ? 'warning' : 'info') }}">
                                            {{ $leave->type }}
                                        </span>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="fw-bold">السبب:</label>
                            <p>{{ $leave->reason }}</p>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="fw-bold">الحالة:</label>
                                    <p>
                                        @if($leave->status === 'pending')
                                            <span class="badge bg-warning">في الانتظار</span>
                                        @elseif($leave->status === 'approved')
                                            <span class="badge bg-success">تمت الموافقة</span>
                                        @else
                                            <span class="badge bg-danger">مرفوض</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="fw-bold">تاريخ الطلب:</label>
                                    <p>{{ $leave->created_at->format('Y-m-d H:i') }}</p>
                                </div>
                            </div>
                        </div>

                        @if($leave->status !== 'pending')
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="fw-bold">تمت المعالجة بواسطة:</label>
                                    <p>{{ $leave->approver->name ?? 'غير محدد' }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="fw-bold">تاريخ المعالجة:</label>
                                    <p>{{ $leave->approved_at ? $leave->approved_at->format('Y-m-d H:i') : 'غير محدد' }}</p>
                                </div>
                            </div>
                        </div>
                        @endif

                        @if($leave->status === 'rejected' && $leave->rejection_reason)
                        <div class="mb-3">
                            <label class="fw-bold">سبب الرفض:</label>
                            <p class="text-danger">{{ $leave->rejection_reason }}</p>
                        </div>
                        @endif

                        @if($leave->status === 'pending')
                        <div class="mt-4">
                            <h6>إجراءات الطلب:</h6>
                            <div class="d-flex gap-2">
                                @can('approve_leave_requests')
                                <form action="{{ route('leave-requests.approve', $leave->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-success" onclick="return confirm('هل أنت متأكد من الموافقة على هذا الطلب؟')">
                                        <i class="feather-check me-2"></i>موافقة
                                    </button>
                                </form>
                                @endcan
                                
                                @can('approve_leave_requests')
                                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal">
                                    <i class="feather-x me-2"></i>رفض
                                </button>
                                @endcan
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- [ Main Content ] end -->
</div>

<!-- Reject Modal -->
@can('approve_leave_requests')
@if($leave->status === 'pending')
<div class="modal fade" id="rejectModal" tabindex="-1">
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
@endsection

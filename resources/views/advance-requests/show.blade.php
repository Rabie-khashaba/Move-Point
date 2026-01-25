@extends('layouts.app')

@section('title', 'تفاصيل طلب السلف')

@section('content')
<div class="nxl-content">
    <!-- [ page-header ] start -->
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
                <li class="breadcrumb-item"><a href="{{ route('advance-requests.index') }}">طلبات السلف</a></li>
                <li class="breadcrumb-item">تفاصيل الطلب #{{ $advance->id }}</li>
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
                    <a href="{{ route('advance-requests.index') }}" class="btn btn-outline-secondary">
                        <i class="feather-arrow-right me-2"></i>
                        <span>العودة للقائمة</span>
                    </a>
                    @can('approve_advance_requests')
                        @if($advance->status === 'pending')
                           {{-- <a href="{{ route('advance-requests.approve', $advance->id) }}"
                               class="btn btn-success"
                               onclick="return confirm('هل أنت متأكد من الموافقة على هذا الطلب؟')">
                                <i class="feather-check me-2"></i>
                                <span>موافقة</span>
                            </a> --}}

                            <form action="{{ route('advance-requests.approve', $advance->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit"
                                        class="btn btn-success d-inline-flex align-items-center"
                                        onclick="return confirm('هل أنت متأكد من الموافقة على هذا الطلب؟')">
                                    <i class="feather-check me-2"></i>
                                    <span>موافقة</span>
                                </button>
                            </form>
                            <a href="{{ route('advance-requests.reject', $advance->id) }}"
                               class="btn btn-danger"
                               onclick="return confirm('هل sأنت متأكد من رفض هذا الطلب؟')">
                                <i class="feather-x me-2"></i>
                                <span>رفض</span>
                            </a>
                        @endif
                    @endcan

                    <!-- زر فتح المودال -->
                    <a href="javascript:void(0);" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editCodeModal{{ $advance->id }}">
                        <i class="feather-edit me-2"></i>
                        <span>تعديل الكود</span>
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

    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Request Details Card -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="feather-file-text me-2"></i>
                        تفاصيل طلب السلف #{{ $advance->id }}
                    </h5>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge fs-6
                            @if($advance->status === 'approved') bg-success
                            @elseif($advance->status === 'rejected') bg-danger
                            @else bg-warning @endif">
                            @if($advance->status === 'approved')
                                <i class="feather-check me-1"></i>موافق عليه
                            @elseif($advance->status === 'rejected')
                                <i class="feather-x me-1"></i>مرفوض
                            @else
                                <i class="feather-clock me-1"></i>في الانتظار
                            @endif
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Basic Information -->
                        <div class="col-md-6">
                            <h6 class="text-primary mb-3">
                                <i class="feather-user me-2"></i>
                                معلومات الطالب
                            </h6>

                            <div class="mb-3">
                                <label class="form-label text-muted">الاسم</label>
                                <div class="fw-bold">{{ $advance->requester_name }}</div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-muted">رقم الهاتف</label>
                                <div class="fw-bold">{{ $advance->requester_phone }}</div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-muted">نوع الطالب</label>
                                <div class="fw-bold">
                                    @if($advance->isFromRepresentative())
                                        <span class="badge bg-info">
                                            <i class="feather-user me-1"></i>مندوب
                                        </span>
                                    @elseif($advance->isFromEmployee())
                                        <span class="badge bg-primary">
                                            <i class="feather-briefcase me-1"></i>موظف
                                        </span>
                                    @elseif($advance->isFromSupervisor())
                                        <span class="badge bg-warning">
                                            <i class="feather-users me-1"></i>مشرف
                                        </span>
                                    @endif
                                </div>
                            </div>

                            @if($advance->representative && $advance->representative->governorate)
                                <div class="mb-3">
                                    <label class="form-label text-muted">المحافظة</label>
                                    <div class="fw-bold">{{ $advance->representative->governorate->name }}</div>
                                </div>
                            @endif
                        </div>

                        <!-- Financial Information -->
                        <div class="col-md-6">
                            <h6 class="text-success mb-3">
                                <i class="feather-dollar-sign me-2"></i>
                                المعلومات المالية
                            </h6>

                            <div class="mb-3">
                                <label class="form-label text-muted">مبلغ السلفة</label>
                                <div class="fw-bold text-success fs-5">{{ number_format($advance->amount, 2) }} ج.م</div>
                            </div>

                            @if($advance->is_installment)
                                <div class="mb-3">
                                    <label class="form-label text-muted">عدد أشهر التقسيط</label>
                                    <div class="fw-bold">{{ $advance->installment_months }} شهر</div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label text-muted">القسط الشهري</label>
                                    <div class="fw-bold text-info">{{ number_format($advance->monthly_installment, 2) }} ج.م</div>
                                </div>
                            @else
                                <div class="mb-3">
                                    <label class="form-label text-muted">نوع السلفة</label>
                                    <div class="fw-bold">
                                        <span class="badge bg-secondary">
                                            <i class="feather-credit-card me-1"></i>مبلغ واحد
                                        </span>
                                    </div>
                                </div>
                            @endif

                            @if($advance->status === 'approved')
                                <div class="mb-3">
                                    <label class="form-label text-muted">المبلغ المتبقي</label>
                                    <div class="fw-bold text-warning">{{ number_format($advance->remaining_amount, 2) }} ج.م</div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Request Reason -->
                    <div class="row">
                        <div class="col-12">
                            <h6 class="text-info mb-3">
                                <i class="feather-message-square me-2"></i>
                                سبب الطلب
                            </h6>
                            <div class="bg-light p-3 rounded">
                                <p class="mb-0">{{ $advance->reason ?: 'لم يتم تحديد سبب' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Receipt Image -->
                    @if($advance->has_receipt)
                        <div class="row mt-4">
                            <div class="col-12">
                                <h6 class="text-secondary mb-3">
                                    <i class="feather-image me-2"></i>
                                    صورة الإيصال
                                </h6>
                                <div class="text-center">
                                    <img src="{{ $advance->receipt_image_url }}"
                                         alt="صورة الإيصال"
                                         class="img-fluid rounded shadow-sm"
                                         style="max-height: 400px; cursor: pointer;"
                                         data-bs-toggle="modal"
                                         data-bs-target="#receiptModal">
                                    <div class="mt-2">
                                        <small class="text-muted">اضغط على الصورة لعرضها بحجم أكبر</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Status Timeline -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="feather-clock me-2"></i>
                        سير العمل
                    </h6>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <!-- Request Created -->
                        <div class="timeline-item">
                            <div class="timeline-marker bg-primary"></div>
                            <div class="timeline-content">
                                <h6 class="timeline-title">تم إنشاء الطلب</h6>
                                <p class="timeline-text text-muted">{{ $advance->created_at->format('Y-m-d H:i') }}</p>
                            </div>
                        </div>

                        @if($advance->status === 'approved' || $advance->status === 'rejected')
                            <div class="timeline-item">
                                <div class="timeline-marker
                                    @if($advance->status === 'approved') bg-success @else bg-danger @endif">
                                </div>
                                <div class="timeline-content">
                                    <h6 class="timeline-title">
                                        @if($advance->status === 'approved') تمت الموافقة @else تم الرفض @endif
                                    </h6>
                                    <p class="timeline-text text-muted">{{ $advance->approved_at->format('Y-m-d H:i') }}</p>
                                    @if($advance->approver)
                                        <p class="timeline-text text-muted">
                                            بواسطة: {{ $advance->approver->name }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                        @else
                            <div class="timeline-item">
                                <div class="timeline-marker bg-warning"></div>
                                <div class="timeline-content">
                                    <h6 class="timeline-title">في انتظار المراجعة</h6>
                                    <p class="timeline-text text-muted">الطلب في انتظار مراجعة الإدارة</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Approval Actions -->
            @if($advance->status === 'pending' && auth()->user()->can('approve_advance_requests'))
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="card-title mb-0">
                            <i class="feather-check-circle me-2"></i>
                            إجراءات الموافقة
                        </h6>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('advance-requests.approve', $advance->id) }}" class="mb-3">
                            @csrf
                            <button type="submit" class="btn btn-success w-100"
                                    onclick="return confirm('هل أنت متأكد من الموافقة على هذا الطلب؟')">
                                <i class="feather-check me-2"></i>
                                موافقة على الطلب
                            </button>
                        </form>

                        <form method="POST" action="{{ route('advance-requests.reject', $advance->id) }}">
                            @csrf
                            <div class="mb-3">
                                <label for="rejection_reason" class="form-label">سبب الرفض</label>
                                <textarea class="form-control" id="rejection_reason" name="rejection_reason"
                                          rows="3" placeholder="اكتب سبب رفض الطلب..."></textarea>
                            </div>
                            <button type="submit" class="btn btn-danger w-100"
                                    onclick="return confirm('هل أنت متأكد من رفض هذا الطلب؟')">
                                <i class="feather-x me-2"></i>
                                رفض الطلب
                            </button>
                        </form>
                    </div>
                </div>
            @endif

            <!-- Request Info -->
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="feather-info me-2"></i>
                        معلومات إضافية
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">رقم الطلب:</span>
                        <span class="fw-bold">#{{ $advance->id }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">تاريخ الإنشاء:</span>
                        <span class="fw-bold">{{ $advance->created_at->format('Y-m-d') }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">وقت الإنشاء:</span>
                        <span class="fw-bold">{{ $advance->created_at->format('H:i') }}</span>
                    </div>
                    @if($advance->approved_at)
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">تاريخ المراجعة:</span>
                            <span class="fw-bold">{{ $advance->approved_at->format('Y-m-d H:i') }}</span>
                        </div>
                    @endif
                    @if($advance->isFromMobileApp())
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">مصدر الطلب:</span>
                            <span class="fw-bold">
                                <span class="badge bg-info">
                                    <i class="feather-smartphone me-1"></i>التطبيق
                                </span>
                            </span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Receipt Image Modal -->
@if($advance->has_receipt)
<div class="modal fade" id="receiptModal" tabindex="-1" aria-labelledby="receiptModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="receiptModalLabel">
                    <i class="feather-image me-2"></i>
                    صورة الإيصال
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
            </div>
            <div class="modal-body text-center">
                <img src="{{ $advance->receipt_image_url }}"
                     alt="صورة الإيصال"
                     class="img-fluid rounded">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                <a href="{{ $advance->receipt_image_url }}" target="_blank" class="btn btn-primary">
                    <i class="feather-external-link me-2"></i>
                    فتح في تبويب جديد
                </a>
            </div>
        </div>
    </div>
</div>
@endif


<!-- Modal -->
<div class="modal fade" id="editCodeModal{{ $advance->id }}" tabindex="-1" aria-labelledby="editCodeModalLabel{{ $advance->id }}" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form action="{{ route('advance-requests.updateCode', $advance->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="modal-header">
          <h5 class="modal-title" id="editCodeModalLabel{{ $advance->id }}">تعديل الكود</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label for="codeInput{{ $advance->id }}" class="form-label">الكود</label>
            <input type="text" name="code" id="codeInput{{ $advance->id }}" class="form-control" value="" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
          <button type="submit" class="btn btn-success"><i class="feather-save me-2"></i>حفظ</button>
        </div>
      </form>
    </div>
  </div>
</div>

@endsection

@push('styles')
<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #dee2e6;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: -22px;
    top: 5px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: 3px solid #fff;
    box-shadow: 0 0 0 2px #dee2e6;
}

.timeline-title {
    font-size: 14px;
    font-weight: 600;
    margin-bottom: 5px;
}

.timeline-text {
    font-size: 12px;
    margin-bottom: 0;
}

.card-title {
    color: #495057;
}

.badge {
    font-size: 0.75rem;
}

@media (max-width: 768px) {
    .page-header-right-items-wrapper {
        flex-direction: column;
        gap: 10px;
    }

    .page-header-right-items-wrapper .btn {
        width: 100%;
    }
}
</style>
@endpush

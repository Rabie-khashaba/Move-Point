@extends('layouts.app')

@section('title', 'عرض طلب الاستقالة')

@section('content')
    <!-- [ page-header ] start -->
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الصفحة الرئيسية</a></li>
                <li class="breadcrumb-item"><a href="{{ route('resignation-requests.index') }}">طلبات الاستقالة</a></li>
                <li class="breadcrumb-item">عرض الطلب</li>
            </ul>
        </div>
        <div class="page-header-right ms-auto">
            <div class="page-header-right-items">
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('resignation-requests.index') }}" class="btn btn-light">
                        <i class="feather-arrow-right me-2"></i>
                        <span>رجوع</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <!-- [ page-header ] end -->

    <!-- [ Main Content ] start -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">تفاصيل طلب الاستقالة</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">اسم الموظف</label>
                                <p class="form-control-plaintext">
                                    @if($resignation->employee)
                                        {{ $resignation->employee->name }}
                                    @elseif($resignation->representative)
                                        {{ $resignation->representative->name }}
                                     @elseif($resignation->supervisor)
                                        {{ $resignation->supervisor->name }}
                                    @else
                                        غير محدد
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">رقم الهاتف</label>
                                <p class="form-control-plaintext">
                                    @if($resignation->employee)
                                        {{ $resignation->employee->phone }}
                                    @elseif($resignation->representative)
                                        {{ $resignation->representative->phone }}
                                     @elseif($resignation->supervisor)
                                        {{ $resignation->supervisor->phone }}
                                    @else
                                        غير محدد
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">تاريخ الاستقالة</label>
                                <p class="form-control-plaintext">{{ $resignation->resignation_date ? \Carbon\Carbon::parse($resignation->resignation_date)->format('Y-m-d') : 'غير محدد' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">الحالة</label>
                                <p class="form-control-plaintext">
                                    @php
                                        $statusColors = [
                                            'pending' => 'bg-warning text-dark',
                                            'approved' => 'bg-success text-white',
                                            'rejected' => 'bg-danger text-white'
                                        ];
                                        $statusTexts = [
                                            'pending' => 'في الانتظار',
                                            'approved' => 'موافق عليه',
                                            'rejected' => 'مرفوض'
                                        ];
                                        $statusColor = $statusColors[$resignation->status] ?? 'bg-secondary text-white';
                                        $statusText = $statusTexts[$resignation->status] ?? $resignation->status;
                                    @endphp
                                    <span class="badge {{ $statusColor }}">{{ $statusText }}</span>
                                </p>
                            </div>
                        </div>
                        @if($resignation->reason)
                        <div class="col-12">
                            <div class="mb-3">
                                <label class="form-label fw-bold">سبب الاستقالة</label>
                                <p class="form-control-plaintext">{{ $resignation->reason }}</p>
                            </div>
                        </div>
                        @endif
                        @if($resignation->rejection_reason)
                        <div class="col-12">
                            <div class="mb-3">
                                <label class="form-label fw-bold">سبب الرفض</label>
                                <p class="form-control-plaintext text-danger">{{ $resignation->rejection_reason }}</p>
                            </div>
                        </div>
                        @endif
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">تاريخ التقديم</label>
                                <p class="form-control-plaintext">{{ $resignation->created_at->format('Y-m-d H:i') }}</p>
                            </div>
                        </div>
                        @if($resignation->approved_at)
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">تاريخ الموافقة/الرفض</label>
                                <p class="form-control-plaintext">{{ \Carbon\Carbon::parse($resignation->approved_at)->format('Y-m-d H:i') }}</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">الإجراءات</h5>
                </div>
                <div class="card-body">
                    @if($resignation->status === 'pending')
                        @can('approve_resignation_requests')
                        <form action="{{ route('resignation-requests.approve', $resignation->id) }}" method="POST" class="mb-3">
                            @csrf
                            <button type="submit" class="btn btn-success w-100" onclick="return confirm('هل أنت متأكد من الموافقة على طلب الاستقالة؟')">
                                <i class="feather-check me-2"></i>
                                الموافقة على الاستقالة
                            </button>
                        </form>
                        @endcan

                        @can('approve_resignation_requests')
                        <button type="button" class="btn btn-danger w-100" data-bs-toggle="modal" data-bs-target="#rejectModal">
                            <i class="feather-x me-2"></i>
                            رفض الطلب
                        </button>
                        @endcan
                    @endif
                </div>
            </div>
        </div>
    </div>
    <!-- [ Main Content ] end -->

    <!-- Reject Modal -->
    @can('approve_resignation_requests')
    <div class="modal fade" id="rejectModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">رفض طلب الاستقالة</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('resignation-requests.reject', $resignation->id) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="rejection_reason" class="form-label">سبب الرفض</label>
                            <textarea name="rejection_reason" id="rejection_reason" class="form-control" rows="4" required placeholder="أدخل سبب رفض الطلب..."></textarea>
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
    @endcan
@endsection

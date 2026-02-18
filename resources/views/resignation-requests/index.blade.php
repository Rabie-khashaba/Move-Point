@extends('layouts.app')

@section('title', 'طلبات الاستقالة')

@section('content')
<div class="nxl-content">
    <!-- [ page-header ] start -->
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
                <li class="breadcrumb-item">طلبات الاستقالة</li>
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
                    <a href="{{ route('resignation-requests.export') }}" class="btn btn-success">
                        <i class="feather-download me-2"></i>
                        <span>تصدير Excel</span>
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

    <div id="collapseOne" class="accordion-collapse show collapse page-header-collapse ">
            <div class="accordion-body ">
                <div class="row">
                    <div class="col-xxl-3 col-md-6 mb-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="avatar-text avatar-xl rounded">
                                            <i class="feather-file-text"></i>
                                        </div>
                                        <a href="javascript:void(0);" class="fw-bold d-block">
                                            <span class="d-block">الإجمالي</span>
                                            <span class="fs-24 fw-bolder d-block" id="totalResignations">{{ $totalResignations }}</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{--<div class="col-xxl-3 col-md-6 mb-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="avatar-text avatar-xl rounded bg-warning">
                                            <i class="feather-clock"></i>
                                        </div>
                                        <a href="javascript:void(0);" class="fw-bold d-block text-warning">
                                            <span class="d-block">في الانتظار</span>
                                            <span class="fs-24 fw-bolder d-block" id="pendingResignations">{{ $pendingResignations }}</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xxl-3 col-md-6 mb-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="avatar-text avatar-xl rounded bg-success">
                                            <i class="feather-check-circle"></i>
                                        </div>
                                        <a href="javascript:void(0);" class="fw-bold d-block text-success">
                                            <span class="d-block">تمت الموافقة</span>
                                            <span class="fs-24 fw-bolder d-block" id="approvedResignations">{{ $approvedResignations }}</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xxl-3 col-md-6 mb-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="avatar-text avatar-xl rounded bg-danger">
                                            <i class="feather-x-circle"></i>
                                        </div>
                                        <a href="javascript:void(0);" class="fw-bold d-block text-danger">
                                            <span class="d-block">مرفوض</span>
                                            <span class="fs-24 fw-bolder d-block" id="rejectedResignations">{{ $rejectedResignations }}</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    --}}
                    @php
                        $companyCardStyles = [
                            ['bg' => 'bg-primary', 'text' => 'text-primary'],
                            ['bg' => 'bg-success', 'text' => 'text-success'],
                            ['bg' => 'bg-info', 'text' => 'text-info'],
                            ['bg' => 'bg-warning', 'text' => 'text-warning'],
                            ['bg' => 'bg-danger', 'text' => 'text-danger'],
                            ['bg' => 'bg-dark', 'text' => 'text-dark'],
                        ];
                    @endphp
                    @foreach($companyResignationStats as $companyStat)
                        @php
                            $style = $companyCardStyles[$loop->index % count($companyCardStyles)];
                        @endphp
                        <div class="col-xxl-3 col-md-6 mb-3">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="avatar-text avatar-xl rounded {{ $style['bg'] }}">
                                                <i class="feather-briefcase"></i>
                                            </div>
                                            <a href="javascript:void(0);" class="fw-bold d-block {{ $style['text'] }}">
                                                <span class="d-block">العدد في شركة {{ $companyStat['name'] }}</span>
                                                <span class="fs-24 fw-bolder d-block" id="companyResignations{{ $companyStat['id'] }}">{{ $companyStat['count'] }}</span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

    <!-- Filter Collapse -->
    <div class="collapse show" id="filterCollapse">
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('resignation-requests.index') }}" class="row g-3">
                    <div class="col-md-2">
                        <label class="form-label">البحث</label>
                        <input type="text" name="search" class="form-control" placeholder="ابحث عن الموظفين..." value="{{ request('search') }}">
                    </div>

                     <div class="col-md-2">
                        <label class="form-label">تاريخ الطلب من</label>
                        <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">تاريخ الطلب إلى</label>
                        <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">الحالة</label>
                        <select name="status" class="form-control">
                            <option value="">جميع الحالات</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>في الانتظار</option>
                            <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>تمت الموافقة</option>
                            <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>مرفوض</option>
                            <option value="unresign" {{ request('status') === 'unresign' ? 'selected' : '' }}>تم الرجوع للعمل</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">المصدر</label>
                        <select name="source" class="form-control">
                            <option value="">جميع المصادر</option>
                            <option value="training_session" {{ request('source') === 'training_session' ? 'selected' : '' }}>محاضرات التدريب</option>
                            <option value="work_start" {{ request('source') === 'work_start' ? 'selected' : '' }}>بدء العمل</option>
                            <option value="app" {{ request('source') === 'app' ? 'selected' : '' }}>التطبيق</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">الشركة</label>
                        <select name="company_id" class="form-control">
                            <option value="">جميع الشركات</option>
                            @foreach($companies as $company)
                                <option value="{{ $company->id }}" {{ request('company_id') == $company->id ? 'selected' : '' }}>
                                    {{ $company->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">تصفية</button>
                        <a href="{{ route('resignation-requests.index') }}" class="btn btn-light">مسح</a>
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
                        <h5 class="card-title mb-0">قائمة طلبات الاستقالة</h5>
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

                        @if($resignations->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>الموظف</th>
                                             <th>الشركه</th>
                                            <!--<th>القسم</th>-->
                                            <!--<th>تاريخ الاستقالة</th>-->
                                            <!--<th>آخر يوم عمل</th>-->
                                            <!--<th>فترة الإشعار</th>-->
                                            <th>السبب</th>
                                            <th>المصدر</th>
                                             <th>المديونية</th>
                                            <th>الحالة</th>
                                            <th>تاريخ الطلب</th>
                                            <th>الإجراءات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($resignations as $resignation)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">

                                                    <div>
                                                        @php
                                                            $requesterName = $resignation->employee?->name ?? $resignation->representative?->name ?? $resignation->supervisor?->name ?? 'غير محدد';
                                                            $requesterCode = $resignation->employee?->code ?? $resignation->representative?->code ?? $resignation->supervisor?->code ?? 'غير محدد';
                                                        @endphp
                                                        <h6 class="mb-0">{{ $requesterName }}</h6>
                                                        <small class="text-muted">{{ $requesterCode }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-info">{{ $resignation->employee?->company?->name ??
                                                $resignation->representative?->company?->name ?? $resignation->supervisor?->company?->name ?? 'غير محدد'
                                                }}</span>
                                            </td>

                                            <!--<td>{{ $resignation->employee?->department?->name ?? 'غير محدد' }}</td>-->
                                            <!--<td>{{ $resignation->resignation_date ? $resignation->resignation_date->format('Y-m-d') : '-' }}</td>-->
                                            <!--<td>{{ $resignation->last_working_day ? $resignation->last_working_day->format('Y-m-d') : '-' }}</td>-->
                                            <!--<td>{{ $resignation->notice_period }} يوم</td>-->
                                            <td>
                                                <span class="text-truncate" style="max-width: 200px;" title="{{ $resignation->reason }}">
                                                    {{ Str::limit($resignation->reason, 60) }}
                                                </span>
                                            </td>
                                            <td>
                                                @if ($resignation->source === 'training_session')
                                                    <span class="badge bg-info">محاضرات التدريب</span>
                                                @elseif ($resignation->source === 'work_start')
                                                    <span class="badge bg-success">بدء العمل</span>
                                                @else
                                                    <span class="badge bg-warning"> APP</span>
                                                @endif
                                            </td>
                                            <td>
                                                @php
                                                    $hasUnpaidDebt =
                                                        ($resignation->employee && $resignation->employee->debits->where('status', 'لم يسدد')->isNotEmpty()) ||
                                                        ($resignation->representative && $resignation->representative->debits->where('status', 'لم يسدد')->isNotEmpty()) ||
                                                        ($resignation->supervisor && $resignation->supervisor->debits->where('status', 'لم يسدد')->isNotEmpty());


                                                    $debit =
                                                    $resignation->employee?->debits->where('status', 'لم يسدد')->first()
                                                    ?? $resignation->representative?->debits->where('status', 'لم يسدد')->first()
                                                    ?? $resignation->supervisor?->debits->where('status', 'لم يسدد')->first();
                                                @endphp

                                                @if($debit)
                                                <span class="badge bg-info">{{ $debit->loan_amount }}</span>
                                                @else
                                                    <span class="badge bg-success">لا توجد مديونية</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($resignation->status === 'pending')
                                                    <span class="badge bg-warning">في الانتظار</span>
                                                @elseif($resignation->status === 'approved')
                                                    <span class="badge bg-success">تمت الموافقة</span>
                                                @elseif($resignation->status === 'unresign')
                                                    <span class="badge bg-success">تم الرجوع للعمل</span>
                                                @else
                                                    <span class="badge bg-danger">مرفوض</span>
                                                @endif
                                            </td>
                                            <td>{{ $resignation->created_at ? $resignation->created_at->format('Y-m-d') : '-' }}</td>
                                            <td>

                                                 @php
                                                    $person =
                                                        $resignation->representative
                                                        ?? $resignation->employee
                                                        ?? $resignation->supervisor;
                                                @endphp
                                                <div class="dropdown">
                                                    <button class="btn btn-sm btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                        <i class="feather-more-horizontal"></i>
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        @can('view_resignation_requests')
                                                        <li><a class="dropdown-item" href="{{ route('resignation-requests.show', $resignation->id) }}">
                                                            <i class="feather-eye me-2"></i>عرض
                                                        </a></li>


                                                        <li>
                                                            <button type="button"
                                                                    class="dropdown-item"
                                                                    onclick="viewResignationDetails(this)"
                                                                    data-id="{{ $resignation->id }}"
                                                                    data-name="{{ $requesterName }}"
                                                                    data-phone="{{ $resignation->employee?->phone ?? $resignation->representative?->phone ?? $resignation->supervisor?->phone ?? '' }}"
                                                                    data-status="{{ $resignation->status_text }}"
                                                                    data-status-code="{{ $resignation->status }}"
                                                                    data-reason="{{ e($resignation->reason) }}"
                                                                    data-resign-date="{{ $resignation->resignation_date ? $resignation->resignation_date->format('Y-m-d') : '-' }}"
                                                                    data-last-day="{{ $resignation->last_working_day ? $resignation->last_working_day->format('Y-m-d') : '-' }}"
                                                                    data-hasunpaid="{{ $hasUnpaidDebt ? '1' : '0' }}">
                                                                <i class="feather-file-text me-2"></i>تفاصيل الطلب
                                                            </button>
                                                        </li>
                                                        @endcan

                                                        @if($resignation->status === 'pending')
                                                            @can('approve_resignation_requests')
                                                            <li>
                                                                {{-- <form action="{{ route('resignation-requests.approve', $resignation->id) }}" method="POST" class="d-inline">
                                                                    @csrf
                                                                    <button type="submit" class="dropdown-item text-success" onclick="return confirm('هل أنت متأكد من الموافقة على الاستقالة؟ سيتم إلغاء تفعيل الموظف.')">
                                                                        <i class="feather-check me-2"></i>موافقة
                                                                    </button>
                                                                </form>  --}}

                                                                <button type="button"
                                                                        class="dropdown-item text-success"
                                                                        data-bs-toggle="modal"
                                                                        data-bs-target="#approveModal"
                                                                        data-id="{{ $resignation->id }}"
                                                                        data-hasunpaid="{{ $hasUnpaidDebt ? '1' : '0' }}">
                                                                    <i class="feather-check me-2"></i>موافقة
                                                                </button>
                                                            </li>
                                                            <li>
                                                                <a class="dropdown-item text-danger" href="#" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $resignation->id }}">
                                                                    <i class="feather-x me-2"></i>رفض
                                                                </a>
                                                            </li>
                                                            @endcan
                                                        @endif

                                                        @if($person && $person->is_active === false)
                                                        <li>
                                                            @can('edit_representatives')
                                                                <button
                                                                    type="button"
                                                                    class="dropdown-item text-{{ $person->is_active ? 'danger' : 'success' }} d-flex align-items-center openStatusModal"
                                                                    data-id="{{ $person->id }}"
                                                                    data-type="{{ class_basename($person) }}"
                                                                    data-bs-toggle="modal"
                                                                    data-bs-target="#StatusModal"
                                                                    title="{{ $person->is_active ? 'استقالة' : 'تفعيل' }}"
                                                                >
                                                                    <i class="feather-{{ $person->is_active ? 'pause' : 'play' }} me-2"></i>
                                                                    <span>{{ $person->is_active ? 'استقالة' : 'تفعيل' }}</span>
                                                                </button>
                                                            @endcan
                                                        </li>
                                                        @endif

                                                        @can('delete_resignation_requests')
                                                        <li>
                                                            <form action="{{ route('resignation-requests.destroy', $resignation->id) }}" method="POST" class="d-inline">
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
                                        @can('approve_resignation_requests')
                                        @if($resignation->status === 'pending')
                                        <div class="modal fade" id="rejectModal{{ $resignation->id }}" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">رفض طلب الاستقالة</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <form action="{{ route('resignation-requests.reject', $resignation->id) }}" method="POST">
                                                        @csrf
                                                        <div class="modal-body">
                                                            <div class="alert alert-info">
                                                                @php
                                                                    $requesterName = $resignation->employee?->name ?? $resignation->representative?->name ?? $resignation->supervisor?->name ?? 'غير محدد';
                                                                @endphp
                                                                <strong>الموظف:</strong> {{ $requesterName }}<br>
                                                                <strong>تاريخ الاستقالة:</strong> {{ $resignation->resignation_date ? $resignation->resignation_date->format('Y-m-d') : '-' }}
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label">سبب الرفض <span class="text-danger">*</span></label>
                                                                <textarea name="rejection_reason" class="form-control" rows="3" required placeholder="أدخل سبب رفض طلب الاستقالة..."></textarea>
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
                                {{ $resignations->appends(request()->query())->links('pagination::bootstrap-5') }}
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="feather-user-minus fs-48 text-muted mb-3"></i>
                                <h5 class="text-muted">لا توجد طلبات استقالة</h5>
                                <p class="text-muted">لم يتم تقديم أي طلبات استقالة بعد</p>
                                <small class="text-muted">يتم تقديم طلبات الاستقالة من التطبيق فقط</small>
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


@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const approveModal = document.getElementById('approveModal');
        approveModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const id = button.getAttribute('data-id');
            const hasUnpaid = button.getAttribute('data-hasunpaid') === '1';

            const form = approveModal.querySelector('form');
            form.action = "{{ route('resignation-requests.approve', '__id__') }}".replace('__id__', id);

            const paymentGroup = approveModal.querySelector('.payment-location-group');
            paymentGroup.style.display = hasUnpaid ? 'block' : 'none';
            const paymentDueInput = approveModal.querySelector('[name=\"payment_location_due\"]');
            paymentDueInput.required = hasUnpaid;
        });
    });


     function viewResignationDetails(button) {
        const resignationId = button.getAttribute('data-id');
        const name = button.getAttribute('data-name') || 'غير محدد';
        const phone = button.getAttribute('data-phone') || 'غير محدد';
        const status = button.getAttribute('data-status') || 'غير محدد';
        const statusCode = button.getAttribute('data-status-code') || '';
        const reason = button.getAttribute('data-reason') || 'غير متوفر';
        const resignDate = button.getAttribute('data-resign-date') || '-';
        const lastDay = button.getAttribute('data-last-day') || '-';
        const hasUnpaid = button.getAttribute('data-hasunpaid') === '1';

        const html = `
            <div class="row">
                <div class="col-md-6">
                    <h6>معلومات مقدم الطلب</h6>
                    <p><strong>الاسم:</strong> ${name}</p>
                    <p><strong>الهاتف:</strong> ${phone}</p>
                </div>
                <div class="col-md-6">
                    <h6>تفاصيل الطلب</h6>
                    <p><strong>تاريخ الاستقالة:</strong> ${resignDate}</p>
                    <p><strong>آخر يوم عمل:</strong> ${lastDay}</p>
                    <p><strong>حالة الطلب:</strong> ${status}</p>
                    <p><strong>المديونية:</strong> ${hasUnpaid ? 'عليه مديونية غير مسددة' : 'لا توجد مديونية'}</p>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-12">
                    <h6 class="mb-3">السبب</h6>
                    <div class="alert alert-light">
                        ${reason}
                    </div>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-12">
                    <h6 class="mb-3">الملاحظات</h6>
                    <div id="notesContainer" style="max-height: 300px; overflow-y: auto; margin-bottom: 20px;">
                        <div class="text-center py-3">
                            <div class="spinner-border spinner-border-sm" role="status">
                                <span class="visually-hidden">جاري التحميل...</span>
                            </div>
                            <p class="text-muted mt-2">جاري تحميل الملاحظات...</p>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">إضافة ملاحظة جديدة <span class="text-danger">*</span></label>
                        <textarea id="newNoteText" class="form-control" rows="3" placeholder="أدخل ملاحظة جديدة....."></textarea>
                    </div>

                    <button type="button" class="btn btn-primary" onclick="saveNote(${resignationId})">
                        <i class="feather-save me-2"></i>حفظ الملاحظة
                    </button>
                </div>
            </div>
        `;

        const body = document.getElementById('resignationDetailsContent');
        if (body) {
            body.innerHTML = html;
        }

        const modalEl = document.getElementById('resignationDetailsModal');
        if (modalEl) {
            const modal = new bootstrap.Modal(modalEl);
            modal.show();

            // تحميل الملاحظات بعد فتح المودال
            loadNotes(resignationId);
        }
    }

    function loadNotes(resignationId) {
        const notesContainer = document.getElementById('notesContainer');

        fetch(`{{ url('resignation-requests') }}/${resignationId}/notes`)
            .then(res => res.json())
            .then(data => {
                if (data.success && data.notes.length > 0) {
                    let notesHtml = '';
                    data.notes.forEach(note => {
                        const statusBadge = note.status_text ?
                            `<span class="badge bg-${note.status === 'approved' ? 'success' : 'danger'} ms-2">${note.status_text}</span>` : '';
                        notesHtml += `
                            <div class="card mb-3">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div>
                                            <strong>${note.created_by}</strong>
                                            ${statusBadge}
                                            <small class="text-muted ms-2">${note.created_at_formatted}</small>
                                        </div>
                                        <small class="text-muted">${note.created_at}</small>
                                    </div>
                                    <p class="mb-0">${note.note.replace(/\n/g, '<br>')}</p>
                                </div>
                            </div>
                        `;
                    });
                    notesContainer.innerHTML = notesHtml;
                } else {
                    notesContainer.innerHTML = '<div class="text-center py-3 text-muted">لا توجد ملاحظات بعد</div>';
                }
            })
            .catch(err => {
                console.error(err);
                notesContainer.innerHTML = '<div class="alert alert-danger">حدث خطأ أثناء تحميل الملاحظات</div>';
            });
    }

    function saveNote(resignationId) {
        const noteText = document.getElementById('newNoteText').value.trim();

        if (!noteText) {
            alert('يرجى إدخال ملاحظة');
            document.getElementById('newNoteText').focus();
            return;
        }

        const btn = event.target;
        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>جاري الحفظ...';

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

        fetch(`{{ url('resignation-requests') }}/${resignationId}/notes`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                note: noteText
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                document.getElementById('newNoteText').value = '';
                // loadNotes(resignationId);
                // btn.disabled = false;
                // btn.innerHTML = originalText;

                // // إذا تم تحديث الحالة، إعادة تحميل الصفحة
                // if (data.note.status) {
                //     setTimeout(() => {
                //         window.location.reload();
                //     }, 1000);
                // }

                 window.location.reload();
            } else {
                alert(data.message || 'حدث خطأ أثناء حفظ الملاحظة');
                btn.disabled = false;
                btn.innerHTML = originalText;
            }
        })
        .catch(err => {
            console.error(err);
            alert('حدث خطأ أثناء حفظ الملاحظة');
            btn.disabled = false;
            btn.innerHTML = originalText;
        });
    }

    function updateStatus(resignationId, status, originalStatus) {
        if (!status) {
            return;
        }

        const statusText = status === 'approved' ? 'موافق' : 'غير موافق';
        if (!confirm(`هل أنت متأكد من تغيير الحالة إلى "${statusText}"؟`)) {
            // إعادة تعيين القيمة السابقة
            const select = document.getElementById('statusSelect');
            select.value = originalStatus || '';
            return;
        }

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
        const select = document.getElementById('statusSelect');
        const originalValue = originalStatus || '';
        select.disabled = true;

        fetch(`{{ url('resignation-requests') }}/${resignationId}/update-status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                status: status
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert('تم تحديث الحالة بنجاح');
                // إعادة تحميل الصفحة لتحديث البيانات
                window.location.reload();
            } else {
                alert(data.message || 'حدث خطأ أثناء تحديث الحالة');
                select.value = originalValue;
                select.disabled = false;
            }
        })
        .catch(err => {
            console.error(err);
            alert('حدث خطأ أثناء تحديث الحالة');
            select.value = originalValue;
            select.disabled = false;
        });
    }




document.addEventListener('DOMContentLoaded', function () {

    const modal = document.getElementById('StatusModal');
    const form  = document.getElementById('statusForm');
    const govSelect = document.getElementById('status_governorate_id');
    const locSelect = document.getElementById('status_location_id');

    // عند فتح المودال
    modal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const repId = button.getAttribute('data-id');
        const type = button.getAttribute('data-type');

        form.querySelector('input[name="representative_id"]').value = repId;
        form.querySelector('input[name="type"]').value = type;

        // اضبط الفورم ليعمل POST للرابط مباشرة
        form.action = "{{ url('resignation-requests') }}/" + repId + "/toggle-status";
    });

    // تحميل المناطق من المحافظة
    if (govSelect) {
        govSelect.addEventListener('change', function () {
            const govId = this.value;

            if (!govId) {
                locSelect.innerHTML = '<option value="">اختر المحافظة أولاً</option>';
                return;
            }

            fetch("{{ url('getlocations') }}/" + govId)
                .then(res => res.json())
                .then(data => {
                    locSelect.innerHTML = '<option value="">اختر المنطقة</option>';
                    data.forEach(loc => {
                        locSelect.innerHTML += `<option value="${loc.id}">${loc.name}</option>`;
                    });
                })
                .catch(err => {
                    console.error(err);
                    locSelect.innerHTML = '<option value="">خطأ في تحميل المناطق</option>';
                });
        });
    }

});
</script>
@endsection



<!--notes-->
<div class="modal fade" id="resignationDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">تفاصيل طلب الاستقالة</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="resignationDetailsContent">
                <!-- سيتم تحميل التفاصيل هنا -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">إغلاق</button>
            </div>
        </div>
    </div>
</div>


<!-- Modal -->
    <div class="modal fade" id="StatusModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md"> <!-- توسيط المودال وتحديد الحجم -->
            <div class="modal-content">

                <!-- Header -->
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title mb-0">تغيير حالة المندوب</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <!-- Body -->
                <form id="statusForm" method="POST">
                    @csrf
                    <input type="hidden" name="representative_id">
                    <input type="hidden" name="type">
                    <div class="modal-body">

                        <div class="mb-3">
                            <label for="status_governorate_id" class="form-label fw-bold">المحافظة</label>
                            <select id="status_governorate_id" name="governorate_id" class="form-select">
                                <option value="">اختر المحافظة</option>
                                @foreach ($governorates as $gov)
                                    <option value="{{ $gov->id }}">{{ $gov->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="status_location_id" class="form-label fw-bold">المنطقة</label>
                            <select id="status_location_id" name="location_id" class="form-select">
                                <option value="">اختر المحافظة أولاً</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="status_company_id" class="form-label fw-bold">الشركة</label>
                            <select id="status_company_id" name="company_id" class="form-select">
                                <option value="">اختر الشركة</option>
                                @foreach ($companies as $comp)
                                    <option value="{{ $comp->id }}">{{ $comp->name }}</option>
                                @endforeach
                            </select>
                        </div>

                    </div>

                    <!-- Footer -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-primary">حفظ</button>
                    </div>
                </form>

            </div>
        </div>
    </div>

<!-- Approval Modal -->
<div class="modal fade" id="approveModal" tabindex="-1" aria-labelledby="approveModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="approveModalLabel">تأكيد الموافقة</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="#">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">التاريخ</label>
                        <input type="datetime-local" name="appointment_date" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">العنوان / المكان</label>
                        <input type="text" name="payment_location" class="form-control" placeholder="أدخل العنوان أو مكان الاستلام/التسديد" required>
                    </div>


                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-success">تأكيد الموافقة</button>
                </div>
            </form>
        </div>
    </div>
</div>

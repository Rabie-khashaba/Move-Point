@extends('layouts.app')

@section('title', 'المندوبين')

@section('content')
    <div class="nxl-content">
        <!-- [ page-header ] start -->
        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">المندوبين المنتظرين</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
                    <li class="breadcrumb-item">المندوبين المنتظرين</li>
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
                        <a href="javascript:void(0);" class="btn btn-icon btn-light-brand" data-bs-toggle="collapse"
                            data-bs-target="#filterCollapse">
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


        <div id="collapseOne" class="accordion-collapse show  collapse page-header-collapse mb-4">
            <div class="accordion-body pb-2">
                <div class="row">
                    <div class="col-xxl-2 col-md-4 mb-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="avatar-text avatar-xl rounded">
                                            <i class="feather-users"></i>
                                        </div>
                                        <a href="javascript:void(0);" class="fw-bold d-block">
                                            <span class="d-block">الاجمالي</span>
                                            <span class="fs-24 fw-bolder d-block"
                                                id="totalLeads">{{ $totalRepresentatives }}</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xxl-2 col-md-4 mb-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="avatar-text avatar-xl rounded bg-primary">
                                            <i class="feather-user-check"></i>
                                        </div>
                                        <a href="javascript:void(0);" class="fw-bold d-block text-blue">
                                            <span class="d-block">شركه نون</span>
                                            <span class="fs-24 fw-bolder d-block"
                                                id="activeLeads">{{ $NoonRepresentatives }}</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xxl-2 col-md-4 mb-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="avatar-text avatar-xl rounded bg-info">
                                            <i class="feather-user-plus"></i>
                                        </div>
                                        <a href="javascript:void(0);" class="fw-bold d-block text-black">
                                            <span class="d-block">شركه بوسطه</span>
                                            <span class="fs-24 fw-bolder d-block"
                                                id="qualifiedLeads">{{ $BoostaRepresentatives }}</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xxl-2 col-md-4 mb-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="avatar-text avatar-xl rounded bg-success">
                                            <i class="feather-activity"></i>
                                        </div>
                                        <a href="javascript:void(0);" class="fw-bold d-block text-success">
                                            <span class="d-block">مرضي</span>
                                            <span class="fs-24 fw-bolder d-block">{{ $postponeReasonSick }}</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xxl-2 col-md-4 mb-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="avatar-text avatar-xl rounded bg-warning">
                                            <i class="feather-alert-circle"></i>
                                        </div>
                                        <a href="javascript:void(0);" class="fw-bold d-block text-warning">
                                            <span class="d-block">الـ zone مقفول</span>
                                            <span class="fs-24 fw-bolder d-block">{{ $postponeReasonZoneClosed }}</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xxl-2 col-md-4 mb-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="avatar-text avatar-xl rounded bg-secondary">
                                            <i class="feather-help-circle"></i>
                                        </div>
                                        <a href="javascript:void(0);" class="fw-bold d-block text-secondary">
                                            <span class="d-block">اخري</span>
                                            <span class="fs-24 fw-bolder d-block">{{ $postponeReasonOther }}</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                </div>
            </div>
        </div>

        <!-- Filter Collapse -->
        <div class="collapse show" id="filterCollapse">
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('waiting-representatives.index') }}" class="row g-3">
                        <div class="col-md-2">
                            <label class="form-label">البحث</label>
                            <input type="text" name="search" class="form-control" placeholder="البحث في المندوبين..."
                                value="{{ request('search') }}">
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">من تاريخ</label>
                            <input type="date" name="date_from"
                                class="form-control {{ request('date_from') ? 'filter-active' : '' }}"
                                value="{{ request('date_from') }}">
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">إلى تاريخ</label>
                            <input type="date" name="date_to"
                                class="form-control {{ request('date_to') ? 'filter-active' : '' }}"
                                value="{{ request('date_to') }}">
                        </div>
                        {{-- <div class="col-md-2">
                            <label class="form-label">النوع</label>
                            <select name="type" class="form-control">
                                <option value="">جميع الأنواع</option>
                                <option value="أونلاين" {{ request('type')=='أونلاين' ? 'selected' : '' }}>أونلاين</option>
                                <option value=" офلاين" {{ request('type')==' офلاين' ? 'selected' : '' }}> офلاين</option>
                            </select>
                        </div> --}}
                        <div class="col-md-2">
                            <label class="form-label">الشركة</label>
                            <select name="company_id" class="form-control">
                                <option value="">جميع الشركات</option>
                                @foreach(\App\Models\Company::where('is_active', true)->get() as $company)
                                    <option value="{{ $company->id }}" {{ request('company_id') == $company->id ? 'selected' : ''
                                                                                                                                                                                                                                                                                                                                                                    }}>
                                        {{ $company->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">المحافظة</label>
                            <select name="governorate_id" id="governorate_id" class="form-control">
                                <option value="">جميع المحافظات</option>
                                @foreach(\App\Models\Governorate::all() as $governorate)
                                    <option value="{{ $governorate->id }}"
                                        {{ request('governorate_id') == $governorate->id ? 'selected' : '' }}>
                                        {{ $governorate->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">سبب التأجيل</label>
                            <select name="postpone_reason" class="form-control">
                                <option value="">جميع الأسباب</option>
                                <option value="مرضي" {{ request('postpone_reason') == 'مرضي' ? 'selected' : '' }}>مرضي</option>
                                <option value="الـ zone مقفول" {{ request('postpone_reason') == 'الـ zone مقفول' ? 'selected' : '' }}>الـ zone مقفول</option>
                                <option value="اخرى" {{ request('postpone_reason') == 'اخرى' ? 'selected' : '' }}>اخرى</option>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">حالة المتابعة</label>
                            <select name="followup_status" class="form-control">
                                <option value="">جميع الحالات</option>
                                <option value="لم يرد" {{ request('followup_status') == 'لم يرد' ? 'selected' : '' }}>لم يرد</option>
                                <option value="متابعة مره اخري" {{ request('followup_status') == 'متابعة مره اخري' ? 'selected' : '' }}>متابعة مره اخري</option>
                                <option value="تغيير الشركه" {{ request('followup_status') == 'تغيير الشركه' ? 'selected' : '' }}>تغيير الشركه</option>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">المنطقة</label>
                            <select name="location_id" id="location_id" class="form-control">
                                <option value="">جميع المناطق</option>
                                @foreach(\App\Models\Location::all() as $location)
                                    <option value="{{ $location->id }}"
                                        data-governorate="{{ $location->governorate_id }}"
                                        {{ request('location_id') == $location->id ? 'selected' : '' }}>
                                        {{ $location->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary me-2">تصفية</button>
                            <a href="{{ route('waiting-representatives.index') }}" class="btn btn-light">مسح</a>
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
                            <h5 class="card-title mb-0">قائمه بدء العمل</h5>
                        </div>
                        <div class="card-body">
                            @if(session('success'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    {{ session('success') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            @endif

                            @if($waitings->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>اسم المندوب</th>
                                                <th>المحافظة</th>
                                                <th>الشركة</th>
                                                <th>المصدر</th>
                                                <th>سبب التأجيل</th>
                                                <th>الحالة</th>
                                                <th>تاريخ آخر متابعة</th>
                                                <th>الإجراءات</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($waitings as $waiting)
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="avatar-text avatar-sm rounded-circle bg-primary me-3">
                                                                <i class="feather-user"></i>
                                                            </div>
                                                            <div>
                                                                <h6 class="mb-0">
                                                                    <a
                                                                        href="{{ route('representatives-not-completed.show', $waiting->representative->id) }}">
                                                                        {{ $waiting->representative->name }}
                                                                    </a>
                                                                </h6>
                                                                <small
                                                                    class="text-muted">{{ $waiting->representative->phone }}</small>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            {{ $waiting->representative->governorate->name }} <br>
                                                            {{ $waiting->representative->location->name }}
                                                        </div>
                                                    </td>

                                                    <td>
                                                        <span
                                                            class="badge bg-info">{{ $waiting->representative->company->name ?? 'غير محدد' }}</span>
                                                    </td>
                                                    <td>
                                                        @if($waiting->source === 'training')
                                                            محاضرات التدريب
                                                        @elseif($waiting->source === 'work_start')
                                                            بدء العمل
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        {{ $latestPostponeReasons[$waiting->representative_id] ?? '-' }}
                                                    </td>
                                                    <td>
                                                        {{ $latestFollowupStatuses[$waiting->id] ?? '-' }}
                                                    </td>
                                                    @php($lastFollowupDate = $latestFollowupDates[$waiting->id] ?? null)
                                                    @php($lastPostponeDate = $latestPostponeDates[$waiting->representative_id] ?? $waiting->date)
                                                    <td>
                                                        @if($lastFollowupDate)
                                                            {{ \Carbon\Carbon::parse($lastFollowupDate)->format('d/m/Y') }}
                                                        @elseif($lastPostponeDate)
                                                            {{ \Carbon\Carbon::parse($lastPostponeDate)->format('d/m/Y') }}
                                                        @else
                                                            -
                                                        @endif
                                                    </td>

                                                    <td class="d-flex flex-wrap gap-2">
                                                        <button type="button" class="btn btn-sm btn-info" title="رسالة بدء العمل"
                                                            data-bs-toggle="modal" data-bs-target="#interviewModal"
                                                            data-id="{{ $waiting->representative_id}}">

                                                            رساله بدء العمل
                                                        </button>

                                                        <button type="button" class="btn btn-sm btn-warning" title="تغير المنطقه"
                                                            data-bs-toggle="modal" data-bs-target="#interviewModal2"
                                                            data-id="{{ $waiting->representative_id}}">

                                                            تغير المنطقه
                                                        </button>

                                                        <button type="button"
                                                            class="btn btn-sm btn-outline-success openFollowupModal"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#followupModal"
                                                            data-waiting-id="{{ $waiting->id }}">
                                                            متابعة
                                                        </button>

                                                        <button type="button"
                                                            class="btn btn-sm btn-outline-danger openResignModal"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#resignModal"
                                                            data-rep-id="{{ $waiting->representative_id }}">
                                                            استقاله
                                                        </button>


                                                    </td>
                                                </tr>

                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Pagination -->
                                @if($waitings->hasPages())
                                    <div class="d-flex justify-content-center mt-4">
                                        {{ $waitings->links('pagination::bootstrap-5') }}
                                    </div>
                                @endif
                            @else
                                <div class="text-center py-5">
                                    <div class="avatar-text avatar-xl mx-auto mb-3">
                                        <i class="feather-user-plus"></i>
                                    </div>
                                    <h5>لا توجد نتائج</h5>
                                    <!-- <p class="text-muted">ابدأ بإضافة أول مندوب.</p>
                                                                                                                                                                                                            <a href="{{ route('representatives-not-completed.create') }}" class="btn btn-primary">
                                                                                                                                                                                                                <i class="feather-plus me-2"></i>إضافة مندوب
                                                                                                                                                                                                            </a> -->
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="interviewModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">رساله بدئ العمل</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="interviewForm" method="POST">
                    @csrf
                    <input type="hidden" name="representative_id">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">المحافظة</label>
                                <select name="government_id" id="interview_government_id" class="form-control" required>
                                    <option value="">اختر المحافظة</option>
                                    @foreach(\App\Models\Governorate::all() as $governorate)
                                        <option value="{{ $governorate->id }}">{{ $governorate->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">المنطقة (اختياري)</label>
                                <select name="location_id" id="interview_location_id" class="form-control">
                                    <option value="">اختر المنطقة (اختياري)</option>
                                </select>
                                <small class="text-muted">يمكن اختيار المحافظة فقط أو المحافظة والمنطقة معاً</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">الشركه</label>
                                <select name="company_id" id="company_id" class="form-control" required>
                                    <option value="">اختر الشركه</option>
                                    @foreach(\App\Models\Company::all() as $company)
                                        <option value="{{ $company->id }}">{{ $company->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">تايخ بدء العمل</label>
                                <input type="datetime-local" name="date" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">الرسالة</label>
                                <select name="message_id" id="interview_message_id" class="form-control select2" required>
                                    <option value="">اختر الرسالة</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">معاينة الرسالة</label>
                            <div id="messagePreview" class="border rounded p-3 bg-light">
                                <small class="text-muted">اختر المحافظة لعرض الرسائل المتاحة</small>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-warning" onclick="return confirm('هل أنت متأكد ؟')">
                            <i class="feather-calendar me-1"></i>رساله بدء العمل
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="interviewModal2" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">تغير المنطقه</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="interviewForm2" method="POST">
                    @csrf
                    <input type="hidden" name="representative_id">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">المحافظة</label>
                                <select name="government_id" id="change_government_id" class="form-control" required>
                                    <option value="">اختر المحافظة</option>
                                    @foreach(\App\Models\Governorate::all() as $governorate)
                                        <option value="{{ $governorate->id }}">{{ $governorate->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">المنطقة (اختياري)</label>
                                <select name="location_id" id="change_location_id" class="form-control">
                                    <option value="">اختر المنطقة (اختياري)</option>
                                </select>
                                <small class="text-muted">يمكن اختيار المحافظة فقط أو المحافظة والمنطقة معاً</small>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-warning" onclick="return confirm('هل أنت متأكد ؟')">
                            <i class="feather-calendar me-1"></i>تغير المنطقه
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="followupModal" tabindex="-1">
        <div class="modal-dialog">
            <form method="POST" id="followupForm">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">متابعة المندوب</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div id="followupHistory" class="mb-3 d-none">
                            <label class="fw-bold">المتابعات السابقة</label>
                            <div class="border rounded p-2 bg-light" id="followupHistoryList"></div>
                        </div>

                        <label class="fw-bold">الحالة</label>
                        <select name="status" class="form-select" required>
                            <option value="">اختر الحالة</option>
                            <option value="لم يرد">لم يرد</option>
                            <option value="متابعة مره اخري">متابعة مره اخري</option>
                            <option value="تغيير الشركه">تغيير الشركه</option>
                        </select>

                        <label class="mt-3 fw-bold">تاريخ المتابعة</label>
                        <input type="date" name="follow_up_date" class="form-control" required>

                        <label class="mt-3 fw-bold">ملاحظات</label>
                        <textarea name="note" class="form-control" rows="4" required
                            placeholder="اكتب التفاصيل هنا..."></textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-success">حفظ المتابعة</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="resignModal" tabindex="-1">
        <div class="modal-dialog">
            <form method="POST" id="resignForm">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">استقاله المندوب</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <label class="fw-bold">مكان الاستلام</label>
                        <input type="text" name="pickup_location" class="form-control" required>

                        <label class="mt-3 fw-bold">التاريخ</label>
                        <input type="date" name="appointment_date" class="form-control" required>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-danger">إرسال رسالة الاستقالة</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

@endsection

@push('scripts')

    <script>

        document.addEventListener('DOMContentLoaded', function () {
        const allLocationOptions = $('#location_id').html();

        $('#governorate_id').on('change', function () {
            const governorateId = $(this).val();
            const locationSelect = $('#location_id');

            locationSelect.html(allLocationOptions);

            if (governorateId) {
                locationSelect.find('option').each(function () {
                    const option = $(this);
                    const optionGovernorate = option.data('governorate');
                    if (option.val() !== '' && optionGovernorate != governorateId) {
                        option.remove();
                    }
                });
            }

            locationSelect.val('').trigger('change');
        });

        const initialGovernorateId = "{{ request('governorate_id') }}";
        const initialLocationId = "{{ request('location_id') }}";
        if (initialGovernorateId) {
            $('#governorate_id').trigger('change');
            if (initialLocationId) {
                setTimeout(function () {
                    $('#location_id').val(initialLocationId).trigger('change');
                }, 100);
            }
        }

        var interviewModal = document.getElementById('interviewModal');
        var interviewForm  = document.getElementById('interviewForm');

        // عند فتح المودال: ضبط الفورم + id
        interviewModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            var id   = button.getAttribute('data-id');

            document.querySelector('input[name="representative_id"]').value = id;

            interviewForm.action = "{{ route('representatives-not-completed.startRealRepresentative', ':id') }}"
                .replace(':id', id);
        });


        const govSelect = document.getElementById('interview_government_id');
        const locSelect = document.getElementById('interview_location_id');
        const companySelect = document.getElementById('company_id');
        const messageSelect = document.getElementById('interview_message_id');
        const messagePreview = document.getElementById('messagePreview');


        // تحميل الرسائل
        function loadMessages(governorateId, locationId = null, companyId = null) {

            if (!messageSelect) return;

            let url = `{{ url('getmessagesStartWork') }}?government_id=${governorateId}`;
            if (locationId) url += `&location_id=${locationId}`;
            if (companyId) url += `&company_id=${companyId}`;

            fetch(url)
                .then(res => res.json())
                .then(data => {
                    messageSelect.innerHTML = '<option value="">اختر الرسالة</option>';
                    data.forEach(msg => {
                        messageSelect.innerHTML += `
                            <option value="${msg.id}">
                                ${msg.description}
                            </option>`;
                    });

                    messagePreview.innerHTML =
                        '<small class="text-muted">اختر الرسالة لعرض المعاينة</small>';
                })
                .catch(err => {
                    messageSelect.innerHTML = '<option value="">خطأ في التحميل</option>';
                    messagePreview.innerHTML = '<small class="text-danger">خطأ في تحميل الرسائل</small>';
                });
        }

        // تغيير المحافظة: تحميل المناطق فقط
        if (govSelect) {
            govSelect.addEventListener('change', function () {
                const governorateId = this.value;

                if (!governorateId) {
                    locSelect.innerHTML = '<option value="">اختر المنطقة</option>';
                    messageSelect.innerHTML = '<option value="">اختر الرسالة</option>';
                    messagePreview.innerHTML = '<small class="text-muted">اختر المحافظة</small>';
                    return;
                }

                fetch(`{{ url('getlocations') }}/${governorateId}`)
                    .then(res => res.json())
                    .then(data => {
                        locSelect.innerHTML = '<option value="">اختر المنطقة (اختياري)</option>';
                        data.forEach(loc => {
                            locSelect.innerHTML += `<option value="${loc.id}">${loc.name}</option>`;
                        });

                        messageSelect.innerHTML = '<option value="">اختر الرسالة</option>';
                        messagePreview.innerHTML =
                            '<small class="text-muted">اختر المنطقة والشركة لعرض الرسائل</small>';
                    })
                    .catch(err => {
                        locSelect.innerHTML = '<option value="">خطأ في تحميل المناطق</option>';
                    });
            });
        }

        // عند تغيير المنطقة
        if (locSelect) {
            locSelect.addEventListener('change', function () {
                const governorateId = govSelect.value;
                const locationId = this.value;
                const companyId = companySelect.value;

                if (governorateId && companyId) {
                    loadMessages(governorateId, locationId, companyId);
                }
            });
        }

        // عند تغيير الشركة
        if (companySelect) {
            companySelect.addEventListener('change', function () {
                const governorateId = govSelect.value;
                const locationId = locSelect.value;
                const companyId = this.value;

                if (governorateId && companyId) {
                    loadMessages(governorateId, locationId, companyId);
                }
            });
        }

        // عرض المعاينة عند اختيار الرسالة
        if (messageSelect) {
            messageSelect.addEventListener('change', function () {
                const messageId = this.value;

                if (!messageId) {
                    messagePreview.innerHTML = '<small class="text-muted">اختر الرسالة لعرضها</small>';
                    return;
                }

                fetch(`{{ url('getmessageStartWork') }}/${messageId}`)
                    .then(res => res.json())
                    .then(data => {
                        messagePreview.innerHTML = `
                            <div><strong>الرسالة:</strong></div>
                            <div class="mt-2">${data.description}</div>
                            ${data.google_map_url
                                ? `<div class="mt-2"><strong>الخريطة:</strong> <a target="_blank" href="${data.google_map_url}">${data.google_map_url}</a></div>`
                                : ''
                            }
                        `;
                    })
                    .catch(err => {
                        messagePreview.innerHTML = '<small class="text-danger">خطأ في تحميل الرسالة</small>';
                    });
            });
        }

        });


        document.addEventListener('DOMContentLoaded', function () {

            const interviewModal = document.getElementById('interviewModal2');

            interviewModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget; // الزرار اللي فتح المودال
                const repId = button.getAttribute('data-id');

                // حط id جوه الفورم
                const hiddenInput = interviewModal.querySelector('input[name="representative_id"]');
                hiddenInput.value = repId;

                // حدّث الفورم عشان يبعت للـ route الصح
                const form = interviewModal.querySelector('form');
                form.action = "{{ route('waiting-representatives.changeLocation', ':id') }}"
                    .replace(':id', repId);
            });
            // Interview modal functionality
            const interviewGovSelect = document.getElementById('change_government_id');
            const interviewLocSelect = document.getElementById('change_location_id');
            // const messagePreview = document.getElementById('messagePreview');

            // Load locations when governorate changes
            if (interviewGovSelect && interviewLocSelect) {
                interviewGovSelect.addEventListener('change', function () {
                    const governorateId = this.value;

                    if (!governorateId) {
                        interviewLocSelect.innerHTML = '<option value="">اختر المنطقة</option>';
                        // Clear messages when governorate is cleared
                        if (interviewMessageSelect) {
                            interviewMessageSelect.innerHTML = '<option value="">اختر الرسالة</option>';
                            messagePreview.innerHTML = '<small class="text-muted">اختر المحافظة لعرض الرسائل المتاحة</small>';
                        }
                        return;
                    }

                    fetch(`{{ url('getlocations') }}/${governorateId}`)
                        .then(res => res.json())
                        .then(data => {
                            interviewLocSelect.innerHTML = '<option value="">اختر المنطقة</option>';
                            data.forEach(loc => {
                                interviewLocSelect.innerHTML += `<option value="${loc.id}">${loc.name}</option>`;
                            });

                            // Load messages for government only (without location)
                        })
                        .catch(err => {
                            console.error(err);
                            interviewLocSelect.innerHTML = '<option value="">خطأ في تحميل البيانات</option>';
                        });
                });
            }


        });
    </script>

    <script>
        document.querySelectorAll('.openFollowupModal').forEach(btn => {
            btn.addEventListener('click', function () {
                const id = this.dataset.waitingId;
                const form = document.getElementById('followupForm');
                form.action = "{{ route('waiting-representatives.followup', ':id') }}".replace(':id', id);

                const historyWrapper = document.getElementById('followupHistory');
                const historyList = document.getElementById('followupHistoryList');
                historyWrapper.classList.add('d-none');
                historyList.innerHTML = '';

                fetch("{{ route('waiting-representatives.followup-history', ':id') }}".replace(':id', id))
                    .then(res => res.json())
                    .then(data => {
                        if (!data.success || !data.items || data.items.length === 0) {
                            return;
                        }

                        historyWrapper.classList.remove('d-none');
                        historyList.innerHTML = data.items.map(item => {
                            const date = item.follow_up_date ? new Date(item.follow_up_date).toLocaleDateString('ar-EG') : '-';
                            const status = item.status || '';
                            const note = item.note || '';
                            const createdAt = item.created_at ? new Date(item.created_at).toLocaleDateString('ar-EG') : '';
                            const createdBy = item.created_by_name || '';
                            return `
                                <div class="mb-2 p-2 border rounded bg-white">
                                    <div><strong>الحالة:</strong> ${status}</div>
                                    <div><strong>تاريخ المتابعة:</strong> ${date}</div>
                                    <div><strong>الملاحظات:</strong> ${note}</div>
                                    <div><strong>أضيف بواسطة:</strong> ${createdBy || '-'}</div>
                                </div>
                            `;
                        }).join('');
                    })
                    .catch(() => {
                        // ignore history errors
                    });
            });
        });
    </script>

    <script>
        document.querySelectorAll('.openResignModal').forEach(btn => {
            btn.addEventListener('click', function () {
                const id = this.dataset.repId;
                const form = document.getElementById('resignForm');
                form.action = "{{ route('waiting-representatives.resign', ':id') }}".replace(':id', id);
            });
        });
    </script>
@endpush

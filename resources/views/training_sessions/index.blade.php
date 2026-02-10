@extends('layouts.app')

@section('title', 'المندوبين')

@section('content')
    <div class="nxl-content">
        <!-- [ page-header ] start -->
        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">الجلسات التدريبية</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
                    <li class="breadcrumb-item">الجلسات التدريبية</li>
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

                        <a href="{{ route('resignation-representatives.export') }}" class="btn btn-success">
                            <i class="feather-download me-2"></i>تصدير Excel
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
                    <div class="col-xxl-3 col-md-6 mb-3">
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
                    <div class="col-xxl-3 col-md-6 mb-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="avatar-text avatar-xl rounded bg-primary">
                                            <i class="feather-user-check"></i>
                                        </div>
                                        <a href="javascript:void(0);" class="fw-bold d-block text-blue">
                                            <span class="d-block">حضر</span>
                                            <span class="fs-24 fw-bolder d-block"
                                                id="activeLeads">{{ $attendedRepresentatives }}</span>
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
                                        <div class="avatar-text avatar-xl rounded bg-info">
                                            <i class="feather-user-plus"></i>
                                        </div>
                                        <a href="javascript:void(0);" class="fw-bold d-block text-black">
                                            <span class="d-block">لم يحضر</span>
                                            <span class="fs-24 fw-bolder d-block"
                                                id="qualifiedLeads">{{ $notAttendedRepresentatives }}</span>
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
                                            <i class="feather-user-x"></i>
                                        </div>
                                        <a href="javascript:void(0);" class="fw-bold d-block text-danger">
                                            <span class="d-block">مستقيليين</span>
                                            <span class="fs-24 fw-bolder d-block"
                                                id="inactiveRepresentatives">{{ $inactiveRepresentatives }}</span>
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
                    <form method="GET" action="{{ route('training_sessions.index') }}" class="row g-3">
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
                        <div class="col-md-2">
                            <label class="form-label">حالة التدريب</label>
                            <select name="is_training" class="form-control">
                                <option value="">جميع الحالات</option>
                                <option value="1" {{ request('is_training') == '1' ? 'selected' : '' }}>حضر</option>
                                <option value="0" {{ request('is_training') == '0' ? 'selected' : '' }}>لم يحضر</option>
                            </select>
                        </div>

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

                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary me-2">تصفية</button>
                            <a href="{{ route('training_sessions.index') }}" class="btn btn-light">مسح</a>
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
                            <h5 class="card-title mb-0">قائمة الجلسات التدريبية </h5>
                        </div>
                        <div class="card-body">
                            @if(session('success'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    {{ session('success') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            @endif

                            @if($sessions->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>اسم المندوب</th>
                                                <th>المحافظة</th>
                                                <th>النوع</th>
                                                <th>الشركة</th>
                                                <th>الأوراق الناقصه</th>
                                                <th> سبب تأجيل</th>
                                                <th>التاريخ</th>
                                                <th>الإجراءات</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($sessions as $session)
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="avatar-text avatar-sm rounded-circle bg-primary me-3">
                                                                <i class="feather-user"></i>
                                                            </div>
                                                            <div>
                                                                <h6 class="mb-0">
                                                                    <a
                                                                        href="{{ route('representatives-not-completed.show', $session->representative->id) }}">
                                                                        {{ $session->representative->name }}
                                                                    </a>
                                                                    <div class="text-muted small">
                                                                        {{ $session->representative->phone }}
                                                                    </div>
                                                                </h6>


                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            {{ $session->governorate->name }} <br>
                                                            {{ $session->location->name }}
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span
                                                            class="badge bg-info">{{ $session->message->type ?? 'غير محدد' }}</span>
                                                    </td>
                                                    <td>
                                                        <span
                                                            class="badge bg-info">{{ $session->representative->company->name ?? 'غير محدد' }}</span>
                                                    </td>
                                                    <td>
                                                        @if(count($session->representative->missingDocs()) > 0)
                                                            @foreach($session->representative->missingDocs() as $doc)
                                                                    <span>{{ $doc }}</span><br>
                                                            @endforeach
                                                        @else
                                                            <span class="badge bg-success">كل الأوراق مكتملة</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        {{ $latestPostponeReasons[$session->id] ?? '-' }}
                                                    </td>
                                                    <td>
                                                        {{ $session->date }}
                                                    </td>
                                                    <td class="d-flex gap-2">

                                                    <div class="btn-group">
                                                            <button type="button" class="btn btn-sm btn-primary dropdown-toggle" data-bs-toggle="dropdown">
                                                                حالة التدريب
                                                            </button>

                                                            <ul class="dropdown-menu">

                                                                <!-- حضر -->
                                                                <li>
                                                                    <form method="POST"
                                                                        action="{{ route('representatives-not-completed.toggleTraining', $session->representative_id) }}">
                                                                        @csrf
                                                                        @method('PUT')
                                                                        <input type="hidden" name="is_training" value="1">
                                                                        <button class="dropdown-item">حضر</button>
                                                                    </form>
                                                                </li>

                                                                <!-- لم يحضر -->
                                                                <li>
                                                                    <button type="button"
                                                                            class="dropdown-item text-danger openAbsentModal"
                                                                            data-id="{{ $session->representative_id }}">
                                                                        لم يحضر
                                                                    </button>
                                                                </li>

                                                            </ul>
                                                        </div>

                                                        <button type="button"
                                                            class="btn btn-sm btn-outline-danger uniform-btn {{ !$session->representative->is_active ? 'disabled' : '' }}"
                                                            data-bs-toggle="modal" data-bs-target="#reasonModal{{ $session->id }}">
                                                            <!--{{ $session->representative->is_active ? 'استقاله' : 'تفعيل' }}-->
                                                            استقاله
                                                        </button>


                                                        <button type="button" class="btn btn-sm btn-info uniform-btn"
                                                            title="إرسال بيانات التدريب" data-bs-toggle="modal"
                                                            data-bs-target="#SendMessageTrainingModal"
                                                            data-id="{{ $session->representative_id }}"
                                                            data-name="{{ $session->representative->name }}">
                                                            تغيير موعد المحاضره
                                                        </button>


                                                        @if($session->representative->is_training)
                                                        <button type="button" class="btn btn-sm btn-warning" title="رسالة بدء العمل"
                                                            data-bs-toggle="modal" data-bs-target="#interviewModal"
                                                            data-id="{{ $session->representative_id}}">
                                                            <i class="feather-clock"></i>
                                                        </button>


                                                        <button type="button"
                                                            class="btn btn-outline-success d-flex align-items-center openPostponeModal"
                                                            title="تأجيل"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#postponeModal"
                                                            data-session-id="{{ $session->id }}">
                                                            <i class="feather-clock"></i>
                                                            <span class="ms-1">تأجيل</span>
                                                        </button>

                                                        @endif

                                                    </td>

                                                </tr>

                                                {{-- resign reason --}}
                                                <div class="modal fade" id="reasonModal{{ $session->id }}" tabindex="-1">
                                                    <div class="modal-dialog">
                                                        <form
                                                            action="{{ route('training_sessions.toggle-status', $session->representative_id) }}"
                                                            method="POST">
                                                            @csrf
                                                            <div class="modal-content">

                                                                <div class="modal-header">
                                                                    <h5 class="modal-title">
                                                                        {{ $session->representative->is_active ? 'سبب الاستقالة' : 'سبب التفعيل' }}
                                                                    </h5>
                                                                    <button type="button" class="btn-close"
                                                                        data-bs-dismiss="modal"></button>
                                                                </div>

                                                                <div class="modal-body">
                                                                    <label class="form-label">السبب</label>
                                                                    <textarea name="reason" class="form-control" required
                                                                        placeholder="اكتب السبب هنا..."></textarea>
                                                                </div>

                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary"
                                                                        data-bs-dismiss="modal">إلغاء</button>
                                                                    <button type="submit" class="btn btn-primary">تأكيد</button>
                                                                </div>



                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>


                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Pagination -->
                                @if($sessions->hasPages())
                                    <div class="d-flex justify-content-center mt-4">
                                        {{ $sessions->links('pagination::bootstrap-5') }}
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



    {{-- send training data --}}
    <div class="modal fade" id="SendMessageTrainingModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">ارسال بيانات التدريب</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="trainingForm" method="POST">
                    @csrf
                    <input type="hidden" name="representative_id" id="trainingRepId">
                    <div class="modal-body">
                        <div class="row">
                            <!-- نفس الفورم بتاعك -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">نوع التدريب</label>
                                <select name="type" id="trainingType" class="form-control" required>
                                    <option value="">اختر النوع</option>
                                    <option value="أونلاين">تدريب Online</option>
                                    <option value="في المقر">تدريب في المقر</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">المحافظة</label>
                                <select name="government_id" id="governmentT_id" class="form-control" required>
                                    <option value="">اختر المحافظة</option>
                                    @foreach(\App\Models\Governorate::all() as $governorate)
                                        <option value="{{ $governorate->id }}">{{ $governorate->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">المنطقة (اختياري)</label>
                                <select name="location_id" id="locationT_id" class="form-control">
                                    <option value="">اختر المنطقة (اختياري)</option>
                                </select>
                                <small class="text-muted">يمكن اختيار المحافظة فقط أو المحافظة والمنطقة معاً</small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">الشركه</label>
                                <select name="company_id" id="companyT_id" class="form-control" required>
                                    <option value="">اختر الشركه</option>
                                    @foreach(\App\Models\Company::all() as $company)
                                        <option value="{{ $company->id }}">{{ $company->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">تاريخ التدريب</label>
                                <input type="datetime-local" name="date" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">الرسالة</label>
                                <select name="message_id" id="messageT_id" class="form-control select2" required>
                                    <option value="">اختر الرسالة</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">معاينة الرسالة</label>
                            <div id="messagePreviewT" class="border rounded p-3 bg-light">
                                <small class="text-muted">اختر المحافظة لعرض الرسائل المتاحة</small>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-info" onclick="return confirm('هل أنت متأكد ؟')">
                            <i class="feather-calendar me-1"></i> ارسال بيانات التدريب
                        </button>
                    </div>
                </form>
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


    <div class="modal fade" id="absentModal" tabindex="-1">
        <div class="modal-dialog">
            <form method="POST" id="absentForm">
                @csrf
                @method('PUT')

                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">سبب عدم حضور التدريب</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>


                    <div class="modal-body">
                        <textarea name="note" class="form-control" rows="4" placeholder="اكتب السبب..." required></textarea>
                         <!-- إضافة الـ Select الجديد -->
                        <label class="mt-3 fw-bold">الحالة</label>
                        <select name="status" class="form-select" required>
                            <option value="">اختر الحالة</option>
                            <option value="لم يرد">لم يرد</option>
                            <option value="متابعه">متابعة</option>
                        </select>
                        <input type="hidden" name="is_training" value="0">
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-danger">تأكيد</button>
                    </div>
                </div>

            </form>
        </div>
    </div>

    <div class="modal fade" id="postponeModal" tabindex="-1">
        <div class="modal-dialog">
            <form method="POST" id="postponeForm">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">تأجيل الجلسة التدريبية</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div id="postponeHistory" class="mb-3 d-none">
                            <label class="fw-bold">التأجيلات السابقة</label>
                            <div class="border rounded p-2 bg-light" id="postponeHistoryList"></div>
                        </div>

                        <label class="fw-bold">الحالة</label>
                        <select name="reason" class="form-select" required>
                            <option value="">اختر السبب</option>
                            <option value="مرضي">مرضي</option>
                            <option value="الـ zone مقفول">الـ zone مقفول</option>
                            <option value="اخرى">اخرى</option>
                        </select>

                        <label class="mt-3 fw-bold">تاريخ المتابعة</label>
                        <input type="date" name="follow_up_date" class="form-control" required>

                        <label class="mt-3 fw-bold">ملاحظات</label>
                        <textarea name="note" class="form-control" rows="4" required
                            placeholder="اكتب التفاصيل هنا..."></textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-success">تأكيد التأجيل</button>
                    </div>
                </div>
            </form>
        </div>
    </div>







@endsection

@push('scripts')
    <script>
        // Set up CSRF token for all AJAX requests
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $(document).ready(function () {
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
            // Handle modal data
            $('#transferModal').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget);
                var representativeId = button.data('representative-id');
                var representativeName = button.data('representative-name');
                var currentSupervisor = button.data('current-supervisor');

                $('#representativeId').val(representativeId);
                $('#representativeName').val(representativeName);
                $('#currentSupervisor').val(currentSupervisor);
                $('#newSupervisorId').val('');
                $('#transferReason').val('');
                $('#filterGovernorate').val('');
                $('#filterLocation').val('');

                // Show all supervisor options initially
                $('#newSupervisorId option').show();
            });

            // Handle governorate filter
            $('#filterGovernorate').on('change', function () {
                var governorateId = $(this).val();
                loadFilterLocations(governorateId);
                filterSupervisors();
            });

            // Handle location filter
            $('#filterLocation').on('change', function () {
                filterSupervisors();
            });

            function loadFilterLocations(governorateId) {
                if (!governorateId) {
                    $('#filterLocation').empty().append('<option value="">جميع المقار</option>');
                    return;
                }

                $.ajax({
                    url: '/getlocations/' + governorateId,
                    type: 'GET',
                    success: function (response) {
                        $('#filterLocation').empty().append('<option value="">جميع المقار</option>');

                        response.forEach(function (location) {
                            var option = new Option(location.name, location.id, false, false);
                            $('#filterLocation').append(option);
                        });
                    },
                    error: function () {
                        $('#filterLocation').empty().append('<option value="">خطأ في تحميل المقار</option>');
                    }
                });
            }

            function filterSupervisors() {
                var governorateId = $('#filterGovernorate').val();
                var locationId = $('#filterLocation').val();

                if (locationId) {
                    // Load supervisors by location via AJAX
                    $.ajax({
                        url: '{{ route("supervisors.by-location", ":locationId") }}'.replace(':locationId', locationId),
                        type: 'GET',
                        success: function (response) {
                            $('#newSupervisorId').empty().append('<option value="">اختر المشرف الجديد</option>');

                            response.forEach(function (supervisor) {
                                var governorateInfo = supervisor.governorate ? ' (' + supervisor.governorate.name + ')' : '';
                                var option = new Option(
                                    supervisor.name + ' - ' + (supervisor.location_name || 'غير محدد') + governorateInfo,
                                    supervisor.id,
                                    false,
                                    false
                                );
                                $('#newSupervisorId').append(option);
                            });
                        },
                        error: function () {
                            $('#newSupervisorId').empty().append('<option value="">خطأ في تحميل المشرفين</option>');
                        }
                    });
                } else if (governorateId) {
                    // Load supervisors by governorate via AJAX
                    $.ajax({
                        url: '{{ route("supervisors.by-governorate", ":governorateId") }}'.replace(':governorateId', governorateId),
                        type: 'GET',
                        success: function (response) {
                            $('#newSupervisorId').empty().append('<option value="">اختر المشرف الجديد</option>');

                            response.forEach(function (supervisor) {
                                var governorateInfo = supervisor.governorate ? ' (' + supervisor.governorate.name + ')' : '';
                                var option = new Option(
                                    supervisor.name + ' - ' + (supervisor.location_name || 'غير محدد') + governorateInfo,
                                    supervisor.id,
                                    false,
                                    false
                                );
                                $('#newSupervisorId').append(option);
                            });
                        },
                        error: function () {
                            $('#newSupervisorId').empty().append('<option value="">خطأ في تحميل المشرفين</option>');
                        }
                    });
                } else {
                    // Show all supervisors if no filters are selected
                    $('#newSupervisorId option').show();
                }

                // Reset selection if current selection is hidden
                var selectedOption = $('#newSupervisorId option:selected');
                if (selectedOption.length && selectedOption.is(':hidden')) {
                    $('#newSupervisorId').val('');
                }
            }

            // Handle form submission
            $('#transferForm').on('submit', function (e) {
                e.preventDefault();

                var formData = $(this).serialize();
                var submitBtn = $(this).find('button[type="submit"]');
                var originalText = submitBtn.html();

                // Disable button and show loading
                submitBtn.prop('disabled', true).html('<i class="feather-loader me-2"></i>جاري النقل...');

                $.ajax({
                    url: '{{ route("supervisors.transfer-representative") }}',
                    type: 'POST',
                    data: formData,
                    success: function (response) {
                        if (response.success) {
                            // Show success message
                            if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'تم النقل بنجاح!',
                                    text: response.message,
                                    confirmButtonText: 'حسناً'
                                }).then(() => {
                                    // Reload page to show updated data
                                    location.reload();
                                });
                            } else {
                                alert('تم النقل بنجاح! ' + response.message);
                                location.reload();
                            }
                        } else {
                            if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'خطأ!',
                                    text: response.message || 'حدث خطأ أثناء نقل المندوب',
                                    confirmButtonText: 'حسناً'
                                });
                            } else {
                                alert('خطأ! ' + (response.message || 'حدث خطأ أثناء نقل المندوب'));
                            }
                        }
                    },
                    error: function (xhr) {
                        var errorMessage = 'حدث خطأ أثناء نقل المندوب';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }

                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'error',
                                title: 'خطأ!',
                                text: errorMessage,
                                confirmButtonText: 'حسناً'
                            });
                        } else {
                            alert('خطأ! ' + errorMessage);
                        }
                    },
                    complete: function () {
                        // Re-enable button
                        submitBtn.prop('disabled', false).html(originalText);
                    }
                });
            });
        });



        document.addEventListener('DOMContentLoaded', function () {

        var interviewModal = document.getElementById('interviewModal');
        var interviewForm  = document.getElementById('interviewForm');

        // عند فتح المودال: ضبط الفورم + id
        interviewModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            var id   = button.getAttribute('data-id');

            document.querySelector('input[name="representative_id"]').value = id;

            interviewForm.action = "{{ route('training_sessions.startRealRepresentative', ':id') }}"
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

            const reactiveModal = document.getElementById('ReactiveModal');

            reactiveModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const repId = button.getAttribute('data-id');

                const form = reactiveModal.querySelector('form');
                const hiddenInput = form.querySelector('input[name="representative_id"]');

                hiddenInput.value = repId;

                // حدّث الـ action بالرابط الصحيح
                form.action = "{{ route('representatives-not-completed.transferToActive', ':id') }}"
                    .replace(':id', repId);
            });

            // Interview modal functionality
            const interviewGovSelect = document.getElementById('government_id');
            const interviewLocSelect = document.getElementById('location_id');
            const interviewMessageSelect = document.getElementById('message_id');
            const messagePreview = document.getElementById('messagePreview2');

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
                            loadMessagesForGovernment(governorateId);
                        })
                        .catch(err => {
                            console.error(err);
                            interviewLocSelect.innerHTML = '<option value="">خطأ في تحميل البيانات</option>';
                        });
                });
            }

            // Load messages when location changes
            if (interviewLocSelect && interviewMessageSelect) {
                interviewLocSelect.addEventListener('change', function () {
                    const locationId = this.value;
                    const governorateId = interviewGovSelect.value;

                    if (!governorateId) {
                        interviewMessageSelect.innerHTML = '<option value="">اختر الرسالة</option>';
                        messagePreview.innerHTML = '<small class="text-muted">اختر المحافظة أولاً</small>';
                        return;
                    }

                    if (!locationId) {
                        // If location is cleared, load messages for government only
                        loadMessagesForGovernment(governorateId);
                        return;
                    }

                    // Load messages for specific government and location
                    loadMessagesForGovernmentAndLocation(governorateId, locationId);
                });
            }

            // Function to load messages for government only
            function loadMessagesForGovernment(governorateId) {
                if (!interviewMessageSelect) return;

                fetch(`{{ url('getmessagesStartWork') }}?government_id=${governorateId}`)
                    .then(res => res.json())
                    .then(data => {
                        interviewMessageSelect.innerHTML = '<option value="">اختر الرسالة</option>';
                        data.forEach(msg => {
                            interviewMessageSelect.innerHTML += `<option value="${msg.id}">${msg.description}</option>`;
                        });
                        messagePreview.innerHTML = '<small class="text-muted">اختر الرسالة لعرض المعاينة</small>';
                    })
                    .catch(err => {
                        console.error(err);
                        interviewMessageSelect.innerHTML = '<option value="">خطأ في تحميل الرسائل</option>';
                        messagePreview.innerHTML = '<small class="text-danger">خطأ في تحميل الرسائل</small>';
                    });
            }

            // Function to load messages for specific government and location
            function loadMessagesForGovernmentAndLocation(governorateId, locationId) {
                if (!interviewMessageSelect) return;

                fetch(`{{ url('getmessagesStartWork') }}?government_id=${governorateId}&location_id=${locationId}`)
                    .then(res => res.json())
                    .then(data => {
                        interviewMessageSelect.innerHTML = '<option value="">اختر الرسالة</option>';
                        data.forEach(msg => {
                            interviewMessageSelect.innerHTML += `<option value="${msg.id}">${msg.description}</option>`;
                        });
                        messagePreview.innerHTML = '<small class="text-muted">اختر الرسالة لعرض المعاينة</small>';
                    })
                    .catch(err => {
                        console.error(err);
                        interviewMessageSelect.innerHTML = '<option value="">خطأ في تحميل الرسائل</option>';
                        messagePreview.innerHTML = '<small class="text-danger">خطأ في تحميل الرسائل</small>';
                    });
            }

            // Show message preview when message is selected
            if (interviewMessageSelect && messagePreview) {
                interviewMessageSelect.addEventListener('change', function () {
                    const messageId = this.value;

                    if (!messageId) {
                        messagePreview.innerHTML = '<small class="text-muted">اختر الرسالة لعرض المعاينة</small>';
                        return;
                    }

                    fetch(`{{ url('getmessageStartWork') }}/${messageId}`)
                        .then(res => res.json())
                        .then(data => {
                            messagePreview.innerHTML = `
                             <div class="mb-2"><strong>الوصف:</strong> ${data.description}</div>
                             ${data.google_map_url ? `<div><strong>رابط الخريطة:</strong> <a href="${data.google_map_url}" target="_blank">${data.google_map_url}</a></div>` : ''}
                         `;
                        })
                        .catch(err => {
                            console.error(err);
                            messagePreview.innerHTML = '<small class="text-danger">خطأ في تحميل الرسالة</small>';
                        });
                });
            }


        });



        document.addEventListener('DOMContentLoaded', function () {
            var trainingModal = document.getElementById('SendMessageTrainingModal');
            var trainingForm  = document.getElementById('trainingForm');
            var trainingRepId = document.getElementById('trainingRepId');

            // تحديث البيانات عند فتح المودال
            trainingModal.addEventListener('show.bs.modal', function (event) {
                var button = event.relatedTarget;
                var id   = button.getAttribute('data-id');
                var name = button.getAttribute('data-name');

                // hidden input
                trainingRepId.value = id;

                // تعديل الفورم action
                trainingForm.action = "{{ route('representatives-not-completed.send_message_training', ':id') }}".replace(':id', id);
            });

            const interviewGovSelect = document.getElementById('governmentT_id');
            const interviewLocSelect = document.getElementById('locationT_id');
            const interviewMessageSelect = document.getElementById('messageT_id');
            const messagePreview = document.getElementById('messagePreviewT');
            const trainingTypeSelect = document.getElementById('trainingType');
            const companySelect = document.getElementById('companyT_id');

            // Function to load messages
            function loadMessages(governorateId, locationId = null, type = null , companyId = null) {
                if (!interviewMessageSelect) return;

                let url = `{{ url('getmessagesTraining') }}?government_id=${governorateId}`;
                if (locationId) url += `&location_id=${locationId}`;
                if (type) url += `&type=${type}`;
                if (companyId) url += `&company_id=${companyId}`;


                fetch(url)
                    .then(res => res.json())
                    .then(data => {
                        interviewMessageSelect.innerHTML = '<option value="">اختر الرسالة</option>';
                        data.forEach(msg => {
                            let optionText = msg.type === "online" ? msg.description_training : msg.description_location;
                            interviewMessageSelect.innerHTML += `<option value="${msg.id}" data-type="${msg.type}">${optionText}</option>`;
                        });
                        messagePreview.innerHTML = '<small class="text-muted">اختر الرسالة لعرض المعاينة</small>';
                    })
                    .catch(err => {
                        console.error(err);
                        interviewMessageSelect.innerHTML = '<option value="">خطأ في تحميل الرسائل</option>';
                        messagePreview.innerHTML = '<small class="text-danger">خطأ في تحميل الرسائل</small>';
                    });
            }

            // عند تغيير المحافظة
            if (interviewGovSelect) {
                interviewGovSelect.addEventListener('change', function() {
                    const governorateId = this.value;

                    if (!governorateId) {
                        interviewLocSelect.innerHTML = '<option value="">اختر المنطقة</option>';
                        interviewMessageSelect.innerHTML = '<option value="">اختر الرسالة</option>';
                        messagePreview.innerHTML = '<small class="text-muted">اختر المحافظة والمنطقة والشركة لعرض الرسائل المتاحة</small>';
                        return;
                    }

                    // تحميل المناطق فقط
                    fetch(`{{ url('getlocations') }}/${governorateId}`)
                        .then(res => res.json())
                        .then(data => {
                            interviewLocSelect.innerHTML = '<option value="">اختر المنطقة</option>';
                            data.forEach(loc => {
                                interviewLocSelect.innerHTML += `<option value="${loc.id}">${loc.name}</option>`;
                            });

                            // مسح الرسائل وإضافة رسالة توضيحية
                            interviewMessageSelect.innerHTML = '<option value="">اختر الرسالة</option>';
                            messagePreview.innerHTML = '<small class="text-muted">يجب اختيار المنطقة والشركة لعرض الرسائل</small>';
                        })
                        .catch(err => {
                            console.error(err);
                            interviewLocSelect.innerHTML = '<option value="">خطأ في تحميل البيانات</option>';
                        });
                });
            }

            // عند تغيير المنطقة
            if (interviewLocSelect) {
                interviewLocSelect.addEventListener('change', function() {
                    const locationId = this.value;
                    const governorateId = interviewGovSelect.value;
                    const companyId = companySelect.value;
                    const type = trainingTypeSelect.value;

                    // التحقق من اختيار الثلاثة معًا
                    if (governorateId && locationId && companyId) {
                        loadMessages(governorateId, locationId, type, companyId);
                    } else if (!locationId) {
                        interviewMessageSelect.innerHTML = '<option value="">اختر الرسالة</option>';
                        messagePreview.innerHTML = '<small class="text-muted">يجب اختيار المنطقة والشركة لعرض الرسائل</small>';
                    } else if (!companyId) {
                        interviewMessageSelect.innerHTML = '<option value="">اختر الرسالة</option>';
                        messagePreview.innerHTML = '<small class="text-muted">يجب اختيار الشركة لعرض الرسائل</small>';
                    }
                });
            }

            // عند تغيير النوع
            if (trainingTypeSelect) {
                trainingTypeSelect.addEventListener('change', function() {
                    const governorateId = interviewGovSelect.value;
                    const locationId = interviewLocSelect.value;
                    const companyId = companySelect.value;
                    const type = this.value;

                    // التحقق من اختيار الثلاثة معًا
                    if (governorateId && locationId && companyId) {
                        loadMessages(governorateId, locationId, type, companyId);
                    } else {
                        interviewMessageSelect.innerHTML = '<option value="">اختر الرسالة</option>';
                        messagePreview.innerHTML = '<small class="text-muted">يجب اختيار المحافظة والمنطقة والشركة لعرض الرسائل</small>';
                    }
                });
            }

            // عند تغيير الشركة
            if (companySelect) {
                companySelect.addEventListener('change', function() {
                    const governorateId = interviewGovSelect.value;
                    const locationId = interviewLocSelect.value;
                    const type = trainingTypeSelect.value;
                    const companyId = this.value;

                    // التحقق من اختيار الثلاثة معًا
                    if (governorateId && locationId && companyId) {
                        loadMessages(governorateId, locationId, type, companyId);
                    } else if (!companyId) {
                        interviewMessageSelect.innerHTML = '<option value="">اختر الرسالة</option>';
                        messagePreview.innerHTML = '<small class="text-muted">يجب اختيار الشركة لعرض الرسائل</small>';
                    } else if (!governorateId || !locationId) {
                        interviewMessageSelect.innerHTML = '<option value="">اختر الرسالة</option>';
                        messagePreview.innerHTML = '<small class="text-muted">يجب اختيار المحافظة والمنطقة والشركة لعرض الرسائل</small>';
                    }
                });
            }

            // عرض المعاينة عند اختيار الرسالة
            if (interviewMessageSelect) {
                interviewMessageSelect.addEventListener('change', function() {
                    const messageId = this.value;
                    if (!messageId) {
                        messagePreview.innerHTML = '<small class="text-muted">اختر الرسالة لعرض المعاينة</small>';
                        return;
                    }

                    fetch(`{{ url('getmessageTraining') }}/${messageId}`)
                        .then(res => res.json())
                        .then(data => {
                            if (data.type === "أونلاين") {
                                messagePreview.innerHTML = `
                                    <div class="mb-2"><strong>الوصف:</strong> ${data.description_training}</div>
                                    ${data.link_training ? `<div><strong>الرابط:</strong> <a href="${data.link_training}" target="_blank">${data.link_training}</a></div>` : ''}
                                `;
                            } else {
                                messagePreview.innerHTML = `
                                    <div class="mb-2"><strong>الوصف:</strong> ${data.description_location}</div>
                                    ${data.google_map_url ? `<div><strong>رابط الخريطة:</strong> <a href="${data.google_map_url}" target="_blank">${data.google_map_url}</a></div>` : ''}
                                `;
                            }
                        })
                        .catch(err => {
                            console.error(err);
                            messagePreview.innerHTML = '<small class="text-danger">خطأ في تحميل الرسالة</small>';
                        });
                });
            }
        });

        document.addEventListener('DOMContentLoaded', function () {
            var sendNotesModal = document.getElementById('SendNotesModal');
            sendNotesModal.addEventListener('show.bs.modal', function (event) {
                var button = event.relatedTarget;

                var id = button.getAttribute('data-id');
                var name = button.getAttribute('data-name');
                var phone = button.getAttribute('data-phone');
                var gov = button.getAttribute('data-gov');

                document.getElementById('repId').value = id;
                document.getElementById('repName').innerText = name;
                document.getElementById('repPhone').innerText = phone;
                document.getElementById('repGov').innerText = gov;

                document.getElementById('sendNotesForm').action = "{{ route('representatives-not-completed.save-note', ':id') }}".replace(':id', id);

                var notes = button.getAttribute('data-notes'); // مثال: تخزن كل الملاحظات كـ JSON string
                var previousNotesDiv = document.getElementById('previousNotes');

                if (notes) {
                    notes = JSON.parse(notes); // إذا كانت JSON string
                    previousNotesDiv.innerHTML = ''; // امسح الافتراضي
                    notes.forEach(function (note) {
                        var noteDiv = document.createElement('div');
                        noteDiv.classList.add('mb-2', 'p-2', 'border', 'rounded', 'bg-white');
                        noteDiv.innerHTML = `
                                                                                                                                            <div>${note.note}</div>
                                                                                                                                            <small class="text-muted">${note.user} - ${note.created_at}</small>
                                                                                                                                        `;
                        previousNotesDiv.appendChild(noteDiv);
                    });
                } else {
                    previousNotesDiv.innerHTML = '<small class="text-muted">لا توجد ملاحظات سابقة</small>';
                }
            });
        });
    </script>




    <script>
        document.querySelectorAll('.openAbsentModal').forEach(btn => {
            btn.addEventListener('click', function () {
                const id = this.dataset.id;
                const form = document.getElementById('absentForm');

                form.action =
                    "{{ route('representatives-not-completed.toggleTraining', ':id') }}"
                    .replace(':id', id);

                const myModal = new bootstrap.Modal(document.getElementById('absentModal'));
                myModal.show();
            });
        });
    </script>

    <script>
        document.querySelectorAll('.openPostponeModal').forEach(btn => {
            btn.addEventListener('click', function () {
                const id = this.dataset.sessionId;
                const form = document.getElementById('postponeForm');
                form.action = "{{ route('training_sessions.postpone', ':id') }}".replace(':id', id);

                const historyWrapper = document.getElementById('postponeHistory');
                const historyList = document.getElementById('postponeHistoryList');
                historyWrapper.classList.add('d-none');
                historyList.innerHTML = '';

                fetch("{{ route('training_sessions.postpone-history', ':id') }}".replace(':id', id))
                    .then(res => res.json())
                    .then(data => {
                        if (!data.success || !data.items || data.items.length === 0) {
                            return;
                        }

                        historyWrapper.classList.remove('d-none');
                        historyList.innerHTML = data.items.map(item => {
                            const date = item.follow_up_date ? new Date(item.follow_up_date).toLocaleDateString('ar-EG') : '-';
                            const reason = item.reason || '';
                            const note = item.note || '';
                            const createdAt = item.created_at ? new Date(item.created_at).toLocaleDateString('ar-EG') : '';
                            const createdBy = item.created_by_name || '';
                            return `
                                <div class="mb-2 p-2 border rounded bg-white">
                                    <div><strong>الحالة:</strong> ${reason}</div>
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









@endpush

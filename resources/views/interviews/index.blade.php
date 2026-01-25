@extends('layouts.app')

@section('title', 'المقابلات')



@section('content')
<style>
    .nxl-content.small { font-size: 0.85rem; }
    .nxl-content.small .table { font-size: 0.85rem; }


    #interviewStatusSelect {
        appearance: none;
        -webkit-appearance: none;
        -moz-appearance: none;
        background-image: none !important; /* يمنع السهم الإضافي */
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

</style>
<div class="nxl-content small">
    <!-- [ page-header ] start -->
    <div class="page-header d-flex align-items-center justify-content-between">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="m-b-10">المقابلات المجدولة</h5>
            </div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
                <li class="breadcrumb-item">المقابلات</li>
            </ul>

             <a href="{{ route('interviews.export', request()->query()) }}" class="btn btn-success mb-3">
                <i class="feather-download"></i> تصدير إلى Excel
            </a>
        </div>
        <div class="page-header-right">
            <button class="btn btn-outline-primary" type="button" data-bs-toggle="collapse" data-bs-target="#filterCollapse">
                <i class="feather-filter me-1"></i> الفلترة
            </button>


        </div>
    </div>
    <!-- [ page-header ] end -->

        <div id="collapseOne" class="accordion-collapse show collapse page-header-collapse mb-4">
            <div class="accordion-body pb-2">
                <label class="fw-bold mb-3 d-block">الإحصائيات:</label>

                <div class="d-flex flex-nowrap overflow-auto gap-3">

                    {{-- الإجمالي --}}
                    <div class="card" style="min-width: 200px;">
                        <div class="card-body">
                            <div class="d-flex align-items-center gap-3">
                                <div class="avatar-text avatar-xl rounded">
                                    <i class="feather-users"></i>
                                </div>
                                <div>
                                    <span class="d-block fw-bold">الإجمالي</span>
                                    <span class="fs-24 fw-bolder" id="totalLeads">{{ $totalInterviews }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- غير مهتم --}}
                    <div class="card" style="min-width: 200px;">
                        <div class="card-body">
                            <div class="d-flex align-items-center gap-3">
                                <div class="avatar-text avatar-xl rounded bg-primary text-white">
                                    <i class="feather-user-check"></i>
                                </div>
                                <div>
                                    <span class="d-block fw-bold">غير مهتم</span>
                                    <span class="fs-24 fw-bolder">{{ $notInterestedCount }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- موافق على العمل --}}
                    <div class="card" style="min-width: 200px;">
                        <div class="card-body">
                            <div class="d-flex align-items-center gap-3">
                                <div class="avatar-text avatar-xl rounded bg-info text-white">
                                    <i class="feather-user-plus"></i>
                                </div>
                                <div>
                                    <span class="d-block fw-bold">موافق</span>
                                    <span class="fs-24 fw-bolder">{{ $acceptedCount }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- هيفكر --}}
                    <div class="card" style="min-width: 200px;">
                        <div class="card-body">
                            <div class="d-flex align-items-center gap-3">
                                <div class="avatar-text avatar-xl rounded bg-warning text-white">
                                    <i class="feather-help-circle"></i>
                                </div>
                                <div>
                                    <span class="d-block fw-bold">هيفكر</span>
                                    <span class="fs-24 fw-bolder">{{ $thinkingCount }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- لم يرد --}}
                    <div class="card" style="min-width: 200px;">
                        <div class="card-body">
                            <div class="d-flex align-items-center gap-3">
                                <div class="avatar-text avatar-xl rounded bg-secondary text-white">
                                    <i class="feather-phone-off"></i>
                                </div>
                                <div>
                                    <span class="d-block fw-bold">لم يرد</span>
                                    <span class="fs-24 fw-bolder">{{ $noResponseCount }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- لم يحضر --}}
                    <div class="card" style="min-width: 200px;">
                        <div class="card-body">
                            <div class="d-flex align-items-center gap-3">
                                <div class="avatar-text avatar-xl rounded bg-danger text-white">
                                    <i class="feather-user-x"></i>
                                </div>
                                <div>
                                    <span class="d-block fw-bold">لم يحضر</span>
                                    <span class="fs-24 fw-bolder">{{ $absentCount }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- المتابعة مرة أخرى --}}
                    <div class="card" style="min-width: 200px;">
                        <div class="card-body">
                            <div class="d-flex align-items-center gap-3">
                                <div class="avatar-text avatar-xl rounded bg-success text-white">
                                    <i class="feather-repeat"></i>
                                </div>
                                <div>
                                    <span class="d-block fw-bold">متابعة مرة أخرى</span>
                                    <span class="fs-24 fw-bolder">{{ $followUpNextTimeCount }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card" style="min-width: 200px;">
                        <div class="card-body">
                            <div class="d-flex align-items-center gap-3">
                                <div class="avatar-text avatar-xl rounded bg-success text-white">
                                    <i class="feather-repeat"></i>
                                </div>
                                <div>
                                    <span class="d-block fw-bold">غير محدد</span>
                                    <span class="fs-24 fw-bolder">{{ $undefinedCount }}</span>
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
                <form method="GET" action="{{ route('interviews.index') }}" class="row g-3">
                    <div class="col-md-2">
                        <label class="form-label">البحث في الاسم أو رقم الهاتف</label>
                        <input type="text" name="search" class="form-control" placeholder="ابحث بالاسم أو الهاتف..." value="{{ $search }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">من تاريخ</label>
                        <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">إلى تاريخ</label>
                        <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">الحالة</label>
                        <select name="status" class="form-control">
                            <option selected disabled>جميع الحالات</option>
                            <option value="غير مهتم" {{ request('status') === 'غير مهتم' ? 'selected' : '' }}>غير مهتم</option>
                            <option value="موافق على العمل" {{ request('status') === 'موافق على العمل' ? 'selected' : '' }}>موافق على العمل</option>
                            <option value="هيفكر" {{ request('status') === 'هيفكر' ? 'selected' : '' }}>هيفكر</option>
                            <option value="لم يرد" {{ request('status') === 'لم يرد' ? 'selected' : '' }}>لم يرد</option>
                            <option value="لم يحضر" {{ request('status') === 'لم يحضر' ? 'selected' : '' }}>لم يحضر</option>
                            <option value="المتابعة مرة أخرى" {{ request('status') === 'المتابعة مرة أخرى' ? 'selected' : '' }}>المتابعة مرة أخرى</option>
                        </select>

                    </div>

                    <div class="col-md-2">
                        <label class="form-label">الموظف</label>
                        <select name="employee_id" class="form-control">
                            <option value="" selected disabled>اختر الموظف</option>
                            @foreach($employees as $employee)
                                <option value="{{ $employee->id }}" {{ request('employee_id') == $employee->id ? 'selected' : '' }}>
                                    {{ $employee->employee?->name ?? $employee->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">المحافظة</label>
                        <select name="governorate_id" class="form-control">
                            <option value="" selected disabled>اختر المحافظة</option>
                            @foreach($governorates as $governorate)
                                <option value="{{ $governorate->id }}" {{ request('governorate_id') == $governorate->id ? 'selected' : '' }}>
                                    {{ $governorate->name}}
                                </option>
                            @endforeach
                        </select>
                    </div>


                    <div class="col-md-2 d-flex align-items-end">
                        <div class="d-flex gap-2 w-100">
                            <button type="submit" class="btn btn-primary flex-fill">
                                <i class="feather-search me-1"></i> بحث
                            </button>
                            <a href="{{ route('interviews.index') }}" class="btn btn-light">
                                <i class="feather-refresh-cw"></i>
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
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
    <div class="main-content">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">قائمة المقابلات المجدولة</h5>
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

                        @if($interviews->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr >
                                             <th class="wd-30">
                                                <div class="btn-group mb-1">
                                                    <div class="form-check ms-1">
                                                        <input type="checkbox" class="form-check-input" id="checkAllLead">
                                                        <label class="form-check-label" for="checkAllLead"></label>
                                                    </div>
                                                </div>
                                            </th>
                                            <th>#</th>
                                            <th>اسم العميل</th>
                                            <th>رقم الهاتف</th>
                                            <th>المحافظة</th>

                                            <th>تاريخ المقابلة</th>
                                            <th>اللملاحظة</th>
                                            <th>الحالة</th>
                                            <th>الإجراءات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($interviews as $interview)
                                            <tr class="single-item" id="lead-{{ $interview->id }}">
                                                 <td>
                                                    <div class="form-check ms-1">
                                                        <input type="checkbox" class="form-check-input lead-checkbox" id="checkBox_{{ $interview->id }}" value="{{ $interview->id }}">
                                                        <label class="form-check-label" for="checkBox_{{ $interview->id }}"></label>
                                                    </div>
                                                </td>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-text avatar-sm me-2">
                                                            <i class="feather-user"></i>
                                                        </div>
                                                        <div>
                                                            <strong>{{ $interview->lead->name ?? 'غير محدد' }}</strong>
                                                            {{-- @if($interview->lead)
                                                                <br><small class="text-muted">من: {{ $interview->lead->source->name ?? 'غير محدد' }}</small>
                                                            @endif --}}
                                                            <br><small class="text-muted">من: {{ $interview->assignedTo->employee->name ?? 'غير محدد' }}</small>

                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    @if($interview->lead && $interview->lead->phone)
                                                        <a href="tel:{{ $interview->lead->phone }}" class="text-primary">
                                                            <i class="feather-phone me-1"></i>{{ $interview->lead->phone }}
                                                        </a>
                                                    @else
                                                        <span class="text-muted">غير متوفر</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    {{ $interview->message->government->name ?? 'غير محدد' }} <br>
                                                    <small class="text-muted">{{ $interview->message->location->name ?? 'غير محدد' }}</small><br>
                                                    <small class="text-muted">

                                                        @if($interview->supervisor)
                                                          المشرف :  {{ $interview->supervisor->name }}
                                                        @else

                                                        @endif
                                                    </small>

                                                </td>
                                                <td>
                                                        <div class="d-flex flex-column align-items-start">
                                                            <!-- التاريخ والوقت -->
                                                            <div class="mb-2">
                                                                <i class="bi bi-calendar-date text-primary"></i>
                                                                <span class="fw-bold">{{ $interview->date_interview->format('d m, Y') }}</span>

                                                                <br>
                                                                <i class="bi bi-clock text-secondary"></i>
                                                                <small class="text-muted">{{ $interview->date_interview->format('H:i') }}</small>
                                                            </div>

                                                            <!-- الحالة -->
                                                          {{--  <div>

                                                                @if($interview->date_interview->isPast())
                                                                    <span class="badge rounded-pill bg-danger">
                                                                        <i class="bi bi-x-circle"></i> منتهية
                                                                    </span>
                                                                @elseif($interview->date_interview->isToday())
                                                                    <span class="badge rounded-pill bg-info text-dark">
                                                                        <i class="bi bi-calendar-check"></i> اليوم
                                                                    </span>
                                                                @elseif($interview->date_interview->diffInDays(now()) == 1)
                                                                    <span class="badge rounded-pill bg-warning text-dark">
                                                                        <i class="bi bi-clock-history"></i> غداً
                                                                    </span>
                                                                @elseif($interview->date_interview->diffInDays(now()) <= 3)
                                                                    <span class="badge rounded-pill bg-primary">
                                                                        <i class="bi bi-calendar-week"></i> هذا الأسبوع
                                                                    </span>
                                                                @elseif($interview->date_interview->diffInDays(now()) <= 7)
                                                                    <span class="badge rounded-pill bg-secondary">
                                                                        <i class="bi bi-calendar-month"></i> الأسبوع القادم
                                                                    </span>
                                                                @else
                                                                    <span class="badge rounded-pill bg-success">
                                                                        <i class="bi bi-arrow-right-circle"></i> قادمة
                                                                    </span>
                                                                @endif
                                                            </div> --}}
                                                        </div>

                                                    </td>

                                                <td>
                                                    @if($interview->notes->count() > 0)
                                                        @php
                                                            $latestNote = $interview->notes->first();
                                                        @endphp
                                                        <div class="text-truncate" style="max-width: 200px;" title="{{ $latestNote->note }}">
                                                            <div class="fw-bold">{{ Str::limit($latestNote->note, 40) }}</div>
                                                            <small class="text-muted">
                                                                <i class="feather-clock me-1"></i>
                                                                {{ $latestNote->created_at->format('Y-m-d H:i') }}
                                                            </small>
                                                        </div>
                                                    @else
                                                        <div class="text-truncate" style="max-width: 200px;" title="{{ $interview->note ?? 'غير محدد' }}">
                                                            <div class="fw-bold">{{ Str::limit($interview->note ?? 'غير محدد', 40) }}</div>
                                                            <small class="text-muted">
                                                                <i class="feather-clock me-1"></i>
                                                                {{ $interview->created_at->format('Y-m-d H:i') }}
                                                            </small>
                                                        </div>
                                                    @endif
                                                </td>
                                                <td>
                                                    {{--
                                                    @if($interview->date_interview->isPast())
                                                        <span class="badge bg-danger">منتهية</span>
                                                    @elseif($interview->date_interview->isToday())
                                                        <span class="badge bg-info">اليوم</span>
                                                    @elseif($interview->date_interview->diffInDays(now()) == 1)
                                                        <span class="badge bg-warning">غداً</span>
                                                    @elseif($interview->date_interview->diffInDays(now()) <= 3)
                                                        <span class="badge bg-primary">هذا الأسبوع</span>
                                                    @elseif($interview->date_interview->diffInDays(now()) <= 7)
                                                        <span class="badge bg-secondary">الأسبوع القادم</span>
                                                    @else
                                                        <span class="badge bg-success">قادمة</span>
                                                    @endif
                                                    --}}

                                                    {{$interview->status ?? '-'}}
                                                </td>
                                                <td >
                                                    <div class="btn-group" role="group">
                                                        <button type="button" class="btn btn-sm btn-outline-primary m-1"
                                                                onclick="viewInterview({{ $interview->id }})"
                                                                title="عرض التفاصيل">
                                                            <i class="feather-eye"></i>
                                                        </button>
                                                        @if($interview->lead && $interview->lead->phone)
                                                            <button type="button" class="btn btn-sm btn-outline-success m-1"
                                                                    onclick="resendWhatsApp({{ $interview->id }})"
                                                                    title="إعادة إرسال رسالة واتساب">
                                                                <i class="feather-message-circle"></i>
                                                            </button>
                                                        @endif

                                                       {{-- <button type="button"
                                                                class="btn btn-sm btn-warning m-1"
                                                                onclick="new bootstrap.Modal(document.getElementById('interviewModal')).show()"                                                                title="تعديل وقت المقابلة">
                                                            <i class="feather-clock"></i>
                                                        </button> --}}

                                                        <button type="button"
                                                                class="btn btn-sm btn-warning m-1"
                                                                title="تعديل وقت المقابلة"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#interviewModal"
                                                                data-lead="{{ $interview->lead_id }}"
                                                                data-id="{{ $interview->id }}">
                                                            <i class="feather-clock"></i>
                                                        </button>


                                                        @can('create_representatives')
                                                            @if($interview->status == 'موافق على العمل' )
                                                                <button type="button"
                                                                        class="btn btn-sm text-white m-1"
                                                                        style="background-color: black; border-color: black;"
                                                                        onclick="window.location.href='{{ route('representatives.create', ['lead_id' => $interview->lead_id]) }}'"
                                                                        title="إضافة ممثل">
                                                                    <i class="feather-user-plus me-1"></i> <!-- أيقونة + مسافة بسيطة -->
                                                                </button>
                                                            @endif
                                                        @endcan

                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            @if($interviews->hasPages())
                                <div class="d-flex justify-content-center mt-4">
                                    {{ $interviews->links('pagination::bootstrap-5') }}
                                </div>
                            @endif
                        @else
                            <div class="text-center py-5">
                                <div class="avatar-text avatar-xl mx-auto mb-3">
                                    <i class="feather-calendar"></i>
                                </div>
                                <h5>لم يتم العثور على مقابلات</h5>
                                <p class="text-muted">لا توجد مقابلات مجدولة حالياً.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="interviewDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">تفاصيل المقابلة</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="interviewDetailsContent">
                <!-- Content will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">إغلاق</button>
                <button type="button" class="btn btn-primary" id="saveInterviewNoteBtn" style="display:none;">
                    <i class="feather-save me-1"></i> حفظ الملاحظة
                </button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="interviewModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">جدولة مقابلة</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
{{--            action="{{ route('interviews.update-date',$interview->id) }}"--}}
            <form id="interviewForm"  method="POST">
                @csrf
{{--                <input type="hidden" name="lead_id" value="{{ $interview->lead_id}}">--}}
                <input type="hidden" name="lead_id" id="modal_lead_id">
                <input type="hidden" name="interview_id" id="modal_interview_id">
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

                        {{-- -المشرفين --}}
                        <div class="col-md-6 mb-3">
                            <label class="form-label">المشرفين</label>
                            <select name="supervisor_id" id="interview_supervisor_id" class="form-control">
                                <option value="">اختر المشرف</option>
                                <!-- سيتم ملؤه ديناميكيًا -->
                            </select>
                            <small class="text-muted">اختر مشرف المنطقة المختارة</small>
                        </div>


                        <div class="col-md-6 mb-3">
                            <label class="form-label">تاريخ المقابلة</label>
                            <input type="datetime-local" name="date_interview" class="form-control" required>
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
                    <button type="submit" class="btn btn-warning"
                            onclick="return confirm('هل أنت متأكد من تغيير تاريخ المقابلة؟')">
                        <i class="feather-calendar me-1"></i> جدولة المقابلة
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


 <!-- Loading Overlay -->
    <div id="loadingOverlay" class="loading-overlay" style="display: none;">
        <div class="loading-content">
            <div class="loading-spinner"></div>
            <div class="loading-text">جاري تعيين العملاء المحتملين...</div>
            <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-3" onclick="hideLoadingOverlay()"></button>
        </div>
    </div>

@endsection

<script>
function viewInterview(interviewId) {
    // Load interview details via AJAX
    fetch(`{{ url('interviews') }}/${interviewId}`)
        .then(res => res.json())
        .then(data => {
            if (data.success && data.interview) {
                const interview = data.interview;
                const existingNote = interview.note ? interview.note : '';
                const content = `
                    <div class="row">
                        <div class="col-md-6">
                            <h6>معلومات العميل</h6>
                            <p><strong>الاسم:</strong> ${interview.lead ? interview.lead.name : 'غير محدد'}</p>
                            <p><strong>الهاتف:</strong> ${interview.lead ? interview.lead.phone : 'غير محدد'}</p>
                            <p><strong>المصدر:</strong> ${interview.lead && interview.lead.source ? interview.lead.source.name : 'غير محدد'}</p>
                        </div>
                        <div class="col-md-6">
                            <h6>معلومات المقابلة</h6>
                            <p><strong>التاريخ:</strong> ${new Date(interview.date_interview).toLocaleDateString('ar-EG', {
                                year: 'numeric',
                                month: 'long',
                                day: 'numeric',
                                hour: '2-digit',
                                minute: '2-digit'
                            })}</p>
                            <p><strong>المحافظة:</strong> ${interview.message && interview.message.government ? interview.message.government.name : 'غير محدد'}</p>
                            <p><strong>المنطقة:</strong> ${interview.message && interview.message.location ? interview.message.location.name : 'غير محدد'}</p>
                        </div>
                    </div>
                    <hr>
                    <div class="row mb-3">
                        <div class="col-12">
                            <h6>الملاحظات</h6>
                            <div id="interviewNotesList" class="d-flex flex-column gap-2">
                                ${(interview.notes||[]).map(n => `
                                    <div class="border rounded p-2 bg-light">
                                        <div class="small text-muted">${new Date(n.created_at).toLocaleString('ar-EG')} · ${(n.created_by && n.created_by.name) ? n.created_by.name : ''}</div>
                                        <div>${n.note}</div>
                                        <div>${n.status}</div>
                                    </div>
                                `).join('')}
                            </div>

                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <h6>الرسالة المرسلة</h6>
                            <div class="border rounded p-3 bg-light">
                                ${interview.message ? interview.message.description : 'غير محدد'}
                            </div>
                            ${interview.message && interview.message.google_map_url ?
                                `<div class="mt-2"><a href="${interview.message.google_map_url}" target="_blank" class="btn btn-sm btn-outline-primary">
                                    <i class="feather-map-pin me-1"></i>عرض على الخريطة
                                </a></div>` : ''
                            }
                            <hr>
                            <h6>إضافة ملاحظة</h6>
                            <textarea id="interviewNoteInput" class="form-control" rows="3" placeholder="أدخل ملاحظة جديدة..."></textarea>
                             <hr>

                            <h6>تغيير الحالة</h6>
                            <select id="interviewStatusSelect" class="form-select">
                                <option value="" disabled selected>اختر الحالة</option>
                                <option value="غير مهتم">غير مهتم</option>
                                <option value="موافق على العمل">موافق على العمل</option>
                                <option value="هفكر">هفكر</option>
                                <option value="لم يرد">لم يرد</option>
                                <option value="لم يحضر">لم يحضر</option>
                                <option value="المتابعة مرة أخرى">المتابعة مرة أخرى</option>
                            </select>
                        </div>
                    </div>
                `;
                document.getElementById('interviewDetailsContent').innerHTML = content;
                const saveBtn = document.getElementById('saveInterviewNoteBtn');
                if (saveBtn) {
                    saveBtn.style.display = 'inline-block';
                    saveBtn.onclick = function() {
                        const noteVal = document.getElementById('interviewNoteInput').value;
                        const statusVal = document.getElementById('interviewStatusSelect').value;
                        if (!noteVal.trim()) {
                            alert('يرجى إدخال ملاحظة');
                            return;
                        }

                        if (!statusVal.trim()) {
                            alert('يرجى اختيار حالة');
                            return;
                        }
                        fetch(`{{ url('interviews') }}/${interviewId}/note`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                note: noteVal ,
                                status: statusVal
                            })
                        })
                        .then(res => {
                            if (!res.ok) {
                                throw new Error(`HTTP error! status: ${res.status}`);
                            }
                            return res.json();
                        })
                        .then(resp => {
                            if (resp.success) {
                                saveBtn.classList.remove('btn-primary');
                                saveBtn.classList.add('btn-success');
                                saveBtn.innerHTML = '<i class="feather-check"></i> تم الإضافة';
                                const list = document.getElementById('interviewNotesList');
                                if (list && Array.isArray(resp.notes)) {
                                    list.innerHTML = resp.notes.map(n => `
                                        <div class="border rounded p-2 bg-light">
                                            <div class="small text-muted">${new Date(n.created_at).toLocaleString('ar-EG')} · ${(n.created_by && n.created_by.name) ? n.created_by.name : ''}</div>
                                            <div>${n.note}</div>
                                        </div>
                                    `).join('');
                                }
                                document.getElementById('interviewNoteInput').value = '';
                                setTimeout(() => {
                                    saveBtn.classList.remove('btn-success');
                                    saveBtn.classList.add('btn-primary');
                                    saveBtn.innerHTML = '<i class="feather-save me-1"></i> حفظ الملاحظة';
                                }, 1500);
                            } else {
                                alert(resp.message || 'تعذر حفظ الملاحظة');
                            }
                        })
                        .catch(err => {
                            console.error('Error saving note:', err);
                            alert('تعذر حفظ الملاحظة: ' + err.message);
                        });
                    };
                }
                new bootstrap.Modal(document.getElementById('interviewDetailsModal')).show();
            } else {
                // Handle error case
                const errorMessage = data.message || 'المقابلة غير موجودة';
                const debugInfo = data.debug_info ? JSON.stringify(data.debug_info, null, 2) : '';
                document.getElementById('interviewDetailsContent').innerHTML = `
                    <div class="alert alert-danger">
                        <h6>خطأ في تحميل المقابلة</h6>
                        <p>${errorMessage}</p>
                        ${debugInfo ? `<pre class="small">${debugInfo}</pre>` : ''}
                    </div>
                `;
                new bootstrap.Modal(document.getElementById('interviewDetailsModal')).show();
            }
        })
        .catch(err => {
            console.error('Error loading interview:', err);
            alert('حدث خطأ في تحميل التفاصيل: ' + err.message);
        });
}

function resendWhatsApp(interviewId) {
    if (confirm('هل تريد إعادة إرسال رسالة واتساب؟')) {
        fetch(`{{ url('interviews') }}/${interviewId}/resend-whatsapp`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(res => {
            if (!res.ok) {
                throw new Error(`HTTP error! status: ${res.status}`);
            }
            return res.json();
        })
        .then(data => {
            if (data.success) {
                alert('تم إرسال رسالة واتساب بنجاح!');
            } else {
                alert('حدث خطأ: ' + (data.message || 'خطأ غير معروف'));
            }
        })
        .catch(err => {
            console.error('Error resending WhatsApp:', err);
            alert('حدث خطأ في الاتصال: ' + err.message);
        });
    }
}

function deleteInterview(interviewId) {
    if (confirm('هل أنت متأكد من حذف هذه المقابلة؟')) {
        fetch(`{{ url('interviews') }}/${interviewId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(res => {
            if (!res.ok) {
                throw new Error(`HTTP error! status: ${res.status}`);
            }
            return res.json();
        })
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('حدث خطأ: ' + (data.message || 'خطأ غير معروف'));
            }
        })
        .catch(err => {
            console.error('Error deleting interview:', err);
            alert('حدث خطأ في الاتصال: ' + err.message);
        });
    }
}

// Handle status change in follow-up modal
document.addEventListener('DOMContentLoaded', function() {
    const interviewModal = document.getElementById('interviewModal');
    interviewModal.addEventListener('show.bs.modal', function (event) {
        let button = event.relatedTarget; // الزرار اللي ضغط عليه
        let leadId = button.getAttribute('data-lead');
        let interviewId = button.getAttribute('data-id');

        // حط القيم جوه ال inputs
        document.getElementById('modal_lead_id').value = leadId;
        document.getElementById('modal_interview_id').value = interviewId;

        // كمان لو عندك route فيه id تقدر تغيره بال JS
        let form = interviewModal.querySelector('form');

        form.action = "{{ route('interviews.update-date', ':id') }}"
            .replace(':id', interviewId);
    });

    // Interview modal functionality
    const interviewGovSelect = document.getElementById('interview_government_id');
    const interviewLocSelect = document.getElementById('interview_location_id');
    const interviewMessageSelect = document.getElementById('interview_message_id');
    const messagePreview = document.getElementById('messagePreview');

    // Load locations when governorate changes
    if (interviewGovSelect && interviewLocSelect) {
        interviewGovSelect.addEventListener('change', function() {
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
        interviewLocSelect.addEventListener('change', function() {
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

        fetch(`{{ url('getmessages') }}?government_id=${governorateId}`)
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

        fetch(`{{ url('getmessages') }}?government_id=${governorateId}&location_id=${locationId}`)
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
        interviewMessageSelect.addEventListener('change', function() {
            const messageId = this.value;

            if (!messageId) {
                messagePreview.innerHTML = '<small class="text-muted">اختر الرسالة لعرض المعاينة</small>';
                return;
            }

            fetch(`{{ url('getmessage') }}/${messageId}`)
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


    function loadSupervisors(governorateId, locationId) {
        const supervisorSelect = document.getElementById('interview_supervisor_id');
        if (!supervisorSelect) return;

        if (!locationId) {
            supervisorSelect.innerHTML = '<option value="">اختر المشرف</option>';
            return;
        }

        fetch(`{{ url('get-supervisors') }}?government_id=${governorateId}&location_id=${locationId}`)
            .then(res => res.json())
            .then(data => {
                supervisorSelect.innerHTML = '<option value="">اختر المشرف</option>';
                data.forEach(sup => {
                    supervisorSelect.innerHTML += `<option value="${sup.id}">${sup.name}</option>`;
                });
            })
            .catch(err => {
                console.error(err);
                supervisorSelect.innerHTML = '<option value="">خطأ في تحميل المشرفين</option>';
            });
    }

    // ضمن event change للمنطقة
    interviewLocSelect.addEventListener('change', function() {
        const locationId = this.value;
        const governorateId = interviewGovSelect.value;

        if (!locationId) {
            loadMessagesForGovernment(governorateId);
            document.getElementById('interview_supervisor_id').innerHTML = '<option value="">اختر المشرف</option>';
            return;
        }

        loadMessagesForGovernmentAndLocation(governorateId, locationId);
        loadSupervisors(governorateId, locationId);
    });






});


</script>

<script>
    $(document).ready(function() {
        $('#interview_message_id').select2({
            width: '100%',
            placeholder: "اختر الرسالة"
        });
    });



/*
    document.addEventListener('DOMContentLoaded', function() {

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

                    fetch(`{{ route('interview.bulkAssign') }}`, {
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
    });
 */
</script>
<script>

// Function to hide loading overlay
    function hideLoadingOverlay() {
        const loadingOverlay = document.getElementById('loadingOverlay');
        if (loadingOverlay) {
            loadingOverlay.style.display = 'none';
        }
    }


document.addEventListener('DOMContentLoaded', function() {

    // --- 1) Select all / delegation (works even if table is re-rendered) ---
    document.addEventListener('change', function(e) {
        // عندما يتغير الـ checkbox "تحديد الكل"
        if (e.target && e.target.id === 'checkAllLead') {
            const checked = e.target.checked;
            document.querySelectorAll('input.lead-checkbox').forEach(cb => cb.checked = checked);
        }
    });

    // optional: keep checkAll checked state in sync if user manually toggles individual boxes
    document.addEventListener('change', function(e) {
        if (e.target && e.target.classList && e.target.classList.contains('lead-checkbox')) {
            const all = document.querySelectorAll('input.lead-checkbox');
            const checkedAll = all.length > 0 && Array.from(all).every(cb => cb.checked);
            const checkAllEl = document.getElementById('checkAllLead');
            if (checkAllEl) checkAllEl.checked = checkedAll;
        }
    });

    // --- 2) Bulk assign handler (uses 'interviews' as payload) ---
    const bulkAssignBtn = document.getElementById('bulkAssignBtn');
    if (!bulkAssignBtn) return; // لا تتابع لو الزر غير موجود

    bulkAssignBtn.addEventListener('click', function() {
        const employeeSelect = document.getElementById('bulkAssignEmployee');
        const employeeId = employeeSelect ? employeeSelect.value : null;

        // اجمع الـ checked interview ids
        const selected = Array.from(document.querySelectorAll('input.lead-checkbox:checked')).map(i => i.value);

        if (selected.length === 0) {
            toastr.error('يرجى اختيار المقابلات');
            return;
        }

        const confirmMessage = employeeId
            ? 'هل أنت متأكد من تعيين المقابلات؟'
            : 'سيتم التعيين تلقائياً (Round-Robin) إلى الموظف الأقل، هل أنت متأكد؟';

        if (!confirm(confirmMessage)) return;

        // show overlay if موجود
        const loadingOverlay = document.getElementById('loadingOverlay');
        if (loadingOverlay) {
            loadingOverlay.style.display = 'flex';
            const lt = loadingOverlay.querySelector('.loading-text');
            if (lt) lt.textContent = `جاري تعيين ${selected.length} مقابلة...`;
        }

        // disable button UI
        bulkAssignBtn.disabled = true;
        const originalText = bulkAssignBtn.innerHTML;
        bulkAssignBtn.innerHTML = '<i class="feather-loader spin me-2"></i> جاري التعيين...';

        const overlayTimeout = setTimeout(() => {
            if (loadingOverlay) loadingOverlay.style.display = 'none';
            bulkAssignBtn.disabled = false;
            bulkAssignBtn.innerHTML = originalText;
            toastr.error('انتهت مهلة العملية. يرجى المحاولة مرة أخرى.');
        }, 15000);

        // POST request: note 'interviews' in body
        fetch("{{ route('interview.bulkAssign') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                employee_id: employeeId || null,
                interviews: selected
            })
        })
        .then(res => {
            clearTimeout(overlayTimeout);
            if (!res.ok) throw new Error('فشل الاتصال بالخادم: ' + res.status);
            return res.json();
        })
        .then(data => {
            if (data.success) {
                toastr.success(data.message || 'تم التعيين بنجاح');
                // إعادة تحميل الصفحة بعد تأخير بسيط
                setTimeout(() => location.reload(), 1200);
            } else {
                toastr.error(data.message || 'فشل التعيين');
            }
        })
        .catch(err => {
            console.error('Bulk assign error:', err);
            toastr.error(err.message || 'حدث خطأ أثناء الاتصال بالخادم');
        })
        .finally(() => {
            clearTimeout(overlayTimeout);
            if (loadingOverlay) loadingOverlay.style.display = 'none';
            bulkAssignBtn.disabled = false;
            bulkAssignBtn.innerHTML = originalText;

             // reload بعد العملية في كل الحالات
            setTimeout(() => location.reload(), 1200);

            // uncheck everything (optional)
            document.querySelectorAll('input.lead-checkbox:checked').forEach(cb => cb.checked = false);
            const checkAllEl = document.getElementById('checkAllLead');
            if (checkAllEl) checkAllEl.checked = false;
        });
    });


});
</script>

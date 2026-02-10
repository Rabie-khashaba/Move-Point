@extends('layouts.app')

@section('title', 'عرض المندوب')

@section('content')
    <div class="nxl-content">
        <!-- [ page-header ] start -->
        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">المندوبين</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('representatives-not-completed.index') }}">المندوبين</a>
                    </li>
                </ul>
            </div>
            <div class="page-header-right ms-auto">
                <div class="page-header-right-items d-flex align-items-center gap-2">
                    @can('edit_representatives')
                        <a href="{{ route('representatives-not-completed.edit', $representative->id) }}"
                            class="btn btn-warning d-flex align-items-center">
                            <i class="feather-edit me-2"></i>
                            <span>تعديل</span>
                        </a>
                    @endcan
                    {{-- @can('edit_representatives')
                    <form action="{{ route('representatives-not-completed.toggle-status', $representative->id) }}"
                        method="POST">
                        @csrf
                        <button type="submit"
                            class="btn btn-outline-{{ $representative->is_active ? 'danger' : 'success' }} d-flex align-items-center"
                            title="{{ $representative->is_active ? 'إيقاف' : 'تفعيل' }}">
                            <i class="feather-{{ $representative->is_active ? 'pause' : 'play' }} me-2"></i>
                            <span>{{ $representative->is_active ? 'إيقاف' : 'تفعيل' }}</span>
                        </button>
                    </form>
                    @endcan --}}

                    <button type="button"
                        class="btn btn-outline-{{ $representative->is_active ? 'danger' : 'success' }} d-flex align-items-center"
                        title="إستقاله" data-id="{{ $representative->id }}" data-bs-toggle="modal"
                        data-bs-target="#ReactiveModal">
                        <i class="feather-{{ $representative->is_active ? 'pause' : 'play' }} me-2"></i>
                        <span>إستقاله</span>
                    </button>

                    @if($representative->delivery_deposits_count == 7)
                        @can('edit_representatives')
                            <form action="{{ route('representatives-not-completed.transferToActive2', $representative->id) }}"
                                method="POST">
                                @csrf
                                <button type="submit" class="btn btn-outline-success d-flex align-items-center"
                                    title="{{ $representative->status ? 'إيقاف' : 'تحويل  ل مندوب فعلي' }}">
                                    <i class="feather-user-check me-2"></i>
                                    <span>{{ $representative->status ? 'إيقاف' : 'تحويل لمندوب فعلي' }}</span>
                                </button>
                            </form>
                        @endcan
                    @endif
                </div>

            </div>
        </div>
        <!-- [ page-header ] end -->

        <!-- [ Main Content ] start -->
        <div class="main-content">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">تفاصيل المندوب</h5>
                        </div>
                        <div class="card-body">
                            @php
                                $inquiry = $representative->inquiry;
                                $inquiryType = $inquiry->inquiry_type ?? $representative->inquiry_type;
                                $inquiryFieldResult = $inquiry->inquiry_field_result ?? $representative->inquiry_field_result;
                                $inquiryFieldNotes = $inquiry->inquiry_field_notes ?? $representative->inquiry_field_notes;
                                $inquirySecurityResult = $inquiry->inquiry_security_result ?? $representative->inquiry_security_result;
                                $inquirySecurityNotes = $inquiry->inquiry_security_notes ?? $representative->inquiry_security_notes;
                                $fieldInquiryAttachments = $inquiry->inquiry_field_attachments ?? $representative->inquiry_field_attachments ?? [];
                                $securityInquiryAttachments = $inquiry->inquiry_security_attachments ?? $representative->inquiry_security_attachments ?? [];
                            @endphp

                            @if($inquirySecurityResult === 'has_judgments')
                                <div class="alert alert-danger">
                                    الحساب غير نشط لأسباب أمنية
                                </div>
                            @endif
                            <div class="row">
                                <!-- الاسم -->
                                <div class="col-md-6 mb-3">
                                    <label class="fw-bold">الاسم:</label>
                                    <span>{{ $representative->name ?: 'غير محدد' }}</span>
                                </div>

                                <!-- رقم التليفون -->
                                <div class="col-md-6 mb-3">
                                    <label class="fw-bold">رقم التليفون:</label>
                                    <span>{{ $representative->phone ?: 'غير محدد' }}</span>
                                </div>

                                <!-- العنوان -->
                                <div class="col-md-6 mb-3">
                                    <label class="fw-bold">العنوان:</label>
                                    <span>{{ $representative->address ?: 'غير محدد' }}</span>
                                </div>

                                <!-- التواصل -->
                                <div class="col-md-6 mb-3">
                                    <label class="fw-bold">التواصل:</label>
                                    <span>{{ $representative->contact ?: 'غير محدد' }}</span>
                                </div>

                                <!-- رقم البطاقة -->
                                <div class="col-md-6 mb-3">
                                    <label class="fw-bold">رقم البطاقة:</label>
                                    <span>{{ $representative->national_id ?: 'غير محدد' }}</span>
                                </div>

                                <!-- المرتب -->
                                <div class="col-md-6 mb-3">
                                    <label class="fw-bold">المرتب:</label>
                                    <span>{{ $representative->salary ? number_format($representative->salary, 2) . ' ج.م' : 'غير محدد' }}</span>
                                </div>

                                <!-- تاريخ بداية العمل -->
                                <div class="col-md-6 mb-3">
                                    <label class="fw-bold">تاريخ بداية العمل:</label>
                                    @if($representative->start_date)
                                        <span class="badge bg-primary">{{ $representative->start_date->format('d/m/Y') }}</span>
                                        <small class="text-muted d-block mt-1">تم التعيين في
                                            {{ $representative->start_date->diffForHumans() }}</small>
                                    @else
                                        <span class="text-muted">غير محدد</span>
                                    @endif
                                </div>

                                <!-- الشركة -->
                                <div class="col-md-6 mb-3">
                                    <label class="fw-bold">الشركة:</label>
                                    <span class="badge bg-info">{{ $representative->company->name ?? 'غير محدد' }}</span>
                                </div>

                                <!-- رقم محفظة او حساب بنكي -->
                                <div class="col-md-6 mb-3">
                                    <label class="fw-bold">رقم محفظة او حساب بنكي:</label>
                                    <span>{{ $representative->bank_account ?: 'غير محدد' }}</span>
                                </div>

                                <!-- كود المندوب -->
                                <div class="col-md-6 mb-3">
                                    <label class="fw-bold">كود المندوب:</label>
                                    <span class="badge bg-secondary">{{ $representative->code ?: 'غير محدد' }}</span>
                                </div>

                                <!-- المحافظة -->
                                <div class="col-md-6 mb-3">
                                    <label class="fw-bold">المحافظة:</label>
                                    <span>{{ $representative->governorate->name ?? 'غير محدد' }}</span>
                                </div>

                                <!-- الموقع -->
                                <div class="col-md-6 mb-3">
                                    <label class="fw-bold">المنطقة:</label>
                                    <span>{{ $representative->location->name ?? 'غير محدد' }}</span>
                                </div>

                                <!-- الاستعلام -->
                                <div class="col-md-12 mb-3">
                                    <label class="fw-bold">الاستعلام:</label>
                                    <div>
                                        @if($inquiryType === 'field')
                                            <span class="badge bg-primary">ميداني</span>
                                        @elseif($inquiryType === 'security')
                                            <span class="badge bg-danger">أمني</span>
                                        @else
                                            <span class="badge bg-secondary">لا يوجد</span>
                                        @endif
                                    </div>
                                </div>

                                @if($inquiryType === 'field')
                                    <div class="col-md-6 mb-3">
                                        <label class="fw-bold">نتيجة الاستعلام الميداني:</label>
                                        <span>{{ $inquiryFieldResult === 'good' ? 'حسن السمعه' : ($inquiryFieldResult === 'bad' ? 'سيء السمعه' : 'غير محدد') }}</span>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="fw-bold">ملاحظات الاستعلام الميداني:</label>
                                        <span>{{ $inquiryFieldNotes ?: 'غير محدد' }}</span>
                                    </div>
                                @endif

                                @if($inquiryType === 'security')
                                    <div class="col-md-6 mb-3">
                                        <label class="fw-bold">نتيجة الاستعلام الأمني:</label>
                                        <span>{{ $inquirySecurityResult === 'has_judgments' ? 'عليه أحكام' : ($inquirySecurityResult === 'no_judgments' ? 'ليس عليه أحكام' : 'غير محدد') }}</span>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="fw-bold">ملاحظات الاستعلام الأمني:</label>
                                        <span>{{ $inquirySecurityNotes ?: 'غير محدد' }}</span>
                                    </div>
                                @endif

                                @if($inquiryType === 'field' && count($fieldInquiryAttachments) > 0)
                                    <div class="col-md-12 mb-3">
                                        <label class="fw-bold">مرفقات الاستعلام الميداني:</label>
                                        <div class="mt-2 d-flex flex-wrap gap-2">
                                            @foreach($fieldInquiryAttachments as $attachment)
                                                @php
                                                    $path = $attachment['path'] ?? null;
                                                @endphp
                                                @if($path)
                                                    <a href="{{ asset('storage/app/public/' . str_replace(['\\', '//'], ['/', '/'], $path)) }}" target="_blank" class="btn btn-sm btn-outline-primary">عرض</a>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                                @if($inquiryType === 'security' && count($securityInquiryAttachments) > 0)
                                    <div class="col-md-12 mb-3">
                                        <label class="fw-bold">مرفقات الاستعلام الأمني:</label>
                                        <div class="mt-2 d-flex flex-wrap gap-2">
                                            @foreach($securityInquiryAttachments as $attachment)
                                                @php
                                                    $path = $attachment['path'] ?? null;
                                                @endphp
                                                @if($path)
                                                    <a href="{{ asset('storage/app/public/' . str_replace(['\\', '//'], ['/', '/'], $path)) }}" target="_blank" class="btn btn-sm btn-outline-danger">عرض</a>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                <!-- بيانات الاستعلام -->
                                <div class="col-md-6 mb-3">
                                    <label class="fw-bold">سبب الاستقالة :</label>
                                    @if($representative->resignationRequest)
                                        <div class="mt-2">
                                            <span>{{ $representative->resignationRequest->reason }}</span>
                                        </div>
                                    @else
                                        <span class="text-muted">غير محدد</span>
                                    @endif
                                </div>

                                @if($representative->unResignBy)
                                    <div class="col-md-6 mb-3">
                                        <label class="fw-bold">تم التفعيل بواسطة :</label>
                                        <div class="mt-2">
                                            <span>{{ $representative->unResignBy->name }}</span>
                                            @if($representative->unresign_date)
                                            <small class="text-muted">
                                                    {{ $representative->unresign_date ? \Carbon\Carbon::parse($representative->unresign_date)->format('Y-m-d H:i') : 'غير محدد' }}
                                                </small>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                                <div class="col-md-6 mb-3">
                                    <label class="fw-bold">بيانات الحساب البنكي</label>

                                    <div class="mt-2">
                                        <div class="mb-1">
                                            <strong>اسم البنك:</strong>
                                            <span class="badge bg-info">{{ $representative->bankAccounts->first()?->bank?->name ?? '-' }}</span>
                                        </div>

                                        <div class="mb-1">
                                            <strong>رقم الحساب:</strong>
                                            <span class="badge bg-info">{{ $representative->bankAccounts->first()?->account_number ?? '-' }}</span>
                                        </div>

                                        <div class="mb-1">
                                            <strong>اسم صاحب الحساب:</strong>
                                            <span class="badge bg-info">{{ $representative->bankAccounts->first()?->account_owner_name ?? '-' }}</span>
                                        </div>


                                    </div>
                                </div>








                                <!-- لوكيشن المنزل -->
                                <div class="col-md-12 mb-3">
                                    <label class="fw-bold">لوكيشن المنزل:</label>
                                    @if($representative->home_location)
                                        <div class="mt-2">
                                            <div class="d-flex align-items-start gap-2">
                                                <span class="flex-grow-1">{{ $representative->home_location }}</span>
                                                <a href="{{ $representative->home_location }}" target="_blank"
                                                    class="btn btn-sm btn-outline-info">
                                                    <i class="feather-map-pin me-1"></i>فتح في الخريطة
                                                </a>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-muted">غير محدد</span>
                                    @endif
                                </div>

                                <!-- الحالة -->
                                <div class="col-md-6 mb-3">
                                    <label class="fw-bold">الحالة:</label>
                                    <span class="badge bg-{{ $representative->is_active ? 'success' : 'danger' }}">
                                        {{ $representative->is_active ? 'نشط' : 'غير نشط' }}
                                    </span>
                                </div>
                            </div>

                            <!-- المرفقات -->
                            @if(isset($representative->attachments_with_urls) && count($representative->attachments_with_urls) > 0)
                                    <div class="row mt-4">
                                        <div class="col-12">
                                            <label class="fw-bold">المرفقات:</label>
                                            <div class="row mt-2">
                                                @foreach($representative->attachments_with_urls as $attachment)
                                                    @php
                                                        $extension = strtolower(pathinfo($attachment['path'], PATHINFO_EXTENSION));
                                                        $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'gif']);
                                                    @endphp
                                                    <div class="col-md-4 mb-3">
                                                        <div class="card">
                                                            <div class="card-body p-3">
                                                                <div class="d-flex align-items-center mb-2">
                                                                    @if($isImage)
                                                                        <i class="feather-image me-2 text-success"></i>
                                                                    @else
                                                                        <i class="feather-file-text me-2 text-primary"></i>
                                                                    @endif
                                                                    <small class="text-muted fw-bold">
                                                                        {{ $attachment['type'] ?? 'مرفق' }}
                                                                    </small>
                                                                </div>

                                                                @if($isImage)
                                                                    <div class="mb-2">
                                                                        <img src="{{ $attachment['url'] }}" alt="معاينة المرفق"
                                                                            class="img-fluid rounded"
                                                                            style="max-height: 150px; width: 100%; object-fit: cover;">
                                                                    </div>
                                                                @endif

                                                                <div class="d-flex gap-2">
                                                                    <a href="{{ $attachment['url'] }}" target="_blank"
                                                                        class="btn btn-sm btn-outline-primary">
                                                                        <i class="feather-eye me-1"></i> عرض
                                                                    </a>

                                                                    <a href="{{ $attachment['url'] }}" download
                                                                        class="btn btn-sm btn-outline-success">
                                                                        <i class="feather-download me-1"></i> تحميل
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>


                                        </div>
                                    </div>
                                </div>
                            @else
                            <div class="row mt-4">
                                <div class="col-12">
                                    <label class="fw-bold">المرفقات:</label>
                                    <div class="mt-2">
                                        <span class="text-muted">لا توجد مرفقات</span>
                                    </div>
                                </div>
                            </div>
                        @endif


                        <hr class="my-4">

                        <div class="row">
                            <div class="col-md-6">
                                <label class="fw-bold">تاريخ الإنشاء:</label>
                                <span>{{ $representative->created_at->format('d/m/Y H:i') }}</span>
                            </div>

                            <div class="col-md-6">
                                <label class="fw-bold">آخر تحديث:</label>
                                <span>{{ $representative->updated_at->format('d/m/Y H:i') }}</span>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-6 mb-2">
                                <label class="fw-bold">تاريخ التعيين:</label>
                                <span>{{ $representative->start_date ? $representative->start_date->format('d/m/Y') : 'غير محدد' }}</span>
                            </div>

                            <div class="col-md-6 mb-2">
                                <label class="fw-bold">تاريخ التحويل لمندوب فعلي:</label>
                                <span>{{ $representative->converted_to_active_date ? \Carbon\Carbon::parse($representative->converted_to_active_date)->format('d/m/Y') : 'غير محدد' }}</span>
                            </div>

                            <div class="col-md-6 mb-2">
                                <label class="fw-bold">تاريخ التحويل من فعلي لغير مكتمل:</label>
                                <span>{{ $representative->converted_to_notcompleted_date ? \Carbon\Carbon::parse($representative->converted_to_notcompleted_date)->format('d/m/Y H:i') : 'غير محدد' }}</span>
                            </div>

                            <div class="col-md-6 mb-2">
                                <label class="fw-bold">تاريخ المحاضرة:</label>
                                <span>{{ $trainingDate ? \Carbon\Carbon::parse($trainingDate)->format('d/m/Y') : 'غير محدد' }}</span>
                            </div>

                            <div class="col-md-6 mb-2">
                                <label class="fw-bold">تاريخ بدء العمل:</label>
                                <span>{{ $workStartDate ? \Carbon\Carbon::parse($workStartDate->date)->format('d/m/Y') : 'غير محدد' }}</span>
                            </div>

                            <div class="col-md-6 mb-2">
                                <label class="fw-bold">تاريخ الاستقالة:</label>
                                <span>{{ $representative->resign_date ? \Carbon\Carbon::parse($representative->resign_date)->format('d/m/Y H:i') : 'غير محدد' }}</span>
                            </div>

                            <div class="col-md-6 mb-2">
                                <label class="fw-bold">تاريخ التفعيل:</label>
                                <span>{{ $representative->unresign_date ? \Carbon\Carbon::parse($representative->unresign_date)->format('d/m/Y H:i') : 'غير محدد' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <!-- HR Management Tabs -->
        <div class="row mt-4">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <ul class="nav nav-tabs" id="hrTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="leave-tab" data-bs-toggle="tab" data-bs-target="#leave" type="button" role="tab">
                                    <i class="feather-calendar me-2"></i>طلبات السلف
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="schedule-tab" data-bs-toggle="tab" data-bs-target="#schedule" type="button" role="tab">
                                    <i class="feather-clock me-2"></i>المديونات
                                </button>
                            </li>


                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content" id="hrTabsContent">
                            <!-- Leave Requests Tab -->
                            <div class="tab-pane fade show active" id="leave" role="tabpanel">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="mb-0">طلبات السلف</h6>
                                  {{--  @can('create_leave_requests')
                                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addLeaveModal">
                                        <i class="feather-plus me-1"></i>إضافة طلب إجازة
                                    </button>
                                    @endcan --}}
                                </div>
                                @php
                                    $advanceRequests = $representative->advanceRequests()->latest()->take(5)->get();
                                @endphp
                                @if($advanceRequests->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>المبلغ</th>
                                                <th>التقسيط</th>
                                                <th>الحاله</th>
                                                <th>تاريخ الطلب</th>

                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($advanceRequests as $advance)
                                            <tr>

                                                <td>{{ number_format($advance->amount, 2) }} ج.م</td>
                                                <td>
                                                    @if($advance->is_installment)
                                                        <span class="badge bg-info">{{ $advance->installment_months }} شهر</span>
                                                        <br>
                                                        <small>{{ number_format($advance->monthly_installment, 2) }} ج.م/شهر</small>
                                                    @else
                                                        <span class="badge bg-secondary">دفعة واحدة</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($advance->status === 'pending')
                                                        <span class="badge bg-warning">في الانتظار</span>
                                                    @elseif($advance->status === 'approved')
                                                        <span class="badge bg-success">تمت الموافقة</span>
                                                    @else
                                                        <span class="badge bg-danger">مرفوض</span>
                                                    @endif
                                                </td>
                                                <td>{{ $advance->created_at->format('Y-m-d') }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                               {{-- <div class="text-center mt-3">
                                    <a href="{{ route('advance-requests.index', ['search' => $representative->name]) }}" class="btn btn-sm btn-outline-primary w-auto">عرض جميع الطلبات</a>
                                </div> --}}
                                @else
                                <div class="text-center py-3">
                                    <i class="feather-calendar text-muted fs-24 mb-2"></i>
                                    <p class="text-muted mb-0">لا توجد  طلبات سلف</p>
                                </div>
                                @endif
                            </div>

                            <!-- Work Schedule Tab -->
                            <div class="tab-pane fade" id="schedule" role="tabpanel">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="mb-0">المديونات</h6>
                                   {{-- @can('create_work_schedules')
                                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addScheduleModal">
                                        <i class="feather-plus me-1"></i>إضافة مواعيد عمل
                                    </button>
                                    @endcan --}}
                                </div>
                                @php
                                    $debits = $representative->debits()->latest()->take(3)->get();
                                @endphp
                                @if($debits->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>الشركه</th>
                                                <th>المبلغ</th>
                                                <th>الحاله</th>
                                                <th> اخر تحديث</th>

                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($debits as $debit)
                                            <tr>
                                                <td>{{ $debit->representative->company->name }}</td>
                                                <td>{{ number_format($debit->loan_amount, 2) }} ج.م</td>
                                                <td>
                                                    @if($debit->status === 'سدد')
                                                        <span class="badge bg-success">سدد</span>
                                                    @else
                                                        <span class="badge bg-danger">لم يسدد</span>
                                                    @endif
                                                </td>
                                                <td>{{ $debit->updated_at?->format('Y-m-d H:i') ?? 'غير متاح' }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                @else
                                <div class="text-center py-3">
                                    <i class="feather-clock text-muted fs-24 mb-2"></i>
                                    <p class="text-muted mb-0">لا توجد مديونات</p>
                                </div>
                                @endif
                            </div>


                        </div>
                    </div>
                </div>
            </div>
        </div>


        </div>
    </div>
    <!-- [ Main Content ] end -->
    </div>


    <div class="modal fade" id="ReactiveModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">الاستقاله</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="interviewForm" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="representative_id">
                    <div class="modal-body">
                        <div class="row">

                            <div class="col-md-6 mb-3">
                                <label class="form-label">تاريخ الاستقاله</label>
                                <input type="datetime-local" name="date" class="form-control" required
                                    value="{{ now()->format('Y-m-d\TH:i') }}">
                            </div>

                            <div class="col-md-12 mb-3">
                                <label class="form-label">السبب</label>
                                <textarea name="reason" class="form-control" rows="4" required></textarea>
                            </div>

                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-success" onclick="return confirm('هل أنت متأكد ؟')">
                            <i class="feather-calendar me-1"></i>تنفيذ الاستقاله
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection
<script>

    document.addEventListener('DOMContentLoaded', function () {

        const reactiveModal = document.getElementById('ReactiveModal');

        reactiveModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const repId = button.getAttribute('data-id');

            const form = reactiveModal.querySelector('form');
            const hiddenInput = form.querySelector('input[name="representative_id"]');

            hiddenInput.value = repId;

            // حدّث الـ action بالرابط الصحيح
            form.action = "{{ route('representatives-not-completed.resignation', ':id') }}"
                .replace(':id', repId);
        });

        // Interview modal functionality



    });
</script>












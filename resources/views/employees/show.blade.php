@extends('layouts.app')

@section('title', 'عرض الموظف')

@section('content')
<div class="nxl-content">
    <!-- [ page-header ] start -->
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="m-b-10">الموظفين</h5>
            </div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('employees.index') }}">الموظفين</a></li>
                <li class="breadcrumb-item">عرض</li>
            </ul>
        </div>
        <div class="page-header-right ms-auto">
            <div class="page-header-right-items">
                @can('edit_employees')
                <a href="{{ route('employees.edit', $employee->id) }}" class="btn btn-warning">
                    <i class="feather-edit me-2"></i>
                    <span>تعديل</span>
                </a>
                @endcan
                {{--
                <form action="{{ route('employees.destroy', $employee->id) }}" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" onclick="return confirm('هل أنت متأكد أنك تريد حذف هذا الموظف؟')">
                        <i class="feather-trash-2 me-2"></i>
                        <span>حذف</span>
                    </button>
                </form>
                --}}
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
                        <h5 class="card-title mb-0">تفاصيل الموظف</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">

                            <div class="col-md-6 mb-3">
                                <label class="fw-bold">الاسم:</label>
                                <span>{{ $employee->name ?: 'غير متوفر' }}</span>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="fw-bold">الهاتف:</label>
                                <span>{{ $employee->phone ?: 'غير متوفر' }}</span>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="fw-bold">رقم الواتس:</label>
                                <span>{{ $employee->whatsapp_phone ?: 'غير متوفر' }}</span>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="fw-bold">العنوان:</label>
                                <span>{{ $employee->address ?: 'غير متوفر' }}</span>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="fw-bold"> جهة تواصل الاسرة:</label>
                                <span>{{ $employee->contact ?: 'غير متوفر' }}</span>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="fw-bold">الراتب:</label>
                                <span>{{ $employee->salary ?: 'غير متوفر' }}</span>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="fw-bold">تاريخ البداية:</label>
                                <span>{{ $employee->start_date ? $employee->start_date->format('d M, Y') : 'غير متوفر' }}</span>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="fw-bold">القسم:</label>
                                <span>{{ $employee->department->name ?? 'غير متوفر' }}</span>
                            </div>



                            <!-- Attachments -->
                            <div class="col-md-6 mb-3">
                                <label class="fw-bold">المرفقات:</label>
                                @if($employee->attachments)
                                    @php
                                        $attachments = null;
                                        if (is_string($employee->attachments)) {
                                            $attachments = json_decode($employee->attachments, true);
                                        } elseif (is_array($employee->attachments)) {
                                            $attachments = $employee->attachments;
                                        }
                                    @endphp

                                    @if($attachments && is_array($attachments) && count($attachments) > 0)
                                        <div class="mt-2">
                                            @foreach($attachments as $index => $attachment)
                                                @if($attachment)
                                                    @php
                                                        $isImage = in_array(strtolower(pathinfo($attachment, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                                    @endphp
                                                    <div class="card mb-3">
                                                        <div class="card-body">
                                                            @if($isImage)
                                                            <div class="mb-2">
                                                                @php
                                                                    // Check if attachment is already a full URL
                                                                    if (filter_var($attachment, FILTER_VALIDATE_URL)) {
                                                                        // If it's an old storage URL format, convert it to the new format
                                                                        if (strpos($attachment, '/storage/attachments/') !== false) {
                                                                            $imageUrl = str_replace('/storage/attachments/', '/storage/app/public/attachments/', $attachment);
                                                                        } elseif (strpos($attachment, '/storage/representatives/attachments/') !== false) {
                                                                            $imageUrl = str_replace('/storage/representatives/attachments/', '/storage/app/public/representatives/attachments/', $attachment);
                                                                        } elseif (strpos($attachment, '/storage/delivery-receipts/') !== false) {
                                                                            $imageUrl = str_replace('/storage/delivery-receipts/', '/storage/app/public/delivery-receipts/', $attachment);
                                                                        } elseif (strpos($attachment, '/storage/sliders/') !== false) {
                                                                            $imageUrl = str_replace('/storage/sliders/', '/storage/app/public/sliders/', $attachment);
                                                                        } else {
                                                                            $imageUrl = $attachment;
                                                                        }
                                                                    } else {
                                                                        $imageUrl = asset('storage/app/public/' . $attachment);
                                                                    }
                                                                @endphp
                                                                <img src="{{ $imageUrl }}"
                                                                     alt="معاينة المرفق"
                                                                     class="img-fluid rounded"
                                                                     style="max-height: 150px; width: 100%; object-fit: cover;"
                                                                     onerror="this.style.display='none'">
                                                            </div>
                                                            @endif

                                                            <div class="d-flex gap-2">
                                                                <a href="{{ route('employees.attachment.view', ['id' => $employee->id, 'index' => $index]) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                                    <i class="feather-eye me-1"></i> عرض
                                                                </a>
                                                                <a href="{{ route('employees.attachment.download', ['id' => $employee->id, 'index' => $index]) }}" class="btn btn-sm btn-outline-success">
                                                                    <i class="feather-download me-1"></i> تحميل
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-muted">لا توجد مرفقات</span>
                                    @endif
                                @else
                                    <span class="text-muted">لا توجد مرفقات</span>
                                @endif
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="fw-bold">الحالة:</label>
                                <span class="badge bg-{{ $employee->is_active ? 'success' : 'danger' }}">
                                    {{ $employee->is_active ? 'نشط' : 'غير نشط' }}
                                </span>
                            </div>
                        </div>

                        <div class="mt-4">
                            <label class="fw-bold">تم الإنشاء في:</label>
                            <span>{{ $employee->created_at->format('d M, Y') }}</span>
                        </div>

                        <div class="mt-2">
                            <label class="fw-bold">آخر تحديث:</label>
                            <span>{{ $employee->updated_at->format('d M, Y') }}</span>
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
                                    <i class="feather-calendar me-2"></i>طلبات الإجازة
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="schedule-tab" data-bs-toggle="tab" data-bs-target="#schedule" type="button" role="tab">
                                    <i class="feather-clock me-2"></i>مواعيد العمل
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="targets-tab" data-bs-toggle="tab" data-bs-target="#targets" type="button" role="tab">
                                    <i class="feather-target me-2"></i>الأهداف
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="salary-tab" data-bs-toggle="tab" data-bs-target="#salary" type="button" role="tab">
                                    <i class="feather-dollar-sign me-2"></i>المرتبات
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="resignation-tab" data-bs-toggle="tab" data-bs-target="#resignation" type="button" role="tab">
                                    <i class="feather-user-minus me-2"></i>طلبات الاستقالة
                                </button>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content" id="hrTabsContent">
                            <!-- Leave Requests Tab -->
                            <div class="tab-pane fade show active" id="leave" role="tabpanel">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="mb-0">طلبات الإجازة</h6>
                                    @can('create_leave_requests')
                                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addLeaveModal">
                                        <i class="feather-plus me-1"></i>إضافة طلب إجازة
                                    </button>
                                    @endcan
                                </div>
                                @php
                                    $leaveRequests = $employee->leaveRequests()->latest()->take(5)->get();
                                @endphp
                                @if($leaveRequests->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>النوع</th>
                                                <th>من</th>
                                                <th>إلى</th>
                                                <th>المدة</th>
                                                <th>الحالة</th>
                                                <th>التاريخ</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($leaveRequests as $leave)
                                            <tr>
                                                <td><span class="badge bg-info">{{ $leave->type }}</span></td>
                                                <td>{{ $leave->start_date->format('Y-m-d') }}</td>
                                                <td>{{ $leave->end_date->format('Y-m-d') }}</td>
                                                <td>{{ $leave->duration }} يوم</td>
                                                <td>
                                                    @if($leave->status === 'pending')
                                                        <span class="badge bg-warning">في الانتظار</span>
                                                    @elseif($leave->status === 'approved')
                                                        <span class="badge bg-success">تمت الموافقة</span>
                                                    @else
                                                        <span class="badge bg-danger">مرفوض</span>
                                                    @endif
                                                </td>
                                                <td>{{ $leave->created_at->format('Y-m-d') }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <div class="text-center mt-3">
                                    <a href="{{ route('leave-requests.index', ['search' => $employee->name]) }}" class="btn btn-sm btn-outline-primary">عرض جميع الطلبات</a>
                                </div>
                                @else
                                <div class="text-center py-3">
                                    <i class="feather-calendar text-muted fs-24 mb-2"></i>
                                    <p class="text-muted mb-0">لا توجد طلبات إجازة</p>
                                </div>
                                @endif
                            </div>

                            <!-- Work Schedule Tab -->
                            <div class="tab-pane fade" id="schedule" role="tabpanel">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="mb-0">مواعيد العمل</h6>
                                    @can('create_work_schedules')
                                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addScheduleModal">
                                        <i class="feather-plus me-1"></i>إضافة مواعيد عمل
                                    </button>
                                    @endcan
                                </div>
                                @php
                                    $workSchedules = $employee->workSchedules()->latest()->take(3)->get();
                                @endphp
                                @if($workSchedules->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>الشيفت</th>
                                                <th>وقت البداية</th>
                                                <th>وقت النهاية</th>
                                                <th>أيام العمل</th>
                                                <th>الحالة</th>
                                                <th>تاريخ التفعيل</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($workSchedules as $schedule)
                                            <tr>
                                                <td><span class="badge bg-{{ $schedule->shift === 'صباحي' ? 'success' : 'warning' }}">{{ $schedule->shift }}</span></td>
                                                <td>{{ $schedule->start_time }}</td>
                                                <td>{{ $schedule->end_time }}</td>
                                                <td>{{ count($schedule->work_days) }} أيام</td>
                                                <td>
                                                    <span class="badge bg-{{ $schedule->is_active ? 'success' : 'secondary' }}">
                                                        {{ $schedule->is_active ? 'نشط' : 'غير نشط' }}
                                                    </span>
                                                </td>
                                                <td>{{ $schedule->effective_date->format('Y-m-d') }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                @else
                                <div class="text-center py-3">
                                    <i class="feather-clock text-muted fs-24 mb-2"></i>
                                    <p class="text-muted mb-0">لا توجد مواعيد عمل محددة</p>
                                </div>
                                @endif
                            </div>

                            <!-- Targets Tab -->
                            <div class="tab-pane fade" id="targets" role="tabpanel">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="mb-0">الأهداف الشهرية</h6>
                                </div>
                                @php
                                    $currentTarget = $employee->targets()->where('year', now()->year)->where('month', now()->month)->first();
                                    $recentTargets = $employee->targets()->latest()->take(6)->get();
                                @endphp

                                @if($currentTarget)
                                <div class="alert alert-info mb-3">
                                    <h6><i class="feather-target me-2"></i>هدف الشهر الحالي ({{ now()->format('F Y') }})</h6>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <strong>عدد المتابعات:</strong> {{ $currentTarget->target_follow_ups }}
                                        </div>
                                        <div class="col-md-3">
                                            <strong>المحقق:</strong> {{ $currentTarget->achieved_follow_ups_with_update }}
                                        </div>
                                        <div class="col-md-3">
                                            <strong>النسبة:</strong> {{ $currentTarget->achievement_percentage }}%
                                        </div>
                                        <div class="col-md-3">
                                            <strong>المتبقي:</strong> {{ $currentTarget->remaining_follow_ups }}
                                        </div>
                                    </div>
                                </div>
                                @endif

                                @if($recentTargets->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>الشهر</th>
                                                <th>عدد المتابعات</th>
                                                <th>المحقق</th>
                                                <th>النسبة</th>
                                                <th>المتبقي</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($recentTargets as $target)
                                            <tr>
                                                <td>{{ $target->month_name }} {{ $target->year }}</td>
                                                <td>{{ $target->target_follow_ups }}</td>
                                                <td>{{ $target->achieved_follow_ups_with_update }}</td>
                                                <td>
                                                    @php
                                                        $achieved = $target->achieved_follow_ups_with_update;
                                                        $percentage = $target->target_follow_ups > 0 ? round(($achieved / $target->target_follow_ups) * 100, 2) : 0;
                                                    @endphp
                                                    <span class="badge bg-{{ $percentage >= 100 ? 'success' : ($percentage >= 75 ? 'warning' : 'danger') }}">
                                                        {{ $percentage }}%
                                                    </span>
                                                </td>
                                                <td>{{ max(0, $target->target_follow_ups - $achieved) }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                @else
                                <div class="text-center py-3">
                                    <i class="feather-target text-muted fs-24 mb-2"></i>
                                    <p class="text-muted mb-0">لا توجد أهداف محددة</p>
                                </div>
                                @endif
                            </div>

                            <!-- Salary Tab -->
                            <div class="tab-pane fade" id="salary" role="tabpanel">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="mb-0">سجلات المرتبات</h6>
                                </div>
                                @php
                                    $recentSalaries = $employee->salaryRecords()->latest()->take(6)->get();
                                @endphp
                                @if($recentSalaries->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>الشهر</th>
                                                <th>المرتب الأساسي</th>
                                                <th>الخصومات</th>
                                                <th>الإضافات</th>
                                                <th>صافي المرتب</th>
                                                <th>حالة الدفع</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($recentSalaries as $salary)
                                            <tr>
                                                <td>{{ $salary->month_name }} {{ $salary->year }}</td>
                                                <td>{{ number_format($salary->base_salary, 0) }}</td>
                                                <td class="text-danger">{{ number_format($salary->total_deductions, 0) }}</td>
                                                <td class="text-success">{{ number_format($salary->total_additions, 0) }}</td>
                                                <td class="fw-bold">{{ number_format($salary->net_salary, 0) }}</td>
                                                <td>
                                                    <span class="badge bg-{{ $salary->is_paid ? 'success' : 'warning' }}">
                                                        {{ $salary->is_paid ? 'تم الدفع' : 'لم يدفع' }}
                                                    </span>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                @else
                                <div class="text-center py-3">
                                    <i class="feather-dollar-sign text-muted fs-24 mb-2"></i>
                                    <p class="text-muted mb-0">لا توجد سجلات مرتبات</p>
                                </div>
                                @endif
                            </div>

                            <!-- Resignation Tab -->
                            <div class="tab-pane fade" id="resignation" role="tabpanel">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="mb-0">طلبات الاستقالة</h6>
                                </div>
                                @php
                                    $resignationRequests = $employee->resignationRequests()->latest()->take(3)->get();
                                @endphp
                                @if($resignationRequests->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>تاريخ الاستقالة</th>
                                                <th>آخر يوم عمل</th>
                                                <th>السبب</th>
                                                <th>الحالة</th>
                                                <th>تاريخ الطلب</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($resignationRequests as $resignation)
                                            <tr>
                                                <td>{{ $resignation->resignation_date->format('Y-m-d') }}</td>
                                                <td>{{ $resignation->last_working_day->format('Y-m-d') }}</td>
                                                <td>{{ Str::limit($resignation->reason, 30) }}</td>
                                                <td>
                                                    @if($resignation->status === 'pending')
                                                        <span class="badge bg-warning">في الانتظار</span>
                                                    @elseif($resignation->status === 'approved')
                                                        <span class="badge bg-success">تمت الموافقة</span>
                                                    @else
                                                        <span class="badge bg-danger">مرفوض</span>
                                                    @endif
                                                </td>
                                                <td>{{ $resignation->created_at->format('Y-m-d') }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                @else
                                <div class="text-center py-3">
                                    <i class="feather-user-minus text-muted fs-24 mb-2"></i>
                                    <p class="text-muted mb-0">لا توجد طلبات استقالة</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- [ Main Content ] end -->
</div>

<!-- Add Leave Request Modal -->
@can('create_leave_requests')
<div class="modal fade" id="addLeaveModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">إضافة طلب إجازة</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('leave-requests.store') }}" method="POST">
                @csrf
                <input type="hidden" name="employee_id" value="{{ $employee->id }}">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">تاريخ البداية</label>
                                <input type="date" name="start_date" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">تاريخ النهاية</label>
                                <input type="date" name="end_date" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">نوع الإجازة</label>
                        <select name="type" class="form-control" required>
                            <option value="">اختر نوع الإجازة</option>
                            <option value="سنوية">سنوية</option>
                            <option value="مرضية">مرضية</option>
                            <option value="طارئة">طارئة</option>
                            <option value="أخرى">أخرى</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">السبب</label>
                        <textarea name="reason" class="form-control" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">إضافة الطلب</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endcan

<!-- Add Work Schedule Modal -->
@can('create_work_schedules')
<div class="modal fade" id="addScheduleModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">إضافة مواعيد عمل</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('work-schedules.store') }}" method="POST">
                @csrf
                <input type="hidden" name="employee_id" value="{{ $employee->id }}">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">الشيفت</label>
                                <select name="shift" class="form-control" required>
                                    <option value="">اختر الشيفت</option>
                                    <option value="صباحي">صباحي</option>
                                    <option value="مسائي">مسائي</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">تاريخ التفعيل</label>
                                <input type="date" name="effective_date" class="form-control" value="{{ now()->format('Y-m-d') }}" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">وقت البداية</label>
                                <input type="time" name="start_time" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">وقت النهاية</label>
                                <input type="time" name="end_time" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">أيام العمل</label>
                        <div class="row">
                            @php
                                $workDays = ['السبت', 'الأحد', 'الاثنين', 'الثلاثاء', 'الأربعاء', 'الخميس', 'الجمعة'];
                            @endphp
                            @foreach($workDays as $day)
                                <div class="col-md-4 col-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="work_days[]" value="{{ $day }}" id="day_{{ $loop->index }}"
                                            {{ in_array($day, ['السبت', 'الأحد', 'الاثنين', 'الثلاثاء', 'الأربعاء', 'الخميس']) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="day_{{ $loop->index }}">
                                            {{ $day }}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">ملاحظات</label>
                        <textarea name="notes" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">إضافة مواعيد العمل</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endcan
@endsection

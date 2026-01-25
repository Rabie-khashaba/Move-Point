@extends('layouts.app')

@section('title', 'أهداف الموظفين')

@section('content')
    <div class="nxl-content">
        <!-- [ page-header ] start -->
        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
                    <li class="breadcrumb-item">تارجت الموظفين</li>
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
                        <a href="javascript:void(0);" class="btn btn-icon btn-light-brand" data-bs-toggle="collapse"
                            data-bs-target="#filterCollapse">
                            <i class="feather-filter"></i>
                        </a>
                        <a href="{{ route('employee-targets.export', ['year' => $currentYear, 'month' => $currentMonth]) }}"
                            class="btn btn-success">
                            <i class="feather-download me-2"></i>
                            <span>تصدير Excel</span>
                        </a>
                        @can('create_employee_targets')
                            <form action="{{ route('employee-targets.generate-monthly') }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-info"
                                    onclick="return confirm('هل تريد إنشاء أهداف للشهر الحالي لجميع الموظفين؟')">
                                    <i class="feather-refresh-cw me-2"></i>
                                    <span>إنشاء أهداف الشهر</span>
                                </button>
                            </form>
                        @endcan
                        @can('edit_employee_targets')
                            <form
                                action="{{ route('employee-targets.refresh-achieved', ['year' => $currentYear, 'month' => $currentMonth]) }}"
                                method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-warning"
                                    onclick="return confirm('هل تريد تحديث المحقق لجميع الأهداف؟')">
                                    <i class="feather-refresh-cw me-2"></i>
                                    <span>تحديث المحقق</span>
                                </button>
                            </form>
                        @endcan

                        @can('view_employee_targets')
                            <a href="{{ route('employee-targets.salary')}}" class="btn btn-primary">
                                <i class="feather-plus me-2"></i>
                                <span>إضافة خصم او مكافأه</span>
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

        <!-- Month/Year Selection -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('employee-targets.index') }}" class="row align-items-end g-3">
                    <div class="col-md-3">
                        <label class="form-label">السنة</label>
                        <select name="year" class="form-control">
                            @for($year = 2020; $year <= 2030; $year++)
                                <option value="{{ $year }}" {{ $currentYear == $year ? 'selected' : '' }}>{{ $year }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">الشهر</label>
                        <select name="month" class="form-control">
                            @php
                                $months = [
                                    1 => 'يناير',
                                    2 => 'فبراير',
                                    3 => 'مارس',
                                    4 => 'أبريل',
                                    5 => 'مايو',
                                    6 => 'يونيو',
                                    7 => 'يوليو',
                                    8 => 'أغسطس',
                                    9 => 'سبتمبر',
                                    10 => 'أكتوبر',
                                    11 => 'نوفمبر',
                                    12 => 'ديسمبر'
                                ];
                            @endphp
                            @foreach($months as $monthNum => $monthName)
                                <option value="{{ $monthNum }}" {{ $currentMonth == $monthNum ? 'selected' : '' }}>
                                    {{ $monthName }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary">عرض</button>
                    </div>
                    <div class="col-md-4">
                        <div class="text-end">
                            <h5 class="mb-0">{{ $months[$currentMonth] }} {{ $currentYear }}</h5>
                            <small class="text-muted">إجمالي الموظفين: {{ $targets->count() }}</small>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Filter Collapse -->
        <div class="collapse" id="filterCollapse">
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('employee-targets.index') }}" class="row g-3">
                        <input type="hidden" name="year" value="{{ $currentYear }}">
                        <input type="hidden" name="month" value="{{ $currentMonth }}">
                        <div class="col-md-4">
                            <label class="form-label">البحث</label>
                            <input type="text" name="search" class="form-control" placeholder="ابحث عن الموظفين..."
                                value="{{ request('search') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">القسم</label>
                            <select name="department_id" class="form-control">
                                <option value="">جميع الأقسام</option>
                                @foreach($departments as $department)
                                    <option value="{{ $department->id }}" {{ request('department_id') == $department->id ? 'selected' : '' }}>
                                        {{ $department->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary me-2">تصفية</button>
                            <a href="{{ route('employee-targets.index', ['year' => $currentYear, 'month' => $currentMonth]) }}"
                                class="btn btn-light">مسح</a>
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
                            <h5 class="card-title mb-0">أهداف موظفي المبيعات</h5>
                            <small class="text-muted">يتم عرض أهداف موظفي المبيعات فقط</small>
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
                            {{--
                            @if(config('app.debug'))
                            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                                <h6><i class="feather-alert-triangle me-2"></i>Debug Mode:</h6>
                                <small>
                                    Current Year: {{ $currentYear }}, Current Month: {{ $currentMonth }}<br>
                                    Total Targets: {{ $targets->count() }}<br>
                                    Targets with ID: {{ $targets->where('id', '!=', null)->count() }}<br>
                                    Targets without ID: {{ $targets->where('id', null)->count() }}<br>
                                    Sample Target Data:
                                    @if($targets->count() > 0)
                                    @php $sample = $targets->first(); @endphp
                                    ID: {{ $sample->id ?? 'null' }},
                                    Employee: {{ $sample->employee->name ?? 'N/A' }},
                                    Target: {{ $sample->target_follow_ups ?? 'N/A' }}
                                    @else
                                    No targets found
                                    @endif
                                </small>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                            @endif
                            --}}

                            @if($targets->count() > 0)
                                {{-- <div class="alert alert-info mb-4">
                                    <h6><i class="feather-info me-2"></i>معلومات مهمة:</h6>
                                    <ul class="mb-0">
                                        <li>الأهداف مخصصة لموظفي قسم المبيعات فقط (القسم رقم 7)</li>
                                        <li>المحقق يتم حسابه تلقائياً من المتابعات الفعلية التي قام بها الموظف في الشهر</li>
                                        <li><strong>لا يمكن تعديل المحقق يدوياً</strong> - يتم تحديثه تلقائياً عند إضافة متابعات
                                            جديدة</li>
                                        <li>يتم حساب النسبة المئوية تلقائياً بناءً على الهدف والمحقق</li>
                                        <li><strong>جميع الأهداف يمكن تعديلها</strong> - بما في ذلك الأهداف المحفوظة مسبقاً</li>
                                        <li>يمكنك إدخال الأهداف للموظفين الجدد وتعديل الأهداف الموجودة</li>
                                        <li>يمكن تصدير البيانات بصيغة CSV لاستخدامها في Excel</li>
                                    </ul> --}}
                                </div>
                                <form action="{{ route('employee-targets.bulk-update') }}" method="POST" id="bulkUpdateForm">
                                    @csrf
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>الموظف</th>
                                                    <th>المرتب الأساسي</th>
                                                    <!--<th>القسم</th> -->

                                                        <th>عدد المندوبين المحولين</th>

                                                        <th>مكافأه  تارجت</th>
                                                        <th>خصم</th>
                                                        <th>سلفه</th>
                                                        <th>مكافأه</th>
                                                        <th>إجمالي مرتب</th>
                                                        <!-- <th>ملاحظات</th> -->
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($targets as $target)
                                                        <tr>
                                                            <td>
                                                                <div class="d-flex align-items-center">
                                                                    <div>
                                                                        <h6 class="mb-0">{{ $target->employee->name ?? 'غير محدد' }}</h6>
                                                                        <small class="text-muted">{{ $target->employee->department->name ?? 'غير محدد' }}</small>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td>{{ $target->employee->salary ?? 'غير محدد'}}</td>
                                                            
                                                            <td>
                                                                <span class="badge bg-info">
                                                                    {{ $target->calculated_active_count }}
                                                                </span>
                                                            </td>

                                                            <td>
                                                                @if($target->calculated_target_bonus > 0)
                                                                    <span class="badge bg-success">{{ number_format($target->calculated_target_bonus, 2) }} جنيه</span>
                                                                @else
                                                                    <span class="text-muted">لا يوجد مكافأة</span>
                                                                @endif
                                                            </td>

                                                            <td>{{ number_format($target->calculated_manual_deduction, 2) }}</td>

                                                            <td>{{ number_format($target->calculated_advance_deduction, 2) }}</td>

                                                            <td>{{ number_format($target->calculated_manual_bonus, 2) }}</td>

                                                            <td>
                                                            
                                                           
                                                            
                                                            {{ number_format($target->final_salary, 2) }} 
                                                        
                                                            </td>

                                                           <!--  <td>
                                                                <input type="text" name="targets[{{ $loop->index }}][notes]" class="form-control form-control-sm"
                                                                       value="{{ $target->notes }}" placeholder="ملاحظات...">
                                                            </td> -->
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>

                                        @can('edit_employee_targets')
                                            <div class="d-flex justify-content-between align-items-center mt-3">
                                                <div class="text-muted">
                                                    <small>
                                                        <i class="feather-info me-1"></i>
                                                        المحقق يتم تحديثه تلقائياً عند الحفظ
                                                    </small>
                                                </div>
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="feather-save me-2"></i>حفظ جميع التغييرات
                                                </button>
                                            </div>
                                        @endcan
                                    </form>
                            @else
                                <div class="text-center py-5">
                                    <i class="feather-target fs-48 text-muted mb-3"></i>
                                    <h5 class="text-muted">لا توجد أهداف للموظفين</h5>
                                    <p class="text-muted">
                                        @if($allEmployees->count() == 0)
                                            لا يوجد موظفين في قسم المبيعات (القسم رقم 7)
                                        @else
                                            لم يتم إنشاء أهداف لهذا الشهر بعد
                                        @endif
                                    </p>
                                    @can('create_employee_targets')
                                        @if($allEmployees->count() > 0)
                                            <form action="{{ route('employee-targets.generate-monthly') }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-primary" onclick="return confirm('هل تريد إنشاء أهداف للشهر الحالي لجميع موظفي المبيعات؟')">
                                                    <i class="feather-plus me-2"></i>إنشاء أهداف الشهر
                                                </button>
                                            </form>
                                        @endif
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

@push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-calculate percentage when target values change
        const targetInputs = document.querySelectorAll('input[name*="[target_follow_ups]"]');
        const bulkUpdateForm = document.getElementById('bulkUpdateForm');

        function updatePercentage(row) {
            const targetInput = row.querySelector('input[name*="[target_follow_ups]"]');
            const targetBadge = row.querySelector('td:nth-child(4) .badge');
            const achievedBadge = row.querySelector('td:nth-child(5) .badge');
            const percentageBadge = row.querySelector('td:nth-child(6) .badge');
            const remainingCell = row.cells[6];

            // Get target value from input or badge
            let target = 0;
            if (targetInput) {
                target = parseInt(targetInput.value) || 0;
            } else if (targetBadge) {
                target = parseInt(targetBadge.textContent) || 0;
            }

            const achieved = parseInt(achievedBadge.textContent) || 0;

            const percentage = target > 0 ? Math.round((achieved / target) * 100) : 0;
            const remaining = Math.max(0, target - achieved);

            percentageBadge.textContent = percentage + '%';
            percentageBadge.className = `badge bg-${percentage >= 100 ? 'success' : (percentage >= 75 ? 'warning' : 'danger')}`;
            remainingCell.textContent = remaining;
        }

        // Initialize percentage calculations on page load
        targetInputs.forEach(input => {
            updatePercentage(input.closest('tr'));

            // Add event listener for real-time updates
            input.addEventListener('input', function() {
                updatePercentage(this.closest('tr'));
            });

            // Add event listener for focus to show current value
            input.addEventListener('focus', function() {
                this.select(); // Select all text when focused
            });
        });

        // Handle form submission
        if (bulkUpdateForm) {
            bulkUpdateForm.addEventListener('submit', function(e) {
                const submitButton = this.querySelector('button[type="submit"]');
                const originalText = submitButton.innerHTML;

                // Show loading state
                submitButton.disabled = true;
                submitButton.innerHTML = '<i class="feather-loader me-2"></i>جاري الحفظ...';

                // Re-enable after a short delay (in case of validation errors)
                setTimeout(() => {
                    submitButton.disabled = false;
                    submitButton.innerHTML = originalText;
                }, 3000);
            });
        }

        // Add visual feedback for changed values (only for editable inputs)
        targetInputs.forEach(input => {
            const originalValue = input.value;
            input.addEventListener('input', function() {
                if (this.value !== originalValue) {
                    this.style.backgroundColor = '#fff3cd'; // Light yellow background for changed values
                    this.style.borderColor = '#ffc107';
                } else {
                    this.style.backgroundColor = '';
                    this.style.borderColor = '';
                }
            });

            // Add event listener for focus to show current value
            input.addEventListener('focus', function() {
                this.select(); // Select all text when focused
            });
        });
    });
    </script>
@endpush

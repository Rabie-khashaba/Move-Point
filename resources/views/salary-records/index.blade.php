@extends('layouts.app')

@section('title', 'سجلات المرتبات - جميع المستخدمين')

@section('content')
<div class="nxl-content">
    <!-- [ page-header ] start -->
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
                <li class="breadcrumb-item">سجلات المرتبات</li>
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
                    <!-- Active Filters Indicator -->
                    @if(request('search') || request('user_type') || request('payment_status'))
                        <div class="me-3">
                                                    <span class="badge bg-info">
                            <i class="feather-filter me-1"></i>
                            @php
                                $filterCount = 0;
                                if (request('search')) $filterCount++;
                                if (request('user_type')) $filterCount++;
                                if (request('payment_status')) $filterCount++;
                            @endphp
                            {{ $filterCount }} فلتر نشط
                        </span>
                        </div>
                    @endif
                    
                    <a href="javascript:void(0);" class="btn btn-icon btn-light-brand" data-bs-toggle="collapse" data-bs-target="#filterCollapse">
                        <i class="feather-filter"></i>
                    </a>
                    <a href="{{ route('salary-records.export', [
                        'year' => $currentYear, 
                        'month' => $currentMonth,
                        'search' => request('search'),
                        'user_type' => request('user_type'),
                        'payment_status' => request('payment_status')
                    ]) }}" class="btn btn-success">
                        <i class="feather-download me-2"></i>
                        <span>تصدير Excel</span>
                        @if(request('search') || request('user_type') || request('payment_status'))
                            <small class="d-block">(مع الفلاتر)</small>
                        @endif
                    </a>
                    @can('create_salary_records')
                    <form action="{{ route('salary-records.generate-monthly') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-info" onclick="return confirm('هل تريد إنشاء سجلات المرتبات للشهر الحالي؟')">
                            <i class="feather-refresh-cw me-2"></i>
                            <span>إنشاء مرتبات الشهر</span>
                        </button>
                    </form>
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
            <form method="GET" action="{{ route('salary-records.index') }}" class="row align-items-end g-3">
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
                                1 => 'يناير', 2 => 'فبراير', 3 => 'مارس', 4 => 'أبريل',
                                5 => 'مايو', 6 => 'يونيو', 7 => 'يوليو', 8 => 'أغسطس',
                                9 => 'سبتمبر', 10 => 'أكتوبر', 11 => 'نوفمبر', 12 => 'ديسمبر'
                            ];
                        @endphp
                        @foreach($months as $monthNum => $monthName)
                            <option value="{{ $monthNum }}" {{ $currentMonth == $monthNum ? 'selected' : '' }}>{{ $monthName }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary">عرض</button>
                </div>
                                    <div class="col-md-4">
                        <div class="text-end">
                            <h5 class="mb-0">{{ $months[$currentMonth] }} {{ $currentYear }}</h5>
                            @if(request('search') || request('user_type') || request('payment_status'))
                                <small class="text-info">نتائج مفلترة: {{ $salaries->count() }} سجل</small>
                            @else
                                <small class="text-muted">إجمالي الموظفين: {{ $salaries->count() }}</small>
                            @endif
                        </div>
                    </div>
            </form>
        </div>
    </div>

    <!-- Filter Collapse -->
    <div class="collapse" id="filterCollapse">
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('salary-records.index') }}" class="row g-3">
                    <input type="hidden" name="year" value="{{ $currentYear }}">
                    <input type="hidden" name="month" value="{{ $currentMonth }}">
                    <div class="col-md-3">
                        <label class="form-label">البحث</label>
                                                 <input type="text" name="search" class="form-control" placeholder="ابحث عن المستخدمين..." value="{{ request('search') }}">
                    </div>
                                         <div class="col-md-3">
                         <label class="form-label">نوع المستخدم</label>
                         <select name="user_type" class="form-control">
                             <option value="">جميع الأنواع</option>
                             <option value="employee" {{ request('user_type') === 'employee' ? 'selected' : '' }}>موظف</option>
                             <option value="representative" {{ request('user_type') === 'representative' ? 'selected' : '' }}>مندوب</option>
                             <option value="supervisor" {{ request('user_type') === 'supervisor' ? 'selected' : '' }}>مشرف</option>
                         </select>
                     </div>
                    <div class="col-md-3">
                        <label class="form-label">حالة الدفع</label>
                        <select name="payment_status" class="form-control">
                            <option value="">جميع الحالات</option>
                            <option value="paid" {{ request('payment_status') === 'paid' ? 'selected' : '' }}>تم الدفع</option>
                            <option value="unpaid" {{ request('payment_status') === 'unpaid' ? 'selected' : '' }}>لم يدفع</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">تصفية</button>
                        <a href="{{ route('salary-records.index', ['year' => $currentYear, 'month' => $currentMonth]) }}" class="btn btn-light">مسح</a>
                        @if(request('search') || request('user_type') || request('payment_status'))
                            <a href="{{ route('salary-records.index', ['year' => $currentYear, 'month' => $currentMonth]) }}" class="btn btn-outline-danger ms-2">
                                <i class="feather-x me-1"></i>إلغاء جميع الفلاتر
                            </a>
                        @endif
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
                         <h5 class="card-title mb-0">سجلات المرتبات - جميع المستخدمين</h5>
                         <small class="text-muted">موظفين، مندوبين، ومشرفين</small>
                     </div>
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <!-- Filter Summary - Always show when filters are active -->
                        @if(request('search') || request('user_type') || request('payment_status'))
                            <div class="alert alert-info mb-3">
                                <h6><i class="feather-filter me-2"></i>الفلاتر المطبقة:</h6>
                                <div class="row">
                                    @if(request('search'))
                                        <div class="col-md-3">
                                            <strong>البحث:</strong> {{ request('search') }}
                                        </div>
                                    @endif
                                    @if(request('user_type'))
                                        <div class="col-md-3">
                                            <strong>النوع:</strong> 
                                            @switch(request('user_type'))
                                                @case('employee')
                                                    موظف
                                                    @break
                                                @case('representative')
                                                    مندوب
                                                    @break
                                                @case('supervisor')
                                                    مشرف
                                                    @break
                                            @endswitch
                                        </div>
                                    @endif
                                    @if(request('payment_status'))
                                        <div class="col-md-3">
                                            <strong>حالة الدفع:</strong> 
                                            {{ request('payment_status') === 'paid' ? 'تم الدفع' : 'لم يدفع' }}
                                        </div>
                                    @endif
                                </div>
                                                                    <small class="text-muted">سيتم تصدير البيانات المفلترة فقط</small>
                                    <div class="mt-2">
                                        <strong>عدد النتائج:</strong> {{ $salaries->count() }} سجل
                                        <div class="mt-2">
                                            <a href="{{ route('salary-records.index', ['year' => $currentYear, 'month' => $currentMonth]) }}" class="btn btn-sm btn-outline-primary me-2">
                                                <i class="feather-eye me-1"></i>عرض جميع البيانات
                                            </a>
                                            <a href="{{ route('salary-records.index', ['year' => $currentYear, 'month' => $currentMonth]) }}" class="btn btn-sm btn-outline-danger">
                                                <i class="feather-x me-1"></i>إلغاء الفلاتر
                                            </a>
                                        </div>
                                    </div>
                            </div>
                        @endif

                        @if($salaries->count() > 0)
                            <!-- Show filtered results count -->
                            @if(request('search') || request('user_type') || request('payment_status'))
                                <div class="alert alert-success mb-3">
                                    <i class="feather-check-circle me-2"></i>
                                    تم العثور على <strong>{{ $salaries->count() }}</strong> نتيجة للفلاتر المطبقة
                                </div>
                            @endif
                            
                            <!-- Summary Cards -->
                            <div class="row mb-4">
                                <div class="col-md-3">
                                    <div class="card border-danger">
                                        <div class="card-body text-center">
                                            <h6 class="card-title text-danger">إجمالي الخصومات</h6>
                                            <h4 class="text-danger mb-0">{{ number_format($salaries->sum('advances') + $salaries->sum('deductions') + $salaries->sum('lost_orders_penalty') + $salaries->sum('delivery_penalty'), 0) }}</h4>
                                            <small class="text-muted">
                                                سلف: {{ number_format($salaries->sum('advances'), 0) }} | 
                                                خصومات: {{ number_format($salaries->sum('deductions'), 0) }} | 
                                                غرامات: {{ number_format($salaries->sum('lost_orders_penalty') + $salaries->sum('delivery_penalty'), 0) }}
                                            </small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card border-success">
                                        <div class="card-body text-center">
                                            <h6 class="card-title text-success">إجمالي المكافآت</h6>
                                            <h4 class="text-success mb-0">{{ number_format($salaries->sum('commissions') + $salaries->sum('cashback'), 0) }}</h4>
                                            <small class="text-muted">
                                                عمولات: {{ number_format($salaries->sum('commissions'), 0) }} | 
                                                كاش باك: {{ number_format($salaries->sum('cashback'), 0) }}
                                            </small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card border-primary">
                                        <div class="card-body text-center">
                                            <h6 class="card-title text-primary">إجمالي المرتبات الأساسية</h6>
                                            <h4 class="text-primary mb-0">{{ number_format($salaries->sum('base_salary'), 0) }}</h4>
                                            <small class="text-muted">{{ $salaries->count() }} مستخدم</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card border-info">
                                        <div class="card-body text-center">
                                            <h6 class="card-title text-info">إجمالي صافي المرتبات</h6>
                                            <h4 class="text-info mb-0">{{ number_format($salaries->sum('net_salary'), 0) }}</h4>
                                            <small class="text-muted">بعد الخصومات والمكافآت</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="table-responsive">
                                <table class="table table-hover table-sm">
                                    <thead>
                                         <tr>
                                             <th width="50">#</th>
                                             <th>المستخدم</th>
                                             <th>الكود</th>
                                             <th>النوع</th>
                                             <th>المرتب الأساسي</th>
                                             <th colspan="3" class="text-center text-danger">الخصومات والغرامات</th>
                                             <th colspan="2" class="text-center text-success">المكافآت</th>
                                             <th>صافي المرتب</th>
                                         </tr>
                                         <tr>
                                             <th></th>
                                             <th></th>
                                             <th></th>
                                             <th></th>
                                             <th></th>
                                             <th class="text-danger">السلف</th>
                                             <th class="text-danger">الخصومات</th>
                                             <th class="text-danger">الغرامات</th>
                                             <th class="text-success">العمولات</th>
                                             <th class="text-success">كاش باك</th>
                                             <th></th>
                                         </tr>
                                    <tbody>
                                                                                @foreach($salaries as $salary)
                                        <tr>
                                             <td class="text-center fw-bold">{{ $loop->iteration }}</td>
                                             <td>
                                                 <div class="d-flex align-items-center">
                                                     <div>
                                                         <h6 class="mb-0 small">{{ $salary->user_name }}</h6>
                                                         <small class="text-muted">{{ $salary->user_phone }}</small>
                                                     </div>
                                                 </div>
                                             </td>
                                             <td>{{ $salary->user_code }}</td>
                                             <td>
                                                 @switch($salary->user_type)
                                                     @case('employee')
                                                         <span class="badge bg-primary">موظف</span>
                                                         @break
                                                     @case('representative')
                                                         <span class="badge bg-success">مندوب</span>
                                                         @break
                                                     @case('supervisor')
                                                         <span class="badge bg-info">مشرف</span>
                                                         @break
                                                     @default
                                                         <span class="badge bg-secondary">غير محدد</span>
                                                 @endswitch
                                             </td>
                                            <td class="fw-bold">{{ number_format($salary->base_salary, 0) }}</td>
                                            <td class="text-danger">{{ number_format($salary->advances, 0) }}</td>
                                            <td class="text-danger">{{ number_format($salary->deductions, 0) }}</td>
                                            <td class="text-danger">{{ number_format($salary->lost_orders_penalty + $salary->delivery_penalty, 0) }}</td>
                                            <td class="text-success">{{ number_format($salary->commissions, 0) }}</td>
                                            <td class="text-success">{{ number_format($salary->cashback, 0) }}</td>
                                            <td class="fw-bold text-primary">{{ number_format($salary->net_salary, 0) }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="table-light">
                                        <tr>
                                            <th colspan="4">الإجمالي</th>
                                            <th>{{ number_format($salaries->sum('base_salary'), 0) }}</th>
                                            <th class="text-danger">{{ number_format($salaries->sum('advances'), 0) }}</th>
                                            <th class="text-danger">{{ number_format($salaries->sum('deductions'), 0) }}</th>
                                            <th class="text-danger">{{ number_format($salaries->sum('lost_orders_penalty') + $salaries->sum('delivery_penalty'), 0) }}</th>
                                            <th class="text-success">{{ number_format($salaries->sum('commissions'), 0) }}</th>
                                            <th class="text-success">{{ number_format($salaries->sum('cashback'), 0) }}</th>
                                            <th class="fw-bold text-primary">{{ number_format($salaries->sum('net_salary'), 0) }}</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            
                            <!-- Table Legend -->
                            <div class="row mt-4">
                                <div class="col-12">
                                    <div class="card border-light">
                                        <div class="card-body">
                                            <h6 class="card-title mb-3">
                                                <i class="feather-info me-2"></i>شرح الأعمدة
                                            </h6>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <h6 class="text-danger">الخصومات والغرامات:</h6>
                                                    <ul class="list-unstyled">
                                                        <li><strong>السلف:</strong> المبالغ المسحوبة مقدماً من المرتب</li>
                                                        <li><strong>الخصومات:</strong> خصومات أخرى مثل التأمين أو الضرائب</li>
                                                        <li><strong>الغرامات:</strong> غرامة الأوردر الضائع + غرامة الإيداع</li>
                                                    </ul>
                                                </div>
                                                <div class="col-md-6">
                                                    <h6 class="text-success">المكافآت:</h6>
                                                    <ul class="list-unstyled">
                                                        <li><strong>العمولات:</strong> عمولات المبيعات أو الإنجازات</li>
                                                        <li><strong>كاش باك:</strong> مكافآت إضافية أو حوافز</li>
                                                    </ul>
                                                </div>
                                            </div>
                                            <div class="mt-3 p-2 bg-light rounded">
                                                <small class="text-muted">
                                                    <strong>صافي المرتب = المرتب الأساسي - الخصومات + المكافآت</strong>
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="feather-dollar-sign fs-48 text-muted mb-3"></i>
                                @if(request('search') || request('user_type') || request('payment_status'))
                                    <h5 class="text-muted">لا توجد نتائج للفلاتر المطبقة</h5>
                                    <p class="text-muted">جرب تغيير معايير البحث أو إلغاء بعض الفلاتر</p>
                                    <div class="mt-3">
                                        <a href="{{ route('salary-records.index', ['year' => $currentYear, 'month' => $currentMonth]) }}" class="btn btn-outline-primary me-2">
                                            <i class="feather-refresh-cw me-1"></i>إلغاء جميع الفلاتر
                                        </a>
                                        <a href="{{ route('salary-records.index', ['year' => $currentYear, 'month' => $currentMonth]) }}" class="btn btn-outline-info">
                                            <i class="feather-eye me-1"></i>عرض جميع البيانات
                                        </a>
                                    </div>
                                @else
                                    <h5 class="text-muted">لا توجد سجلات مرتبات</h5>
                                    <p class="text-muted">لم يتم إنشاء سجلات المرتبات لهذا الشهر بعد (موظفين، مندوبين، ومشرفين)</p>
                                @endif
                                
                                @can('create_salary_records')
                                <form action="{{ route('salary-records.generate-monthly') }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-primary" onclick="return confirm('هل تريد إنشاء سجلات المرتبات للشهر الحالي؟')">
                                        <i class="feather-plus me-2"></i>إنشاء مرتبات الشهر
                                    </button>
                                </form>
                                @endcan
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- [ Main Content ] end -->=
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle export button click with filter confirmation
    const exportBtn = document.querySelector('a[href*="salary-records.export"]');
    if (exportBtn) {
        exportBtn.addEventListener('click', function(e) {
            const hasFilters = {{ request('search') || request('user_type') || request('payment_status') ? 'true' : 'false' }};
            
            if (hasFilters) {
                const confirmed = confirm('سيتم تصدير البيانات المفلترة فقط. هل تريد المتابعة؟');
                if (!confirmed) {
                    e.preventDefault();
                }
            }
        });
    }
    
    // Auto-submit form when filters change
    const filterInputs = document.querySelectorAll('#filterCollapse select, #filterCollapse input[name="search"]');
    filterInputs.forEach(input => {
        input.addEventListener('change', function() {
            if (this.name === 'search') {
                // Don't auto-submit search input, let user press enter or click filter
                return;
            }
            // Auto-submit for select dropdowns
            this.closest('form').submit();
        });
    });
    
    // Add visual feedback for active filters
    const filterForm = document.querySelector('#filterCollapse form');
    if (filterForm) {
        const inputs = filterForm.querySelectorAll('input, select');
        inputs.forEach(input => {
            if (input.value && input.name !== 'year' && input.name !== 'month') {
                input.style.borderColor = '#28a745';
                input.style.backgroundColor = '#f8fff9';
            }
        });
    }
    
    // Show loading state when applying filters
    const filterButton = document.querySelector('#filterCollapse button[type="submit"]');
    if (filterButton) {
        filterButton.addEventListener('click', function() {
            this.innerHTML = '<i class="feather-loader me-2"></i>جاري التصفية...';
            this.disabled = true;
        });
    }
});
</script>
@endpush

@extends('layouts.app')

@section('title', 'إدارة مكونات المرتبات')

@section('content')
<div class="nxl-content">
    <!-- [ page-header ] start -->
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
                <li class="breadcrumb-item">مكونات المرتبات</li>
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
                    <a href="{{ route('salary-components.export', [
                        'year' => $currentYear,
                        'month' => $currentMonth,
                        'user_type' => $userType
                    ]) }}" class="btn btn-success">
                        <i class="feather-download me-2"></i>
                        <span>تصدير Excel</span>
                    </a>
                    <a href="{{ route('salary-components.create', [
                        'year' => $currentYear,
                        'month' => $currentMonth,
                        'user_type' => $userType
                    ]) }}" class="btn btn-primary">
                        <i class="feather-plus me-2"></i>
                        <span>إضافة مكونات</span>
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

    <!-- Month/Year Selection -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('salary-components.index') }}" class="row align-items-end g-3">
                <div class="col-md-2">
                    <label class="form-label">السنة</label>
                    <select name="year" class="form-control">
                        @for($year = 2020; $year <= 2030; $year++)
                            <option value="{{ $year }}" {{ $currentYear == $year ? 'selected' : '' }}>{{ $year }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-2">
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
                    <label class="form-label">نوع المستخدم</label>
                    <select name="user_type" class="form-control">
                        <option value="">جميع الأنواع</option>
                        <option value="employee" {{ $userType === 'employee' ? 'selected' : '' }}>موظف</option>
                        <option value="representative" {{ $userType === 'representative' ? 'selected' : '' }}>مندوب</option>
                        <option value="supervisor" {{ $userType === 'supervisor' ? 'selected' : '' }}>مشرف</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary">عرض</button>
                </div>
                <div class="col-md-4">
                    <div class="text-end">
                        <h5 class="mb-0">{{ $months[$currentMonth] }} {{ $currentYear }}</h5>
                        <small class="text-muted">إدارة مكونات المرتبات</small>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-danger">
                <div class="card-body text-center">
                    <h6 class="card-title text-danger">إجمالي الخصومات</h6>
                    <h4 class="text-danger mb-0">{{ number_format($totals['total_deductions'], 0) }}</h4>
                    <small class="text-muted">
                        سلف: {{ number_format($totals['advances'], 0) }} |
                        خصومات: {{ number_format($totals['deductions'], 0) }}
                    </small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-warning">
                <div class="card-body text-center">
                    <h6 class="card-title text-warning">إجمالي الغرامات</h6>
                    <h4 class="text-warning mb-0">{{ number_format($totals['total_penalties'], 0) }}</h4>
                    <small class="text-muted">
                        أوردر ضائع: {{ number_format($totals['lost_orders_penalty'], 0) }} |
                        إيصال تسليم: {{ number_format($totals['delivery_penalty'], 0) }}
                    </small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-success">
                <div class="card-body text-center">
                    <h6 class="card-title text-success">إجمالي المكافآت</h6>
                    <h4 class="text-success mb-0">{{ number_format($totals['total_bonuses'], 0) }}</h4>
                    <small class="text-muted">
                        عمولات: {{ number_format($totals['commissions'], 0) }} |
                        كاش باك: {{ number_format($totals['cashback'], 0) }}
                    </small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-info">
                <div class="card-body text-center">
                    <h6 class="card-title text-info">عدد السجلات</h6>
                    <h4 class="text-info mb-0">{{ $salaryRecords->count() }}</h4>
                    <small class="text-muted">سجل مرتب</small>
                </div>
            </div>
        </div>
    </div>

    <!-- [ Main Content ] start -->
    <div class="main-content">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">مكونات المرتبات - {{ $months[$currentMonth] }} {{ $currentYear }}</h5>
                        <small class="text-muted">إدارة السلف، الخصومات، الغرامات، العمولات، وكاش باك</small>
                    </div>
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if($salaryRecords->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover table-sm">
                                    <thead>
                                        <tr>
                                            <th width="50">#</th>
                                            <th>المستخدم</th>
                                            <th>النوع</th>
                                            <th>المرتب الأساسي</th>
                                            <th colspan="2" class="text-center text-danger">الخصومات</th>
                                            <th colspan="2" class="text-center text-warning">الغرامات</th>
                                            <th colspan="2" class="text-center text-success">المكافآت</th>
                                            <th>صافي المرتب</th>
                                            <th width="120">الإجراءات</th>
                                        </tr>
                                        <tr>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th class="text-danger">السلف</th>
                                            <th class="text-danger">الخصومات</th>
                                            <th class="text-warning">أوردر ضائع</th>
                                            <th class="text-warning">إيصال تسليم</th>
                                            <th class="text-success">العمولات</th>
                                            <th class="text-success">كاش باك</th>
                                            <th></th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($salaryRecords as $record)
                                        <tr>
                                            <td class="text-center fw-bold">{{ $loop->iteration }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div>
                                                        <h6 class="mb-0 small">{{ $record->user_name }}</h6>
                                                        <small class="text-muted">{{ $record->user_phone }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                @switch($record->user_type)
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
                                            <td class="fw-bold">{{ number_format($record->base_salary, 0) }}</td>
                                            <td class="text-danger">{{ number_format($record->advances, 0) }}</td>
                                            <td class="text-danger">{{ number_format($record->deductions, 0) }}</td>
                                            <td class="text-warning">{{ number_format($record->lost_orders_penalty, 0) }}</td>
                                            <td class="text-warning">{{ number_format($record->delivery_penalty, 0) }}</td>
                                            <td class="text-success">{{ number_format($record->commissions, 0) }}</td>
                                            <td class="text-success">{{ number_format($record->cashback, 0) }}</td>
                                            <td class="fw-bold text-primary">{{ number_format($record->net_salary, 0) }}</td>
                                            <td>
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <a href="{{ route('salary-components.edit', $record->id) }}"
                                                       class="btn btn-outline-primary" title="تعديل">
                                                        <i class="feather-edit-2"></i>
                                                    </a>
                                                    <a href="{{ route('salary-records.show', $record->id) }}"
                                                       class="btn btn-outline-info" title="عرض">
                                                        <i class="feather-eye"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="table-light">
                                        <tr>
                                            <th colspan="3">الإجمالي</th>
                                            <th>{{ number_format($salaryRecords->sum('base_salary'), 0) }}</th>
                                            <th class="text-danger">{{ number_format($totals['advances'], 0) }}</th>
                                            <th class="text-danger">{{ number_format($totals['deductions'], 0) }}</th>
                                            <th class="text-warning">{{ number_format($totals['lost_orders_penalty'], 0) }}</th>
                                            <th class="text-warning">{{ number_format($totals['delivery_penalty'], 0) }}</th>
                                            <th class="text-success">{{ number_format($totals['commissions'], 0) }}</th>
                                            <th class="text-success">{{ number_format($totals['cashback'], 0) }}</th>
                                            <th class="fw-bold text-primary">{{ number_format($salaryRecords->sum('net_salary'), 0) }}</th>
                                            <th></th>
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
                                                <i class="feather-info me-2"></i>شرح مكونات المرتب
                                            </h6>
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <h6 class="text-danger">الخصومات:</h6>
                                                    <ul class="list-unstyled">
                                                        <li><strong>السلف:</strong> المبالغ المسحوبة مقدماً من المرتب</li>
                                                        <li><strong>الخصومات:</strong> خصومات أخرى مثل التأمين أو الضرائب</li>
                                                    </ul>
                                                </div>
                                                <div class="col-md-4">
                                                    <h6 class="text-warning">الغرامات:</h6>
                                                    <ul class="list-unstyled">
                                                        <li><strong>غرامة الأوردر الضائع:</strong> عقوبة على الأوردرات المفقودة</li>
                                                        <li><strong>غرامة إيصال التسليم:</strong> عقوبة على تأخير التسليم</li>
                                                    </ul>
                                                </div>
                                                <div class="col-md-4">
                                                    <h6 class="text-success">المكافآت:</h6>
                                                    <ul class="list-unstyled">
                                                        <li><strong>العمولات:</strong> عمولات المبيعات أو الإنجازات</li>
                                                        <li><strong>كاش باك:</strong> مكافآت إضافية أو حوافز</li>
                                                    </ul>
                                                </div>
                                            </div>
                                            <div class="mt-3 p-2 bg-light rounded">
                                                <small class="text-muted">
                                                    <strong>صافي المرتب = المرتب الأساسي - (السلف + الخصومات + الغرامات) + (العمولات + كاش باك)</strong>
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="feather-dollar-sign fs-48 text-muted mb-3"></i>
                                <h5 class="text-muted">لا توجد سجلات مكونات مرتبات</h5>
                                <p class="text-muted">لم يتم إنشاء سجلات مكونات المرتبات لهذا الشهر بعد</p>
                                <a href="{{ route('salary-components.create', ['year' => $currentYear, 'month' => $currentMonth]) }}" class="btn btn-primary">
                                    <i class="feather-plus me-2"></i>إضافة مكونات المرتب
                                </a>
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
    // Auto-submit form when filters change
    const filterInputs = document.querySelectorAll('select[name="year"], select[name="month"], select[name="user_type"]');
    filterInputs.forEach(input => {
        input.addEventListener('change', function() {
            this.closest('form').submit();
        });
    });
});
</script>
@endpush

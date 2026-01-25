@extends('layouts.app')

@section('title', 'إضافة خصم أو مكافأه')

@section('content')
<div class="nxl-content">
    <!-- [ page-header ] start -->
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
                <li class="breadcrumb-item"><a href="{{ route('employee-targets.index') }}">الخصومات  والمكافأت</a></li>
                <li class="breadcrumb-item">إضافة خصم أو مكافأه</li>
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
                    <a href="{{ route('employee-targets.index', [
                        'year' => $currentYear,
                        'month' => $currentMonth,
                    ]) }}" class="btn btn-light">
                        <i class="feather-arrow-left me-2"></i>
                        <span>العودة</span>
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

    <!-- [ Main Content ] start -->
    <div class="main-content">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">إضافة الخصومات أو  المكافات</h5>
                        <small class="text-muted">إضافة الخصومات،المكافأت</small>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('employee-targets.storeSalary') }}" method="POST">
                            @csrf

                            <!-- Basic Information -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h6 class="text-primary mb-3">
                                        <i class="feather-info me-2"></i>المعلومات الأساسية
                                    </h6>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">السنة <span class="text-danger">*</span></label>
                                    <select name="year" class="form-control @error('year') is-invalid @enderror" required>
                                        @for($year = 2020; $year <= 2030; $year++)
                                            <option value="{{ $year }}" {{ $currentYear == $year ? 'selected' : '' }}>{{ $year }}</option>
                                        @endfor
                                    </select>
                                    @error('year')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">الشهر <span class="text-danger">*</span></label>
                                    <select name="month" class="form-control @error('month') is-invalid @enderror" required>
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
                                    @error('month')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                               <div class="col-md-3">
                                    <label class="form-label">الموظف <span class="text-danger">*</span></label>
                                    <select name="employee_id" id="employee_id" class="form-control @error('employee_id') is-invalid @enderror" required>
                                        <option value="">اختر الموظف</option>
                                        @foreach(\App\Models\Employee::where('is_active', 1)->orderBy('name')->get() as $employee)
                                            <option value="{{ $employee->user_id }}" {{ old('employee_id') == $employee->user_id ? 'selected' : '' }}>
                                                {{ $employee->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('employee_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                            </div>

                             <!-- Deductions Section -->
                             <div class="row mb-4">
                                 <div class="col-12">
                                     <h6 class="text-danger mb-3">
                                         <i class="feather-minus-circle me-2"></i>الخصومات والمكافأت
                                     </h6>
                                 </div>

                                 <div class="col-md-5">
                                     <label class="form-label">الخصومات <span class="text-danger">*</span></label>
                                     <div class="input-group">
                                         <input type="number" name="deductions" class="form-control @error('deductions') is-invalid @enderror"
                                                value="{{ old('deductions', 0) }}" min="0" step="0.01" required>
                                         <span class="input-group-text">ج.م</span>
                                     </div>
                                     <small class="text-muted">خصومات أخرى مثل التأمين أو الضرائب</small>
                                     @error('deductions')
                                         <div class="invalid-feedback">{{ $message }}</div>
                                     @enderror
                                 </div>

                                 <div class="col-md-5">
                                     <label class="form-label">المكافأت <span class="text-danger">*</span></label>
                                     <div class="input-group">
                                         <input type="number" name="bonus_amount" class="form-control @error('bonus_amount') is-invalid @enderror"
                                                value="{{ old('bonus_amount', 0) }}" min="0" step="0.01" required>
                                         <span class="input-group-text">ج.م</span>
                                     </div>
                                     @error('bonus_amount')
                                         <div class="invalid-feedback">{{ $message }}</div>
                                     @enderror
                                 </div>

                                 <div class="col-md-6 mt-3">
                                    <label class="form-label">السبب</label>
                                    <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" rows="3" placeholder="أدخل أي ملاحظات إضافية...">{{ old('notes') }}</textarea>
                                    @error('notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>



                             </div>


                            <!-- Submit Buttons -->
                            <div class="row">
                                <div class="col-12">
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="{{ route('salary-components.index', [
                                            'year' => $currentYear,
                                            'month' => $currentMonth,
                                        ]) }}" class="btn btn-light">
                                            <i class="feather-x me-2"></i>إلغاء
                                        </a>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="feather-save me-2"></i>حفظ مكونات المرتب
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- [ Main Content ] end -->
</div>
@endsection

@push('scripts')

@endpush

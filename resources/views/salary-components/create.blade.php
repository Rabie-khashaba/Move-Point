@extends('layouts.app')

@section('title', 'إضافة مكونات المرتب')

@section('content')
<div class="nxl-content">
    <!-- [ page-header ] start -->
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
                <li class="breadcrumb-item"><a href="{{ route('salary-components.index') }}">مكونات المرتبات</a></li>
                <li class="breadcrumb-item">إضافة مكونات</li>
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
                    <a href="{{ route('salary-components.index', [
                        'year' => $currentYear, 
                        'month' => $currentMonth,
                        'user_type' => $userType
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
                        <h5 class="card-title mb-0">إضافة مكونات المرتب</h5>
                        <small class="text-muted">إضافة السلف، الخصومات، الغرامات، العمولات، وكاش باك</small>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('salary-components.store') }}" method="POST">
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
                                    <label class="form-label">نوع المستخدم <span class="text-danger">*</span></label>
                                    <select name="user_type" id="user_type" class="form-control @error('user_type') is-invalid @enderror" required>
                                        <option value="">اختر النوع</option>
                                        <option value="employee" {{ $userType === 'employee' ? 'selected' : '' }}>موظف</option>
                                        <option value="representative" {{ $userType === 'representative' ? 'selected' : '' }}>مندوب</option>
                                        <option value="supervisor" {{ $userType === 'supervisor' ? 'selected' : '' }}>مشرف</option>
                                    </select>
                                    @error('user_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">المستخدم <span class="text-danger">*</span></label>
                                    <select name="user_id" id="user_id" class="form-control @error('user_id') is-invalid @enderror" required>
                                        <option value="">اختر المستخدم أولاً</option>
                                    </select>
                                    @error('user_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                                                         <!-- Deductions Section -->
                             <div class="row mb-4">
                                 <div class="col-12">
                                     <h6 class="text-danger mb-3">
                                         <i class="feather-minus-circle me-2"></i>الخصومات
                                     </h6>
                                 </div>
                                 <div class="col-md-6">
                                     <div class="alert alert-info">
                                         <h6 class="alert-heading">
                                             <i class="feather-info me-2"></i>السلف
                                         </h6>
                                         <p class="mb-0">سيتم جلب السلف تلقائياً من جدول طلبات السلف المعتمدة</p>
                                         <small class="text-muted">المبالغ المسحوبة مقدماً من المرتب</small>
                                         <div class="mt-2">
                                             <small class="text-muted">
                                                 <i class="feather-info me-1"></i>
                                                 <strong>ملاحظة:</strong> السلف ذات الأقساط ستظهر كقسط شهري في كل شهر
                                             </small>
                                         </div>
                                     </div>
                                 </div>
                                 <div class="col-md-6">
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
                             </div>

                            <!-- Penalties Section -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h6 class="text-warning mb-3">
                                        <i class="feather-alert-triangle me-2"></i>الغرامات
                                    </h6>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">غرامة الأوردر الضائع <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="number" name="lost_orders_penalty" class="form-control @error('lost_orders_penalty') is-invalid @enderror" 
                                               value="{{ old('lost_orders_penalty', 0) }}" min="0" step="0.01" required>
                                        <span class="input-group-text">ج.م</span>
                                    </div>
                                    <small class="text-muted">عقوبة على الأوردرات المفقودة</small>
                                    @error('lost_orders_penalty')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">غرامة إيصال التسليم <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="number" name="delivery_penalty" class="form-control @error('delivery_penalty') is-invalid @enderror" 
                                               value="{{ old('delivery_penalty', 0) }}" min="0" step="0.01" required>
                                        <span class="input-group-text">ج.م</span>
                                    </div>
                                    <small class="text-muted">عقوبة على تأخير التسليم</small>
                                    @error('delivery_penalty')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Bonuses Section -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h6 class="text-success mb-3">
                                        <i class="feather-plus-circle me-2"></i>المكافآت
                                    </h6>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">العمولات <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="number" name="commissions" class="form-control @error('commissions') is-invalid @enderror" 
                                               value="{{ old('commissions', 0) }}" min="0" step="0.01" required>
                                        <span class="input-group-text">ج.م</span>
                                    </div>
                                    <small class="text-muted">عمولات المبيعات أو الإنجازات</small>
                                    @error('commissions')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">كاش باك <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="number" name="cashback" class="form-control @error('cashback') is-invalid @enderror" 
                                               value="{{ old('cashback', 0) }}" min="0" step="0.01" required>
                                        <span class="input-group-text">ج.م</span>
                                    </div>
                                    <small class="text-muted">مكافآت إضافية أو حوافز</small>
                                    @error('cashback')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Notes Section -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <label class="form-label">ملاحظات</label>
                                    <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" 
                                              rows="3" placeholder="أضف ملاحظات إضافية...">{{ old('notes') }}</textarea>
                                    @error('notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Salary Preview -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <div class="card border-info">
                                        <div class="card-body">
                                            <h6 class="card-title text-info">
                                                <i class="feather-calculator me-2"></i>معاينة المرتب
                                            </h6>
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <strong>المرتب الأساسي:</strong>
                                                    <span id="base_salary_preview">0</span> ج.م
                                                </div>
                                                <div class="col-md-3">
                                                    <strong class="text-danger">إجمالي الخصومات:</strong>
                                                    <span id="total_deductions_preview" class="text-danger">0</span> ج.م
                                                </div>
                                                <div class="col-md-3">
                                                    <strong class="text-success">إجمالي المكافآت:</strong>
                                                    <span id="total_bonuses_preview" class="text-success">0</span> ج.م
                                                </div>
                                                <div class="col-md-3">
                                                    <strong class="text-primary">صافي المرتب:</strong>
                                                    <span id="net_salary_preview" class="text-primary fw-bold">0</span> ج.م
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Buttons -->
                            <div class="row">
                                <div class="col-12">
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="{{ route('salary-components.index', [
                                            'year' => $currentYear, 
                                            'month' => $currentMonth,
                                            'user_type' => $userType
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
<script>
document.addEventListener('DOMContentLoaded', function() {
    const userTypeSelect = document.getElementById('user_type');
    const userIdSelect = document.getElementById('user_id');
    
    // Users data
    const users = {
        employee: @json($employees->map(function($user) {
            return ['id' => $user->id, 'name' => $user->name, 'salary' => $user->salary];
        })),
        representative: @json($representatives->map(function($user) {
            return ['id' => $user->id, 'name' => $user->name, 'salary' => $user->salary];
        })),
        supervisor: @json($supervisors->map(function($user) {
            return ['id' => $user->id, 'name' => $user->name, 'salary' => $user->salary];
        }))
    };
    
    // Update user dropdown when user type changes
    userTypeSelect.addEventListener('change', function() {
        const selectedType = this.value;
        userIdSelect.innerHTML = '<option value="">اختر المستخدم</option>';
        
        if (selectedType && users[selectedType]) {
            users[selectedType].forEach(user => {
                const option = document.createElement('option');
                option.value = user.id;
                option.textContent = `${user.name} (${user.salary} ج.م)`;
                option.dataset.salary = user.salary;
                userIdSelect.appendChild(option);
            });
        }
        
        updateSalaryPreview();
    });
    
    // Update salary preview when user is selected
    userIdSelect.addEventListener('change', function() {
        updateSalaryPreview();
    });
    
         // Update salary preview when any amount changes
     const amountInputs = document.querySelectorAll('input[name="deductions"], input[name="lost_orders_penalty"], input[name="delivery_penalty"], input[name="commissions"], input[name="cashback"]');
     amountInputs.forEach(input => {
         input.addEventListener('input', updateSalaryPreview);
     });
     
     function updateSalaryPreview() {
         const selectedOption = userIdSelect.options[userIdSelect.selectedIndex];
         const baseSalary = selectedOption ? parseFloat(selectedOption.dataset.salary) || 0 : 0;
         
         // Advances will be calculated automatically from the database
         const advances = 0; // This will be fetched from advance_requests table
         const deductions = parseFloat(document.querySelector('input[name="deductions"]').value) || 0;
         const lostOrdersPenalty = parseFloat(document.querySelector('input[name="lost_orders_penalty"]').value) || 0;
         const deliveryPenalty = parseFloat(document.querySelector('input[name="delivery_penalty"]').value) || 0;
         const commissions = parseFloat(document.querySelector('input[name="commissions"]').value) || 0;
         const cashback = parseFloat(document.querySelector('input[name="cashback"]').value) || 0;
         
         const totalDeductions = advances + deductions + lostOrdersPenalty + deliveryPenalty;
         const totalBonuses = commissions + cashback;
         const netSalary = Math.max(0, baseSalary - totalDeductions + totalBonuses);
         
         document.getElementById('base_salary_preview').textContent = baseSalary.toFixed(2);
         document.getElementById('total_deductions_preview').textContent = totalDeductions.toFixed(2);
         document.getElementById('total_bonuses_preview').textContent = totalBonuses.toFixed(2);
         document.getElementById('net_salary_preview').textContent = netSalary.toFixed(2);
     }
    
    // Initialize if user type is pre-selected
    if (userTypeSelect.value) {
        userTypeSelect.dispatchEvent(new Event('change'));
    }
});
</script>
@endpush

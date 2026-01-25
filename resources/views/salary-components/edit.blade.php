@extends('layouts.app')

@section('title', 'تعديل مكونات المرتب')

@section('content')
<div class="nxl-content">
    <!-- [ page-header ] start -->
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
                <li class="breadcrumb-item"><a href="{{ route('salary-components.index') }}">مكونات المرتبات</a></li>
                <li class="breadcrumb-item">تعديل مكونات</li>
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
                        'year' => $salaryRecord->year, 
                        'month' => $salaryRecord->month,
                        'user_type' => $salaryRecord->user_type
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
                        <h5 class="card-title mb-0">تعديل مكونات المرتب</h5>
                        <small class="text-muted">
                            {{ $salaryRecord->user_name }} - 
                            {{ $salaryRecord->user_type === 'employee' ? 'موظف' : ($salaryRecord->user_type === 'representative' ? 'مندوب' : 'مشرف') }} - 
                            {{ $salaryRecord->year }}/{{ $salaryRecord->month }}
                        </small>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('salary-components.update', $salaryRecord->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            
                            <!-- User Information Display -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <div class="alert alert-info">
                                        <h6 class="alert-heading">
                                            <i class="feather-user me-2"></i>معلومات المستخدم
                                        </h6>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <strong>الاسم:</strong> {{ $salaryRecord->user_name }}
                                            </div>
                                            <div class="col-md-3">
                                                <strong>النوع:</strong> 
                                                @switch($salaryRecord->user_type)
                                                    @case('employee')
                                                        <span class="badge bg-primary">موظف</span>
                                                        @break
                                                    @case('representative')
                                                        <span class="badge bg-success">مندوب</span>
                                                        @break
                                                    @case('supervisor')
                                                        <span class="badge bg-info">مشرف</span>
                                                        @break
                                                @endswitch
                                            </div>
                                            <div class="col-md-3">
                                                <strong>المرتب الأساسي:</strong> {{ number_format($salaryRecord->base_salary, 0) }} ج.م
                                            </div>
                                            <div class="col-md-3">
                                                <strong>الشهر/السنة:</strong> {{ $salaryRecord->month }}/{{ $salaryRecord->year }}
                                            </div>
                                        </div>
                                    </div>
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
                                             <strong>السلف الحالية:</strong> {{ number_format($salaryRecord->advances, 0) }} ج.م
                                         </div>
                                         
                                         @if(isset($advancesInfo) && count($advancesInfo['details']) > 0)
                                         <div class="mt-3">
                                             <h6 class="mb-2">تفاصيل السلف:</h6>
                                             @foreach($advancesInfo['details'] as $advance)
                                                 <div class="border-start border-info ps-2 mb-2">
                                                                                                           @if($advance['type'] === 'installment')
                                                          <div class="d-flex justify-content-between">
                                                             <span class="text-muted">قسط {{ $advance['installment_number'] }}/{{ $advance['total_installments'] }}</span>
                                                             <span class="badge bg-warning">أقساط</span>
                                                         </div>
                                                         <div class="fw-bold">{{ number_format($advance['amount'], 0) }} ج.م</div>
                                                         <small class="text-muted">
                                                             من إجمالي {{ number_format($advance['total_amount'], 0) }} ج.م
                                                             ({{ $advance['reason'] }})
                                                             <br>
                                                             <span class="text-info">الأقساط المتبقية: {{ $advance['remaining_installments'] }}</span>
                                                         </small>
                                                     @else
                                                         <div class="d-flex justify-content-between">
                                                             <span class="fw-bold">{{ number_format($advance['amount'], 0) }} ج.م</span>
                                                             <span class="badge bg-primary">دفعة واحدة</span>
                                                         </div>
                                                         <small class="text-muted">{{ $advance['reason'] }}</small>
                                                     @endif
                                                 </div>
                                             @endforeach
                                         </div>
                                         @endif
                                         
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
                                                value="{{ old('deductions', $salaryRecord->deductions) }}" min="0" step="0.01" required>
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
                                               value="{{ old('lost_orders_penalty', $salaryRecord->lost_orders_penalty) }}" min="0" step="0.01" required>
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
                                               value="{{ old('delivery_penalty', $salaryRecord->delivery_penalty) }}" min="0" step="0.01" required>
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
                                               value="{{ old('commissions', $salaryRecord->commissions) }}" min="0" step="0.01" required>
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
                                               value="{{ old('cashback', $salaryRecord->cashback) }}" min="0" step="0.01" required>
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
                                              rows="3" placeholder="أضف ملاحظات إضافية...">{{ old('notes', $salaryRecord->notes) }}</textarea>
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
                                                    <span id="base_salary_preview">{{ number_format($salaryRecord->base_salary, 0) }}</span> ج.م
                                                </div>
                                                <div class="col-md-3">
                                                    <strong class="text-danger">إجمالي الخصومات:</strong>
                                                    <span id="total_deductions_preview" class="text-danger">{{ number_format($salaryRecord->advances + $salaryRecord->deductions + $salaryRecord->lost_orders_penalty + $salaryRecord->delivery_penalty, 0) }}</span> ج.م
                                                </div>
                                                <div class="col-md-3">
                                                    <strong class="text-success">إجمالي المكافآت:</strong>
                                                    <span id="total_bonuses_preview" class="text-success">{{ number_format($salaryRecord->commissions + $salaryRecord->cashback, 0) }}</span> ج.م
                                                </div>
                                                <div class="col-md-3">
                                                    <strong class="text-primary">صافي المرتب:</strong>
                                                    <span id="net_salary_preview" class="text-primary fw-bold">{{ number_format($salaryRecord->net_salary, 0) }}</span> ج.م
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
                                            'year' => $salaryRecord->year, 
                                            'month' => $salaryRecord->month,
                                            'user_type' => $salaryRecord->user_type
                                        ]) }}" class="btn btn-light">
                                            <i class="feather-x me-2"></i>إلغاء
                                        </a>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="feather-save me-2"></i>تحديث مكونات المرتب
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
         // Update salary preview when any amount changes
     const amountInputs = document.querySelectorAll('input[name="deductions"], input[name="lost_orders_penalty"], input[name="delivery_penalty"], input[name="commissions"], input[name="cashback"]');
     amountInputs.forEach(input => {
         input.addEventListener('input', updateSalaryPreview);
     });
     
     function updateSalaryPreview() {
         const baseSalary = {{ $salaryRecord->base_salary }};
         
         // Advances will be calculated automatically from the database
         const advances = {{ $salaryRecord->advances }}; // This will be fetched from advance_requests table
         const deductions = parseFloat(document.querySelector('input[name="deductions"]').value) || 0;
         const lostOrdersPenalty = parseFloat(document.querySelector('input[name="lost_orders_penalty"]').value) || 0;
         const deliveryPenalty = parseFloat(document.querySelector('input[name="delivery_penalty"]').value) || 0;
         const commissions = parseFloat(document.querySelector('input[name="commissions"]').value) || 0;
         const cashback = parseFloat(document.querySelector('input[name="cashback"]').value) || 0;
         
         const totalDeductions = advances + deductions + lostOrdersPenalty + deliveryPenalty;
         const totalBonuses = commissions + cashback;
         const netSalary = Math.max(0, baseSalary - totalDeductions + totalBonuses);
         
         document.getElementById('total_deductions_preview').textContent = totalDeductions.toLocaleString('ar-EG');
         document.getElementById('total_bonuses_preview').textContent = totalBonuses.toLocaleString('ar-EG');
         document.getElementById('net_salary_preview').textContent = netSalary.toLocaleString('ar-EG');
     }
});
</script>
@endpush

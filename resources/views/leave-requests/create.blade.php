@extends('layouts.app')

@section('title', 'إضافة طلب إجازة')

@section('content')
<div class="nxl-content">
    <!-- [ page-header ] start -->
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
                <li class="breadcrumb-item"><a href="{{ route('leave-requests.index') }}">طلبات الإجازة</a></li>
                <li class="breadcrumb-item">إضافة طلب</li>
            </ul>
        </div>
    </div>
    <!-- [ page-header ] end -->

    <!-- [ Main Content ] start -->
    <div class="main-content">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">إضافة طلب إجازة جديد</h5>
                    </div>
                    <div class="card-body">
                        @if($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('leave-requests.store') }}" method="POST">
                            @csrf
                            
                            <!-- Requester Type Selection -->
                            <div class="mb-4">
                                <label class="form-label">نوع مقدم الطلب <span class="text-danger">*</span></label>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="requester_type" id="employee_type" value="employee" checked>
                                            <label class="form-check-label" for="employee_type">
                                                موظف
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="requester_type" id="representative_type" value="representative">
                                            <label class="form-check-label" for="representative_type">
                                                مندوب
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="requester_type" id="supervisor_type" value="supervisor">
                                            <label class="form-check-label" for="supervisor_type">
                                                مشرف
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Employee Selection -->
                            <div class="mb-3" id="employee_selection">
                                <label class="form-label">اختر الموظف <span class="text-danger">*</span></label>
                                <select name="employee_id" class="form-control" id="employee_id">
                                    <option value="">اختر الموظف</option>
                                    @foreach($employees as $employee)
                                        <option value="{{ $employee->id }}" {{ old('employee_id') == $employee->id ? 'selected' : '' }}>
                                            {{ $employee->name }} - {{ $employee->phone }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Representative Selection -->
                            <div class="mb-3" id="representative_selection" style="display: none;">
                                <label class="form-label">اختر المندوب <span class="text-danger">*</span></label>
                                <select name="representative_id" class="form-control" id="representative_id">
                                    <option value="">اختر المندوب</option>
                                    @foreach(\App\Models\Representative::all() as $representative)
                                        <option value="{{ $representative->id }}" {{ old('representative_id') == $representative->id ? 'selected' : '' }}>
                                            {{ $representative->name }} - {{ $representative->phone }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Supervisor Selection -->
                            <div class="mb-3" id="supervisor_selection" style="display: none;">
                                <label class="form-label">اختر المشرف <span class="text-danger">*</span></label>
                                <select name="supervisor_id" class="form-control" id="supervisor_id">
                                    <option value="">اختر المشرف</option>
                                    @foreach(\App\Models\Supervisor::all() as $supervisor)
                                        <option value="{{ $supervisor->id }}" {{ old('supervisor_id') == $supervisor->id ? 'selected' : '' }}>
                                            {{ $supervisor->name }} - {{ $supervisor->phone }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">تاريخ البداية <span class="text-danger">*</span></label>
                                        <input type="date" name="start_date" class="form-control" value="{{ old('start_date') }}" required min="{{ date('Y-m-d') }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">تاريخ النهاية <span class="text-danger">*</span></label>
                                        <input type="date" name="end_date" class="form-control" value="{{ old('end_date') }}" required min="{{ date('Y-m-d') }}">
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">نوع الإجازة <span class="text-danger">*</span></label>
                                <select name="type" class="form-control" required>
                                    <option value="">اختر نوع الإجازة</option>
                                    <option value="سنوية" {{ old('type') === 'سنوية' ? 'selected' : '' }}>سنوية</option>
                                    <option value="مرضية" {{ old('type') === 'مرضية' ? 'selected' : '' }}>مرضية</option>
                                    <option value="طارئة" {{ old('type') === 'طارئة' ? 'selected' : '' }}>طارئة</option>
                                    <option value="أخرى" {{ old('type') === 'أخرى' ? 'selected' : '' }}>أخرى</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">السبب</span></label>
                                <textarea name="reason" class="form-control" rows="4"  placeholder="أدخل سبب الإجازة...">{{ old('reason') }}</textarea>
                            </div>

                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('leave-requests.index') }}" class="btn btn-light">إلغاء</a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="feather-save me-2"></i>حفظ الطلب
                                </button>
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
    const requesterTypeRadios = document.querySelectorAll('input[name="requester_type"]');
    const employeeSelection = document.getElementById('employee_selection');
    const representativeSelection = document.getElementById('representative_selection');
    const supervisorSelection = document.getElementById('supervisor_selection');

    function updateSelectionVisibility() {
        const selectedType = document.querySelector('input[name="requester_type"]:checked').value;
        
        // Hide all selections
        employeeSelection.style.display = 'none';
        representativeSelection.style.display = 'none';
        supervisorSelection.style.display = 'none';
        
        // Clear all values
        document.getElementById('employee_id').value = '';
        document.getElementById('representative_id').value = '';
        document.getElementById('supervisor_id').value = '';
        
        // Show selected type
        switch(selectedType) {
            case 'employee':
                employeeSelection.style.display = 'block';
                break;
            case 'representative':
                representativeSelection.style.display = 'block';
                break;
            case 'supervisor':
                supervisorSelection.style.display = 'block';
                break;
        }
    }

    // Add event listeners
    requesterTypeRadios.forEach(radio => {
        radio.addEventListener('change', updateSelectionVisibility);
    });

    // Initialize on page load
    updateSelectionVisibility();
});
</script>
@endpush

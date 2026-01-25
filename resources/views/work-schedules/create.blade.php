@extends('layouts.app')

@section('title', 'إضافة مواعيد عمل')

@section('content')
<div class="nxl-content">
    <!-- [ page-header ] start -->
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
                <li class="breadcrumb-item"><a href="{{ route('work-schedules.index') }}">مواعيد العمل</a></li>
                <li class="breadcrumb-item">إضافة مواعيد عمل</li>
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
                        <h5 class="card-title mb-0">إضافة مواعيد عمل جديدة</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('work-schedules.store') }}" method="POST">
                            @csrf
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="employee_id" class="form-label">الموظف <span class="text-danger">*</span></label>
                                        <select name="employee_id" id="employee_id" class="form-control @error('employee_id') is-invalid @enderror" required>
                                            <option value="">اختر الموظف</option>
                                            @foreach($employees as $employee)
                                                <option value="{{ $employee->id }}" {{ old('employee_id') == $employee->id ? 'selected' : '' }}>
                                                    {{ $employee->name }} - {{ $employee->department->name ?? 'غير محدد' }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('employee_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="shift" class="form-label">الشيفت <span class="text-danger">*</span></label>
                                        <select name="shift" id="shift" class="form-control @error('shift') is-invalid @enderror" required>
                                            <option value="">اختر الشيفت</option>
                                            <option value="صباحي" {{ old('shift') === 'صباحي' ? 'selected' : '' }}>صباحي</option>
                                            <option value="مسائي" {{ old('shift') === 'مسائي' ? 'selected' : '' }}>مسائي</option>
                                        </select>
                                        @error('shift')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="start_time" class="form-label">وقت البداية <span class="text-danger">*</span></label>
                                        <input type="time" name="start_time" id="start_time" class="form-control @error('start_time') is-invalid @enderror" value="{{ old('start_time') }}" required>
                                        @error('start_time')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="end_time" class="form-label">وقت النهاية <span class="text-danger">*</span></label>
                                        <input type="time" name="end_time" id="end_time" class="form-control @error('end_time') is-invalid @enderror" value="{{ old('end_time') }}" required>
                                        @error('end_time')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">أيام العمل <span class="text-danger">*</span></label>
                                <div class="row">
                                    @php
                                        $workDays = ['السبت', 'الأحد', 'الاثنين', 'الثلاثاء', 'الأربعاء', 'الخميس', 'الجمعة'];
                                        $selectedDays = old('work_days', ['السبت', 'الأحد', 'الاثنين', 'الثلاثاء', 'الأربعاء', 'الخميس']);
                                    @endphp
                                    @foreach($workDays as $day)
                                        <div class="col-md-3 col-6">
                                            <div class="form-check">
                                                <input class="form-check-input @error('work_days') is-invalid @enderror" type="checkbox" name="work_days[]" value="{{ $day }}" id="day_{{ $loop->index }}" 
                                                    {{ in_array($day, $selectedDays) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="day_{{ $loop->index }}">
                                                    {{ $day }}
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                @error('work_days')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="effective_date" class="form-label">تاريخ التفعيل <span class="text-danger">*</span></label>
                                        <input type="date" name="effective_date" id="effective_date" class="form-control @error('effective_date') is-invalid @enderror" value="{{ old('effective_date', now()->format('Y-m-d')) }}" required>
                                        @error('effective_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="end_date" class="form-label">تاريخ الانتهاء</label>
                                        <input type="date" name="end_date" id="end_date" class="form-control @error('end_date') is-invalid @enderror" value="{{ old('end_date') }}">
                                        <small class="form-text text-muted">اتركه فارغاً إذا كان غير محدد</small>
                                        @error('end_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="notes" class="form-label">ملاحظات</label>
                                <textarea name="notes" id="notes" class="form-control @error('notes') is-invalid @enderror" rows="3" placeholder="أي ملاحظات إضافية...">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('work-schedules.index') }}" class="btn btn-light">إلغاء</a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="feather-save me-2"></i>حفظ مواعيد العمل
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
    // Auto-calculate end time based on shift
    const shiftSelect = document.getElementById('shift');
    const startTimeInput = document.getElementById('start_time');
    const endTimeInput = document.getElementById('end_time');

    function updateEndTime() {
        const shift = shiftSelect.value;
        const startTime = startTimeInput.value;
        
        if (shift && startTime) {
            let startHour = parseInt(startTime.split(':')[0]);
            let startMinute = parseInt(startTime.split(':')[1]);
            
            if (shift === 'صباحي') {
                // Morning shift: 8 hours
                startHour += 8;
            } else {
                // Evening shift: 8 hours
                startHour += 8;
            }
            
            // Handle day overflow
            if (startHour >= 24) {
                startHour -= 24;
            }
            
            const endTime = `${startHour.toString().padStart(2, '0')}:${startMinute.toString().padStart(2, '0')}`;
            endTimeInput.value = endTime;
        }
    }

    shiftSelect.addEventListener('change', updateEndTime);
    startTimeInput.addEventListener('change', updateEndTime);
});
</script>
@endpush

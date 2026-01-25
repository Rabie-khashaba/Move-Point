@extends('layouts.app')

@section('title', 'تعديل طلب السلف')

@section('content')
<div class="nxl-content">
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
                <li class="breadcrumb-item"><a href="{{ route('advance-requests.index') }}">طلبات السلف</a></li>
                <li class="breadcrumb-item">تعديل الطلب</li>
            </ul>
        </div>
    </div>

    <div class="main-content">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">تعديل طلب السلف</h5>
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

                        <form action="{{ route('advance-requests.update', $advanceRequest->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <!-- Requester Type -->
                            <div class="mb-4">
                                <label class="form-label">نوع مقدم الطلب <span class="text-danger">*</span></label>
                                <div class="row">
                                    @foreach(['representative'=>'مندوب','employee'=>'موظف','supervisor'=>'مشرف'] as $type => $label)
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="requester_type" value="{{ $type }}"
                                                id="{{ $type }}_type"
                                                {{ (old('requester_type') ?? $advanceRequest->requester_type) == $type ? 'checked' : '' }}>
                                            <label class="form-check-label" for="{{ $type }}_type">
                                                {{ $label }}
                                            </label>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Representative Selection -->
                            <div class="mb-3" id="representative_selection">
                                <label class="form-label">اختر المندوب <span class="text-danger">*</span></label>
                                <select name="representative_id" class="form-control">
                                    <option value="">اختر المندوب</option>
                                    @foreach(\App\Models\Representative::all() as $rep)
                                        <option value="{{ $rep->id }}"
                                            {{ (old('representative_id') ?? $advanceRequest->representative_id) == $rep->id ? 'selected' : '' }}>
                                            {{ $rep->name }} - {{ $rep->phone }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Employee Selection -->
                            <div class="mb-3" id="employee_selection">
                                <label class="form-label">اختر الموظف <span class="text-danger">*</span></label>
                                <select name="employee_id" class="form-control">
                                    <option value="">اختر الموظف</option>
                                    @foreach(\App\Models\Employee::active()->get() as $emp)
                                        <option value="{{ $emp->id }}"
                                            {{ (old('employee_id') ?? $advanceRequest->employee_id) == $emp->id ? 'selected' : '' }}>
                                            {{ $emp->name }} - {{ $emp->phone }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Supervisor Selection -->
                            <div class="mb-3" id="supervisor_selection">
                                <label class="form-label">اختر المشرف <span class="text-danger">*</span></label>
                                <select name="supervisor_id" class="form-control">
                                    <option value="">اختر المشرف</option>
                                    @foreach(\App\Models\Supervisor::all() as $sup)
                                        <option value="{{ $sup->id }}"
                                            {{ (old('supervisor_id') ?? $advanceRequest->supervisor_id) == $sup->id ? 'selected' : '' }}>
                                            {{ $sup->name }} - {{ $sup->phone }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">مبلغ السلف <span class="text-danger">*</span></label>
                                    <input type="number" name="amount" class="form-control"
                                        value="{{ old('amount') ?? $advanceRequest->amount }}" step="0.01" min="0" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">مدة التقسيط (شهور)</label>
                                    <input type="number" name="installment_months" class="form-control"
                                        value="{{ old('installment_months') ?? $advanceRequest->installment_months }}" min="1" max="12">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">السبب</label>
                                <textarea name="reason" class="form-control" rows="4">{{ old('reason') ?? $advanceRequest->reason }}</textarea>
                            </div>

                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('advance-requests.index') }}" class="btn btn-light">إلغاء</a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="feather-save me-2"></i>
                                    تحديث الطلب
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const requesterTypeRadios = document.querySelectorAll('input[name="requester_type"]');
    const representativeSelection = document.getElementById('representative_selection');
    const employeeSelection = document.getElementById('employee_selection');
    const supervisorSelection = document.getElementById('supervisor_selection');

    function updateSelectionVisibility() {
        const selectedType = document.querySelector('input[name="requester_type"]:checked').value;
        representativeSelection.style.display = selectedType === 'representative' ? 'block' : 'none';
        employeeSelection.style.display = selectedType === 'employee' ? 'block' : 'none';
        supervisorSelection.style.display = selectedType === 'supervisor' ? 'block' : 'none';
    }

    requesterTypeRadios.forEach(radio => radio.addEventListener('change', updateSelectionVisibility));

    updateSelectionVisibility();
});
</script>
@endpush

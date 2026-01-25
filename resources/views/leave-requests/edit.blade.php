@extends('layouts.app')

@section('title', 'تعديل طلب الإجازة')

@section('content')
<div class="nxl-content">
    <!-- [ page-header ] start -->
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
                <li class="breadcrumb-item"><a href="{{ route('leave-requests.index') }}">طلبات الإجازة</a></li>
                <li class="breadcrumb-item">تعديل طلب</li>
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
                        <h5 class="card-title mb-0">تعديل طلب الإجازة</h5>
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

                        <form action="{{ route('leave-requests.update', $leave->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            
                            <!-- Current Requester Info -->
                            <div class="alert alert-info">
                                <strong>مقدم الطلب الحالي:</strong> {{ $leave->requester_name }}
                                @if($leave->employee)
                                    (موظف)
                                @elseif($leave->representative)
                                    (مندوب)
                                @elseif($leave->supervisor)
                                    (مشرف)
                                @endif
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">تاريخ البداية <span class="text-danger">*</span></label>
                                        <input type="date" name="start_date" class="form-control" value="{{ old('start_date', $leave->start_date->format('Y-m-d')) }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">تاريخ النهاية <span class="text-danger">*</span></label>
                                        <input type="date" name="end_date" class="form-control" value="{{ old('end_date', $leave->end_date->format('Y-m-d')) }}" required>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">نوع الإجازة <span class="text-danger">*</span></label>
                                <select name="type" class="form-control" required>
                                    <option value="">اختر نوع الإجازة</option>
                                    <option value="سنوية" {{ old('type', $leave->type) === 'سنوية' ? 'selected' : '' }}>سنوية</option>
                                    <option value="مرضية" {{ old('type', $leave->type) === 'مرضية' ? 'selected' : '' }}>مرضية</option>
                                    <option value="طارئة" {{ old('type', $leave->type) === 'طارئة' ? 'selected' : '' }}>طارئة</option>
                                    <option value="أخرى" {{ old('type', $leave->type) === 'أخرى' ? 'selected' : '' }}>أخرى</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">السبب</span></label>
                                <textarea name="reason" class="form-control" rows="4" placeholder="أدخل سبب الإجازة...">{{ old('reason', $leave->reason) }}</textarea>
                            </div>

                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('leave-requests.index') }}" class="btn btn-light">إلغاء</a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="feather-save me-2"></i>حفظ التغييرات
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

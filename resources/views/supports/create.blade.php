@extends('layouts.app')

@section('title', 'إضافة بلاغ جديد')

@section('content')
<div class="nxl-content">
    <div class="page-header">
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('supports.index') }}">الدعم الفني</a></li>
            <li class="breadcrumb-item">إضافة</li>
        </ul>
        <a href="{{ route('supports.index') }}" class="btn btn-light-brand">
            <i class="feather-arrow-left me-2"></i>
            <span>رجوع</span>
        </a>
    </div>

    <div class="main-content">
        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="card-title mb-0">إضافة بلاغ جديد</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('supports.store') }}" method="POST">
                    @csrf

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">اسم الشخص <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" required value="{{ old('name') }}">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">رقم الهاتف <span class="text-danger">*</span></label>
                            <input type="text" name="phone" class="form-control" required value="{{ old('phone') }}">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">التاريخ</label>
                            <input type="date" name="date" class="form-control" value="{{ old('date', date('Y-m-d')) }}">
                        </div>

                        <div class="col-md-12 mb-3">
                            <label class="form-label">وصف المشكلة <span class="text-danger">*</span></label>
                            <textarea name="issue" class="form-control" rows="4" required>{{ old('issue') }}</textarea>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('supports.index') }}" class="btn btn-light">إلغاء</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="feather-save me-2"></i> حفظ البلاغ
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

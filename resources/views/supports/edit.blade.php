@extends('layouts.app')

@section('title', 'تعديل البلاغ')

@section('content')
<div class="nxl-content">

    <!-- [ page-header ] start -->
    <div class="page-header">
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('supports.index') }}">الدعم الفني</a></li>
            <li class="breadcrumb-item">تعديل البلاغ</li>
        </ul>
        <a href="{{ route('supports.index') }}" class="btn btn-light-brand">
            <i class="feather-arrow-left me-2"></i>
            <span>رجوع</span>
        </a>
    </div>
    <!-- [ page-header ] end -->


    <div class="main-content">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h5 class="card-title mb-0">تعديل بيانات البلاغ</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('supports.update', $support) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">اسم الشخص <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control" required value="{{ old('name', $support->name) }}">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">رقم الهاتف <span class="text-danger">*</span></label>
                                    <input type="text" name="phone" class="form-control" required value="{{ old('phone', $support->phone) }}">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">التاريخ</label>
                                    <input type="date" name="date" class="form-control" value="{{ old('date', $support->date) }}">
                                </div>

                                <div class="col-md-12 mb-3">
                                    <label class="form-label">المشكلة <span class="text-danger">*</span></label>
                                    <textarea name="issue" class="form-control" rows="4" required>{{ old('issue', $support->issue) }}</textarea>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">الحالة</label>
                                    <select name="status" class="form-select">
                                        <option value="open" {{ $support->status == 'open' ? 'selected' : '' }}>قيد المراجعة</option>
                                        <option value="replied" {{ $support->status == 'replied' ? 'selected' : '' }}>تم الرد</option>
                                        <option value="closed" {{ $support->status == 'closed' ? 'selected' : '' }}>منتهي</option>
                                    </select>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('supports.index') }}" class="btn btn-light">إلغاء</a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="feather-save me-2"></i> حفظ التعديلات
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

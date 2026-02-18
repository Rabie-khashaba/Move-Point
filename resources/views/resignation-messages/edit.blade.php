@extends('layouts.app')

@section('title', 'تعديل رسالة استقالة')

@section('content')
<div class="nxl-content">
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('resignation-messages.index') }}">رسائل الاستقالة</a></li>
                <li class="breadcrumb-item">تعديل</li>
            </ul>
        </div>
        <div class="page-header-right ms-auto">
            <a href="{{ route('resignation-messages.index') }}" class="btn btn-light-brand">
                <i class="feather-arrow-left me-2"></i>
                <span>الرجوع</span>
            </a>
        </div>
    </div>

    <div class="main-content">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">تعديل رسالة استقالة</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('resignation-messages.update', $message->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label">العنوان <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $message->title) }}" required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">المحتوى <span class="text-danger">*</span></label>
                        <textarea name="content" rows="6" class="form-control @error('content') is-invalid @enderror" required>{{ old('content', $message->content) }}</textarea>
                        @error('content')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('resignation-messages.index') }}" class="btn btn-light">إلغاء</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="feather-save me-2"></i>حفظ التعديلات
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

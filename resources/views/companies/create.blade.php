@extends('layouts.app')

@section('title', 'إنشاء شركة')

@section('content')
    <!-- [ page-header ] start -->
    <div class="page-header">
        
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('companies.index') }}">الشركات</a></li>
                <li class="breadcrumb-item">إنشاء</li>
            </ul>
        </div>
    
    <!-- [ page-header ] end -->
    
    <!-- [ Main Content ] start -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">إنشاء شركة جديدة</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('companies.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">اسم الشركة *</label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                       placeholder="أدخل اسم الشركة" value="{{ old('name') }}" required>
                                @error('name')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            {{-- 
                            <div class="col-md-6 mb-3">
                                <label class="form-label">الهاتف</label>
                                <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" 
                                       placeholder="أدخل رقم الهاتف" value="{{ old('phone') }}">
                                @error('phone')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">البريد الإلكتروني</label>
                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                                       placeholder="أدخل عنوان البريد الإلكتروني" value="{{ old('email') }}">
                                @error('email')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">الموقع الإلكتروني</label>
                                <input type="url" name="website" class="form-control @error('website') is-invalid @enderror" 
                                       placeholder="https://example.com" value="{{ old('website') }}">
                                @error('website')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label">العنوان</label>
                                <textarea name="address" class="form-control" rows="2" placeholder="أدخل عنوان الشركة">{{ old('address') }}</textarea>
                                @error('address')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">شعار الشركة</label>
                                <input type="file" name="logo" class="form-control @error('logo') is-invalid @enderror" 
                                       accept="image/*" id="logoInput">
                                <small class="form-text text-muted">رفع ملف JPG، PNG، أو GIF (الحد الأقصى 2MB)</small>
                                @error('logo')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">معاينة الشعار</label>
                                <div class="logo-preview-container">
                                    <img id="logoPreview" src="{{ asset('assets/images/default-company.png') }}" 
                                         alt="معاينة الشعار" class="img-thumbnail" style="max-width: 150px; max-height: 150px;">
                                </div>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label">الوصف</label>
                                <textarea name="description" class="form-control" rows="4" placeholder="أدخل وصف الشركة">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label">الحالة</label>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="is_active" value="1" checked>
                                    <label class="form-check-label">نشط</label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('companies.index') }}" class="btn btn-light">إلغاء</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="feather-save me-2"></i>
                                إنشاء الشركة
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- [ Main Content ] end -->
@endsection

@section('scripts')
<script>
    // وظيفة معاينة الشعار
    document.getElementById('logoInput').addEventListener('change', function(e) {
        const file = e.target.files[0];
        const preview = document.getElementById('logoPreview');
        
        if (file) {
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                };
                reader.readAsDataURL(file);
            } else {
                alert('الرجاء اختيار ملف صورة صالح.');
                this.value = '';
            }
        }
    });
</script>
@endsection

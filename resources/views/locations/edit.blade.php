@extends('layouts.app')

@section('title', 'تعديل الموقع')

@section('content')
<div class="nxl-content">
    <!-- [ page-header ] start -->
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
        
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('locations.index') }}">المناطق</a></li>

                <li class="breadcrumb-item">تعديل</li>
            </ul>
        </div>
       
    </div>
    <!-- [ page-header ] end -->
    
    <!-- [ Main Content ] start -->
    <div class="main-content">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">تعديل المناطق</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('locations.update', $location->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            
                            <div class="row">
                          <div class="col-md-6 mb-3">
                        <label class="form-label">المحافظة</label>
                        <select name="governorate_id" class="form-control" required>
                            <option value="">اختر المحافظة</option>
                            @foreach($governorates as $governorate)
                                <option value="{{ $governorate->id }}" 
                                    {{ old('governorate_id', $location->governorate_id) == $governorate->id ? 'selected' : '' }}>
                                    {{ $governorate->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('governorate_id')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">الاسم</label>
                                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                           placeholder="أدخل الاسم" value="{{ old('name', $location->name) }}" required>
                                    @error('name')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                {{-- 
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">المنطقة</label>
                                    <input type="text" name="location" class="form-control @error('location') is-invalid @enderror" 
                                           placeholder="أدخل المنطقة" value="{{ old('location', $location->location) }}" >
                                    @error('location')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">العنوان</label>
                                    <input type="text" name="address" class="form-control @error('address') is-invalid @enderror" 
                                           placeholder="أدخل العنوان" value="{{ old('address', $location->address) }}" >
                                    @error('address')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">الهاتف</label>
                                    <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" 
                                           placeholder="أدخل الهاتف" value="{{ old('phone', $location->phone) }}" >
                                    @error('phone')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">البريد الإلكتروني</label>
                                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                                           placeholder="أدخل البريد الإلكتروني" value="{{ old('email', $location->email) }}" >
                                    @error('email')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">الوصف</label>
                                    <textarea name="description" class="form-control" rows="4" placeholder="أدخل الوصف">{{ old('description', $location->description) }}</textarea>
                                    @error('description')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                --}}
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">الحالة</label>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="is_active" value="1" {{ $location->is_active ? 'checked' : '' }}>
                                        <label class="form-check-label">نشط</label>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('locations.show', $location->id) }}" class="btn btn-light">إلغاء</a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="feather-save me-2"></i>
                                    تحديث الموقع
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

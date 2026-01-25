@extends('layouts.app')

@section('title', 'عرض الموقع')

@section('content')
<div class="nxl-content">
    <!-- [ page-header ] start -->
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="m-b-10">المواقع</h5>
            </div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('locations.index') }}">المواقع</a></li>
                <li class="breadcrumb-item">عرض</li>
            </ul>
        </div>
        <div class="page-header-right ms-auto">
            <div class="page-header-right-items">
                @can('edit_locations')
                <a href="{{ route('locations.edit', $location->id) }}" class="btn btn-warning">
                    <i class="feather-edit me-2"></i>
                    <span>تعديل</span>
                </a>
                @endcan
                {{--
                <form action="{{ route('locations.destroy', $location->id) }}" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" onclick="return confirm('هل أنت متأكد أنك تريد حذف هذا الموقع؟')">
                        <i class="feather-trash-2 me-2"></i>
                        <span>حذف</span>
                    </button>
                </form>
                --}}
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
                        <h5 class="card-title mb-0">تفاصيل الموقع</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="fw-bold">المحافظة:</label>
                                <span>{{ $location->governorate->name ?? 'غير متوفر' }}</span>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="fw-bold">الاسم:</label>
                                <span>{{ $location->name ?: 'غير متوفر' }}</span>
                            </div>
                            {{-- 
                            <div class="col-md-6 mb-3">
                                <label class="fw-bold">الموقع:</label>
                                <span>{{ $location->location ?: 'غير متوفر' }}</span>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="fw-bold">العنوان:</label>
                                <span>{{ $location->address ?: 'غير متوفر' }}</span>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="fw-bold">الهاتف:</label>
                                <span>{{ $location->phone ?: 'غير متوفر' }}</span>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="fw-bold">البريد الإلكتروني:</label>
                                <span>{{ $location->email ?: 'غير متوفر' }}</span>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="fw-bold">الوصف:</label>
                                <p>{{ $location->description ?: 'لا يوجد وصف' }}</p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="fw-bold">الحالة:</label>
                                <span class="badge bg-{{ $location->is_active ? 'success' : 'danger' }}">
                                    {{ $location->is_active ? 'نشط' : 'غير نشط' }}
                                </span>
                            </div>
                            --}}
                        </div>
                        
                        <div class="mt-4">
                            <label class="fw-bold">تاريخ الإنشاء:</label>
                            <span>{{ $location->created_at->format('d M, Y H:i') }}</span>
                        </div>
                        
                        <div class="mt-2">
                            <label class="fw-bold">آخر تحديث:</label>
                            <span>{{ $location->updated_at->format('d M, Y H:i') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- [ Main Content ] end -->
</div>
@endsection

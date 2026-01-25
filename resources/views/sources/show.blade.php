@extends('layouts.app')

@section('title', 'عرض المصدر')

@section('content')
<div class="nxl-content">
    <!-- [ page-header ] start -->
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="m-b-10">المصادر</h5>
            </div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('sources.index') }}">المصادر</a></li>
                <li class="breadcrumb-item">عرض</li>
            </ul>
        </div>
        <div class="page-header-right ms-auto">
            <div class="page-header-right-items">
                @can('edit_sources')
                <a href="{{ route('sources.edit', $source->id) }}" class="btn btn-warning">
                    <i class="feather-edit me-2"></i>
                    <span>تعديل</span>
                </a>
                @endcan
              
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
                        <h5 class="card-title mb-0">تفاصيل المصدر</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="fw-bold">الاسم:</label>
                                <span>{{ $source->name ?: 'غير متوفر' }}</span>
                            </div>
                            {{-- 
                            <div class="col-md-12 mb-3">
                                <label class="fw-bold">الوصف:</label>
                                <p>{{ $source->description ?: 'لا يوجد وصف متوفر' }}</p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="fw-bold">الحالة:</label>
                                <span class="badge bg-{{ $source->is_active ? 'success' : 'danger' }}">
                                    {{ $source->is_active ? 'نشط' : 'غير نشط' }}
                                </span>
                            </div>
                            --}}
                        </div>
                        
                        <div class="mt-4">
                            <label class="fw-bold">تاريخ الإنشاء:</label>
                            <span>{{ $source->created_at->format('d M, Y H:i') }}</span>
                        </div>
                        
                        <div class="mt-2">
                            <label class="fw-bold">آخر تحديث:</label>
                            <span>{{ $source->updated_at->format('d M, Y H:i') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- [ Main Content ] end -->
</div>
@endsection

@extends('layouts.app')

@section('title', 'عرض المشرف')

@section('content')
<div class="nxl-content">
    <!-- [ page-header ] start -->
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="m-b-10">المشرفين</h5>
            </div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('supervisors.index') }}">المشرفين</a></li>
                <li class="breadcrumb-item">عرض</li>
            </ul>
        </div>
        <div class="page-header-right ms-auto">
            <div class="page-header-right-items">
                @can('edit_supervisors')
                <a href="{{ route('supervisors.edit', $supervisor->id) }}" class="btn btn-warning">
                    <i class="feather-edit me-2"></i>
                    <span>تعديل</span>
                </a>
                @endcan
                {{--
                @can('delete_supervisors')
                <form action="{{ route('supervisors.destroy', $supervisor->id) }}" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" onclick="return confirm('هل أنت متأكد من حذف هذا المشرف؟')">
                        <i class="feather-trash-2 me-2"></i>
                        <span>حذف</span>
                    </button>
                </form>
                @endcan
                --}}
            </div>
        </div>
    </div>
    <!-- [ page-header ] end -->
    
    <!-- [ Main Content ] start -->
    <div class="main-content">
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">تفاصيل المشرف</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="fw-bold">الاسم:</label>
                                <span>{{ $supervisor->name ?: 'غير محدد' }}</span>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="fw-bold">رقم التليفون:</label>
                                <span>{{ $supervisor->phone ?: 'غير محدد' }}</span>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="fw-bold">التواصل:</label>
                                <span>{{ $supervisor->contact ?: 'غير محدد' }}</span>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="fw-bold">رقم البطاقة:</label>
                                <span>{{ $supervisor->national_id ?: 'غير محدد' }}</span>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="fw-bold">المرتب:</label>
                                <span>{{ number_format($supervisor->salary, 2) }} ريال</span>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="fw-bold">تاريخ بداية العمل:</label>
                                <span>{{ $supervisor->start_date ? $supervisor->start_date->format('d/m/Y') : 'غير محدد' }}</span>
                            </div>
                                                         <div class="col-md-6 mb-3">
                                 <label class="fw-bold">المحافظة:</label>
                                 <span>{{ $supervisor->governorate->name ?? 'غير محدد' }}</span>
                             </div>
                             <div class="col-md-6 mb-3">
                                 <label class="fw-bold">المقر:</label>
                                 <span>{{ $supervisor->location->name ?? 'غير محدد' }}</span>
                             </div>
                             <div class="col-md-6 mb-3">
                                 <label class="fw-bold">المقر المسؤول عنه:</label>
                                 <span>{{ $supervisor->location_name ?: 'غير محدد' }}</span>
                             </div>
                            <div class="col-md-6 mb-3">
                                <label class="fw-bold">الحالة:</label>
                                <span class="badge bg-{{ $supervisor->is_active ? 'success' : 'danger' }}">
                                    {{ $supervisor->is_active ? 'نشط' : 'غير نشط' }}
                                </span>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="fw-bold">تاريخ الإضافة:</label>
                                <span>{{ $supervisor->created_at->format('d/m/Y H:i') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">المندوبين المسؤول عنهم</h5>
                    </div>
                    <div class="card-body">
                        @if($supervisor->representatives->count() > 0)
                            <div class="list-group list-group-flush">
                                                                 @foreach($supervisor->representatives as $representative)
                                     <div class="list-group-item d-flex justify-content-between align-items-center">
                                         <div>
                                             <h6 class="mb-1">{{ $representative->name }}</h6>
                                             <small class="text-muted">{{ $representative->phone }}</small>
                                             @if($representative->location)
                                                 <br><small class="text-muted">{{ $representative->location->name }}</small>
                                             @endif
                                         </div>
                                         <div class="text-end">
                                             <span class="badge bg-primary rounded-pill">{{ $representative->company->name ?? 'غير محدد' }}</span>
                                             @if($representative->governorate)
                                                 <br><small class="text-muted">{{ $representative->governorate->name }}</small>
                                             @endif
                                         </div>
                                     </div>
                                 @endforeach
                            </div>
                        @else
                            <div class="text-center py-3">
                                <i class="feather-users text-muted" style="font-size: 3rem;"></i>
                                <p class="text-muted mt-2">لا يوجد مندوبين مسؤول عنهم</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- [ Main Content ] end -->
</div>
@endsection
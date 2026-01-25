@extends('layouts.app')

@section('title', 'عرض الشركة')

@section('content')
<div class="nxl-content">
    <!-- [ page-header ] start -->
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="m-b-10">الشركات</h5>
            </div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('companies.index') }}">الشركات</a></li>
                <li class="breadcrumb-item">عرض</li>
            </ul>
        </div>
        <div class="page-header-right ms-auto">
            @can('edit_companies')
            <div class="page-header-right-items">
                <a href="{{ route('companies.edit', $company->id) }}" class="btn btn-warning">
                    <i class="feather-edit me-2"></i>
                    <span>تعديل</span>
                </a>
                @endcan
                {{--
                @can('delete_companies')
                <form action="{{ route('companies.destroy', $company->id) }}" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" onclick="return confirm('هل أنت متأكد أنك تريد حذف هذه الشركة؟')">
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
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">تفاصيل الشركة</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="fw-bold">الاسم:</label>
                                <span>{{ $company->name ?: 'غير متوفر' }}</span>
                            </div>
                            {{-- 
                            <div class="col-md-6 mb-3">
                                <label class="fw-bold">الهاتف:</label>
                                <span>{{ $company->phone ?: 'غير متوفر' }}</span>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="fw-bold">البريد الإلكتروني:</label>
                                <span>{{ $company->email ?: 'غير متوفر' }}</span>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="fw-bold">العنوان:</label>
                                <span>{{ $company->address ?: 'غير متوفر' }}</span>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="fw-bold">الموقع الإلكتروني:</label>
                                <span>{{ $company->website ?: 'غير متوفر' }}</span>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="fw-bold">الشعار:</label>
                                @if($company->logo)
                                    <img src="{{ asset('storage/app/public/' . $company->logo) }}" alt="الشعار" class="rounded" width="100" height="100">
                                @else
                                    <span>غير متوفر</span>
                                @endif
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="fw-bold">الوصف:</label>
                                <p>{{ $company->description ?: 'لا يوجد وصف متوفر' }}</p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="fw-bold">الحالة:</label>
                                <span class="badge bg-{{ $company->is_active ? 'success' : 'danger' }}">
                                    {{ $company->is_active ? 'نشط' : 'غير نشط' }}
                                </span>
                            </div>
                            --}}
                        </div>
                        
                        <div class="mt-4">
                            <label class="fw-bold">تاريخ الإنشاء:</label>
                            <span>{{ $company->created_at->format('d M, Y H:i') }}</span>
                        </div>
                        
                        <div class="mt-2">
                            <label class="fw-bold">آخر تحديث:</label>
                            <span>{{ $company->updated_at->format('d M, Y H:i') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- [ Main Content ] end -->
</div>
@endsection

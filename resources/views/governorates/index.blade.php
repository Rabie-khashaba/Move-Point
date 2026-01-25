@extends('layouts.app')

@section('title', 'المحافظات')

@section('content')
<div class="nxl-content">
    <!-- [ page-header ] start -->
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
           
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الصفحة الرئيسية</a></li>
                <li class="breadcrumb-item">المحافظات</li>
            </ul>
        </div>
        <div class="page-header-right ms-auto">
            <div class="page-header-right-items">
                <div class="d-flex d-md-none">
                    <a href="javascript:void(0)" class="page-header-right-close-toggle">
                        <i class="feather-arrow-left me-2"></i>
                        <span>رجوع</span>
                    </a>
                </div>
                <div class="d-flex align-items-center gap-2 page-header-right-items-wrapper">
                    <a href="javascript:void(0);" class="btn btn-icon btn-light-brand" data-bs-toggle="collapse" data-bs-target="#filterCollapse">
                        <i class="feather-filter"></i>
                    </a>
                    @can('create_governorates')
                    <a href="{{ route('governorates.create') }}" class="btn btn-primary">
                        <i class="feather-plus me-2"></i>
                        <span>إضافة محافظة</span>
                    </a>
                    @endcan
                </div>
            </div>
            <div class="d-md-none d-flex align-items-center">
                <a href="javascript:void(0)" class="page-header-right-open-toggle">
                    <i class="feather-align-right fs-20"></i>
                </a>
            </div>
        </div>
    </div>
    <!-- [ page-header ] end -->

    <!-- Filter Collapse -->
    <div class="collapse" id="filterCollapse">
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('governorates.index') }}" class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">بحث</label>
                        <input type="text" name="search" class="form-control" placeholder="ابحث في المحافظات..." value="{{ request('search') }}">
                    </div>
                    {{-- 
                    <div class="col-md-4">
                        <label class="form-label">الحالة</label>
                        <select name="status" class="form-control">
                            <option value="">جميع الحالات</option>
                            <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>نشط</option>
                            <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>غير نشط</option>
                        </select>
                    </div>
                    --}}
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">تصفية</button>
                        <a href="{{ route('governorates.index') }}" class="btn btn-light">مسح</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- [ Main Content ] start -->
    <div class="main-content">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">قائمة المحافظات</h5>
                    </div>
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if($governorates->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>الاسم</th>
                                            {{-- 
                                            <th>الوصف</th>
                                            <th>الحالة</th>
                                            <th>عدد المواقع</th>
                                            --}}
                                            <th>تاريخ الإنشاء</th>
                                            <th>الإجراءات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($governorates as $governorate)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-text avatar-sm rounded-circle bg-primary me-3">
                                                        <i class="feather-map-pin"></i>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0">{{ $governorate->name }}</h6>
                                                    </div>
                                                </div>
                                            </td>
                                            {{-- 
                                            <td>
                                                <span class="text-muted">{{ $governorate->description ?: 'لا يوجد وصف' }}</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $governorate->is_active ? 'success' : 'danger' }}">
                                                    {{ $governorate->is_active ? 'نشط' : 'غير نشط' }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-info">{{ $governorate->locations_count ?? 0 }}</span>
                                            </td>
                                            --}}
                                            <td>
                                                <small class="text-muted">{{ $governorate->created_at->format('d M, Y') }}</small>
                                            </td>
                                            
                                            <td>
                                                <div class="d-flex gap-2">
                                                    {{--
                                                    <a href="{{ route('governorates.show', $governorate->id) }}" class="btn btn-sm btn-outline-primary">
                                                        <i class="feather-eye"></i>
                                                    </a>
                                                    --}}
                                                    @can('edit_governorates')
                                                    <a href="{{ route('governorates.edit', $governorate->id) }}" class="btn btn-sm btn-outline-warning">
                                                        <i class="feather-edit"></i>
                                                    </a>
                                                    @endcan
                                                    {{--
                                                    <form action="{{ route('governorates.toggle-status', $governorate->id) }}" method="POST" style="display: inline;">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-outline-{{ $governorate->is_active ? 'danger' : 'success' }}" 
                                                                data-toggle-status="{{ $governorate->is_active ? 'deactivate' : 'activate' }}">
                                                            <i class="feather-{{ $governorate->is_active ? 'pause' : 'play' }}"></i>
                                                        </button>
                                                    </form>
                                                    
                                                    <form action="{{ route('governorates.destroy', $governorate->id) }}" method="POST" style="display: inline;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                                onclick="return confirm('هل أنت متأكد أنك تريد حذف هذه المحافظة؟')">
                                                            <i class="feather-trash-2"></i>
                                                        </button>
                                                    </form>
                                                    --}}
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                           @if($governorates->hasPages())
                                <nav class="d-flex justify-content-center mt-4" dir="rtl">
                                    {{ $governorates->links('pagination::bootstrap-5') }}
                                </nav>
                            @endif
                        @else
                            <div class="text-center py-5">
                                <div class="avatar-text avatar-xl mx-auto mb-3">
                                    <i class="feather-map-pin"></i>
                                </div>
                                <h5>لا توجد محافظات</h5>
                                <p class="text-muted">ابدأ بإضافة أول محافظة.</p>
                                <a href="{{ route('governorates.create') }}" class="btn btn-primary">
                                    <i class="feather-plus me-2"></i>إضافة محافظة
                                </a>
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

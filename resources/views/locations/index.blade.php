@extends('layouts.app')

@section('title', 'المناطق')

@section('content')
<div class="nxl-content">
    <!-- [ page-header ] start -->
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
           
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
                <li class="breadcrumb-item">المناطق</li>
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
                    @can('create_locations')
                    <a href="{{ route('locations.create') }}" class="btn btn-primary">
                        <i class="feather-plus me-2"></i>
                        <span>إضافة منطقة</span>
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
                <form method="GET" action="{{ route('locations.index') }}" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">البحث</label>
                        <input type="text" name="search" class="form-control" placeholder="بحث عن المواقع..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">المحافظة</label>
                        <select name="governorate_id" class="form-control">
                            <option value="">جميع المحافظات</option>
                            @foreach(\App\Models\Governorate::all() as $governorate)
                                <option value="{{ $governorate->id }}" {{ request('governorate_id') == $governorate->id ? 'selected' : '' }}>
                                    {{ $governorate->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">تصفية</button>
                        <a href="{{ route('locations.index') }}" class="btn btn-light">مسح</a>
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
                        <h5 class="card-title mb-0">قائمة المناطق</h5>
                    </div>
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if($locations->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>الاسم</th>
                                            <th>المحافظة</th>
                                            <th>التاريخ</th>
                                            <th>العمليات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($locations as $location)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-text avatar-sm rounded-circle bg-info me-3">
                                                        <i class="feather-map-pin"></i>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0">{{ $location->name }}</h6>
                                                        <small class="text-muted">{{ $location->location }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-primary">{{ $location->governorate->name ?? 'غير متوفر' }}</span>
                                            </td>
                                            <td>
                                                <small class="text-muted">{{ $location->created_at->format('d M, Y') }}</small>
                                            </td>
                                            <td>
                                                <div class="d-flex gap-2">
                                                    {{--
                                                    <a href="{{ route('locations.show', $location->id) }}" class="btn btn-sm btn-outline-primary">
                                                        <i class="feather-eye"></i>
                                                    </a>
                                                    --}}
                                                    @can('edit_locations')
                                                    <a href="{{ route('locations.edit', $location->id) }}" class="btn btn-sm btn-outline-warning">
                                                        <i class="feather-edit"></i>
                                                    </a>
                                                    @endcan
                                                    {{--
                                                    <form action="{{ route('locations.toggle-status', $location->id) }}" method="POST" style="display: inline;">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-outline-{{ $location->is_active ? 'danger' : 'success' }}" 
                                                                data-toggle-status="{{ $location->is_active ? 'deactivate' : 'activate' }}">
                                                            <i class="feather-{{ $location->is_active ? 'pause' : 'play' }}"></i>
                                                        </button>
                                                    </form>
                                                    <form action="{{ route('locations.destroy', $location->id) }}" method="POST" style="display: inline;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                                onclick="return confirm('هل أنت متأكد من أنك تريد حذف هذا الموقع؟')">
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
                            @if($locations->hasPages())
                                <div class="d-flex justify-content-center mt-4">
                                    {{ $locations->links('pagination::bootstrap-5') }}
                                </div>
                            @endif
                        @else
                            <div class="text-center py-5">
                                <div class="avatar-text avatar-xl mx-auto mb-3">
                                    <i class="feather-map-pin"></i>
                                </div>
                                <h5>لم يتم العثور على مواقع</h5>
                                <p class="text-muted">ابدأ بإضافة أول موقع لك.</p>
                                <a href="{{ route('locations.create') }}" class="btn btn-primary">
                                    <i class="feather-plus me-2"></i>إضافة موقع
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

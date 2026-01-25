@extends('layouts.app')

@section('title', 'المصادر')

@section('content')
<div class="nxl-content">
    <!-- [ page-header ] start -->
    <div class="page-header">
        
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
                <li class="breadcrumb-item">المصادر</li>
            </ul>
        </div>
        <div class="page-header-right ms-auto">
            <div class="page-header-right-items">
                <div class="d-flex d-md-none">
                    <a href="javascript:void(0)" class="page-header-right-close-toggle">
                        <i class="feather-arrow-left me-2"></i>
                        <span>العودة</span>
                    </a>
                </div>
                <div class="d-flex align-items-center gap-2 page-header-right-items-wrapper">
                    <a href="javascript:void(0);" class="btn btn-icon btn-light-brand" data-bs-toggle="collapse" data-bs-target="#filterCollapse">
                        <i class="feather-filter"></i>
                    </a>
                    @can('create_sources')
                    <a href="{{ route('sources.create') }}" class="btn btn-primary">
                        <i class="feather-plus me-2"></i>
                        <span>إضافة مصدر</span>
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
                <form method="GET" action="{{ route('sources.index') }}" class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">البحث</label>
                        <input type="text" name="search" class="form-control" placeholder="ابحث عن المصادر..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">تصفية</button>
                        <a href="{{ route('sources.index') }}" class="btn btn-light">مسح</a>
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
                        <h5 class="card-title mb-0">قائمة المصادر</h5>
                    </div>
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if($sources->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>الاسم</th>
                                            <th>تاريخ الإنشاء</th>
                                            <th>الإجراءات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($sources as $source)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-text avatar-sm rounded-circle bg-warning me-3">
                                                        <i class="feather-target"></i>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0">{{ $source->name }}</h6>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <small class="text-muted">{{ $source->created_at->format('d M, Y') }}</small>
                                            </td>
                                            <td>
                                                <div class="d-flex gap-2">
                                                    {{--
                                                    <a href="{{ route('sources.show', $source->id) }}" class="btn btn-sm btn-outline-primary">
                                                        <i class="feather-eye"></i>
                                                    </a>
                                                    --}}
                                                    @can('edit_sources')
                                                    <a href="{{ route('sources.edit', $source->id) }}" class="btn btn-sm btn-outline-warning">
                                                        <i class="feather-edit"></i>
                                                    </a>
                                                    @endcan
                                                    {{--
                                                    <form action="{{ route('sources.toggle-status', $source->id) }}" method="POST" style="display: inline;">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-outline-{{ $source->is_active ? 'danger' : 'success' }}" 
                                                                data-toggle-status="{{ $source->is_active ? 'deactivate' : 'activate' }}">
                                                            <i class="feather-{{ $source->is_active ? 'pause' : 'play' }}"></i>
                                                        </button>
                                                    </form>
                                                    <form action="{{ route('sources.destroy', $source->id) }}" method="POST" style="display: inline;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                                onclick="return confirm('هل أنت متأكد أنك تريد حذف هذا المصدر؟')">
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
                            @if($sources->hasPages())
                                <div class="d-flex justify-content-center mt-4">
                                    {{ $sources->links('pagination::bootstrap-5') }}
                                </div>
                            @endif
                        @else
                            <div class="text-center py-5">
                                <div class="avatar-text avatar-xl mx-auto mb-3">
                                    <i class="feather-target"></i>
                                </div>
                                <h5>لم يتم العثور على مصادر</h5>
                                <p class="text-muted">ابدأ بإضافة مصدرك الأول.</p>
                                <a href="{{ route('sources.create') }}" class="btn btn-primary">
                                    <i class="feather-plus me-2"></i>إضافة مصدر
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

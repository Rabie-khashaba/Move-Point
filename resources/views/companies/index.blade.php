@extends('layouts.app')

@section('title', 'الشركات')

@section('content')
    <!-- [ page-header ] start -->
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
           
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الصفحة الرئيسية</a></li>
                <li class="breadcrumb-item">الشركات</li>
            </ul>
        </div>
        <div class="page-header-right ms-auto">
            <div class="page-header-right-items">
                <div class="d-flex d-md-none">
                    <a href="javascript:void(0)" class="page-header-right-close-toggle">
                        <i class="feather-arrow-left me-2"></i>
                        <span>الرجوع</span>
                    </a>
                </div>
                <div class="d-flex align-items-center gap-2 page-header-right-items-wrapper">
                    <a href="javascript:void(0);" class="btn btn-icon btn-light-brand" data-bs-toggle="collapse" data-bs-target="#filterCollapse">
                        <i class="feather-filter"></i>
                    </a>
                    @can('create_companies')
                    <a href="{{ route('companies.create') }}" class="btn btn-primary">
                        <i class="feather-plus me-2"></i>
                        <span>إضافة شركة</span>
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
                <form method="GET" action="{{ route('companies.index') }}" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">البحث</label>
                        <input type="text" name="search" class="form-control" placeholder="ابحث عن الشركات..." value="{{ request('search') }}">
                    </div>
                    {{--
                    <div class="col-md-3">
                        <label class="form-label">الحالة</label>
                        <select name="status" class="form-control">
                            <option value="">جميع الحالات</option>
                            <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>نشط</option>
                            <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>غير نشط</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">الترتيب حسب</label>
                        <select name="sort" class="form-control">
                            <option value="name" {{ request('sort') === 'name' ? 'selected' : '' }}>الاسم</option>
                            <option value="created_at" {{ request('sort') === 'created_at' ? 'selected' : '' }}>تاريخ الإنشاء</option>
                        </select>
                    </div>
                    --}}
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">تصفية</button>
                        <a href="{{ route('companies.index') }}" class="btn btn-light">إلغاء</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- [ Main Content ] start -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">قائمة الشركات</h5>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if($companies->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>الشعار</th>
                                        <th>الاسم</th>
                                        {{-- 
                                        <th>معلومات الاتصال</th>
                                        <th>الحالة</th>
                                        --}}
                                        <th>تاريخ الإنشاء</th>
                                        <th>الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($companies as $company)
                                    <tr>
                                        <td>
                                            @if($company->logo)
                                                <img src="{{ asset('storage/app/public/' . $company->logo) }}" alt="الشعار" class="rounded-circle" width="40" height="40">
                                            @else
                                                <div class="avatar-text avatar-sm rounded-circle bg-primary">
                                                    <i class="feather-briefcase"></i>
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            <div>
                                                <h6 class="mb-0">{{ $company->name }}</h6>
                                                {{-- 
                                                @if($company->website)
                                                    <small class="text-muted">
                                                        <a href="{{ $company->website }}" target="_blank" class="text-decoration-none">
                                                            <i class="feather-external-link me-1"></i>{{ $company->website }}
                                                        </a>
                                                    </small>
                                                @endif
                                                --}}
                                            </div>
                                        </td>
                                        {{-- 
                                        <td>
                                            <div>
                                                @if($company->phone)
                                                    <div class="mb-1">
                                                        <i class="feather-phone me-2 text-muted"></i>
                                                        <a href="tel:{{ $company->phone }}" class="text-decoration-none">{{ $company->phone }}</a>
                                                    </div>
                                                @endif
                                                @if($company->email)
                                                    <div>
                                                        <i class="feather-mail me-2 text-muted"></i>
                                                        <a href="mailto:{{ $company->email }}" class="text-decoration-none">{{ $company->email }}</a>
                                                    </div>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $company->is_active ? 'success' : 'danger' }}">
                                                {{ $company->is_active ? 'نشط' : 'غير نشط' }}
                                            </span>
                                        </td>
                                        --}}
                                        <td>
                                            <small class="text-muted">{{ $company->created_at->format('d M, Y') }}</small>
                                        </td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                {{--
                                                <a href="{{ route('companies.show', $company->id) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="feather-eye"></i>
                                                </a>
                                                --}}
                                                @can('edit_companies')
                                                <a href="{{ route('companies.edit', $company->id) }}" class="btn btn-sm btn-outline-warning">
                                                    <i class="feather-edit"></i>
                                                </a>
                                                @endcan
                                                {{--
                                                @can('edit_companies')
                                                <form action="{{ route('companies.toggle-status', $company->id) }}" method="POST" style="display: inline;">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-outline-{{ $company->is_active ? 'danger' : 'success' }}" 
                                                            data-toggle-status="{{ $company->is_active ? 'deactivate' : 'activate' }}">
                                                        <i class="feather-{{ $company->is_active ? 'pause' : 'play' }}"></i>
                                                    </button>
                                                </form>
                                                @endcan
                                                @can('delete_companies')
                                                <form action="{{ route('companies.destroy', $company->id) }}" method="POST" style="display: inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                            onclick="return confirm('هل أنت متأكد أنك تريد حذف هذه الشركة؟')">
                                                        <i class="feather-trash-2"></i>
                                                    </button>
                                                </form>
                                                @endcan
                                                --}}
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        @if($companies->hasPages())
                            <div class="d-flex justify-content-center mt-4">
                                {{ $companies->links('pagination::bootstrap-5') }}
                            </div>
                        @endif
                    @else
                        <div class="text-center py-5">
                            <div class="avatar-text avatar-xl mx-auto mb-3">
                                <i class="feather-briefcase"></i>
                            </div>
                            <h5>لم يتم العثور على شركات</h5>
                            <p class="text-muted">ابدأ بإضافة شركتك الأولى.</p>
                            <a href="{{ route('companies.create') }}" class="btn btn-primary">
                                <i class="feather-plus me-2"></i>إضافة شركة
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <!-- [ Main Content ] end -->
@endsection

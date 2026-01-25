@extends('layouts.app')

@section('title', 'المعلنين')

@section('content')
<div class="nxl-content">
    <!-- [ page-header ] start -->
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الصفحة الرئيسية</a></li>
                <li class="breadcrumb-item">المعلنين</li>
            </ul>
        </div>
        <div class="page-header-right ms-auto">
            <div class="page-header-right-items">
                <div class="d-flex align-items-center gap-2 page-header-right-items-wrapper">
                    <a href="javascript:void(0);" class="btn btn-icon btn-light-brand" data-bs-toggle="collapse" data-bs-target="#filterCollapse">
                        <i class="feather-filter"></i>
                    </a>
                    <a href="{{ route('advertisers.create') }}" class="btn btn-primary">
                        <i class="feather-plus me-2"></i>
                        <span>إضافة معلن</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <!-- [ page-header ] end -->

    <!-- Filter Collapse -->
    <div class="collapse" id="filterCollapse">
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('advertisers.index') }}" class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">بحث</label>
                        <input type="text" name="search" class="form-control" placeholder="ابحث باسم المعلن..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">تصفية</button>
                        <a href="{{ route('advertisers.index') }}" class="btn btn-light">مسح</a>
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
                        <h5 class="card-title mb-0">قائمة المعلنين</h5>
                    </div>
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if($advertisers->count() > 0)
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
                                        @foreach($advertisers as $advertiser)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-text avatar-sm rounded-circle bg-primary me-3">
                                                        <i class="feather-user"></i>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0">{{ $advertiser->name }}</h6>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <small class="text-muted">{{ $advertiser->created_at->format('d M, Y') }}</small>
                                            </td>
                                            <td>
                                                <div class="d-flex gap-2">
                                                    <a href="{{ route('advertisers.show', $advertiser->id) }}" class="btn btn-sm btn-outline-primary">
                                                        <i class="feather-eye"></i>
                                                    </a>
                                                    <a href="{{ route('advertisers.edit', $advertiser->id) }}" class="btn btn-sm btn-outline-warning">
                                                        <i class="feather-edit"></i>
                                                    </a>
                                                    <form action="{{ route('advertisers.destroy', $advertiser->id) }}" method="POST" style="display: inline;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('هل أنت متأكد من الحذف؟')">
                                                            <i class="feather-trash-2"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            @if($advertisers->hasPages())
                                <nav class="d-flex justify-content-center mt-4" dir="rtl">
                                    {{ $advertisers->links('pagination::bootstrap-5') }}
                                </nav>
                            @endif
                        @else
                            <div class="text-center py-5">
                                <div class="avatar-text avatar-xl mx-auto mb-3">
                                    <i class="feather-user"></i>
                                </div>
                                <h5>لا يوجد معلنين</h5>
                                <p class="text-muted">ابدأ بإضافة أول معلن.</p>
                                <a href="{{ route('advertisers.create') }}" class="btn btn-primary">
                                    <i class="feather-plus me-2"></i>إضافة معلن
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

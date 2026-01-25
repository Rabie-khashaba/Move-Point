@extends('layouts.app')

@section('title', 'البنوك')

@section('content')
<div class="nxl-content">
    <!-- [ page-header ] start -->
    <div class="page-header">
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
            <li class="breadcrumb-item">البنوك</li>
        </ul>
        <a href="{{ route('banks.create') }}" class="btn btn-primary">
            <i class="feather-plus me-1"></i>إضافة بنك
        </a>
    </div>
    <!-- [ page-header ] end -->


    <!-- Filters Section -->
    <div class="collapse show" id="filterCollapse">
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('banks.index') }}" class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">البحث</label>
                        <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="ابحث عن البنك...">
                    </div>

                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">تصفية</button>
                        <a href="{{ route('banks.index') }}" class="btn btn-light">مسح</a>
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
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">قائمة البنوك</h5>
                        <a href="{{ route('banks.create') }}" class="btn btn-primary btn-sm">
                            <i class="feather-plus me-1"></i>إضافة بنك
                        </a>
                    </div>
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if($banks->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>الاسم</th>
                                            <th>تاريخ الإنشاء</th>
                                            <th>الإجراءات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($banks as $bank)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-text avatar-sm rounded-circle bg-primary me-3">
                                                        <i class="feather-briefcase"></i>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0">{{ $bank->name }}</h6>
                                                    </div>
                                                </div>
                                            </td>

                                            <td>
                                                {{ $bank->created_at->format('Y-m-d') }}
                                            </td>

                                            <td>
                                                <div class="d-flex gap-2">
                                                    <a href="{{ route('banks.edit', $bank->id) }}" class="btn btn-sm btn-outline-warning">
                                                        <i class="feather-edit"></i>
                                                    </a>
                                                    <form action="{{ route('banks.destroy', $bank->id) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا البنك؟');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger">
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

                            <!-- Pagination -->
                            @if($banks->hasPages())
                                <div class="d-flex justify-content-center mt-4">
                                    {{ $banks->links('pagination::bootstrap-5') }}
                                </div>
                            @endif
                        @else
                            <div class="text-center py-5">
                                <div class="avatar-text avatar-xl mx-auto mb-3">
                                    <i class="feather-briefcase"></i>
                                </div>
                                <h5>لم يتم العثور على بنوك</h5>
                                <p class="text-muted">ابدأ بإضافة بنكك الأول.</p>
                                <a href="{{ route('banks.create') }}" class="btn btn-primary">
                                    <i class="feather-plus me-2"></i>إضافة بنك
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


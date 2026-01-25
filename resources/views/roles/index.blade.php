@extends('layouts.app')

@section('title', 'الأدوار')

@section('content')
<div class="nxl-content">
    <!-- [ رأس الصفحة ] start -->
    <div class="page-header">
        
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
                <li class="breadcrumb-item">الأدوار</li>
            </ul>
        </div>
        <div class="page-header-right ms-auto">
            <a href="{{ route('roles.create') }}" class="btn btn-primary">
                <i class="feather-plus me-2"></i>إضافة دور
            </a>
        </div>
    </div>
    <!-- [ رأس الصفحة ] end -->

    <!-- [ المحتوى الرئيسي ] start -->
    <div class="main-content px-3"> {{-- إضافة padding --}}
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">قائمة الأدوار</h5>
                    </div>
                    <div class="card-body">

                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if($roles->count() > 0)
                            <div class="table-responsive"> {{-- إضافة تمرير أفقي --}}
                                <table class="table table-hover text-center">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>اسم الدور</th>
                                            <th>تاريخ الإنشاء</th>
                                            <th>الإجراءات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($roles as $role)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $role->name }}</td>
                                           
                                            <td>{{ $role->created_at->translatedFormat('d M, Y') }}</td>
                                            <td class="d-flex justify-content-center gap-2">
                                                <a href="{{ route('roles.edit', $role->id) }}" class="btn btn-sm btn-outline-warning">
                                                    <i class="feather-edit"></i> تعديل
                                                </a>
                                                <form action="{{ route('roles.destroy', $role->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذا الدور؟');" style="display:inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                                        <i class="feather-trash-2"></i> حذف
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            @if($roles->hasPages())
                                <div class="d-flex justify-content-center mt-4">
                                    {{ $roles->links('pagination::bootstrap-5') }}
                                </div>
                            @endif
                        @else
                            <div class="text-center py-5">
                                <h5>لا توجد أدوار</h5>
                                <p class="text-muted">ابدأ بإضافة أول دور لك.</p>
                                <a href="{{ route('roles.create') }}" class="btn btn-primary">
                                    <i class="feather-plus me-2"></i>إضافة دور
                                </a>
                            </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- [ المحتوى الرئيسي ] end -->
</div>

<style>
    /* التأكد من أن الجدول لا يغطيه الـ sidebar */
    .main-content {
        position: relative;
        z-index: 1;
    }

    .table-responsive {
        overflow-x: auto;
        padding-bottom: 10px;
    }
</style>
@endsection

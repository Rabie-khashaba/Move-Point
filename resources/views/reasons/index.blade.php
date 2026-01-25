@extends('layouts.app')

@section('title', 'الأسباب')

@section('content')
<div class="nxl-content">
    <!-- [ page-header ] start -->
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
                <li class="breadcrumb-item">الأسباب</li>
            </ul>
        </div>
        <div class="page-header-right ms-auto">
            <div class="d-flex align-items-center gap-2">
                @can('create_reasons')
                <a href="{{ route('reasons.create') }}" class="btn btn-primary">
                    <i class="feather-plus me-2"></i>إضافة سبب جديد
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
                        <h5 class="card-title mb-0">قائمة الأسباب</h5>
                    </div>
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if($reasons->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>الاسم</th>
                                            <th>الإجراءات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($reasons as $reason)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-text avatar-sm rounded-circle bg-info me-3">
                                                        <i class="feather-clipboard"></i>
                                                    </div>
                                                    <div>{{ $reason->name }}</div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex gap-2">
                                                    @can('edit_reasons')
                                                    <a href="{{ route('reasons.edit', $reason->id) }}" class="btn btn-sm btn-outline-warning">
                                                        <i class="feather-edit"></i>
                                                    </a>
                                                    @endcan
                                                    {{--
                                                    <form action="{{ route('reasons.destroy', $reason->id) }}" method="POST" style="display:inline-block;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('هل أنت متأكد من الحذف؟')">
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
                            @if($reasons->hasPages())
                                <div class="d-flex justify-content-center mt-4">
                                    {{ $reasons->links('pagination::bootstrap-5') }}
                                </div>
                            @endif
                        @else
                            <div class="text-center py-5">
                                <div class="avatar-text avatar-xl mx-auto mb-3 bg-info">
                                    <i class="feather-clipboard"></i>
                                </div>
                                <h5>لم يتم العثور على أسباب</h5>
                                <p class="text-muted">ابدأ بإضافة السبب الأول.</p>
                                <a href="{{ route('reasons.create') }}" class="btn btn-primary">
                                    <i class="feather-plus me-2"></i>إضافة سبب
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

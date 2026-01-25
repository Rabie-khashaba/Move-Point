@extends('layouts.app')

@section('title', 'الأجهزة')

@section('content')
<div class="nxl-content">
    <!-- [ page-header ] start -->
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
                <li class="breadcrumb-item">الأجهزة</li>
            </ul>
        </div>
        <div class="page-header-right ms-auto">
            <div class="page-header-right-items">
                <a href="{{ route('devices.create') }}" class="btn btn-primary">
                    <i class="feather-plus me-2"></i>
                    <span>إضافة جهاز</span>
                </a>
            </div>
        </div>
    </div>
    <!-- [ page-header ] end -->

    <div class="main-content">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">قائمة الأجهزة</h5>
                    </div>
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if($devices->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead>
                                        <tr>
                                            <th>رقم الهاتف</th>
                                            <th>Device Token</th>
                                            <th>تاريخ الإنشاء</th>
                                            <th>الإجراءات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($devices as $device)
                                        <tr>
                                            <td>{{ $device->phone_number }}</td>
                                            <td><small>{{ $device->device_token }}</small></td>
                                            <td>{{ $device->created_at->format('d M, Y') }}</td>
                                            <td>
                                                <div class="d-flex gap-2">
                                                    <a href="{{ route('devices.show', $device->id) }}" class="btn btn-sm btn-outline-info">
                                                        <i class="feather-eye"></i>
                                                    </a>
                                                    <a href="{{ route('devices.edit', $device->id) }}" class="btn btn-sm btn-outline-warning">
                                                        <i class="feather-edit"></i>
                                                    </a>
                                                    <form action="{{ route('devices.destroy', $device->id) }}" method="POST" style="display:inline-block;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button class="btn btn-sm btn-outline-danger" onclick="return confirm('هل أنت متأكد من الحذف؟')">
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
                        @else
                            <div class="text-center py-5">
                                <div class="avatar-text avatar-xl mx-auto mb-3 bg-secondary text-white">
                                    <i class="feather-smartphone"></i>
                                </div>
                                <h5>لا توجد أجهزة مضافة</h5>
                                <p class="text-muted">ابدأ بإضافة الجهاز الأول.</p>
                                <a href="{{ route('devices.create') }}" class="btn btn-primary">
                                    <i class="feather-plus me-2"></i>إضافة جهاز
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

@extends('layouts.app')

@section('title', 'عرض الجهاز')

@section('content')
<div class="nxl-content">
    <div class="page-header">
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('devices.index') }}">الأجهزة</a></li>
            <li class="breadcrumb-item">عرض</li>
        </ul>
    </div>

    <div class="main-content">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">تفاصيل الجهاز</h5>
            </div>
            <div class="card-body">
                <div class="row">


                    <div class="col-md-6 mb-3">
                        <label class="fw-bold">رقم الهاتف:</label>
                        <span>{{ $device->phone_number }}</span>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="fw-bold">Device Token:</label>
                        <span class="text-muted">{{ $device->device_token }}</span>
                    </div>
                </div>

                <div class="mt-4">
                    <label class="fw-bold">تاريخ الإنشاء:</label>
                    <span>{{ $device->created_at->format('d M, Y H:i') }}</span>
                </div>

                <div class="mt-2">
                    <label class="fw-bold">آخر تحديث:</label>
                    <span>{{ $device->updated_at->format('d M, Y H:i') }}</span>
                </div>

                <div class="mt-4 d-flex gap-2">
                    <a href="{{ route('devices.edit', $device->id) }}" class="btn btn-warning">
                        <i class="feather-edit me-2"></i> تعديل
                    </a>
                    <a href="{{ route('devices.index') }}" class="btn btn-light">رجوع</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

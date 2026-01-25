@extends('layouts.app')

@section('title', 'تعديل الجهاز')

@section('content')
<div class="nxl-content">
    <div class="page-header">
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('devices.index') }}">الأجهزة</a></li>
            <li class="breadcrumb-item">تعديل</li>
        </ul>
    </div>

    <div class="main-content">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">تعديل بيانات الجهاز</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('devices.update', $device->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row">


                        <div class="col-md-6 mb-3">
                            <label class="form-label">رقم الهاتف</label>
                            <input type="text" name="phone_number" class="form-control @error('phone_number') is-invalid @enderror"
                                   value="{{ old('phone_number', $device->phone_number) }}" required>
                            @error('phone_number') <div class="text-danger">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Device Token</label>
                            <input type="text" name="device_token" class="form-control @error('device_token') is-invalid @enderror"
                                   value="{{ old('device_token', $device->device_token) }}" required>
                            @error('device_token') <div class="text-danger">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('devices.index') }}" class="btn btn-light">إلغاء</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="feather-save me-2"></i>
                            تحديث الجهاز
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

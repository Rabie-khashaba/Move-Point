@extends('layouts.app')

@section('title', 'تعديل معلن')

@section('content')
<div class="nxl-content">
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('advertisers.index') }}">المعلنين</a></li>
                <li class="breadcrumb-item">تعديل</li>
            </ul>
        </div>
    </div>

    <div class="main-content">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">تعديل المعلن</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('advertisers.update', $advertiser->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">اسم المعلن</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name', $advertiser->name) }}" required>
                            @error('name')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('advertisers.show', $advertiser->id) }}" class="btn btn-light">إلغاء</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="feather-save me-2"></i>تحديث
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

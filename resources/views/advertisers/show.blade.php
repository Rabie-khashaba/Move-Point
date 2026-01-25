@extends('layouts.app')

@section('title', 'عرض المعلن')

@section('content')
<div class="nxl-content">
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="m-b-10">المعلنين</h5>
            </div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('advertisers.index') }}">المعلنين</a></li>
                <li class="breadcrumb-item">عرض</li>
            </ul>
        </div>
        <div class="page-header-right ms-auto">
            <div class="page-header-right-items">
                <a href="{{ route('advertisers.edit', $advertiser->id) }}" class="btn btn-warning">
                    <i class="feather-edit me-2"></i>
                    <span>تعديل</span>
                </a>
            </div>
        </div>
    </div>

    <div class="main-content">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">تفاصيل المعلن</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="fw-bold">الاسم:</label>
                            <span>{{ $advertiser->name ?: 'غير متوفر' }}</span>
                        </div>

                        <div class="mt-4">
                            <label class="fw-bold">تاريخ الإنشاء:</label>
                            <span>{{ $advertiser->created_at->format('d M, Y H:i') }}</span>
                        </div>

                        <div class="mt-2">
                            <label class="fw-bold">آخر تحديث:</label>
                            <span>{{ $advertiser->updated_at->format('d M, Y H:i') }}</span>
                        </div>

                        <div class="mt-4">
                            <a href="{{ route('advertisers.index') }}" class="btn btn-secondary">
                                <i class="feather-arrow-left me-2"></i>رجوع
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('title', 'عرض سجل المندوب')

@section('content')
<div class="nxl-content">
    <!-- [ page-header ] start -->
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
                <li class="breadcrumb-item"><a href="{{ route('representative-records.index') }}">سجلات المندوبين</a></li>
                <li class="breadcrumb-item">عرض السجل</li>
            </ul>
        </div>
        <div class="page-header-right ms-auto">
            <div class="page-header-right-items">
                <div class="d-flex align-items-center gap-2 page-header-right-items-wrapper">
                    <a href="{{ route('representative-records.index') }}" class="btn btn-secondary">
                        <i class="feather-arrow-right me-2"></i>
                        <span>الرجوع</span>
                    </a>
                    @can('edit_representative_records')
                    <a href="{{ route('representative-records.edit', $record->id) }}" class="btn btn-warning">
                        <i class="feather-edit me-2"></i>
                        <span>تعديل</span>
                    </a>
                    @endcan
                </div>
            </div>
        </div>
    </div>
    <!-- [ page-header ] end -->

    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="card">
                    <div class="card-header">
                        <h5>تفاصيل سجل المندوب</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="info-item">
                                    <label class="form-label text-muted">الموظف:</label>
                                    <p class="h6">{{ $record->employee->name }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-item">
                                    <label class="form-label text-muted">رقم الهاتف:</label>
                                    <p class="h6">{{ $record->employee->phone }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="info-item">
                                    <label class="form-label text-muted">عدد المندوبين:</label>
                                    <p class="h5 text-primary">{{ $record->representative_count }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-item">
                                    <label class="form-label text-muted">المبلغ:</label>
                                    <p class="h5 text-success">{{ number_format($record->amount, 2) }} جنيه</p>
                                </div>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="info-item">
                                    <label class="form-label text-muted">المكافأة المحسوبة:</label>
                                    <p class="h4 text-warning">{{ number_format($record->bonus_amount, 2) }} جنيه</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-item">
                                    <label class="form-label text-muted">معدل المكافأة لكل مندوب:</label>
                                    <p class="h6 text-info">{{ number_format($record->bonus_amount / $record->representative_count, 2) }} جنيه</p>
                                </div>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="info-item">
                                    <label class="form-label text-muted">الشهر:</label>
                                    <p class="h6">{{ $record->month }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-item">
                                    <label class="form-label text-muted">السنة:</label>
                                    <p class="h6">{{ $record->year }}</p>
                                </div>
                            </div>
                        </div>
                        
                        @if($record->notes)
                        <hr>
                        <div class="row">
                            <div class="col-12">
                                <div class="info-item">
                                    <label class="form-label text-muted">الملاحظات:</label>
                                    <p class="h6">{{ $record->notes }}</p>
                                </div>
                            </div>
                        </div>
                        @endif
                        
                        <hr>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="info-item">
                                    <label class="form-label text-muted">تاريخ الإنشاء:</label>
                                    <p class="h6">{{ $record->created_at->format('Y-m-d H:i') }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-item">
                                    <label class="form-label text-muted">آخر تحديث:</label>
                                    <p class="h6">{{ $record->updated_at->format('Y-m-d H:i') }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="alert alert-success mt-3">
                            <i class="feather-check-circle me-2"></i>
                            <strong>تم إضافة المكافأة للراتب تلقائياً!</strong>
                            <br>
                            تم إضافة مبلغ {{ number_format($record->bonus_amount, 2) }} جنيه إلى راتب الموظف {{ $record->employee->name }} للشهر {{ $record->month }}/{{ $record->year }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.info-item {
    margin-bottom: 1rem;
}
</style>
@endsection

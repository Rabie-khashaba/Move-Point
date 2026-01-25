@extends('layouts.app')

@section('title', 'تقرير الأشخاص الذين قاموا بتفعيل طلبات الاستقالة')

@section('content')
<div class="nxl-content">
    <!-- [ page-header ] start -->
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
                <li class="breadcrumb-item"><a href="{{ route('resignation-requests.index') }}">طلبات الاستقالة</a></li>
                <li class="breadcrumb-item">تقرير التفعيل</li>
            </ul>
        </div>
        <!-- <div class="page-header-right ms-auto">
            <div class="page-header-right-items">
                <a href="{{ route('resignation-requests.index') }}" class="btn btn-light">
                    <i class="feather-arrow-right me-2"></i>
                    <span>رجوع</span>
                </a>
            </div>
        </div> -->
    </div>
    <!-- [ page-header ] end -->

    <!-- [ Main Content ] start -->
    <div class="main-content">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">تقرير الأشخاص الذين قاموا بتفعيل طلبات الاستقالة</h5>
                    </div>
                    <div class="card-body">
                        @if($activeByStats->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>اسم الشخص</th>
                                            <th>رقم الموبايل</th>
                                            <th>عدد طلبات الاستقالة</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($activeByStats as $index => $stat)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-text avatar-sm rounded bg-primary me-2">
                                                        <i class="feather-user"></i>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0">{{ $stat['user_name'] }}</h6>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <a href="tel:{{ $stat['user_phone'] }}" class="text-decoration-none">
                                                    <i class="feather-phone me-2"></i>{{ $stat['user_phone'] }}
                                                </a>
                                            </td>
                                            <td>
                                                <span class="badge bg-info fs-14">{{ $stat['count'] }}</span>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    <!-- <tfoot>
                                        <tr class="table-active">
                                            <th colspan="3" class="text-end">الإجمالي:</th>
                                            <th>
                                                <span class="badge bg-primary fs-14">
                                                    {{ $activeByStats->sum('count') }}
                                                </span>
                                            </th>
                                        </tr>
                                    </tfoot> -->
                                </table>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="feather-user-minus fs-48 text-muted mb-3"></i>
                                <h5 class="text-muted">لا توجد بيانات</h5>
                                <p class="text-muted">لم يتم تفعيل أي طلبات استقالة بعد</p>
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


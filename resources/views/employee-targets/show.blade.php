@extends('layouts.app')

@section('title', 'تفاصيل هدف الموظف')

@section('content')
<div class="nxl-content">
    <!-- [ page-header ] start -->
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
                <li class="breadcrumb-item"><a href="{{ route('employee-targets.index') }}">أهداف الموظفين</a></li>
                <li class="breadcrumb-item">تفاصيل الهدف</li>
            </ul>
        </div>
        <div class="page-header-right ms-auto">
            <div class="page-header-right-items">
                <a href="{{ route('employee-targets.index') }}" class="btn btn-light">
                    <i class="feather-arrow-left me-2"></i>
                    <span>العودة</span>
                </a>
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
                        <h5 class="card-title mb-0">تفاصيل هدف الموظف</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6>معلومات الموظف</h6>
                                <table class="table table-borderless">
                                    <tr>
                                        <td><strong>الاسم:</strong></td>
                                        <td>{{ $target->employee->name }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>القسم:</strong></td>
                                        <td>{{ $target->employee->department->name ?? 'غير محدد' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>رقم الهاتف:</strong></td>
                                        <td>{{ $target->employee->phone }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h6>تفاصيل الهدف</h6>
                                <table class="table table-borderless">
                                    <tr>
                                        <td><strong>الشهر:</strong></td>
                                        <td>{{ $target->month }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>السنة:</strong></td>
                                        <td>{{ $target->year }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>الهدف:</strong></td>
                                        <td>{{ $target->target_follow_ups }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>المحقق:</strong></td>
                                        <td>{{ $target->achieved_follow_ups }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>النسبة المئوية:</strong></td>
                                        <td>
                                            @php
                                                $percentage = $target->target_follow_ups > 0 ? round(($target->achieved_follow_ups / $target->target_follow_ups) * 100, 2) : 0;
                                            @endphp
                                            <span class="badge bg-{{ $percentage >= 100 ? 'success' : ($percentage >= 75 ? 'warning' : 'danger') }}">
                                                {{ $percentage }}%
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>الملاحظات:</strong></td>
                                        <td>{{ $target->notes ?? 'لا توجد ملاحظات' }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- [ Main Content ] end -->
</div>
@endsection

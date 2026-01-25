@extends('layouts.app')

@section('content')
<div class="nxl-content">
    <!-- [ page-header ] start -->
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('leads.index') }}">الرئيسية</a></li>
                <li class="breadcrumb-item">استيراد العملاء المحتملين</li>
            </ul>
        </div>
    </div>
    <!-- [ page-header ] end -->

    <!-- [ Main Content ] start -->
    <div class="main-content">
        <div class="row">
            <div class="col-lg-12">
                <div class="card stretch stretch-full">
                    <div class="card-body">
                        <h5 class="fw-bold mb-3">استيراد العملاء المحتملين</h5>

                        @if(session('error'))
                            <div class="alert alert-danger">{{ session('error') }}</div>
                        @endif
                        @if(session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif
                        @if($errors->any())
                            <div class="alert alert-danger">{{ implode(' | ', $errors->all()) }}</div>
                        @endif
                        @if(session('duplicate_phones'))
                            <div class="alert alert-warning">
                                الأرقام دي موجودة قبل كده: {{ implode(' | ', session('duplicate_phones')) }}
                            </div>
                        @endif

                        <form action="{{ route('leads.import') }}" method="POST" enctype="multipart/form-data" class="mb-4">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">ملف الاستيراد</label>
                                <input type="file" name="file" class="form-control" accept=".xlsx,.xls,.csv" required>
                            </div>

                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('leads.index') }}" class="btn btn-light">إلغاء</a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="feather-upload me-2"></i>
                                    استيراد
                                </button>
                            </div>
                        </form>

                        <!-- <div class="mt-4">
                            <h6 class="fw-bold">تنسيق الملف</h6>
                            <p class="text-muted mb-2">
                                استخدم نفس أسماء الأعمدة، واكتب الـ ID للمصدر، المحافظة، المكان، المعلن، والموظف.
                            </p>
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm">
                                    <thead>
                                        <tr>
                                            <th>العمود</th>
                                            <th>ملاحظات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr><td>name</td><td>إجباري</td></tr>
                                        <tr><td>phone</td><td>إجباري (11 رقم)</td></tr>
                                        <tr><td>governorate_id</td><td>إجباري (ID)</td></tr>
                                        <tr><td>source_id</td><td>إجباري (ID)</td></tr>
                                        <tr><td>advertiser_id</td><td>اختياري (ID)</td></tr>
                                        <tr><td>assigned_to</td><td>اختياري (ID)</td></tr>
                                        <tr><td>location_id</td><td>اختياري (ID)</td></tr>
                                        <tr><td>status</td><td>اختياري</td></tr>
                                        <tr><td>notes</td><td>اختياري</td></tr>
                                        <tr><td>transportation</td><td>اختياري</td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div> -->

                        @if(!empty($importFailures))<div class="mt-4">
                                <h6 class="fw-bold text-danger">سجلات لم يتم استيرادها</h6>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-sm">
                                        <thead>
                                            <tr>
                                                <th>رقم الصف</th>
                                                <th>الأخطاء</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($importFailures as $failure)
                                                <tr>
                                                    <td>{{ $failure['row'] }}</td>
                                                    <td>{{ implode(' | ', $failure['errors']) }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

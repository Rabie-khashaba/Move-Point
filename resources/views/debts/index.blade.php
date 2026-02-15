@extends('layouts.app')

@section('title', 'سجل المديونيات')

@section('content')
<div class="nxl-content">
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
                <li class="breadcrumb-item active">سجل المديونيات</li>
            </ul>
        </div>
        <div class="page-header-right ms-auto">
            <div class="d-flex align-items-center flex-nowrap gap-2">
                <a href="{{ route('debts.index2') }}" class="btn btn-primary">
                    <i class="feather-grid me-1"></i>جدول المديونيات الجديد
                </a>
                <a href="javascript:void(0);" class="btn btn-icon btn-light-brand" data-bs-toggle="collapse" data-bs-target="#filterCollapse">
                    <i class="feather-filter"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="collapse show" id="filterCollapse">
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('debts.index') }}" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">بحث بالاسم</label>
                        <input type="text" name="search" class="form-control" placeholder="ابحث عن المندوب أو الموظف..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">الحالة</label>
                        <select name="status" class="form-control">
                            <option value="">جميع الحالات</option>
                            <option value="سدد" {{ request('status') === 'سدد' ? 'selected' : '' }}>سدد</option>
                            <option value="لم يسدد" {{ request('status') === 'لم يسدد' ? 'selected' : '' }}>لم يسدد</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">الشركة</label>
                        <select name="company_id" class="form-control">
                            <option value="">جميع الشركات</option>
                            @foreach($companies as $company)
                                <option value="{{ $company->id }}" {{ request('company_id') == $company->id ? 'selected' : '' }}>
                                    {{ $company->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">من تاريخ</label>
                        <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">إلى تاريخ</label>
                        <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">تصفية</button>
                        <a href="{{ route('debts.index') }}" class="btn btn-light">مسح</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="main-content">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">قائمة المديونيات</h5>
                    </div>
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if($debts->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>الاسم</th>
                                            <th>الشركة</th>
                                            <th>المبلغ</th>
                                            <th>الحالة</th>
                                            <th>تاريخ المديونية</th>
                                            <th>الإجراءات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($debts as $debt)
                                            @php
                                                $person = $debt->representative ?? $debt->employee ?? $debt->supervisor;
                                            @endphp
                                            <tr>
                                                <td>
                                                    <div class="d-flex flex-column">
                                                        <span class="fw-bold">{{ $person->name ?? 'غير محدد' }}</span>
                                                        <span class="text-muted" style="font-size: 0.9em;">
                                                            {{ $person->phone ?? 'لا يوجد رقم' }}
                                                        </span>
                                                    </div>
                                                </td>
                                                <td>{{ $person->company->name ?? 'غير محدد' }}</td>
                                                <td>{{ number_format($debt->loan_amount, 2) }} ج.م</td>
                                                <td>
                                                    @if($debt->status === 'سدد')
                                                        <span class="badge bg-success">سدد</span>
                                                    @else
                                                        <span class="badge bg-danger">لم يسدد</span>
                                                    @endif
                                                </td>
                                                <td>{{ $debt->updated_at?->format('Y-m-d H:i') ?? 'غير متاح' }}</td>
                                                <td>
                                                    <form action="{{ route('debts.toggle-status', $debt) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm {{ $debt->status === 'سدد' ? 'btn-warning' : 'btn-success' }}" onclick="return confirm('هل أنت متأكد من تغيير حالة المديونية؟')">
                                                            {{ $debt->status === 'سدد' ? 'تعيين لم يسدد' : 'تعيين سدد' }}
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="d-flex justify-content-center mt-4">
                                {{ $debts->links('pagination::bootstrap-5') }}
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="feather-alert-circle fs-48 text-muted mb-3"></i>
                                <h5 class="text-muted">لا توجد مديونيات مسجلة</h5>
                                <p class="text-muted">لم يتم تسجيل أي مديونية حتى الآن.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">

    {{--  قسم الفلترة --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <h5 class="mb-3 fw-bold text-primary">
                <i class="bi bi-funnel"></i> تصفية حسب التاريخ
            </h5>
            <form method="GET" class="row gy-2 gx-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label fw-semibold">من تاريخ</label>
                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">إلى تاريخ</label>
                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                </div>
                <div class="col-md-3">
                    <button class="btn btn-primary w-100">
                        <i class="bi bi-search"></i> تصفية النتائج
                    </button>
                </div>
                    <div class="col-md-3">
                        <a href="{{ route('dashboards.moderation') }}" class="btn btn-outline-secondary w-100">
                            <i class="bi bi-x-circle"></i> مسح الفلاتر
                        </a>
                    </div>
            </form>
        </div>
    </div>

    <h5 class="fw-bold mb-3 text-secondary">
        <i class="bi bi-people"></i> المشرفون
    </h5>

    <div class="row g-3">
        @forelse($employees as $employee)
            <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                <div class="card border-0 shadow-lg h-100 position-relative overflow-hidden">
                    <div class="card-body">

                        {{--  الاسم --}}
                        <h5 class="fw-bold text-dark mb-3 text-center">
                            <i class="bi bi-person-badge text-primary me-2"></i>
                            {{ $employee->name }}
                        </h5>

                        {{--  عدد المندوبين --}}
                        <div class="d-flex justify-content-between align-items-center px-2 py-2 border rounded bg-light">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-people-fill text-primary me-2 fs-5"></i>
                                <span class="text-muted fw-semibold">عدد المندوبين</span>
                            </div>
                            <span class="fw-bold text-primary fs-5">{{ $employee->leads_count }}</span>
                        </div>

                    </div>

                    {{--  شريط سفلي جمالي --}}
                    <div class="position-absolute bottom-0 start-0 end-0"
                        style="height: 4px; background: linear-gradient(90deg, #0d6efd, #6610f2);">
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-light text-center border">
                    <i class="bi bi-info-circle"></i> لا توجد بيانات للمشرفين خلال الفترة المحددة.
                </div>
            </div>
        @endforelse
    </div>

</div>
@endsection

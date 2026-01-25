@extends('layouts.app')

<style>
    .fontss{
        font-size : 15px !important;
    }
</style>
@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12 d-flex align-items-center justify-content-between">
            <h4 class="mb-0">لوحة مبيعات</h4>
        </div>
    </div>

    <form class="row g-2 mb-3" method="get">
        <div class="col-12 col-sm-4 col-md-3">
            <label class="form-label small">من تاريخ</label>
            <input type="date" class="form-control" name="date_from" value="{{ $dateFrom ?? '' }}">
        </div>
        <div class="col-12 col-sm-4 col-md-3">
            <label class="form-label small">إلى تاريخ</label>
            <input type="date" class="form-control" name="date_to" value="{{ $dateTo ?? '' }}">
        </div>
        <div class="col-12 col-sm-4 col-md-3 d-flex align-items-end">
            <button type="submit" class="btn btn-primary w-100">تصفية</button>
        </div>
        <div class="col-12 col-sm-4 col-md-3 d-flex align-items-end">
            <a href="{{ route('dashboards.department7') }}" class="btn btn-outline-secondary w-100">اليوم</a>
        </div>
    </form>

    <!-- Summary Card for All Employees -->
    <div class="row g-3 mb-4">
        <div class="col-12">
            <div style="background: #bdbfc1" class="card shadow-sm border-0 text-white">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h4 class="card-title mb-2">ملخص جميع الموظفين</h4>
                            <div class="row">
                                <div class="col-6 col-md-3">
                                    <div class="text-center">
                                        <div class="h3 mb-1">{{ $summaryStats['total_leads'] }}</div>
                                        <small>إجمالي العملاء</small>
                                    </div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <div class="text-center">
                                        <div class="h3 mb-1">{{ $summaryStats['total_follow_ups'] }}</div>
                                        <small>إجمالي عدد المكالمات</small>
                                    </div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <div class="text-center">
                                        <div class="h3 mb-1">{{ $summaryStats['total_today_follow_ups'] }}</div>
                                        <small>المكالمات  الجديده</small>
                                    </div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <div class="text-center">
                                        <div class="h3 mb-1">{{ $summaryStats['employee_count'] }}</div>
                                        <small>عدد الموظفين</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-end">
                                <div class="row g-1">
                                    @foreach($statusList as $status)
                                        @php
                                            $count = $summaryStats['by_status'][$status] ?? 0;
                                            $badge = 'light';
                                            switch ($status) {
                                                case 'جديد': $badge = 'info'; break;
                                                case 'متابعة': $badge = 'primary'; break;
                                                case 'مقابلة': $badge = 'warning'; break;
                                                case 'قديم': $badge = 'dark'; break;
                                                case 'لم يرد': $badge = 'secondary'; break;
                                                case 'غير مهتم': $badge = 'danger'; break;
                                                case 'شفت مسائي': $badge = 'secondary'; break;
                                                case 'بدون وسيلة نقل': $badge = 'secondary'; break;

                                            }
                                        @endphp
                                        <div class="col-6">
                                            <div class="d-flex justify-content-between px-2 py-1 bg-dark bg-opacity-20 rounded">
                                                <span class="small text-white fw-bold">{{ $status }}</span>
                                                <span class="badge bg-{{ $badge }} text-white">{{ $count }}</span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        @if($employeeStats->isEmpty())
            <div class="col-12">
                <div class="alert alert-info mb-0">
                    لا يوجد موظفون في قسم المبيعات (القسم 7) أو لم يتم ربطهم بمستخدمين.
                </div>
            </div>
        @endif
        @foreach($employeeStats as $stat)
            <div class="col-12 col-sm-6 col-lg-4">
                <div class="card shadow-sm h-100 border-0">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div>
                                <h5 class="card-title mb-1">{{ $stat['display_name'] }}</h5>
                                <div class="text-muted d-block fontss">إجمالي العملاء: <strong class="fs-4">{{ $stat['total'] }}</strong></div>
                                <div class="text-muted  fontss"> إجمالي عدد المكالمات: <strong class="fs-4">{{ $stat['followed'] }}</strong></div>
                                <div class="text-muted  fontss"> المكالمات  الجديده: <strong class="fs-4 text-success">{{ $stat['today_follow_ups'] }}</strong></div>
                                <div class="text-muted  fontss">أجمالي المقابلات اليوميه: <strong class="fs-4 text-success">{{ $stat['leadCountfollowups'] }}</strong></div>
                                <div class="text-muted fontss">تارجت : <strong class="fs-4 text-success">

                              {{--  @php
                                    $activeCount = \App\Models\Representative::where('employee_id',$stat['userId']  ?? 0)
                                                                    ->where('status', 1)
                                                                    ->count();
                                @endphp --}}

                                {{ $stat['activeCount'] }}
                                </strong>
                                </div>

                                <div class="text-muted fontss">المندوبين الغير مكتملين : <strong class="fs-4 text-success">

                               {{-- @php
                                    $disactiveCount = \App\Models\Representative::where('employee_id',$stat['userId']  ?? 0)
                                                                    ->where('status', 0)
                                                                    ->count();
                                @endphp --}}

                                {{ $stat['disactiveCount'] }}
                                </strong>
                                </div>


                            </div>
                            <div class="avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width:44px;height:44px;">
                                <span class="fw-bold">{{ $stat['initial'] }}</span>
                            </div>
                        </div>

                        <div class="row g-2">
                            @foreach($statusList as $status)
                                @php
                                    $count = $stat['by_status'][$status] ?? 0;
                                    $badge = 'secondary';
                                    switch ($status) {
                                        case 'جديد': $badge = 'info'; break;
                                        case 'متابعة': $badge = 'primary'; break;
                                        case 'مقابلة': $badge = 'warning'; break;
                                        case 'قديم': $badge = 'dark'; break;
                                        case 'لم يرد': $badge = 'secondary'; break;
                                        case 'غير مهتم': $badge = 'danger'; break;
                                    }
                                @endphp
                                <div class="col-6">
                                    <div class="d-flex justify-content-between px-2 py-1 border rounded">
                                        <span class="text-muted small">{{ $status }}</span>
                                        <span class="badge bg-{{ $badge }}">{{ $count }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection



@extends('layouts.app')

@section('title', 'أهداف المندوبين')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">

            {{-- ============================= --}}
            {{-- جدول أهداف المندوبين --}}
            {{-- ============================= --}}
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">أهداف المندوبين</h3>
                    <div class="card-tools">
                        <a href="{{ route('representative-targets.create') }}" class="btn btn-sm btn-success mr-2">
                            <i class="fas fa-plus"></i> إضافة هدف جديد
                        </a>
                        <form method="POST" action="{{ route('representative-targets.process-bonuses') }}" class="d-inline mr-2">
                            @csrf
                            <input type="hidden" name="year" value="{{ $repYear }}">
                            <input type="hidden" name="month" value="{{ $repMonth }}">
                            <button type="submit" class="btn btn-sm btn-warning"
                                    onclick="return confirm('هل تريد معالجة جميع الأهداف وإضافة المكافآت للموظفين المؤهلين؟')">
                                <i class="fas fa-calculator"></i> معالجة المكافآت
                            </button>
                        </form>

                        <form method="GET"  class="d-inline-flex align-items-center">
                            <input type="hidden" name="filter_type" value="representative">
                            <select name="rep_year" class="form-control form-control-sm mr-2" style="width: 100px;">
                                @for($year = now()->year - 2; $year <= now()->year + 1; $year++)
                                    <option value="{{ $year }}" {{ request('rep_year', $repYear) == $year ? 'selected' : '' }}>
                                        {{ $year }}
                                    </option>
                                @endfor
                            </select>
                            <select name="rep_month" class="form-control form-control-sm mr-2" style="width: 120px;">
                                @for($month = 1; $month <= 12; $month++)
                                    <option value="{{ $month }}" {{ request('rep_month', $repMonth) == $month ? 'selected' : '' }}>
                                        {{ \Carbon\Carbon::create()->month($month)->locale('ar')->monthName }}
                                    </option>
                                @endfor
                            </select>
                            <button type="submit" class="btn btn-sm btn-primary">تحديث</button>
                        </form>


                    </div>
                </div>

                <div class="card-body">

                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                            {{ session('error') }}
                        </div>
                    @endif

                    @if($targets->count() > 0)
                        <form method="POST" action="{{ route('representative-targets.bulk-update') }}">
                            @csrf
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>عدد المندوبين المطلوب</th>
                                            <th>المبلغ الثابت للمكافأة</th>
                                            <!-- <th>المندوبين المحولين فعلياً</th>
                                            <th>حالة الهدف</th>
                                            <th>ملاحظات</th> -->
                                            <th>العمليات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($targets as $target)
                                            <tr>
                                                <td>
                                                    <input type="number" name="targets[{{ $loop->index }}][representatives_count]"
                                                           class="form-control form-control-sm"
                                                           value="{{ $target->representatives_count }}"
                                                           step="1" min="0" placeholder="عدد المندوبين">
                                                </td>
                                                <td>
                                                    <input type="number" name="targets[{{ $loop->index }}][bonus_amount]"
                                                           class="form-control form-control-sm"
                                                           value="{{ $target->bonus_amount }}"
                                                           step="0.01" min="0" placeholder="مبلغ المكافأة">
                                                </td>
                                                <!-- <td>
                                                    <span class="badge bg-info">{{ $target->actual_representatives_count }}</span>
                                                </td>
                                                <td>
                                                    @if($target->isTargetReached())
                                                        <span class="badge bg-success">تم تحقيق الهدف</span>
                                                    @else
                                                        <span class="badge bg-warning">لم يتم تحقيق الهدف</span>
                                                    @endif
                                                </td> -->
                                                <!-- <td>
                                                    <input type="text" name="targets[{{ $loop->index }}][notes]"
                                                           class="form-control form-control-sm"
                                                           value="{{ $target->notes }}"
                                                           placeholder="ملاحظات">
                                                </td> -->
                                                <td>
                                                    <input type="hidden" name="targets[{{ $loop->index }}][id]" value="{{ $target->id }}">
                                                    <a href="#" class="btn btn-sm btn-danger"
                                                            onclick="deleteTarget({{ $target->id }}, 'representative')">
                                                        <i class="fas fa-trash"></i> حذف
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-save"></i> حفظ جميع الأهداف
                                </button>
                            </div>
                        </form>
                    @else
                        <div class="alert alert-info text-center">
                            <h5>لا توجد أهداف لهذا الشهر</h5>
                            <p>اضغط على "إضافة هدف جديد" لإنشاء هدف جديد</p>
                            <a href="{{ route('representative-targets.create') }}" class="btn btn-success">
                                <i class="fas fa-plus"></i> إضافة هدف جديد
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            {{-- ============================= --}}
            {{-- جدول أهداف التسويق Leads --}}
            {{-- ============================= --}}

            <div class="card mt-5">
                <div class="card-header">
                    <h3 class="card-title">أهداف المندوبين</h3>
                    <div class="card-tools">
                        <a href="{{ route('lead-targets.create') }}" class="btn btn-sm btn-success mr-2">
                            <i class="fas fa-plus"></i> إضافة هدف جديد
                        </a>
                        <form method="POST" action="{{ route('lead-targets.process-bonuses') }}" class="d-inline mr-2">
                            @csrf
                            <input type="hidden" name="year" value="{{ $leadYear }}">
                            <input type="hidden" name="month" value="{{ $leadMonth }}">
                            <button type="submit" class="btn btn-sm btn-warning"
                                    onclick="return confirm('هل تريد معالجة جميع الأهداف وإضافة المكافآت للموظفين المؤهلين؟')">
                                <i class="fas fa-calculator"></i> معالجة المكافآت
                            </button>
                        </form>
                        <form method="GET"  class="d-inline-flex align-items-center">
                            <input type="hidden" name="filter_type" value="lead">
                            <select name="lead_year" class="form-control form-control-sm mr-2" style="width: 100px;">
                                @for($year = now()->year - 2; $year <= now()->year + 1; $year++)
                                    <option value="{{ $year }}" {{ request('lead_year', $leadYear) == $year ? 'selected' : '' }}>
                                        {{ $year }}
                                    </option>
                                @endfor
                            </select>
                            <select name="lead_month" class="form-control form-control-sm mr-2" style="width: 120px;">
                                @for($month = 1; $month <= 12; $month++)
                                    <option value="{{ $month }}" {{ request('lead_month', $leadMonth) == $month ? 'selected' : '' }}>
                                        {{ \Carbon\Carbon::create()->month($month)->locale('ar')->monthName }}
                                    </option>
                                @endfor
                            </select>
                            <button type="submit" class="btn btn-sm btn-primary">تحديث</button>
                        </form>

                    </div>
                </div>

                <div class="card-body">



                    @if($leadTargets->count() > 0)
                        <form method="POST" action="{{ route('lead-targets.bulk-update') }}">
                            @csrf
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>عدد المندوبين المطلوب</th>
                                            <th>المبلغ الثابت للمكافأة</th>
                                            <!-- <th>المندوبين المحولين فعلياً</th>
                                            <th>حالة الهدف</th>
                                            <th>ملاحظات</th> -->
                                            <th>العمليات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($leadTargets as $target)
                                            <tr>
                                                <td>
                                                    <input type="number" name="targets[{{ $loop->index }}][leads_count]"
                                                           class="form-control form-control-sm"
                                                           value="{{ $target->leads_count }}"
                                                           step="1" min="0" placeholder="عدد المندوبين">
                                                </td>
                                                <td>
                                                    <input type="number" name="targets[{{ $loop->index }}][bonus_amount]"
                                                           class="form-control form-control-sm"
                                                           value="{{ $target->bonus_amount }}"
                                                           step="0.01" min="0" placeholder="مبلغ المكافأة">
                                                </td>
                                                <!-- <td>
                                                    <span class="badge bg-info">{{ $target->actual_representatives_count }}</span>
                                                </td>
                                                <td>
                                                    @if($target->isTargetReached())
                                                        <span class="badge bg-success">تم تحقيق الهدف</span>
                                                    @else
                                                        <span class="badge bg-warning">لم يتم تحقيق الهدف</span>
                                                    @endif
                                                </td> -->
                                                <!-- <td>
                                                    <input type="text" name="targets[{{ $loop->index }}][notes]"
                                                           class="form-control form-control-sm"
                                                           value="{{ $target->notes }}"
                                                           placeholder="ملاحظات">
                                                </td> -->
                                                <td>
                                                    <input type="hidden" name="targets[{{ $loop->index }}][id]" value="{{ $target->id }}">
                                                    <a href="#" class="btn btn-sm btn-danger"
                                                       onclick="deleteTarget({{ $target->id }} , 'lead')">
                                                        <i class="fas fa-trash"></i> حذف
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-save"></i> حفظ جميع الأهداف
                                </button>
                            </div>
                        </form>
                    @else
                        <div class="alert alert-info text-center">
                            <h5>لا توجد أهداف لهذا الشهر</h5>
                            <p>اضغط على "إضافة هدف جديد" لإنشاء هدف جديد</p>
                            <a href="{{ route('lead-targets.create') }}" class="btn btn-success">
                                <i class="fas fa-plus"></i> إضافة هدف جديد
                            </a>
                        </div>
                    @endif
                </div>
            </div>






        </div>
    </div>
</div>

<!-- Delete Form -->
<form id="deleteForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

@endsection

@section('scripts')
<script>
/* function deleteTarget(targetId) {
    if (confirm('هل أنت متأكد من حذف هذا الهدف؟')) {
        const form = document.getElementById('deleteForm');
        form.action = /representative-targets/${targetId};
        form.submit();
    }
} */

function deleteTarget(id, type) {
    if (confirm('هل أنت متأكد من الحذف؟')) {
        const form = document.getElementById('deleteForm');
        if (type === 'representative') {
            form.action = `/representative-targets/${id}`;
        } else {
            form.action = `/lead-targets/${id}`;
        }
        form.submit();
    }
}
</script>
@endsection

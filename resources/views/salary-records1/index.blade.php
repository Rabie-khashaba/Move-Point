@extends('layouts.app')

@section('title', 'سجلات Last Mile')

@section('content')
<style>
    .compact-table th, .compact-table td {
        padding: 2px 6px !important;
        font-size: 11px !important;
        white-space: nowrap;
    }

    .compact-table th {
        background: #f8f9fa;
        font-weight: bold;
        position: sticky;
        top: 0;
        z-index: 3;
    }

    /* Scroll Box aligned to right */
    .table-scroll {
        width: 47%;
        max-height: 450px;
        overflow-y: auto;
        overflow-x: auto;
        border: 1px solid #ddd;
        margin-right: 0;   /* يجعل الجدول يبدأ من اليمين */
        margin-left: auto; /* المساحة الفاضية على اليسار */
        direction: rtl;
        text-align: right;
    }

    .fixed-width-table {
        width: 1000px;
        min-width: 1000px;
    }


</style>

<div class="nxl-content">
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
                <li class="breadcrumb-item">Salary Records</li>
            </ul>
        </div>
        <div class="page-header-right ms-auto">
            <a href="{{ route('salary-record1.import.form') }}" class="btn btn-success">استيراد Excel</a>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('salary-record1.index') }}" class="row g-2">
                <div class="col-md-2">
                    <input type="text" name="search" class="form-control" placeholder="ابحث عن الاسم أو Star ID..." value="{{ request('search') }}">
                </div>

                <!-- إضافة فلتر الشهر -->
                <div class="col-md-2">
                    <input type="month" name="month" class="form-control" value="{{ request('month') }}">
                </div>


                <div class="col-md-2">
                    <button class="btn btn-primary">بحث</button>
                </div>
            </form>
        </div>
    </div>

    <div class="main-content">
        <div class="card">
            <div class="card-body">

                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                @if($records->count())
                <form id="bulkDeleteForm" action="{{ route('salary-record1.bulk-delete') }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="mb-3">
                        <button type="submit" class="btn btn-danger" onclick="return confirm('هل أنت متأكد من حذف السجلات المحددة؟')" id="bulkDeleteBtn" style="display: none;">
                            <i class="feather-trash-2 me-2"></i>حذف المحدد
                        </button>
                    </div>
                </form>
                <div class="table-scroll">
                    <table class="table table-bordered table-striped table-sm compact-table fixed-width-table">
                        <thead>
                            <tr>
                                <th>
                                    <input type="checkbox" id="selectAll" title="تحديد الكل">
                                </th>
                                <th>#</th>
                                <th>الشهر</th>
                                @foreach(array_keys($records->first()->getAttributes()) as $col)
                                    @if(!in_array($col, ['id', 'created_at', 'updated_at']))
                                        <th>{{ strtoupper(str_replace('_', ' ', $col)) }}</th>
                                    @endif
                                @endforeach
                                <th>إجراءات</th>
                            </tr>
                        </thead>

                        <tbody>
                        @foreach($records as $r)
                            <tr>
                                <td>
                                    <input type="checkbox" name="ids[]" value="{{ $r->id }}" class="record-checkbox">
                                </td>
                                <td>{{ $loop->iteration + ($records->currentPage()-1)*$records->perPage() }}</td>
                                <td>
                                    @if($r->salary_date)
                                        {{ \Carbon\Carbon::parse($r->salary_date)->format('Y-m') }}
                                    @else
                                        -
                                    @endif
                                </td>
                                @foreach($r->getAttributes() as $key => $val)
                                    @if(!in_array($key, ['id', 'created_at', 'updated_at']))
                                        <td>{{ $val }}</td>
                                    @endif
                                @endforeach

                                <td>
                                    <form action="{{ route('salary-record1.destroy', $r) }}" method="POST" onsubmit="return confirm('حذف السجل؟')" style="display: inline;">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-danger">حذف</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                    <div class="text-center py-5">
                        <h5 class="text-muted">لا توجد سجلات</h5>
                    </div>
                @endif

            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.record-checkbox');
    const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
    const bulkDeleteForm = document.getElementById('bulkDeleteForm');

    // تحديد/إلغاء تحديد الكل
    if (selectAll) {
        selectAll.addEventListener('change', function() {
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            toggleBulkDeleteBtn();
        });
    }

    // تحديث زر الحذف عند تغيير أي checkbox
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            // تحديث حالة "تحديد الكل"
            if (selectAll) {
                const allChecked = Array.from(checkboxes).every(cb => cb.checked);
                const someChecked = Array.from(checkboxes).some(cb => cb.checked);
                selectAll.checked = allChecked;
                selectAll.indeterminate = someChecked && !allChecked;
            }
            toggleBulkDeleteBtn();
        });
    });

    function toggleBulkDeleteBtn() {
        const checkedCount = Array.from(checkboxes).filter(cb => cb.checked).length;
        if (checkedCount > 0) {
            bulkDeleteBtn.style.display = 'inline-block';
            bulkDeleteBtn.innerHTML = `<i class="feather-trash-2 me-2"></i>حذف المحدد (${checkedCount})`;
        } else {
            bulkDeleteBtn.style.display = 'none';
        }
    }

    // إرسال النموذج عند الحذف
    if (bulkDeleteForm) {
        bulkDeleteForm.addEventListener('submit', function(e) {
            const checkedIds = Array.from(checkboxes).filter(cb => cb.checked).map(cb => cb.value);
            if (checkedIds.length === 0) {
                e.preventDefault();
                alert('يرجى تحديد سجل واحد على الأقل للحذف');
                return false;
            }
            
            // إضافة IDs المحددة للنموذج
            checkedIds.forEach(id => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'ids[]';
                input.value = id;
                this.appendChild(input);
            });
        });
    }
});
</script>
@endsection

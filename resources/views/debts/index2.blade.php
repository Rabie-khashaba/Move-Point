@extends('layouts.app')

@section('title', 'جدول المديونيات الجديد')

@section('content')
<div class="nxl-content">
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
                <li class="breadcrumb-item"><a href="{{ route('debts.index') }}">المديونيات</a></li>
                <li class="breadcrumb-item active">جدول المديونيات الجديد</li>
            </ul>
        </div>
        <div class="page-header-right ms-auto">
            <a href="{{ route('debts.index') }}" class="btn btn-light">
                <i class="feather-arrow-left me-1"></i>الرجوع للجدول القديم
            </a>
        </div>
    </div>

    <div class="main-content">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">جدول المديونيات الجديد (star_id)</h5>
                        <div class="d-flex flex-nowrap gap-2">
                            <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#importDebtSheetModal">
                                <i class="feather-upload me-1"></i>استيراد Excel
                            </button>
                            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createDebtSheetModal">
                                <i class="feather-plus me-1"></i>إضافة سجل
                            </button>
                        </div>
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

                        @if(session('import_failures') && count(session('import_failures')) > 0)
                            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                                <strong>تم تخطي بعض الصفوف في الاستيراد:</strong>
                                @foreach(session('import_failures') as $failure)
                                    <div>صف {{ $failure['row'] }}: {{ implode('، ', $failure['errors']) }}</div>
                                @endforeach
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <form method="GET" action="{{ route('debts.index2') }}" class="row g-3 mb-3">
                            <div class="col-md-4">
                                <label class="form-label">بحث بـ star_id</label>
                                <input type="text" name="sheet_search" class="form-control" value="{{ request('sheet_search') }}" placeholder="اكتب star_id">
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary me-2">بحث</button>
                                <a href="{{ route('debts.index2') }}" class="btn btn-light">مسح</a>
                            </div>
                        </form>

                        @if($debtSheets->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>star_id</th>
                                            <th>shortage</th>
                                            <th>credit_note</th>
                                            <th>advances</th>
                                            <th>الإجمالي</th>
                                            <th>آخر تحديث</th>
                                            <th>الإجراءات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($debtSheets as $sheet)
                                            <tr>
                                                <td>{{ $sheet->star_id }}</td>
                                                <td>{{ number_format((float) $sheet->shortage, 2) }}</td>
                                                <td>{{ number_format((float) $sheet->credit_note, 2) }}</td>
                                                <td>{{ number_format((float) $sheet->advances, 2) }}</td>
                                                <td class="fw-bold">
                                                    {{ number_format((float) $sheet->shortage + (float) $sheet->credit_note + (float) $sheet->advances, 2) }}
                                                </td>
                                                <td>{{ $sheet->updated_at?->format('Y-m-d H:i') }}</td>
                                                <td>
                                                    <div class="d-flex flex-nowrap gap-1">
                                                    <button type="button"
                                                            class="btn btn-sm btn-warning edit-sheet-btn"
                                                            data-id="{{ $sheet->id }}"
                                                            data-star_id="{{ $sheet->star_id }}"
                                                            data-shortage="{{ $sheet->shortage }}"
                                                            data-credit_note="{{ $sheet->credit_note }}"
                                                            data-advances="{{ $sheet->advances }}"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#editDebtSheetModal">
                                                        تعديل
                                                    </button>
                                                    <form action="{{ route('debts-sheets.destroy', $sheet) }}" method="POST" class="d-inline-flex">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('هل أنت متأكد من الحذف؟')">حذف</button>
                                                    </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="d-flex justify-content-center mt-4">
                                {{ $debtSheets->links('pagination::bootstrap-5') }}
                            </div>
                        @else
                            <div class="text-center py-4">
                                <h6 class="text-muted mb-1">لا توجد سجلات في الجدول الجديد</h6>
                                <small class="text-muted">يمكنك الإضافة يدويًا أو الاستيراد من Excel.</small>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="createDebtSheetModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('debts-sheets.store') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">إضافة سجل مديونية جديد</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">star_id</label>
                        <input type="text" class="form-control" name="star_id" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">shortage</label>
                        <input type="number" step="0.01" class="form-control" name="shortage" value="0">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">credit_note</label>
                        <input type="number" step="0.01" class="form-control" name="credit_note" value="0">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">advances (السلف)</label>
                        <input type="number" step="0.01" class="form-control" name="advances" value="0">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">حفظ</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editDebtSheetModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" id="editDebtSheetForm">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">تعديل سجل المديونية</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">star_id</label>
                        <input type="text" class="form-control" name="star_id" id="edit_star_id" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">shortage</label>
                        <input type="number" step="0.01" class="form-control" name="shortage" id="edit_shortage">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">credit_note</label>
                        <input type="number" step="0.01" class="form-control" name="credit_note" id="edit_credit_note">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">advances (السلف)</label>
                        <input type="number" step="0.01" class="form-control" name="advances" id="edit_advances">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-warning">تحديث</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="importDebtSheetModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('debts-sheets.import') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">استيراد جدول المديونيات الجديد</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted mb-3">الأعمدة المطلوبة: <code>star_id</code>, <code>shortage</code>, <code>credit note</code>, <code>advances</code></p>
                    <input type="file" name="file" class="form-control" accept=".xlsx,.xls,.csv" required>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-success">استيراد</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const editButtons = document.querySelectorAll('.edit-sheet-btn');
    const form = document.getElementById('editDebtSheetForm');
    const starIdInput = document.getElementById('edit_star_id');
    const shortageInput = document.getElementById('edit_shortage');
    const creditNoteInput = document.getElementById('edit_credit_note');
    const advancesInput = document.getElementById('edit_advances');

    editButtons.forEach(function (btn) {
        btn.addEventListener('click', function () {
            const id = btn.getAttribute('data-id');
            form.action = "{{ route('debts-sheets.update', ':id') }}".replace(':id', id);
            starIdInput.value = btn.getAttribute('data-star_id') || '';
            shortageInput.value = btn.getAttribute('data-shortage') || 0;
            creditNoteInput.value = btn.getAttribute('data-credit_note') || 0;
            advancesInput.value = btn.getAttribute('data-advances') || 0;
        });
    });
});
</script>
@endsection

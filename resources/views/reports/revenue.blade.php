@extends('layouts.app')

@section('title', 'تقرير الإيرادات والحركات')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <form class="row g-2" method="get">
                <div class="col-auto">
                    <input type="date" name="from" value="{{ $from }}" class="form-control form-control-sm" placeholder="من">
                </div>
                <div class="col-auto">
                    <input type="date" name="to" value="{{ $to }}" class="form-control form-control-sm" placeholder="إلى">
                </div>
                <div class="col-auto">
                    <button class="btn btn-sm btn-primary">تصفية</button>
                </div>
                <div class="col-auto align-self-center">
                    <span class="badge bg-success">إيداعات: {{ number_format($totalDeposits, 2) }}</span>
                    <span class="badge bg-danger">سحوبات: {{ number_format($totalWithdrawals, 2) }}</span>
                    <span class="badge bg-info">صافي: {{ number_format($totalDeposits - $totalWithdrawals, 2) }}</span>
                </div>
                <div class="col-12 col-md-3 ms-auto">
                    <input type="text" id="revenueGlobalFilter" class="form-control form-control-sm" placeholder="بحث في جميع الأعمدة..." onkeyup="filterRevenueTable()">
                </div>
                <div class="col-12 mt-2">
                    <div class="row g-2">
                        <div class="col-12 col-md-3">
                            <select id="filterSafe" name="safe" class="form-select form-select-sm" onchange="this.form.submit()">
                                <option value="">كل الخزن</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-3">
                            <select id="filterType" name="type" class="form-select form-select-sm" onchange="this.form.submit()">
                                <option value="">الكل (سحب/إيداع)</option>
                                <option value="إيداع">إيداع</option>
                                <option value="سحب">سحب</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-3">
                            <select id="filterExpenseType" name="expense_type_id" class="form-select form-select-sm" onchange="this.form.submit()">
                                <option value="">كل أنواع المصروف</option>
                            </select>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="card-body">
            <table class="table table-bordered" id="revenueTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>الخزنة</th>
                        <th>النوع</th>
                        <th>نوع المصروف</th>
                        <th>المبلغ</th>
                        <th>مرجع</th>
                        <th>بواسطة</th>
                        <th>ملاحظات</th>
                        <th>التاريخ</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($transactions as $t)
                    <tr>
                        <td>{{ $t->id }}</td>
                        <td>{{ $t->safe->name }}</td>
                        <td>
                            <span class="badge {{ $t->type === 'deposit' ? 'bg-success' : 'bg-danger' }}">
                                {{ $t->type === 'deposit' ? 'إيداع' : 'سحب' }}
                            </span>
                        </td>
                        <td>{{ optional(optional($t->expense)->type)->name ?? '—' }}</td>
                        <td>{{ number_format($t->amount, 2) }}</td>
                        <td>
                            @php
                                $refMap = [
                                    'expense' => 'مصروف',
                                    'delivery_deposit' => 'إيداع تسليم',
                                    'leave_request' => 'طلب إجازة',
                                    'advance_request' => 'طلب سلفة',
                                    'resignation_request' => 'طلب استقالة',
                                ];
                                $refType = $t->reference_type;
                                $refLabel = $refType ? ($refMap[$refType] ?? $refType) : null;
                            @endphp
                            {{ $refLabel ? ($refLabel . ' رقم ' . $t->reference_id) : '—' }}
                        </td>
                        <td>{{ optional($t->user)->name ?? '—' }}</td>
                        <td>{{ $t->notes }}</td>
                        <td>{{ $t->created_at->format('Y-m-d H:i') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $transactions->appends(['from' => $from, 'to' => $to])->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function filterRevenueTable() {
    var input = document.getElementById('revenueGlobalFilter');
    var filter = (input.value || '').toLowerCase();
    var table = document.getElementById('revenueTable');
    if (!table) return;
    var trs = table.getElementsByTagName('tr');
    var safeSel = document.getElementById('filterSafe');
    var typeSel = document.getElementById('filterType');
    var expSel  = document.getElementById('filterExpenseType');
    var safeVal = safeSel ? safeSel.value : '';
    var typeVal = typeSel ? typeSel.value : '';
    var expVal  = expSel ? expSel.value : '';
    for (var i = 1; i < trs.length; i++) { // skip header row
        var tds = trs[i].getElementsByTagName('td');
        if (!tds.length) continue;
        var txtAll = '';
        for (var j = 0; j < tds.length; j++) { txtAll += ' ' + (tds[j].innerText || tds[j].textContent || ''); }
        var match = txtAll.toLowerCase().indexOf(filter) > -1;
        // Column indexes after adding expense type: 0 id, 1 safe, 2 type, 3 expense type
        if (safeVal && (tds[1].innerText || '').trim() !== safeVal) match = false;
        if (typeVal && (tds[2].innerText || '').trim() !== typeVal) match = false;
        if (expVal && (tds[3].innerText || '').trim() !== expVal) match = false;
        for (var j = 0; j < tds.length; j++) {
            var txt = (tds[j].innerText || tds[j].textContent || '').toLowerCase();
            if (filter && txt.indexOf(filter) > -1) { match = true; break; }
        }
        trs[i].style.display = match ? '' : 'none';
    }
}

// Build filter options from table content
document.addEventListener('DOMContentLoaded', function() {
    var table = document.getElementById('revenueTable');
    if (!table) return;
    var safes = new Set();
    var types = new Set();
    var exps  = new Set();
    var trs = table.getElementsByTagName('tr');
    for (var i = 1; i < trs.length; i++) {
        var tds = trs[i].getElementsByTagName('td');
        if (!tds.length) continue;
        safes.add((tds[1].innerText || '').trim());
        types.add((tds[2].innerText || '').trim());
        exps.add((tds[3].innerText || '').trim());
    }
    function fillSelect(selId, values) {
        var sel = document.getElementById(selId);
        if (!sel) return;
        var current = sel.value;
        sel.innerHTML = sel.innerHTML; // keep first option
        Array.from(values).filter(v => v && v !== '—').sort().forEach(function(v){
            var opt = document.createElement('option');
            opt.value = v; opt.textContent = v; sel.appendChild(opt);
        });
        if (current) sel.value = current;
    }
    fillSelect('filterSafe', safes);
    fillSelect('filterExpenseType', exps);
});
</script>
@endpush



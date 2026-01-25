@extends('layouts.app')

@section('title', 'المصروفات')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title">سجل المصروفات</h3>
            @can('create_expenses')
            <form class="d-flex" method="post" action="{{ route('expenses.store') }}" enctype="multipart/form-data">
                @csrf

                <select name="expense_scope" class="form-control form-control-sm me-2" required>
                    <option value="">نوع المصروف</option>
                    <option value="عام">مصروف عام</option>
                    <option value="ثابت">مصروف ثابت</option>
                </select>
                <select name="expense_type_id" class="form-control form-control-sm" required>
                    <option value="">نوع المصروف</option>
                    @foreach($types as $t)
                    <option value="{{ $t->id }}">{{ $t->name }}</option>
                    @endforeach
                </select>
                <select name="safe_id" class="form-control form-control-sm ms-2" required>
                    <option value="">الخزنة</option>
                    @foreach($safes as $s)
                    <option value="{{ $s->id }}">{{ $s->name }} ({{ number_format($s->balance,2) }})</option>
                    @endforeach
                </select>
                <input type="number" step="0.01" min="0.01" name="amount" class="form-control form-control-sm ms-2" placeholder="المبلغ" required>
                <input type="text" name="notes" class="form-control form-control-sm ms-2" placeholder="ملاحظات">
                <input type="file" name="attachment" class="form-control form-control-sm ms-2" accept="image/*,application/pdf">
                <button class="btn btn-sm btn-primary ms-2">إضافة</button>
            </form>
            @endcan
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>التصنيف</th>
                        <th>النوع</th>
                        <th>الخزنة</th>
                        <th>المبلغ</th>
                        <th>المرفق</th>
                        <th>ملاحظات</th>
                        <th>التاريخ</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($expenses as $e)
                    <tr>
                        <td>{{ $e->id }}</td>
                        <td>{{ $e->expense_scope }}</td>
                        <td>{{ $e->type->name }}</td>
                        <td>{{ $e->safe->name }}</td>
                        <td>{{ number_format($e->amount, 2) }}</td>
                        <td>
                            @if($e->attachment_path)
                                <a href="{{ Storage::disk('public')->url($e->attachment_path) }}" target="_blank" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center">
                                    <i class="feather-eye me-1"></i>
                                    عرض
                                </a>
                            @else
                                —
                            @endif
                        </td>
                        <td>{{ $e->notes }}</td>
                        <td>{{ $e->created_at->format('Y-m-d H:i') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $expenses->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
@endsection



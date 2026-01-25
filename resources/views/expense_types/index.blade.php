@extends('layouts.app')

@section('title', 'أنواع المصروفات')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title">أنواع المصروفات</h3>
            @can('create_expense_types')
            <form class="d-flex" method="post" action="{{ route('expense-types.store') }}">
                @csrf
                <input type="text" name="name" class="form-control form-control-sm" placeholder="اسم النوع" required>
                <button class="btn btn-sm btn-primary ms-2">إضافة</button>
            </form>
            @endcan
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>الاسم</th>
                        <th>إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($types as $type)
                    <tr>
                        <td>{{ $type->name }}</td>
                        <td>
                            @can('edit_expense_types')
                            <form method="post" action="{{ route('expense-types.update', $type) }}" class="d-inline">
                                @csrf
                                @method('PUT')
                                <input type="text" name="name" value="{{ $type->name }}" class="form-control form-control-sm d-inline w-auto">
                                <button class="btn btn-sm btn-success">حفظ</button>
                            </form>
                            @endcan
                            @can('delete_expense_types')
                            <form method="post" action="{{ route('expense-types.destroy', $type) }}" class="d-inline ms-2" onsubmit="return confirm('حذف؟');">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger">حذف</button>
                            </form>
                            @endcan
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $types->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
@endsection



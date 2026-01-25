@extends('layouts.app')

@section('title', 'المسؤولون')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title">إدارة المسؤولين</h3>
            @can('create_users')
            <form class="d-flex" method="post" action="{{ route('admins.store') }}">
                @csrf
                <input type="text" name="name" class="form-control form-control-sm" placeholder="الاسم">
                <input type="text" name="phone" class="form-control form-control-sm ms-2" placeholder="الهاتف" required>
                <input type="password" name="password" class="form-control form-control-sm ms-2" placeholder="كلمة المرور" required>
                <button class="btn btn-sm btn-primary ms-2">إضافة</button>
            </form>
            @endcan
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>الاسم</th>
                        <th>الهاتف</th>
                        <th>إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($admins as $a)
                    <tr>
                        <td>{{ $a->id }}</td>
                        <td>
                            @can('edit_users')
                            <form method="post" action="{{ route('admins.update', $a) }}" class="d-inline">
                                @csrf
                                @method('PUT')
                                <input type="text" name="name" value="{{ $a->name }}" class="form-control form-control-sm d-inline w-auto">
                                <input type="text" name="phone" value="{{ $a->phone }}" class="form-control form-control-sm d-inline w-auto ms-1">
                                <input type="password" name="password" placeholder="تغيير كلمة المرور" class="form-control form-control-sm d-inline w-auto ms-1">
                                <button class="btn btn-sm btn-success ms-1">حفظ</button>
                            </form>
                            @else
                            {{ $a->name }}
                            @endcan
                        </td>
                        <td>{{ $a->phone }}</td>
                        <td>
                            @can('delete_users')
                            <form method="post" action="{{ route('admins.destroy', $a) }}" onsubmit="return confirm('حذف؟');">
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
            {{ $admins->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
@endsection



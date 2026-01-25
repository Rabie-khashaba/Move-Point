@extends('layouts.app')

@section('title', 'السلايدر')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title">إدارة السلايدر</h3>
            @can('create_sliders')
            <form class="d-flex" method="post" action="{{ route('sliders.store') }}" enctype="multipart/form-data">
                @csrf
                <input type="file" name="image" class="form-control form-control-sm" accept="image/*" required>
                <input type="number" name="sort_order" class="form-control form-control-sm ms-2" placeholder="الترتيب" min="0">
                <button class="btn btn-sm btn-primary ms-2">إضافة</button>
            </form>
            @endcan
        </div>
        <div class="card-body">
            @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
            <table class="table table-bordered align-middle">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>الصورة</th>
                        <th>الترتيب</th>
                        <th>إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sliders as $s)
                    <tr>
                        <td>{{ $s->id }}</td>
                        <td><img src="{{ route('sliders.image', $s) }}" alt="" style="height:60px"></td>
                        <td>
                            @can('edit_sliders')
                            <form method="post" action="{{ route('sliders.update', $s) }}" class="d-inline">
                                @csrf
                                @method('PUT')
                                <input type="number" name="sort_order" value="{{ $s->sort_order }}" class="form-control form-control-sm d-inline w-auto" min="0">
                                <button class="btn btn-sm btn-success">حفظ</button>
                            </form>
                            @else
                            {{ $s->sort_order }}
                            @endcan
                        </td>
                        <td>
                            @can('delete_sliders')
                            <form method="post" action="{{ route('sliders.destroy', $s) }}" onsubmit="return confirm('حذف؟');">
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
            {{ $sliders->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
@endsection



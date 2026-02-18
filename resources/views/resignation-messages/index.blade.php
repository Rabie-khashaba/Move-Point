@extends('layouts.app')

@section('title', 'رسائل الاستقالة')

@section('content')
<div class="nxl-content">
    <div class="page-header">
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
            <li class="breadcrumb-item">رسائل الاستقالة</li>
        </ul>
        @can('create_messages')
            <a href="{{ route('resignation-messages.create') }}" class="btn btn-primary">
                <i class="feather-plus me-1"></i>إضافة رسالة
            </a>
        @endcan
    </div>

    <div class="main-content">
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('resignation-messages.index') }}" class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">البحث</label>
                        <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="ابحث بالعنوان أو المحتوى">
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">تصفية</button>
                        <a href="{{ route('resignation-messages.index') }}" class="btn btn-light">مسح</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">قائمة رسائل الاستقالة</h5>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if($messages->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>العنوان</th>
                                    <th>المحتوى</th>
                                    <th>تاريخ الإنشاء</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($messages as $message)
                                    <tr>
                                        <td>{{ $message->title }}</td>
                                        <td style="max-width: 450px;">
                                            <div class="text-truncate" title="{{ $message->content }}">
                                                {{ $message->content }}
                                            </div>
                                        </td>
                                        <td>{{ $message->created_at?->format('Y-m-d') }}</td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                @can('edit_messages')
                                                    <a href="{{ route('resignation-messages.edit', $message->id) }}" class="btn btn-sm btn-outline-warning">
                                                        <i class="feather-edit"></i>
                                                    </a>
                                                @endcan
                                                @can('delete_messages')
                                                    <form action="{{ route('resignation-messages.destroy', $message->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('هل أنت متأكد من حذف الرسالة؟')">
                                                            <i class="feather-trash-2"></i>
                                                        </button>
                                                    </form>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-center mt-4">
                        {{ $messages->links('pagination::bootstrap-5') }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="feather-message-square fs-48 text-muted mb-3"></i>
                        <h5 class="text-muted">لا توجد رسائل استقالة</h5>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

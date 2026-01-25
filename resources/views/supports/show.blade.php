@extends('layouts.app')

@section('title', 'عرض المشكلة')

@section('content')
<div class="nxl-content">
    <div class="page-header">
        <ul class="breadcrumb">

            <li class="breadcrumb-item"><a href="{{ route('supports.index') }}">الدعم الفني</a></li>
            <li class="breadcrumb-item">عرض</li>
        </ul>
    </div>

    <div class="main-content">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">تفاصيل المشكلة</h5>
            </div>
            <div class="card-body">
                <p><strong>الاسم:</strong> {{ $support->name ?? 'غير محدد' }}</p>
                <p><strong>رقم الهاتف:</strong> {{ $support->phone ?? '-' }}</p>
                <p><strong>المشكلة:</strong></p>
                <div class="bg-light p-3 rounded">{{ $support->issue }}</div>
            </div>
        </div>

        {{-- 🔁 المحادثة --}}
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">المحادثة</h5>
            </div>
            <div class="card-body" style="max-height: 400px; width:600px; overflow-y: auto;">
                @forelse($support->replies as $reply)
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <strong>{{ $reply->user->name ?? $support->name }}</strong>
                            <small class="text-muted">{{ $reply->created_at->diffForHumans() }}</small>
                        </div>
                        <div class="bg-{{ optional($reply->user)->id == auth()->id() ? 'primary text-white' : 'light' }} p-2 rounded mt-1">
                            {{ $reply->message }}
                        </div>
                    </div>
                @empty
                    <p class="text-muted text-center">لا توجد ردود حتى الآن</p>
                @endforelse
            </div>
            <div class="card-footer" style="width:600px;">
                <form method="POST" action="{{ route('supports.replies.store', $support) }}">
                    @csrf
                    <div class="input-group">
                        <input type="text" name="message" class="form-control" placeholder="أكتب ردك هنا..." required>
                        <button class="btn btn-primary" type="submit">
                            <i class="feather-send"></i>
                        </button>
                    </div>
                </form>
            </div>



        </div>
    </div>
</div>
@endsection

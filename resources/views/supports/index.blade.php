@extends('layouts.app')

@section('title', 'الدعم الفني')

@section('content')
<div class="nxl-content">

    <!-- [ page-header ] start -->
    <div class="page-header">
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
            <li class="breadcrumb-item">الدعم الفني</li>
        </ul>
        <a href="{{ route('supports.create') }}" class="btn btn-primary">
            <i class="feather-plus me-1"></i>إضافة بلاغ
        </a>
    </div>
    <!-- [ page-header ] end -->


    <div class="main-content">
        <div class="row">
            <div class="col-lg-12">
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h5 class="card-title mb-0">قائمة البلاغات</h5>
                    </div>
                    <div class="card-body">

                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if($supports->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>الاسم</th>
                                            <th>رقم الهاتف</th>
                                            <th>التاريخ</th>
                                            <th>المشكلة</th>
                                            <th>الحالة</th>
                                            <th>الإجراءات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($supports as $support)
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-text avatar-sm rounded-circle bg-primary me-3">
                                                            <i class="feather-user"></i>
                                                        </div>
                                                        <div>
                                                            <h6 class="mb-0">{{ $support->name }}</h6>
                                                             @if(!$support->is_read)
                                                                <span class="badge bg-danger ms-1">جديد</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>{{ $support->phone }}</td>
                                                <td>{{ $support->date ? date('d M Y', strtotime($support->date)) : '-' }}</td>
                                                <td style="max-width: 350px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                                    {{ $support->issue }}
                                                </td>
                                                <td>
                                                    @if($support->status == 'open')
                                                        <span class="badge bg-warning">قيد المراجعة</span>
                                                    @elseif($support->status == 'replied')
                                                        <span class="badge bg-info">تم الرد</span>
                                                    @else
                                                        <span class="badge bg-success">منتهي</span>
                                                    @endif
                                                </td>
                                                <td>

                                                    <div class="d-flex gap-2">
                                                        @if($support->status == 'open')
                                                            <button type="button" class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#replyModal">
                                                                <i class="feather-mail me-1"></i> رد
                                                            </button>
                                                        @endif
                                                        @if($support->status != 'closed')
                                                            <form action="{{ route('supports.close', $support) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                <button class="btn btn-sm btn-outline-success">
                                                                    <i class="feather-check-circle me-1"></i> إنهاء
                                                                </button>
                                                            </form>
                                                        @endif

                                                        <a href="{{route('supports.show',$support->id)}}"
                                                                class="btn btn-sm btn-outline-primary">

                                                            <i class="feather-eye me-1"></i> عرض
                                                        </a>

                                                        <form action="{{ route('supports.destroy', $support) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('هل أنت متأكد من الحذف؟')">
                                                                <i class="feather-trash-2"></i>
                                                            </button>
                                                        </form>
                                                    </div>


                                                </td>
                                            </tr>

                                            <!-- 🧩 Modal الرد -->
                                            <div class="modal fade" id="replyModal" tabindex="-1" aria-labelledby="replyModalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content border-0 shadow-lg">
                                                        <div class="modal-header bg-info text-white">
                                                            <h5 class="modal-title" id="replyModalLabel">
                                                                <i class="feather-mail me-2"></i> الرد على المشكلة
                                                            </h5>
                                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>

                                                        <div class="modal-body">
                                                            <div class="mb-3">
                                                                <label class="form-label fw-bold">نص المشكلة:</label>
                                                                <div class="p-3 bg-light rounded border">{{ $support->issue }}</div>
                                                            </div>

                                                            <form id="replyForm" action="{{ route('supports.reply', $support) }}" method="POST">
                                                                @csrf
                                                                <div class="mb-3">
                                                                    <label class="form-label fw-bold">الرد:</label>
                                                                    <textarea name="reply_message" class="form-control" rows="4" placeholder="اكتب الرد هنا..." required></textarea>
                                                                </div>
                                                            </form>
                                                        </div>

                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">إلغاء</button>
                                                            <button type="submit" form="replyForm" class="btn btn-info text-white">
                                                                <i class="feather-send me-1"></i> إرسال الرد
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>







                            @if($supports->hasPages())
                                <div class="d-flex justify-content-center mt-4">
                                    {{ $supports->links('pagination::bootstrap-5') }}
                                </div>
                            @endif
                        @else
                            <div class="text-center py-5">
                                <div class="avatar-text avatar-xl mx-auto mb-3">
                                    <i class="feather-alert-circle"></i>
                                </div>
                                <h5>لا توجد بلاغات</h5>
                                <p class="text-muted">ابدأ بإضافة بلاغ جديد الآن.</p>
                                <a href="{{ route('supports.create') }}" class="btn btn-primary">
                                    <i class="feather-plus me-2"></i>إضافة بلاغ
                                </a>
                            </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

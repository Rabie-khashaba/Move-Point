@extends('layouts.app')

@section('title', 'الرسائل')

@section('content')
<div class="nxl-content">
    <!-- [ page-header ] start -->
    <div class="page-header">
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
            <li class="breadcrumb-item">الرسائل</li>
        </ul>
        @can('create_messages')
            <a href="{{ route('messagesTraining.create') }}" class="btn btn-primary">
                <i class="feather-plus me-1"></i>إضافة رسالة
            </a>
            @endcan
    </div>
    <!-- [ page-header ] end -->


    <!-- Filter Collapse -->
    {{-- <div class="collapse" id="filterCollapse">
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('messagesTraining.index') }}" class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">البحث</label>
                        <input type="text" name="search" class="form-control" placeholder="ابحث عن الرسائل..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">تصفية</button>
                        <a href="{{ route('messagesTraining.index') }}" class="btn btn-light">مسح</a>
                    </div>
                </form>
            </div>
        </div>
    </div> --}}


    <!-- Filters Section -->
        <div class="collapse show" id="filterCollapse">
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('messagesTraining.index') }}" class="row g-3">

                        {{-- بحث باسم المحافظة --}}
                        <div class="col-md-3">
                            <label class="form-label">المحافظة</label>
                            <input type="text" name="governorate_name" value="{{ request('governorate_name') }}" class="form-control" placeholder="اكتب اسم المحافظة">
                        </div>

                        {{-- بحث باسم المنطقة --}}
                        <div class="col-md-3">
                            <label class="form-label">الموقع</label>
                            <input type="text" name="location_name" value="{{ request('location_name') }}" class="form-control" placeholder="اكتب اسم المنطقة">
                        </div>

                       

                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary me-2">تصفية</button>
                            <a href="{{ route('messagesTraining.index') }}" class="btn btn-light">مسح</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>



    <!-- [ Main Content ] start -->
    <div class="main-content">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">قائمة الرسائل</h5>
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
                                <table class=" table table-hover" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>النوع</th>
                                            <th>المحافظه</th>
                                            <th>الموقع</th>
                                            <th>اللينك</th>
                                            <th>الشركة</th>
                                            <th>الوصف</th>
                                            <th>الإجراءات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($messages as $message)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-text avatar-sm rounded-circle bg-primary me-3">
                                                        <i class="feather-map-pin"></i>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0">{{ $message->type ?? 'غير محدد' }}</h6>
                                                    </div>
                                                </div>
                                            </td>

                                            {{-- المحافظه --}}
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-text avatar-sm rounded-circle bg-warning me-3">
                                                        <i class="feather-target"></i>
                                                    </div>
                                                    <div>
                                                        @if($message->type === 'أونلاين')
                                                            <h6 class="mb-0">{{ $message->government?->name ?? 'غير محدد' }}</h6>
                                                        @elseif($message->type === 'في المقر')
                                                            <h6 class="mb-0">{{ $message->government?->name ?? 'غير محدد' }}</h6>
                                                        @else
                                                            <h6 class="mb-0">غير محدد</h6>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>

                                            {{-- الموقع --}}
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-text avatar-sm rounded-circle bg-warning me-3">
                                                        <i class="feather-target"></i>
                                                    </div>
                                                    <div>
                                                        @if($message->type === 'أونلاين')
                                                            <h6 class="mb-0">{{ $message->location?->name ?? 'غير محدد' }}</h6>
                                                        @elseif($message->type === 'في المقر')
                                                            <h6 class="mb-0">{{ $message->location?->name ?? 'غير محدد' }}</h6>
                                                        @else
                                                            <h6 class="mb-0">غير محدد</h6>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>

                                            {{-- الوصف أو اللينك حسب النوع --}}
                                            <td>
                                                @if($message->type === 'أونلاين')
                                                    <div style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 500px;">
                                                        <strong>لينك:</strong>
                                                        <a href="{{ $message->link_training }}" target="_blank">{{ $message->link_training }}</a><br>

                                                    </div>
                                                @elseif($message->type === 'في المقر')
                                                    <div style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 500px;">
                                                        <a href="{{ $message->google_map_url }}" target="_blank">Google Maps</a>
                                                    </div>
                                                @endif
                                            </td>
                                            <td>
                                                {{ $message->company->name ?? 'غير محدد' }}
                                            </td>


                                            <td>
                                                @if($message->type === 'أونلاين')
                                                    <div style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 500px;">
                                                        {{ $message->description_training }}
                                                    </div>
                                                @elseif($message->type === 'في المقر')
                                                    <div style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 500px;">
                                                        {{ $message->description_location }} <br>

                                                    </div>
                                                @endif
                                            </td>

                                            <td>
                                                <div class="d-flex gap-2">
                                                    <a href="{{ route('messagesTraining.edit', $message->id) }}" class="btn btn-sm btn-outline-warning">
                                                        <i class="feather-edit"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach

                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            @if($messages->hasPages())
                                <div class="d-flex justify-content-center mt-4">
                                    {{ $messages->links('pagination::bootstrap-5') }}
                                </div>
                            @endif
                        @else
                            <div class="text-center py-5">
                                <div class="avatar-text avatar-xl mx-auto mb-3">
                                    <i class="feather-message-square"></i>
                                </div>
                                <h5>لم يتم العثور على رسائل</h5>
                                <p class="text-muted">ابدأ بإضافة رسالتك الأولى.</p>
                                <a href="{{ route('messages.create') }}" class="btn btn-primary">
                                    <i class="feather-plus me-2"></i>إضافة رسالة
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- [ Main Content ] end -->
</div>
@endsection

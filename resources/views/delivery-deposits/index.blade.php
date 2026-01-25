@extends('layouts.app')

@section('title', 'إيداعات التسليمات')

@section('content')
<div class="nxl-content">
    <!-- [ page-header ] start -->
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
                <li class="breadcrumb-item">إيداعات التسليمات</li>
            </ul>
        </div>
        <div class="page-header-right ms-auto">
            <div class="page-header-right-items">
                <div class="d-flex d-md-none">
                    <a href="javascript:void(0)" class="page-header-right-close-toggle">
                        <i class="feather-arrow-left me-2"></i>
                        <span>الرجوع</span>
                    </a>
                </div>
                <div class="d-flex align-items-center gap-2 page-header-right-items-wrapper">
                    <a href="javascript:void(0);" class="btn btn-icon btn-light-brand" data-bs-toggle="collapse" data-bs-target="#filterCollapse">
                        <i class="feather-filter"></i>
                    </a>
                    <a href="{{ route('delivery-deposits.export') }}" class="btn btn-success">
                        <i class="feather-download me-2"></i>
                        <span>تصدير Excel</span>
                    </a>
                    @can('create_delivery_deposits')
                    <a href="{{ route('delivery-deposits.create') }}" class="btn btn-primary">
                        <i class="feather-plus me-2"></i>
                        <span>إضافة إيداع</span>
                    </a>
                    @endcan
                </div>
            </div>
            <div class="d-md-none d-flex align-items-center">
                <a href="javascript:void(0)" class="page-header-right-open-toggle">
                    <i class="feather-align-right fs-20"></i>
                </a>
            </div>
        </div>
    </div>
    <!-- [ page-header ] end -->

    <!-- Filter Collapse -->
    <div class="collapse" id="filterCollapse">
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('delivery-deposits.index') }}" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">بحث بالاسم/الهاتف/الكود</label>
                        <input type="text" name="search" class="form-control" placeholder="أدخل الاسم أو الهاتف أو الكود" value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">الحالة</label>
                        <select name="status" class="form-control">
                            <option value="">جميع الحالات</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>في الانتظار</option>
                            <option value="delivered" {{ request('status') === 'delivered' ? 'selected' : '' }}>تم التسليم</option>
                            <option value="not_delivered" {{ request('status') === 'not_delivered' ? 'selected' : '' }}>لم يسلم</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">المندوب</label>
                        <select name="representative_id" class="form-control">
                            <option value="">جميع المندوبين</option>
                            @isset($representatives)
                            @foreach($representatives as $rep)
                                <option value="{{ $rep->id }}" {{ request('representative_id') == $rep->id ? 'selected' : '' }}>
                                    {{ $rep->name }}
                                </option>
                            @endforeach
                            @endisset
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">المحافظة</label>
                        <select name="governorate_id" class="form-control">
                            <option value="">جميع المحافظات</option>
                            @foreach($governorates as $governorate)
                                <option value="{{ $governorate->id }}" {{ request('governorate_id') == $governorate->id ? 'selected' : '' }}>
                                    {{ $governorate->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">من تاريخ</label>
                        <input type="date" name="date_from" class="form-control" value="{{ request('date_from', now()->toDateString()) }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">إلى تاريخ</label>
                        <input type="date" name="date_to" class="form-control" value="{{ request('date_to', now()->toDateString()) }}">
                    </div>
                    <div class="col-md-1 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">تصفية</button>
                        <a href="{{ route('delivery-deposits.index') }}" class="btn btn-light">مسح</a>
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
                        <h5 class="card-title mb-0">قائمة إيداعات التسليمات</h5>
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

                        @if($deposits->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>اسم المندوب</th>
                                            <th>اسم الشركة</th>
                                            <th>الكود</th>
                                            <th>رقم التليفون</th>
                                            <th>المحافظة</th>
                                            <th>المبلغ</th>
                                            <th>عدد الطلبات</th>
                                            <th>الحالة</th>
                                            <th>تاريخ الإنشاء</th>
                                            <th>تاريخ التسليم</th>
                                            <th>إيصال الإيداع</th>
                                            <th>الإجراءات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($deposits as $deposit)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar avatar-sm me-3">
                                                        <img src="{{ $deposit->representative->profile_photo_url ?? asset('assets/images/default-avatar.png') }}" alt="Avatar" class="rounded-circle">
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0">{{ $deposit->representative->name }}</h6>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>{{ $deposit->representative->company->name ?? 'غير محدد' }}</td>
                                            <td>{{ $deposit->representative->code ?? 'غير محدد' }}</td>
                                            <td>{{ $deposit->representative->phone }}</td>
                                            <td>{{ $deposit->representative->governorate->name ?? 'غير محدد' }}</td>
                                            <td>{{ number_format($deposit->amount, 2) }} جنيه</td>
                                            <td>{{ $deposit->orders_count ?? '-' }}</td>
                                            <td>
                                                @if($deposit->status === 'pending')
                                                    <span class="badge bg-warning">في الانتظار</span>
                                                @elseif($deposit->status === 'delivered')
                                                    <span class="badge bg-success">تم التسليم</span>
                                                @else
                                                    <span class="badge bg-danger">لم يسلم</span>
                                                @endif
                                            </td>
                                            <td>{{ $deposit->created_at->format('Y-m-d') }}</td>
                                            <td>
                                                @if($deposit->delivered_at)
                                                    {{ $deposit->delivered_at->format('Y-m-d') }}
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                            @if($deposit->receipt_image)
                                                <a href="{{ route('delivery-deposits.receipt', $deposit->id) }}" 
                                                class="btn btn-sm btn-outline-primary">
                                                    <i class="feather-image me-1"></i>عرض الإيصال
                                                </a>
                                            @else
                                                <span class="text-muted">لا يوجد</span>
                                            @endif
                                            </td>
                                            <td>
                                                <div class="dropdown">
                                                    <button class="btn btn-sm btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                        <i class="feather-more-horizontal"></i>
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        @can('view_delivery_deposits')
                                                        <li><a class="dropdown-item" href="{{ route('delivery-deposits.show', $deposit->id) }}">
                                                            <i class="feather-eye me-2"></i>عرض
                                                        </a></li>
                                                        @endcan
                                                        
                                                        @can('edit_delivery_deposits')
                                                        @if($deposit->status !== 'delivered')
                                                        <li>
                                                            <form action="{{ route('delivery-deposits.mark-delivered', $deposit->id) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                <button type="submit" class="dropdown-item text-success" onclick="return confirm('هل أنت متأكد من تحديث الحالة إلى تم التسليم؟')">
                                                                    <i class="feather-check me-2"></i>تم التسليم
                                                                </button>
                                                            </form>
                                                        </li>
                                                        @endif
                                                        
                                                        @if($deposit->status !== 'not_delivered')
                                                        <li>
                                                            <form action="{{ route('delivery-deposits.mark-not-delivered', $deposit->id) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                <button type="submit" class="dropdown-item text-warning" onclick="return confirm('هل أنت متأكد من تحديث الحالة إلى لم يسلم؟')">
                                                                    <i class="feather-x me-2"></i>لم يسلم
                                                                </button>
                                                            </form>
                                                        </li>
                                                        @endif

                                                        <li><a class="dropdown-item" href="{{ route('delivery-deposits.edit', $deposit->id) }}">
                                                            <i class="feather-edit me-2"></i>تعديل
                                                        </a></li>
                                                        @endcan

                                                        @can('delete_delivery_deposits')
                                                        <li>
                                                            <form action="{{ route('delivery-deposits.destroy', $deposit->id) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="dropdown-item text-danger" onclick="return confirm('هل أنت متأكد من حذف هذا الإيداع؟')">
                                                                    <i class="feather-trash-2 me-2"></i>حذف
                                                                </button>
                                                            </form>
                                                        </li>
                                                        @endcan
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <div class="d-flex justify-content-center mt-4">
                                {{ $deposits->appends(request()->query())->links('pagination::bootstrap-5') }}
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="feather-truck fs-48 text-muted mb-3"></i>
                                <h5 class="text-muted">لا توجد إيداعات تسليم</h5>
                                <p class="text-muted">لم يتم إنشاء أي إيداعات تسليم بعد</p>
                                @can('create_delivery_deposits')
                                <a href="{{ route('delivery-deposits.create') }}" class="btn btn-primary">
                                    <i class="feather-plus me-2"></i>إضافة إيداع
                                </a>
                                @endcan
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

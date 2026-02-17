@extends('layouts.app')

@section('title', 'طلبات السلف')

@section('content')
<div class="nxl-content">
    <!-- [ page-header ] start -->
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
                <li class="breadcrumb-item">طلبات السلف</li>
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

                   {{-- <div class="dropdown">
                        <a class="btn btn-icon btn-light-brand" data-bs-toggle="dropdown" data-bs-offset="0, 10" data-bs-auto-close="outside">
                            <i class="feather-paperclip"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <a href="{{ route('advance-requests.export', request()->query()) }}" class="dropdown-item">
                                <i class="bi bi-filetype-csv me-3"></i>
                                <span>CSV</span>
                            </a>

                            <a href="{{ route('advance-requests.export.excel', request()->query()) }}" class="dropdown-item">
                                <i class="bi bi-filetype-xls me-3"></i>
                                <span>Excel</span>
                            </a>
                        </div>
                    </div> --}}

                    <a href="{{ route('advance-requests.export.excel', request()->query()) }}"
                         class="btn btn-success">
                        <i class="feather-download me-2"></i>
                        <span>تصدير Excel</span>
                    </a>

                    @can('create_advance_requests')
                    <a href="{{ route('advance-requests.create') }}" class="btn btn-primary">
                        <i class="feather-plus me-2"></i>
                        <span>إضافة طلب سلف</span>
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
    <div class="collapse show" id="filterCollapse">
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('advance-requests.index') }}" class="row g-3">
                    <div class="col-md-2">
                        <label class="form-label">البحث</label>
                        <input type="text" name="search" class="form-control" placeholder="ابحث عن مقدم الطلب..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">الحالة</label>
                        <select name="status" class="form-control">
                            <option value="">جميع الحالات</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>في الانتظار</option>
                            <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>تمت الموافقة</option>
                            <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>مرفوض</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">من تاريخ</label>
                        <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">إلى تاريخ</label>
                        <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">نوع الموظف</label>
                        <select name="role" class="form-control">
                            <option value="">جميع الأنواع</option>
                            <option value="representative" {{ request('role') === 'representative' ? 'selected' : '' }}>المندوب</option>
                            <option value="employee" {{ request('role') === 'employee' ? 'selected' : '' }}>الموظف</option>
                            <option value="supervisor" {{ request('role') === 'supervisor' ? 'selected' : '' }}>المشرف</option>
                        </select>
                    </div>

                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">تصفية</button>
                        <a href="{{ route('advance-requests.index') }}" class="btn btn-light">مسح</a>
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
                        <h5 class="card-title mb-0">قائمة طلبات السلف</h5>
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

                        @if($advances->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>مقدم الطلب</th>
                                            <th>الكود</th>
                                            <th>رقم المحفظه</th>
                                            <th>المبلغ</th>
                                            <th>التقسيط</th>
                                            <th>المحافظه</th>
                                            <th>الحالة</th>
                                            <th>تاريخ الطلب</th>
                                            <th>الإجراءات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($advances as $advance)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">

                                                    <div>
                                                        <h6 class="mb-0">{{ $advance->requester_name }}</h6>
                                                        <small class="text-muted">
                                                            @if($advance->representative)
                                                                مندوب
                                                            @elseif($advance->employee)
                                                                موظف
                                                            @elseif($advance->supervisor)
                                                                مشرف
                                                            @else
                                                                غير محدد
                                                            @endif
                                                        </small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                {{ $advance->representative?->code ?? $advance->employee?->code ?? $advance->supervisor?->code ?? '-' }}
                                            </td>
                                            <td>{{$advance->representative->bank_account ?? 'غير محدد'}}</td>
                                            <td>{{ number_format($advance->amount, 2) }} ج.م</td>
                                            <td>
                                                @if($advance->is_installment)
                                                    <span class="badge bg-info">{{ $advance->installment_months }} شهر</span>
                                                    <br>
                                                    <small>{{ number_format($advance->monthly_installment, 2) }} ج.م/شهر</small>
                                                @else
                                                    <span class="badge bg-secondary">دفعة واحدة</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($advance->representative?->governorate?->name)
                                                    <strong>{{ $advance->representative->governorate->name }}</strong><br>
                                                    <small class="text-muted">{{ $advance->representative->location->name ?? 'غير محدد' }}</small>
                                                @else
                                                    غير محدد
                                                @endif
                                            </td>
                                            <td>
                                                <span class="text-truncate" style="max-width: 150px;" title="{{ $advance->reason }}">
                                                    {{ Str::limit($advance->reason, 50) }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($advance->status === 'pending')
                                                    <span class="badge bg-warning">في الانتظار</span>
                                                @elseif($advance->status === 'approved')
                                                    <span class="badge bg-success">تمت الموافقة</span>
                                                @else
                                                    <span class="badge bg-danger">مرفوض</span>
                                                @endif
                                            </td>
                                            <td>{{ $advance->created_at->format('Y-m-d') }}</td>
                                            <td>
                                                <div class="dropdown">
                                                    <button class="btn btn-sm btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                        <i class="feather-more-horizontal"></i>
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        @can('view_advance_requests')
                                                        <li><a class="dropdown-item" href="{{ route('advance-requests.show', $advance->id) }}">
                                                            <i class="feather-eye me-2"></i>عرض
                                                        </a></li>
                                                        @endcan

                                                        @if($advance->status === 'pending')
                                                            @can('approve_advance_requests')
                                                           {{-- <li>
                                                                <form action="{{ route('advance-requests.approve', $advance->id) }}" method="POST" class="d-inline">
                                                                    @csrf
                                                                    <button type="submit" class="dropdown-item text-success" onclick="return confirm('هل أنت متأكد من الموافقة على هذا الطلب؟')">
                                                                        <i class="feather-check me-2"></i>موافقة
                                                                    </button>
                                                                </form>
                                                            </li> --}}

                                                            <li>
    <a class="dropdown-item text-success"
       href="#"
       data-bs-toggle="modal"
       data-bs-target="#approveModal{{ $advance->id }}">
        <i class="feather-check me-2"></i>موافقة
    </a>
</li>

                                                            <li>
                                                                <a class="dropdown-item text-danger" href="#" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $advance->id }}">
                                                                    <i class="feather-x me-2"></i>رفض
                                                                </a>
                                                            </li>
                                                            @endcan
                                                        @endif

                                                        @can('edit_advance_requests')
                                                        @if($advance->status === 'pending')
                                                        <li><a class="dropdown-item" href="{{ route('advance-requests.edit', $advance->id) }}">
                                                            <i class="feather-edit me-2"></i>تعديل
                                                        </a></li>
                                                        @endif
                                                        @endcan

                                                        @can('delete_advance_requests')
                                                        <li>
                                                            <form action="{{ route('advance-requests.destroy', $advance->id) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="dropdown-item text-danger" onclick="return confirm('هل أنت متأكد من حذف هذا الطلب؟')">
                                                                    <i class="feather-trash-2 me-2"></i>حذف
                                                                </button>
                                                            </form>
                                                        </li>
                                                        @endcan
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>

                                        <!-- Reject Modal -->
                                        @can('approve_advance_requests')
                                        @if($advance->status === 'pending')
                                        <div class="modal fade" id="rejectModal{{ $advance->id }}" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">رفض طلب السلف</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <form action="{{ route('advance-requests.reject', $advance->id) }}" method="POST">
                                                        @csrf
                                                        <div class="modal-body">
                                                            <div class="mb-3">
                                                                <label class="form-label">سبب الرفض <span class="text-danger">*</span></label>
                                                                <textarea name="rejection_reason" class="form-control" rows="3" required placeholder="أدخل سبب رفض الطلب..."></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">إلغاء</button>
                                                            <button type="submit" class="btn btn-danger">رفض الطلب</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                        @endif
                                        @endcan

                                        <!-- Approve Modal -->
                                        @can('approve_advance_requests')
                                        @if($advance->status === 'pending')
                                        <div class="modal fade" id="approveModal{{ $advance->id }}" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">الموافقة على السلفة</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <form action="{{ route('advance-requests.approve', $advance->id) }}" method="POST">
                                                        @csrf
                                                        <div class="modal-body">
                                                            <div class="mb-3">
                                                                <label class="form-label">مبلغ السلفة</label>
                                                                <input type="number"
                                                                       name="amount"
                                                                       class="form-control"
                                                                       step="0.01"
                                                                       value="{{ $advance->amount }}"
                                                                       required>
                                                                <small class="text-muted">
                                                                    يمكنك تعديل مبلغ السلفة قبل الموافقة
                                                                </small>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">إلغاء</button>
                                                            <button type="submit" class="btn btn-success">
                                                                <i class="feather-check me-1"></i> موافقة
                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                        @endif
                                        @endcan
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <!-- <div class="d-flex justify-content-center mt-4">
                                {{ $advances->appends(request()->query())->links() }}
                            </div> -->

                            <div class="d-flex justify-content-center mt-4">
                                    {{ $advances->links('pagination::bootstrap-5') }}
                                </div>
                        @else
                            <div class="text-center py-5">
                                <i class="feather-dollar-sign fs-48 text-muted mb-3"></i>
                                <h5 class="text-muted">لا توجد طلبات سلف</h5>
                                <p class="text-muted">لم يتم تقديم أي طلبات سلف بعد</p>
                                @can('create_advance_requests')
                                <a href="{{ route('advance-requests.create') }}" class="btn btn-primary">
                                    <i class="feather-plus me-2"></i>إضافة طلب سلف
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

@extends('layouts.app')

@section('title', 'الموظفين')

@section('content')
<div class="nxl-content">
    <!-- [ page-header ] start -->
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">

            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
                <li class="breadcrumb-item">الموظفين</li>
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
                    @can('create_employees')
                    <a href="{{ route('employees.create') }}" class="btn btn-primary">
                        <i class="feather-plus me-2"></i>
                        <span>إضافة موظف</span>
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
                <form method="GET" action="{{ route('employees.index') }}" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">البحث</label>
                        <input type="text" name="search" class="form-control" placeholder="ابحث عن الموظفين..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">القسم</label>
                        <select name="department_id" class="form-control">
                            <option value="">جميع الأقسام</option>
                            @foreach(\App\Models\Department::all() as $department)
                                <option value="{{ $department->id }}" {{ request('department_id') == $department->id ? 'selected' : '' }}>
                                    {{ $department->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">الحالة</label>
                        <select name="status" class="form-control">
                            <option value="">جميع الحالات</option>
                            <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>نشط</option>
                            <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>غير نشط</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">تصفية</button>
                        <a href="{{ route('employees.index') }}" class="btn btn-light">مسح</a>
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
                        <h5 class="card-title mb-0">قائمة الموظفين</h5>
                    </div>
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if($employees->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>الموظف</th>
                                            <th>معلومات الاتصال</th>
                                            <th>القسم</th>
                                             <!-- Added Shift Days Off Column -->

                                            <th>الحالة</th>
                                            <th>تم الإنشاء في</th>
                                            <th>الإجراءات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($employees as $employee)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-text avatar-sm rounded-circle bg-success me-3">
                                                        <i class="feather-user"></i>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0">{{ $employee->name }}</h6>

                                                    </div>
                                                </div>
                                            </td>
                                            <!-- <td>
                                                <div>
                                                    <div class="mb-1">
                                                        <i class="feather-phone me-2 text-muted"></i>
                                                        <a href="tel:{{ $employee->phone }}" class="text-decoration-none">{{ $employee->phone }}</a>
                                                    </div>

                                                     <div class="mb-1">
                                                        <i class="feather-phone me-2 text-muted"></i>
                                                        <a href="tel:{{ $employee->whatsapp_phone }}" class="text-decoration-none">{{ $employee->whatsapp_phone }}</a>
                                                    </div>

                                                </div>
                                            </td> -->

                                            <td>
                                                <div>

                                                    <!-- رقم الموبايل -->
                                                    <div class="mb-1">
                                                        <i class="feather-smartphone me-2 text-primary"></i>
                                                        <span class="fw-bold text-dark">موبايل: </span>
                                                        <a href="tel:{{ $employee->phone }}" class="text-decoration-none">
                                                            {{ $employee->phone }}
                                                        </a>
                                                    </div>

                                                    <!-- رقم الواتس -->
                                                    <div class="mb-1">
                                                        <i class="feather-message-circle me-2 text-success"></i>
                                                        <span class="fw-bold text-dark">واتساب: </span>
                                                        <a href="https://wa.me/{{ $employee->whatsapp_phone }}" class="text-decoration-none" target="_blank">
                                                            {{ $employee->whatsapp_phone }}
                                                        </a>
                                                    </div>

                                                </div>
                                            </td>

                                            <td>
                                                <span class="badge bg-primary">{{ $employee->department->name ?? 'غير متوفر' }}</span>
                                            </td>


                                            <td>
                                                <span class="badge bg-{{ $employee->is_active ? 'success' : 'danger' }}">
                                                    {{ $employee->is_active ? 'نشط' : 'غير نشط' }}
                                                </span>
                                            </td>
                                            <td>
                                                <small class="text-muted">{{ $employee->created_at->format('d M, Y') }}</small>
                                            </td>
                                            <td>
                                                <div class="d-flex gap-2">
                                                    <a href="{{ route('employees.show', $employee->id) }}" class="btn btn-sm btn-outline-primary">
                                                        <i class="feather-eye"></i>
                                                    </a>
                                                    <a href="{{ route('employees.edit', $employee->id) }}" class="btn btn-sm btn-outline-warning">
                                                        <i class="feather-edit"></i>
                                                    </a>
                                                    @can('edit_employees')
                                                    @if($employee->is_active)
                                                        <button type="button" class="btn btn-sm btn-outline-danger" 
                                                                data-bs-toggle="modal" 
                                                                data-bs-target="#deactivateModal{{ $employee->id }}"
                                                                title="إلغاء التفعيل ونقل العملاء">
                                                            <i class="feather-pause"></i>
                                                        </button>
                                                    @else
                                                        <form action="{{ route('employees.toggle-status', $employee->id) }}" method="POST" style="display: inline;">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm btn-outline-info"
                                                                    title="تفعيل">
                                                                <i class="feather-play"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                    @endcan
                                                    {{--
                                                    <form action="{{ route('employees.destroy', $employee->id) }}" method="POST" style="display: inline;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger"
                                                                onclick="return confirm('هل أنت متأكد أنك تريد حذف هذا الموظف؟')">
                                                            <i class="feather-trash-2"></i>
                                                        </button>
                                                    </form>
                                                    --}}
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            @if($employees->hasPages())
                                <div class="d-flex justify-content-center mt-4">
                                    {{ $employees->links('pagination::bootstrap-5') }}
                                </div>
                            @endif
                        @else
                            <div class="text-center py-5">
                                <div class="avatar-text avatar-xl mx-auto mb-3">
                                    <i class="feather-users"></i>
                                </div>
                                <h5>لم يتم العثور على موظفين</h5>
                                <p class="text-muted">ابدأ بإضافة أول موظف لك.</p>
                                <a href="{{ route('employees.create') }}" class="btn btn-primary">
                                    <i class="feather-plus me-2"></i>إضافة موظف
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

<!-- Deactivate and Transfer Modal -->
@foreach($employees as $employee)
@if($employee->is_active)
<div class="modal fade" id="deactivateModal{{ $employee->id }}" tabindex="-1" aria-labelledby="deactivateModalLabel{{ $employee->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deactivateModalLabel{{ $employee->id }}">إلغاء التفعيل ونقل العملاء والمقابلات</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('employees.transfer-leads', $employee->id) }}" method="POST" id="deactivateForm{{ $employee->id }}">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="feather-alert-triangle me-2"></i>
                        سيتم إلغاء تفعيل <strong>{{ $employee->name }}</strong> ونقل جميع العملاء المحتملين والمقابلات المخصصة له إلى الموظف الذي ستختاره.
                    </div>
                    <div class="mb-3">
                        <label for="new_employee_id{{ $employee->id }}" class="form-label">اختر الموظف الجديد <span class="text-danger">*</span></label>
                        <select name="new_employee_id" id="new_employee_id{{ $employee->id }}" class="form-control" required>
                            <option value="">اختر الموظف</option>
                            @foreach($employees as $emp)
                                @if($emp->id != $employee->id && $emp->is_active)
                                    <option value="{{ $emp->id }}">{{ $emp->name }} - {{ $emp->department->name ?? 'غير محدد' }}</option>
                                @endif
                            @endforeach
                        </select>
                        @php
                            $activeEmployeesCount = $employees->where('id', '!=', $employee->id)->where('is_active', true)->count();
                        @endphp
                        @if($activeEmployeesCount == 0)
                            <div class="alert alert-warning mt-2">
                                <i class="feather-alert-circle me-2"></i>
                                لا يوجد موظفين نشطين متاحين للنقل
                            </div>
                        @endif
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="feather-pause me-2"></i>إلغاء التفعيل ونقل
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endforeach

<script>
document.addEventListener('DOMContentLoaded', function() {
    @foreach($employees as $employee)
    @if($employee->is_active)
    const form{{ $employee->id }} = document.getElementById('deactivateForm{{ $employee->id }}');
    if (form{{ $employee->id }}) {
        form{{ $employee->id }}.addEventListener('submit', function(e) {
            const newEmployeeId = document.getElementById('new_employee_id{{ $employee->id }}').value;
            if (!newEmployeeId) {
                e.preventDefault();
                alert('يرجى اختيار الموظف الجديد');
                return false;
            }
            if (!confirm('هل أنت متأكد من إلغاء تفعيل {{ $employee->name }} ونقل جميع العملاء المحتملين والمقابلات إلى الموظف المحدد؟')) {
                e.preventDefault();
                return false;
            }
        });
    }
    @endif
    @endforeach
});
</script>
@endsection

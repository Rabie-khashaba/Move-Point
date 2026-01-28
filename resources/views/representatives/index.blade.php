@extends('layouts.app')

@section('title', 'المندوبين')

@section('content')
<div class="nxl-content">
    <!-- [ page-header ] start -->
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="m-b-10">المندوبين</h5>
            </div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
                <li class="breadcrumb-item">المندوبين</li>
            </ul>
        </div>
        <div class="page-header-right ms-auto">
            <div class="page-header-right-items">
                <div class="d-flex d-md-none">
                    <a href="javascript:void(0)" class="page-header-right-close-toggle">
                        <i class="feather-arrow-left me-2"></i>
                        <span>رجوع</span>
                    </a>
                </div>
                <div class="d-flex align-items-center gap-2 page-header-right-items-wrapper">
                    <a href="javascript:void(0);" class="btn btn-icon btn-light-brand" data-bs-toggle="collapse" data-bs-target="#filterCollapse">
                        <i class="feather-filter"></i>
                    </a>

                    <a href="{{ route('representatives.export', request()->query()) }}" class="btn btn-success">
                        <i class="feather-download me-2"></i> تصدير Excel
                    </a>

                    @can('view_supervisors')
                    <a href="{{ route('supervisor-transfer-logs.index') }}" class="btn btn-info me-2">
                        <i class="feather-activity me-2"></i>
                        <span>سجل النقل</span>
                    </a>
                    @endcan

                    @can('create_representatives')
                    <a href="{{ route('representatives.create') }}" class="btn btn-primary">
                        <i class="feather-plus me-2"></i>
                        <span>إضافة مندوب</span>
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

    <div id="collapseOne" class="accordion-collapse show  collapse page-header-collapse mb-4">
        <div class="accordion-body pb-2">
            <div class="row">
                <div class="col-xxl-4 col-md-6 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="avatar-text avatar-xl rounded">
                                        <i class="feather-users"></i>
                                    </div>
                                    <a href="javascript:void(0);" class="fw-bold d-block">
                                        <span class="d-block">الاجمالي</span>
                                        <span class="fs-24 fw-bolder d-block" id="totalLeads">{{$totalRepresentatives}}</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xxl-4 col-md-6 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="avatar-text avatar-xl rounded bg-primary">
                                        <i class="feather-user-check"></i>
                                    </div>
                                    <a href="javascript:void(0);" class="fw-bold d-block text-blue">
                                        <span class="d-block">عدد في شركه  بوسطه</span>
                                        <span class="fs-24 fw-bolder d-block" id="activeLeads">{{$boostaRepresentatives}}</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xxl-4 col-md-6 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="avatar-text avatar-xl rounded bg-info">
                                        <i class="feather-user-plus"></i>
                                    </div>
                                    <a href="javascript:void(0);" class="fw-bold d-block text-black">
                                        <span class="d-block">العدد في شركه نون</span>
                                        <span class="fs-24 fw-bolder d-block" id="qualifiedLeads">{{$NoonRepresentatives}}</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


            </div>
        </div>
    </div>

    <!-- Filter Collapse -->
    <div class="collapse show" id="filterCollapse">
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('representatives.index') }}" class="row g-3">
                    <div class="col-md-2">
                        <label class="form-label">البحث</label>
                        <input type="text" name="search" class="form-control" placeholder="البحث في المندوبين..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">من تاريخ</label>
                        <input type="date"
                               name="date_from"
                               class="form-control {{ request('date_from') ? 'filter-active' : '' }}"
                               value="{{ request('date_from') }}">
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">إلى تاريخ</label>
                        <input type="date"
                               name="date_to"
                               class="form-control {{ request('date_to') ? 'filter-active' : '' }}"
                               value="{{ request('date_to') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">الشركة</label>
                        <select name="company_id" class="form-control">
                            <option value="">جميع الشركات</option>
                            @foreach(\App\Models\Company::where('is_active', true)->get() as $company)
                                <option value="{{ $company->id }}" {{ request('company_id') == $company->id ? 'selected' : '' }}>
                                    {{ $company->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        @php
                            // جلب الموظفين اللي قسمهم رقم 7
                            $employees = \App\Models\Employee::where('department_id', 7)->where('is_active',1)->get();
                        @endphp
                        <label class="form-label">الموظفين</label>
                        <select name="employee_id" class="form-control">
                            <option value="">اختر موظف</option>
                            @foreach ($employees as $employee)
                                <option value="{{ $employee->user_id }}" {{ request('employee_id') == $employee->id ? 'selected' : '' }}>
                                    {{ $employee->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">استلام الاوراق</label>
                        <select name="document_received" class="form-control">
                            <option value="">جميع الحالات</option>
                            <option value="received" {{ request('document_received') === 'received' ? 'selected' : '' }}>تم الاستلام</option>
                            <option value="pending" {{ request('document_received') === 'pending' ? 'selected' : '' }}> لم  يتم  استلام الاوراق</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">تصفية</button>
                        <a href="{{ route('representatives.index') }}" class="btn btn-light">مسح</a>
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
                        <h5 class="card-title mb-0">قائمة المندوبين</h5>
                    </div>
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if($representatives->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>اسم المندوب</th>
                                            <th>اسم الموظف</th>
                                            <th>الشركة التي يعمل بها</th>
                                            <th>كود المندوب في الشركة</th>
                                            <th>المشرف المسؤول</th>
                                            <th>القائم  بالتحويل</th>
                                            <th>عدد الايصالات</th>
                                            <th>الأوراق الناقصه</th>
                                            <th>استلام الاوراق</th>
                                            <th>الإجراءات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($representatives as $representative)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-text avatar-sm rounded-circle bg-primary me-3">
                                                        <i class="feather-user"></i>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0">{{ $representative->name }}</h6>
                                                        <small class="text-muted">رقم البطاقة: {{ $representative->phone ?? 'غير محدد' }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                {{-- <div class="d-flex align-items-center">
                                                    <i class="feather-phone me-2 text-muted"></i>
                                                    <a href="tel:{{ $representative->phone }}" class="text-decoration-none">{{ $representative->phone }}</a>
                                                </div> --}}
                                                {{ $representative->employee->name ?? 'غير محدد' }}

                                            </td>
                                            <td>
                                                <span class="badge bg-info">{{ $representative->company->name ?? 'غير محدد' }}</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary">{{ $representative->code ?? 'غير محدد' }}</span>
                                            </td>
                                                                                         <td>
                                                 @if($representative->current_supervisor)
                                                     <div>
                                                         <span class="badge bg-info">{{ $representative->current_supervisor->name }}</span>
                                                         <br><small class="text-muted">{{ $representative->current_supervisor->location_name ?? $representative->current_supervisor->location->name ?? 'غير محدد' }}</small>
                                                         @if($representative->current_supervisor->governorate)
                                                             <br><small class="text-muted">{{ $representative->current_supervisor->governorate->name }}</small>
                                                         @endif
                                                     </div>
                                                 @else
                                                     <span class="badge bg-secondary">غير محدد</span>
                                                 @endif
                                             </td>
                                            <td>
                                                @if($representative->convertedActiveBy)
                                                    <div>
                                                        <span class="badge bg-info">{{ $representative->convertedActiveBy->name }}</span>
                                                    </div>
                                                @else
                                                    <span class="badge bg-secondary">غير محدد</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-primary">{{ $representative->delivery_deposits_count }}</span>

                                            </td>
                                            <td>
                                                @if(count($representative->missingDocs()) > 0)
                                                    @foreach($representative->missingDocs() as $doc)
                                                            <span>{{ $doc }}</span><br>
                                                    @endforeach
                                                @else
                                                    <span class="badge bg-success">كل الأوراق مكتملة</span>
                                                @endif
                                            </td>
                                            <td>
                                               @if($representative->documents_received === 'received')
                                                  <span class="text-success fw-bold">
                                                        تم استلام الأوراق
                                                   </span>
                                               @else
                                                  <span class="text-danger fw-bold">
                                                       لم يتم استلام الأوراق
                                                   </span>
                                               @endif
                                            </td>
                                            <td>

                                                <div class="d-flex gap-2">
                                                    @can('view_representatives')
                                                    <a href="{{ route('representatives.show', $representative->id) }}" class="btn btn-sm btn-outline-primary" title="عرض">
                                                        <i class="feather-eye"></i>
                                                    </a>
                                                    @endcan
                                                    @can('edit_representatives')
                                                    <a href="{{ route('representatives.edit', $representative->id) }}" class="btn btn-sm btn-outline-warning" title="تعديل">
                                                        <i class="feather-edit"></i>
                                                    </a>
                                                    @endcan
                                                    @can('edit_supervisors')
                                                                                                         <button type="button" class="btn btn-sm btn-outline-info"
                                                             data-bs-toggle="modal"
                                                             data-bs-target="#transferModal"
                                                             data-representative-id="{{ $representative->id }}"
                                                             data-representative-name="{{ $representative->name }}"
                                                             data-current-supervisor="{{ $representative->current_supervisor ? $representative->current_supervisor->name . ' - ' . ($representative->current_supervisor->location_name ?? $representative->current_supervisor->location->name ?? 'غير محدد') . ($representative->current_supervisor->governorate ? ' (' . $representative->current_supervisor->governorate->name . ')' : '') : 'غير محدد' }}"
                                                             title="تغيير المشرف">
                                                        <i class="feather-users"></i>
                                                    </button>
                                                    @endcan
                                                    @can('edit_representatives')
                                                    <form action="{{ route('representatives.toggle-status', $representative->id) }}" method="POST" style="display: inline;">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-outline-{{ $representative->is_active ? 'danger' : 'success' }}"
                                                                data-toggle-status="{{ $representative->is_active ? 'deactivate' : 'activate' }}"
                                                                title="{{ $representative->is_active ? 'إيقاف' : 'تفعيل' }}">
                                                            <i class="feather-{{ $representative->is_active ? 'pause' : 'play' }}"></i>
                                                        </button>
                                                    </form>
                                                    @endcan

                                                    @can('edit_representatives')
                                                    <form action="{{ route('representatives.mark-not-completed', $representative->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('هل أنت متأكد من تحويل المندوب إلى غير مكتمل؟');">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-outline-secondary" title="تحويل لغير مكتمل">
                                                            <i class="feather-user-x"></i>
                                                        </button>
                                                    </form>
                                                    @endcan

                                                    <form method="POST"
      action="{{ route('representatives.toggleDocumentsStatus', $representative->id) }}"
      class="d-inline">
    @csrf
    @method('PUT')

    <button type="submit"
            class="btn btn-sm {{ $representative->documents_status === 'received' ? 'btn-success' : 'btn-primary' }}"
            title="تغيير حالة استلام الورق">
        @if($representative->documents_status === 'received')
            <i class="feather-check-circle"></i>
        @else
            <i class="feather-clock"></i>
        @endif
    </button>
</form>
                                                    {{--
                                                    <form action="{{ route('representatives.destroy', $representative->id) }}" method="POST" style="display: inline;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger"
                                                                onclick="return confirm('هل أنت متأكد من حذف هذا المندوب؟')"
                                                                title="حذف">
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
                            @if($representatives->hasPages())
                                <div class="d-flex justify-content-center mt-4">
                                    {{ $representatives->links('pagination::bootstrap-5') }}
                                </div>
                            @endif
                        @else
                            <div class="text-center py-5">
                                <div class="avatar-text avatar-xl mx-auto mb-3">
                                    <i class="feather-user-plus"></i>
                                </div>
                                <h5>لا توجد مندوبين</h5>
                                <p class="text-muted">ابدأ بإضافة أول مندوب.</p>
                                <a href="{{ route('representatives.create') }}" class="btn btn-primary">
                                    <i class="feather-plus me-2"></i>إضافة مندوب
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

<!-- Transfer Representative Modal -->
<div class="modal fade" id="transferModal" tabindex="-1" aria-labelledby="transferModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="transferModalLabel">تغيير مشرف المندوب</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="transferForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">المندوب</label>
                        <input type="text" class="form-control" id="representativeName" readonly>
                        <input type="hidden" id="representativeId" name="representative_id">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">المشرف الحالي</label>
                        <input type="text" class="form-control" id="currentSupervisor" readonly>
                    </div>

                                         <div class="mb-3">
                         <label class="form-label">المحافظة</label>
                         <select id="filterGovernorate" class="form-control">
                             <option value="">جميع المحافظات</option>
                             @foreach(\App\Models\Governorate::all() as $governorate)
                                 <option value="{{ $governorate->id }}">{{ $governorate->name }}</option>
                             @endforeach
                         </select>
                     </div>

                                           <div class="mb-3">
                          <label class="form-label">المنطقة</label>
                          <select id="filterLocation" class="form-control">
                              <option value="">جميع المناطق</option>
                          </select>
                      </div>

                     <div class="mb-3">
                         <label class="form-label">المشرف الجديد <span class="text-danger">*</span></label>
                         <select name="new_supervisor_id" id="newSupervisorId" class="form-control" required>
                             <option value="">اختر المشرف الجديد</option>
                             @foreach(\App\Models\Supervisor::where('is_active', true)->get() as $supervisor)
                                 <option value="{{ $supervisor->id }}" data-governorate="{{ $supervisor->governorate_id }}" data-location="{{ $supervisor->location_id }}">
                                     {{ $supervisor->name }} - {{ $supervisor->location_name }}{{ $supervisor->governorate ? ' (' . $supervisor->governorate->name . ')' : '' }}
                                 </option>
                             @endforeach
                         </select>
                     </div>

                    <div class="mb-3">
                        <label class="form-label">سبب النقل</label>
                        <textarea name="reason" id="transferReason" class="form-control" rows="3" placeholder="أدخل سبب النقل (اختياري)"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="feather-save me-2"></i>
                        نقل المندوب
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
// Set up CSRF token for all AJAX requests
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

$(document).ready(function() {
    // Handle modal data
    $('#transferModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var representativeId = button.data('representative-id');
        var representativeName = button.data('representative-name');
        var currentSupervisor = button.data('current-supervisor');

        $('#representativeId').val(representativeId);
        $('#representativeName').val(representativeName);
        $('#currentSupervisor').val(currentSupervisor);
        $('#newSupervisorId').val('');
        $('#transferReason').val('');
        $('#filterGovernorate').val('');
        $('#filterLocation').val('');

        // Show all supervisor options initially
        $('#newSupervisorId option').show();
    });

    // Handle governorate filter
    $('#filterGovernorate').on('change', function() {
        var governorateId = $(this).val();
        loadFilterLocations(governorateId);
        filterSupervisors();
    });

    // Handle location filter
    $('#filterLocation').on('change', function() {
        filterSupervisors();
    });

    function loadFilterLocations(governorateId) {
        if (!governorateId) {
            $('#filterLocation').empty().append('<option value="">جميع المقار</option>');
            return;
        }

        $.ajax({
            url: '/getlocations/' + governorateId,
            type: 'GET',
            success: function(response) {
                $('#filterLocation').empty().append('<option value="">جميع المقار</option>');

                response.forEach(function(location) {
                    var option = new Option(location.name, location.id, false, false);
                    $('#filterLocation').append(option);
                });
            },
            error: function() {
                $('#filterLocation').empty().append('<option value="">خطأ في تحميل المقار</option>');
            }
        });
    }

    function filterSupervisors() {
        var governorateId = $('#filterGovernorate').val();
        var locationId = $('#filterLocation').val();

        if (locationId) {
            // Load supervisors by location via AJAX
            $.ajax({
                url: '{{ route("supervisors.by-location", ":locationId") }}'.replace(':locationId', locationId),
                type: 'GET',
                success: function(response) {
                    $('#newSupervisorId').empty().append('<option value="">اختر المشرف الجديد</option>');

                    response.forEach(function(supervisor) {
                        var governorateInfo = supervisor.governorate ? ' (' + supervisor.governorate.name + ')' : '';
                        var option = new Option(
                            supervisor.name + ' - ' + (supervisor.location_name || 'غير محدد') + governorateInfo,
                            supervisor.id,
                            false,
                            false
                        );
                        $('#newSupervisorId').append(option);
                    });
                },
                error: function() {
                    $('#newSupervisorId').empty().append('<option value="">خطأ في تحميل المشرفين</option>');
                }
            });
        } else if (governorateId) {
            // Load supervisors by governorate via AJAX
            $.ajax({
                url: '{{ route("supervisors.by-governorate", ":governorateId") }}'.replace(':governorateId', governorateId),
                type: 'GET',
                success: function(response) {
                    $('#newSupervisorId').empty().append('<option value="">اختر المشرف الجديد</option>');

                    response.forEach(function(supervisor) {
                        var governorateInfo = supervisor.governorate ? ' (' + supervisor.governorate.name + ')' : '';
                        var option = new Option(
                            supervisor.name + ' - ' + (supervisor.location_name || 'غير محدد') + governorateInfo,
                            supervisor.id,
                            false,
                            false
                        );
                        $('#newSupervisorId').append(option);
                    });
                },
                error: function() {
                    $('#newSupervisorId').empty().append('<option value="">خطأ في تحميل المشرفين</option>');
                }
            });
        } else {
            // Show all supervisors if no filters are selected
            $('#newSupervisorId option').show();
        }

        // Reset selection if current selection is hidden
        var selectedOption = $('#newSupervisorId option:selected');
        if (selectedOption.length && selectedOption.is(':hidden')) {
            $('#newSupervisorId').val('');
        }
    }

    // Handle form submission
    $('#transferForm').on('submit', function(e) {
        e.preventDefault();

        var formData = $(this).serialize();
        var submitBtn = $(this).find('button[type="submit"]');
        var originalText = submitBtn.html();

        // Disable button and show loading
        submitBtn.prop('disabled', true).html('<i class="feather-loader me-2"></i>جاري النقل...');

        $.ajax({
            url: '{{ route("supervisors.transfer-representative") }}',
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    // Show success message
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'success',
                            title: 'تم النقل بنجاح!',
                            text: response.message,
                            confirmButtonText: 'حسناً'
                        }).then(() => {
                            // Reload page to show updated data
                            location.reload();
                        });
                    } else {
                        alert('تم النقل بنجاح! ' + response.message);
                        location.reload();
                    }
                } else {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'error',
                            title: 'خطأ!',
                            text: response.message || 'حدث خطأ أثناء نقل المندوب',
                            confirmButtonText: 'حسناً'
                        });
                    } else {
                        alert('خطأ! ' + (response.message || 'حدث خطأ أثناء نقل المندوب'));
                    }
                }
            },
            error: function(xhr) {
                var errorMessage = 'حدث خطأ أثناء نقل المندوب';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }

                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'error',
                        title: 'خطأ!',
                        text: errorMessage,
                        confirmButtonText: 'حسناً'
                    });
                } else {
                    alert('خطأ! ' + errorMessage);
                }
            },
            complete: function() {
                // Re-enable button
                submitBtn.prop('disabled', false).html(originalText);
            }
        });
    });
});
</script>
@endpush

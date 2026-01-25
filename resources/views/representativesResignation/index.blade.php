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
                        <a href="javascript:void(0);" class="btn btn-icon btn-light-brand" data-bs-toggle="collapse"
                            data-bs-target="#filterCollapse">
                            <i class="feather-filter"></i>
                        </a>

                        <a href="{{ route('resignation-representatives.export') }}" class="btn btn-success">
                            <i class="feather-download me-2"></i>تصدير Excel
                        </a>

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
                                            <span class="fs-24 fw-bolder d-block"
                                                id="totalLeads">{{$totalRepresentatives}}</span>
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
                                            <span class="d-block">عدد في شركه بوسطه</span>
                                            <span class="fs-24 fw-bolder d-block"
                                                id="activeLeads">{{$boostaRepresentatives}}</span>
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
                                            <span class="fs-24 fw-bolder d-block"
                                                id="qualifiedLeads">{{$NoonRepresentatives}}</span>
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
                    <form method="GET" action="{{ route('resignation-representatives.index') }}" class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">البحث</label>
                            <input type="text" name="search" class="form-control" placeholder="البحث في المندوبين..."
                                value="{{ request('search') }}">
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">من تاريخ</label>
                            <input type="date" name="date_from"
                                class="form-control {{ request('date_from') ? 'filter-active' : '' }}"
                                value="{{ request('date_from') }}">
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">إلى تاريخ</label>
                            <input type="date" name="date_to"
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



                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary me-2">تصفية</button>
                            <a href="{{ route('resignation-representatives.index') }}" class="btn btn-light">مسح</a>
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
                                                <th>رقم التليفون</th>
                                                <th>الشركة التي يعمل بها</th>
                                                <th>سبب الاستقاله</th>
                                                <th>الإجراءات</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($representatives as $representative)


                                                @php
                                                    // حضّر الـ notes لكل مندوب — ضبط التوقيت واستخراج اسم المنشئ
                                                    $notes = $representative->notes->map(function ($n) {
                                                        return [
                                                            'note' => $n->note,
                                                            // اضبط المنطقة الزمنية إلى Africa/Cairo ونسّق التاريخ
                                                            'created_at' => $n->created_at->setTimezone('Africa/Cairo')->format('Y-m-d H:i'),
                                                            'user' => $n->createdBy->name ?? 'غير معروف',
                                                        ];
                                                    });
                                                @endphp
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="avatar-text avatar-sm rounded-circle bg-primary me-3">
                                                                <i class="feather-user"></i>
                                                            </div>
                                                            <div>
                                                                <h6 class="mb-0">
                                                                    <a
                                                                        href="{{ route('representatives-not-completed.show', $representative->id) }}">
                                                                        {{ $representative->name }}
                                                                    </a>

                                                                </h6>

                                                                <small class="text-muted">رقم البطاقة:
                                                                    {{ $representative->national_id ?? 'غير محدد' }}</small>
                                                                @if($representative->delivery_deposits_count == 7 && count($representative->missingDocs()) == 0 && $representative->is_training == 1)
                                                                    <p class="text-success fw-bold">جاهز للعمل</p>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <i class="feather-phone me-2 text-muted"></i>
                                                            <a href="tel:{{ $representative->phone }}"
                                                                class="text-decoration-none">{{ $representative->phone }}</a>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span
                                                            class="badge bg-info">{{ $representative->company->name ?? 'غير محدد' }}</span>
                                                    </td>
                                                    <td>
                                                        @php
                                                            $reason = $representative->resignationRequest->reason ?? 'غير محدد';
                                                            $shortReason = \Illuminate\Support\Str::limit($reason, 30);
                                                        @endphp

                                                        <!-- الزر/البادج -->
                                                        <span
                                                            class="badge bg-info"
                                                            style="cursor:pointer;"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#reasonModal{{ $representative->id }}">
                                                            {{ $shortReason }}
                                                        </span>

                                                        <!-- المودال -->
                                                        <div class="modal fade" id="reasonModal{{ $representative->id }}" tabindex="-1">
                                                            <div class="modal-dialog modal-dialog-centered">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title">سبب الاستقالة</h5>
                                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        {{ $reason }}
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>

                                                    <td>
                                                    @can('edit_representatives')
                                                    <button
                                                        type="button"
                                                        class="btn btn-outline-{{ $representative->is_active ? 'danger' : 'success' }} d-flex align-items-center openStatusModal"
                                                        data-id="{{ $representative->id }}"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#StatusModal"
                                                        title="{{ $representative->is_active ? 'استقالة' : 'تفعيل' }}"
                                                    >
                                                        <i class="feather-{{ $representative->is_active ? 'pause' : 'play' }} me-2"></i>
                                                        <span>{{ $representative->is_active ? 'استقالة' : 'تفعيل' }}</span>
                                                    </button>
                                                    @endcan
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
                                    <h5>لا توجد نتائج</h5>
                                    <!-- <p class="text-muted">ابدأ بإضافة أول مندوب.</p>
                                        <a href="{{ route('representatives-not-completed.create') }}" class="btn btn-primary">
                                            <i class="feather-plus me-2"></i>إضافة مندوب
                                        </a> -->
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="interviewModal2" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">تغير المنطقه</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="interviewForm2" method="POST">
                    @csrf
                    <input type="hidden" name="representative_id">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">المحافظة</label>
                                <select name="government_id" id="change_government_id" class="form-control" required>
                                    <option value="">اختر المحافظة</option>
                                    @foreach(\App\Models\Governorate::all() as $governorate)
                                        <option value="{{ $governorate->id }}">{{ $governorate->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">المنطقة (اختياري)</label>
                                <select name="location_id" id="change_location_id" class="form-control">
                                    <option value="">اختر المنطقة (اختياري)</option>
                                </select>
                                <small class="text-muted">يمكن اختيار المحافظة فقط أو المحافظة والمنطقة معاً</small>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-warning" onclick="return confirm('هل أنت متأكد ؟')">
                            <i class="feather-calendar me-1"></i>تغير المنطقه
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <div class="modal fade" id="resignationActionModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">تفعيل استقالة المندوب</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="resignationActionForm" method="POST">
                    @csrf
                    <input type="hidden" name="representative_id">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">الشركة</label>
                                <select name="company_id" class="form-control" required>
                                    <option value="">اختر الشركة</option>
                                    @foreach(\App\Models\Company::where('is_active', true)->get() as $company)
                                        <option value="{{ $company->id }}">{{ $company->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">المحافظة</label>
                                <select name="government_id" id="resignation_government_id" class="form-control" required>
                                    <option value="">اختر المحافظة</option>
                                    @foreach(\App\Models\Governorate::all() as $governorate)
                                        <option value="{{ $governorate->id }}">{{ $governorate->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">المنطقة (اختياري)</label>
                                <select name="location_id" id="resignation_location_id" class="form-control">
                                    <option value="">اختر المنطقة (اختياري)</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-success">
                            <i class="feather-check me-1"></i>تفعيل
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>



    <!-- Modal -->
        <!-- Modal -->
        <div class="modal fade" id="StatusModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-md"> <!-- توسيط المودال وتحديد الحجم -->
                <div class="modal-content">

                    <!-- Header -->
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title mb-0">تغيير حالة المندوب</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>

                    <!-- Body -->
                    <form id="statusForm" method="POST">
                        @csrf
                        <input type="hidden" name="representative_id">
                        <div class="modal-body">

                            <div class="mb-3">
                                <label for="status_governorate_id" class="form-label fw-bold">المحافظة</label>
                                <select id="status_governorate_id" name="governorate_id" class="form-select">
                                    <option value="">اختر المحافظة</option>
                                    @foreach ($governorates as $gov)
                                        <option value="{{ $gov->id }}">{{ $gov->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="status_location_id" class="form-label fw-bold">المنطقة</label>
                                <select id="status_location_id" name="location_id" class="form-select">
                                    <option value="">اختر المحافظة أولاً</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="status_company_id" class="form-label fw-bold">الشركة</label>
                                <select id="status_company_id" name="company_id" class="form-select">
                                    <option value="">اختر الشركة</option>
                                    @foreach ($companies as $comp)
                                        <option value="{{ $comp->id }}">{{ $comp->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                        </div>

                        <!-- Footer -->
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                            <button type="submit" class="btn btn-primary">حفظ</button>
                        </div>
                    </form>

                </div>
            </div>
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

        $(document).ready(function () {
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
            $('#filterGovernorate').on('change', function () {
                var governorateId = $(this).val();
                loadFilterLocations(governorateId);
                filterSupervisors();
            });

            // Handle location filter
            $('#filterLocation').on('change', function () {
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
                    success: function (response) {
                        $('#filterLocation').empty().append('<option value="">جميع المقار</option>');

                        response.forEach(function (location) {
                            var option = new Option(location.name, location.id, false, false);
                            $('#filterLocation').append(option);
                        });
                    },
                    error: function () {
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
                        success: function (response) {
                            $('#newSupervisorId').empty().append('<option value="">اختر المشرف الجديد</option>');

                            response.forEach(function (supervisor) {
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
                        error: function () {
                            $('#newSupervisorId').empty().append('<option value="">خطأ في تحميل المشرفين</option>');
                        }
                    });
                } else if (governorateId) {
                    // Load supervisors by governorate via AJAX
                    $.ajax({
                        url: '{{ route("supervisors.by-governorate", ":governorateId") }}'.replace(':governorateId', governorateId),
                        type: 'GET',
                        success: function (response) {
                            $('#newSupervisorId').empty().append('<option value="">اختر المشرف الجديد</option>');

                            response.forEach(function (supervisor) {
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
                        error: function () {
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
            $('#transferForm').on('submit', function (e) {
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
                    success: function (response) {
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
                    error: function (xhr) {
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
                    complete: function () {
                        // Re-enable button
                        submitBtn.prop('disabled', false).html(originalText);
                    }
                });
            });
        });



document.addEventListener('DOMContentLoaded', function () {

const modal = document.getElementById('StatusModal');
const form  = document.getElementById('statusForm');
const govSelect = document.getElementById('status_governorate_id');
const locSelect = document.getElementById('status_location_id');

// عند فتح المودال
modal.addEventListener('show.bs.modal', function(event) {
    const button = event.relatedTarget;
    const repId = button.getAttribute('data-id');

    form.querySelector('input[name="representative_id"]').value = repId;

    // اضبط الفورم ليعمل POST للرابط مباشرة
    form.action = "{{ url('resignation-representatives') }}/" + repId + "/toggle-status";
});

// تحميل المناطق من المحافظة
if (govSelect) {
    govSelect.addEventListener('change', function () {
        const govId = this.value;

        if (!govId) {
            locSelect.innerHTML = '<option value="">اختر المحافظة أولاً</option>';
            return;
        }

        fetch("{{ url('getlocations') }}/" + govId)
            .then(res => res.json())
            .then(data => {
                locSelect.innerHTML = '<option value="">اختر المنطقة</option>';
                data.forEach(loc => {
                    locSelect.innerHTML += `<option value="${loc.id}">${loc.name}</option>`;
                });
            })
            .catch(err => {
                console.error(err);
                locSelect.innerHTML = '<option value="">خطأ في تحميل المناطق</option>';
            });
    });
}

});



        // Handle status change in follow-up modal
        document.addEventListener('DOMContentLoaded', function () {

            const interviewModal = document.getElementById('interviewModal');

            interviewModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget; // الزرار اللي فتح المودال
                const repId = button.getAttribute('data-id');

                // حط id جوه الفورم
                const hiddenInput = interviewModal.querySelector('input[name="representative_id"]');
                hiddenInput.value = repId;

                // حدّث الفورم عشان يبعت للـ route الصح
                const form = interviewModal.querySelector('form');
                form.action = "{{ route('representatives-not-completed.startRealRepresentative', ':id') }}"
                    .replace(':id', repId);
            });
            // Interview modal functionality
            const interviewGovSelect = document.getElementById('interview_government_id');
            const interviewLocSelect = document.getElementById('interview_location_id');
            const interviewMessageSelect = document.getElementById('interview_message_id');
            const messagePreview = document.getElementById('messagePreview');

            // Load locations when governorate changes
            if (interviewGovSelect && interviewLocSelect) {
                interviewGovSelect.addEventListener('change', function () {
                    const governorateId = this.value;

                    if (!governorateId) {
                        interviewLocSelect.innerHTML = '<option value="">اختر المنطقة</option>';
                        // Clear messages when governorate is cleared
                        if (interviewMessageSelect) {
                            interviewMessageSelect.innerHTML = '<option value="">اختر الرسالة</option>';
                            messagePreview.innerHTML = '<small class="text-muted">اختر المحافظة لعرض الرسائل المتاحة</small>';
                        }
                        return;
                    }

                    fetch(`{{ url('getlocations') }}/${governorateId}`)
                        .then(res => res.json())
                        .then(data => {
                            interviewLocSelect.innerHTML = '<option value="">اختر المنطقة</option>';
                            data.forEach(loc => {
                                interviewLocSelect.innerHTML += `<option value="${loc.id}">${loc.name}</option>`;
                            });

                            // Load messages for government only (without location)
                            loadMessagesForGovernment(governorateId);
                        })
                        .catch(err => {
                            console.error(err);
                            interviewLocSelect.innerHTML = '<option value="">خطأ في تحميل البيانات</option>';
                        });
                });
            }

            // Load messages when location changes
            if (interviewLocSelect && interviewMessageSelect) {
                interviewLocSelect.addEventListener('change', function () {
                    const locationId = this.value;
                    const governorateId = interviewGovSelect.value;

                    if (!governorateId) {
                        interviewMessageSelect.innerHTML = '<option value="">اختر الرسالة</option>';
                        messagePreview.innerHTML = '<small class="text-muted">اختر المحافظة أولاً</small>';
                        return;
                    }

                    if (!locationId) {
                        // If location is cleared, load messages for government only
                        loadMessagesForGovernment(governorateId);
                        return;
                    }

                    // Load messages for specific government and location
                    loadMessagesForGovernmentAndLocation(governorateId, locationId);
                });
            }

            // Function to load messages for government only
            function loadMessagesForGovernment(governorateId) {
                if (!interviewMessageSelect) return;

                fetch(`{{ url('getmessagesStartWork') }}?government_id=${governorateId}`)
                    .then(res => res.json())
                    .then(data => {
                        interviewMessageSelect.innerHTML = '<option value="">اختر الرسالة</option>';
                        data.forEach(msg => {
                            interviewMessageSelect.innerHTML += `<option value="${msg.id}">${msg.description}</option>`;
                        });
                        messagePreview.innerHTML = '<small class="text-muted">اختر الرسالة لعرض المعاينة</small>';
                    })
                    .catch(err => {
                        console.error(err);
                        interviewMessageSelect.innerHTML = '<option value="">خطأ في تحميل الرسائل</option>';
                        messagePreview.innerHTML = '<small class="text-danger">خطأ في تحميل الرسائل</small>';
                    });
            }

            // Function to load messages for specific government and location
            function loadMessagesForGovernmentAndLocation(governorateId, locationId) {
                if (!interviewMessageSelect) return;

                fetch(`{{ url('getmessagesStartWork') }}?government_id=${governorateId}&location_id=${locationId}`)
                    .then(res => res.json())
                    .then(data => {
                        interviewMessageSelect.innerHTML = '<option value="">اختر الرسالة</option>';
                        data.forEach(msg => {
                            interviewMessageSelect.innerHTML += `<option value="${msg.id}">${msg.description}</option>`;
                        });
                        messagePreview.innerHTML = '<small class="text-muted">اختر الرسالة لعرض المعاينة</small>';
                    })
                    .catch(err => {
                        console.error(err);
                        interviewMessageSelect.innerHTML = '<option value="">خطأ في تحميل الرسائل</option>';
                        messagePreview.innerHTML = '<small class="text-danger">خطأ في تحميل الرسائل</small>';
                    });
            }

            // Show message preview when message is selected
            if (interviewMessageSelect && messagePreview) {
                interviewMessageSelect.addEventListener('change', function () {
                    const messageId = this.value;

                    if (!messageId) {
                        messagePreview.innerHTML = '<small class="text-muted">اختر الرسالة لعرض المعاينة</small>';
                        return;
                    }

                    fetch(`{{ url('getmessageStartWork') }}/${messageId}`)
                        .then(res => res.json())
                        .then(data => {
                            messagePreview.innerHTML = `
                             <div class="mb-2"><strong>الوصف:</strong> ${data.description}</div>
                             ${data.google_map_url ? `<div><strong>رابط الخريطة:</strong> <a href="${data.google_map_url}" target="_blank">${data.google_map_url}</a></div>` : ''}
                         `;
                        })
                        .catch(err => {
                            console.error(err);
                            messagePreview.innerHTML = '<small class="text-danger">خطأ في تحميل الرسالة</small>';
                        });
                });
            }


        });

        document.addEventListener('DOMContentLoaded', function () {

            const reactiveModal = document.getElementById('ReactiveModal');

            reactiveModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const repId = button.getAttribute('data-id');

                const form = reactiveModal.querySelector('form');
                const hiddenInput = form.querySelector('input[name="representative_id"]');

                hiddenInput.value = repId;

                // حدّث الـ action بالرابط الصحيح
                form.action = "{{ route('representatives-not-completed.transferToActive', ':id') }}"
                    .replace(':id', repId);
            });

            // Interview modal functionality
            const interviewGovSelect = document.getElementById('government_id');
            const interviewLocSelect = document.getElementById('location_id');
            const interviewMessageSelect = document.getElementById('message_id');
            const messagePreview = document.getElementById('messagePreview2');

            // Load locations when governorate changes
            if (interviewGovSelect && interviewLocSelect) {
                interviewGovSelect.addEventListener('change', function () {
                    const governorateId = this.value;

                    if (!governorateId) {
                        interviewLocSelect.innerHTML = '<option value="">اختر المنطقة</option>';
                        // Clear messages when governorate is cleared
                        if (interviewMessageSelect) {
                            interviewMessageSelect.innerHTML = '<option value="">اختر الرسالة</option>';
                            messagePreview.innerHTML = '<small class="text-muted">اختر المحافظة لعرض الرسائل المتاحة</small>';
                        }
                        return;
                    }

                    fetch(`{{ url('getlocations') }}/${governorateId}`)
                        .then(res => res.json())
                        .then(data => {
                            interviewLocSelect.innerHTML = '<option value="">اختر المنطقة</option>';
                            data.forEach(loc => {
                                interviewLocSelect.innerHTML += `<option value="${loc.id}">${loc.name}</option>`;
                            });

                            // Load messages for government only (without location)
                            loadMessagesForGovernment(governorateId);
                        })
                        .catch(err => {
                            console.error(err);
                            interviewLocSelect.innerHTML = '<option value="">خطأ في تحميل البيانات</option>';
                        });
                });
            }

            // Load messages when location changes
            if (interviewLocSelect && interviewMessageSelect) {
                interviewLocSelect.addEventListener('change', function () {
                    const locationId = this.value;
                    const governorateId = interviewGovSelect.value;

                    if (!governorateId) {
                        interviewMessageSelect.innerHTML = '<option value="">اختر الرسالة</option>';
                        messagePreview.innerHTML = '<small class="text-muted">اختر المحافظة أولاً</small>';
                        return;
                    }

                    if (!locationId) {
                        // If location is cleared, load messages for government only
                        loadMessagesForGovernment(governorateId);
                        return;
                    }

                    // Load messages for specific government and location
                    loadMessagesForGovernmentAndLocation(governorateId, locationId);
                });
            }

            // Function to load messages for government only
            function loadMessagesForGovernment(governorateId) {
                if (!interviewMessageSelect) return;

                fetch(`{{ url('getmessagesStartWork') }}?government_id=${governorateId}`)
                    .then(res => res.json())
                    .then(data => {
                        interviewMessageSelect.innerHTML = '<option value="">اختر الرسالة</option>';
                        data.forEach(msg => {
                            interviewMessageSelect.innerHTML += `<option value="${msg.id}">${msg.description}</option>`;
                        });
                        messagePreview.innerHTML = '<small class="text-muted">اختر الرسالة لعرض المعاينة</small>';
                    })
                    .catch(err => {
                        console.error(err);
                        interviewMessageSelect.innerHTML = '<option value="">خطأ في تحميل الرسائل</option>';
                        messagePreview.innerHTML = '<small class="text-danger">خطأ في تحميل الرسائل</small>';
                    });
            }

            // Function to load messages for specific government and location
            function loadMessagesForGovernmentAndLocation(governorateId, locationId) {
                if (!interviewMessageSelect) return;

                fetch(`{{ url('getmessagesStartWork') }}?government_id=${governorateId}&location_id=${locationId}`)
                    .then(res => res.json())
                    .then(data => {
                        interviewMessageSelect.innerHTML = '<option value="">اختر الرسالة</option>';
                        data.forEach(msg => {
                            interviewMessageSelect.innerHTML += `<option value="${msg.id}">${msg.description}</option>`;
                        });
                        messagePreview.innerHTML = '<small class="text-muted">اختر الرسالة لعرض المعاينة</small>';
                    })
                    .catch(err => {
                        console.error(err);
                        interviewMessageSelect.innerHTML = '<option value="">خطأ في تحميل الرسائل</option>';
                        messagePreview.innerHTML = '<small class="text-danger">خطأ في تحميل الرسائل</small>';
                    });
            }

            // Show message preview when message is selected
            if (interviewMessageSelect && messagePreview) {
                interviewMessageSelect.addEventListener('change', function () {
                    const messageId = this.value;

                    if (!messageId) {
                        messagePreview.innerHTML = '<small class="text-muted">اختر الرسالة لعرض المعاينة</small>';
                        return;
                    }

                    fetch(`{{ url('getmessageStartWork') }}/${messageId}`)
                        .then(res => res.json())
                        .then(data => {
                            messagePreview.innerHTML = `
                             <div class="mb-2"><strong>الوصف:</strong> ${data.description}</div>
                             ${data.google_map_url ? `<div><strong>رابط الخريطة:</strong> <a href="${data.google_map_url}" target="_blank">${data.google_map_url}</a></div>` : ''}
                         `;
                        })
                        .catch(err => {
                            console.error(err);
                            messagePreview.innerHTML = '<small class="text-danger">خطأ في تحميل الرسالة</small>';
                        });
                });
            }


        });

        document.addEventListener('DOMContentLoaded', function () {
            var trainingModal = document.getElementById('SendMessageTrainingModal');
            var trainingForm = document.getElementById('trainingForm');
            var trainingRepId = document.getElementById('trainingRepId');

            // تحديث البيانات عند فتح المودال
            trainingModal.addEventListener('show.bs.modal', function (event) {
                var button = event.relatedTarget;
                var id = button.getAttribute('data-id');
                var name = button.getAttribute('data-name');

                // hidden input
                trainingRepId.value = id;

                // تعديل الفورم action
                trainingForm.action = "{{ route('representatives-not-completed.send_message_training', ':id') }}".replace(':id', id);
            });

            const interviewGovSelect = document.getElementById('governmentT_id');
            const interviewLocSelect = document.getElementById('locationT_id');
            const interviewMessageSelect = document.getElementById('messageT_id');
            const messagePreview = document.getElementById('messagePreviewT');
            const trainingTypeSelect = document.getElementById('trainingType');

            // Function to load messages
            function loadMessages(governorateId, locationId = null, type = null) {
                if (!interviewMessageSelect) return;

                let url = `{{ url('getmessagesTraining') }}?government_id=${governorateId}`;
                if (locationId) url += `&location_id=${locationId}`;
                if (type) url += `&type=${type}`;

                fetch(url)
                    .then(res => res.json())
                    .then(data => {
                        interviewMessageSelect.innerHTML = '<option value="">اختر الرسالة</option>';
                        data.forEach(msg => {
                            let optionText = msg.type === "online" ? msg.description_training : msg.description_location;
                            interviewMessageSelect.innerHTML += `<option value="${msg.id}" data-type="${msg.type}">${optionText}</option>`;
                        });
                        messagePreview.innerHTML = '<small class="text-muted">اختر الرسالة لعرض المعاينة</small>';
                    })
                    .catch(err => {
                        console.error(err);
                        interviewMessageSelect.innerHTML = '<option value="">خطأ في تحميل الرسائل</option>';
                        messagePreview.innerHTML = '<small class="text-danger">خطأ في تحميل الرسائل</small>';
                    });
            }

            // عند تغيير المحافظة
            if (interviewGovSelect) {
                interviewGovSelect.addEventListener('change', function () {
                    const governorateId = this.value;
                    if (!governorateId) {
                        interviewLocSelect.innerHTML = '<option value="">اختر المنطقة</option>';
                        interviewMessageSelect.innerHTML = '<option value="">اختر الرسالة</option>';
                        messagePreview.innerHTML = '<small class="text-muted">اختر المحافظة لعرض الرسائل المتاحة</small>';
                        return;
                    }

                    fetch(`{{ url('getlocations') }}/${governorateId}`)
                        .then(res => res.json())
                        .then(data => {
                            interviewLocSelect.innerHTML = '<option value="">اختر المنطقة</option>';
                            data.forEach(loc => {
                                interviewLocSelect.innerHTML += `<option value="${loc.id}">${loc.name}</option>`;
                            });

                            const type = trainingTypeSelect.value;
                            loadMessages(governorateId, null, type);
                        })
                        .catch(err => {
                            console.error(err);
                            interviewLocSelect.innerHTML = '<option value="">خطأ في تحميل البيانات</option>';
                        });
                });
            }

            // عند تغيير المنطقة
            if (interviewLocSelect) {
                interviewLocSelect.addEventListener('change', function () {
                    const locationId = this.value;
                    const governorateId = interviewGovSelect.value;
                    const type = trainingTypeSelect.value;

                    if (!governorateId) return;

                    loadMessages(governorateId, locationId || null, type);
                });
            }

            // عند تغيير النوع
            if (trainingTypeSelect) {
                trainingTypeSelect.addEventListener('change', function () {
                    const governorateId = interviewGovSelect.value;
                    const locationId = interviewLocSelect.value;
                    const type = this.value;

                    if (governorateId) {
                        loadMessages(governorateId, locationId || null, type);
                    }
                });
            }

            // عرض المعاينة عند اختيار الرسالة
            if (interviewMessageSelect) {
                interviewMessageSelect.addEventListener('change', function () {
                    const messageId = this.value;
                    if (!messageId) {
                        messagePreview.innerHTML = '<small class="text-muted">اختر الرسالة لعرض المعاينة</small>';
                        return;
                    }

                    fetch(`{{ url('getmessageTraining') }}/${messageId}`)
                        .then(res => res.json())
                        .then(data => {
                            if (data.type === "أونلاين") {
                                messagePreview.innerHTML = `
                                <div class="mb-2"><strong>الوصف:</strong> ${data.description_training}</div>
                                ${data.link_training ? `<div><strong>الرابط:</strong> <a href="${data.link_training}" target="_blank">${data.link_training}</a></div>` : ''}
                            `;
                            } else {
                                messagePreview.innerHTML = `
                                <div class="mb-2"><strong>الوصف:</strong> ${data.description_location}</div>
                                ${data.google_map_url ? `<div><strong>رابط الخريطة:</strong> <a href="${data.google_map_url}" target="_blank">${data.google_map_url}</a></div>` : ''}
                            `;
                            }
                        })
                        .catch(err => {
                            console.error(err);
                            messagePreview.innerHTML = '<small class="text-danger">خطأ في تحميل الرسالة</small>';
                        });
                });
            }
        });


        document.addEventListener('DOMContentLoaded', function () {
            var sendNotesModal = document.getElementById('SendNotesModal');
            sendNotesModal.addEventListener('show.bs.modal', function (event) {
                var button = event.relatedTarget;

                var id = button.getAttribute('data-id');
                var name = button.getAttribute('data-name');
                var phone = button.getAttribute('data-phone');
                var gov = button.getAttribute('data-gov');

                document.getElementById('repId').value = id;
                document.getElementById('repName').innerText = name;
                document.getElementById('repPhone').innerText = phone;
                document.getElementById('repGov').innerText = gov;

                document.getElementById('sendNotesForm').action = "{{ route('representatives-not-completed.save-note', ':id') }}".replace(':id', id);

                var notes = button.getAttribute('data-notes'); // مثال: تخزن كل الملاحظات كـ JSON string
                var previousNotesDiv = document.getElementById('previousNotes');

                if (notes) {
                    notes = JSON.parse(notes); // إذا كانت JSON string
                    previousNotesDiv.innerHTML = ''; // امسح الافتراضي
                    notes.forEach(function (note) {
                        var noteDiv = document.createElement('div');
                        noteDiv.classList.add('mb-2', 'p-2', 'border', 'rounded', 'bg-white');
                        noteDiv.innerHTML = `
                        <div>${note.note}</div>
                        <small class="text-muted">${note.user} - ${note.created_at}</small>
                    `;
                        previousNotesDiv.appendChild(noteDiv);
                    });
                } else {
                    previousNotesDiv.innerHTML = '<small class="text-muted">لا توجد ملاحظات سابقة</small>';
                }
            });
        });

     document.addEventListener('DOMContentLoaded', function () {

            const resignationModal = document.getElementById('resignationActionModal');

            resignationModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const repId = button.getAttribute('data-id');

                const form = resignationModal.querySelector('form');
                const hiddenInput = form.querySelector('input[name="representative_id"]');

                hiddenInput.value = repId;

                form.action = "{{ route('resignation-representatives.toggle-status', ':id') }}"
                    .replace(':id', repId);
            });

            const resignationGovSelect = document.getElementById('resignation_government_id');
            const resignationLocSelect = document.getElementById('resignation_location_id');

            if (resignationGovSelect && resignationLocSelect) {
                resignationGovSelect.addEventListener('change', function () {
                    const governorateId = this.value;

                    if (!governorateId) {
                        resignationLocSelect.innerHTML = '<option value="">اختر المنطقة (اختياري)</option>';
                        return;
                    }

                    fetch(`{{ url('getlocations') }}/${governorateId}`)
                        .then(res => res.json())
                        .then(data => {
                            resignationLocSelect.innerHTML = '<option value="">اختر المنطقة (اختياري)</option>';
                            data.forEach(loc => {
                                resignationLocSelect.innerHTML += `<option value="${loc.id}">${loc.name}</option>`;
                            });
                        })
                        .catch(err => {
                            console.error(err);
                            resignationLocSelect.innerHTML = '<option value="">خطأ في تحميل البيانات</option>';
                        });
                });
            }
        });
    </script>








@endpush

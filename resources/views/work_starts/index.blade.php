@extends('layouts.app')

@section('title', 'المندوبين')

@section('content')
    <div class="nxl-content">
        <!-- [ page-header ] start -->
        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">الجلسات التدريبية</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
                    <li class="breadcrumb-item">الجلسات التدريبية</li>
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
                                                id="totalLeads">{{ $totalRepresentatives }}</span>
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
                                            <span class="d-block">شركه نون</span>
                                            <span class="fs-24 fw-bolder d-block"
                                                id="activeLeads">{{ $NoonRepresentatives }}</span>
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
                                            <span class="d-block">شركه بوسته</span>
                                            <span class="fs-24 fw-bolder d-block"
                                                id="qualifiedLeads">{{ $BoostaRepresentatives }}</span>
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
                    <form method="GET" action="{{ route('work_starts.index') }}" class="row g-3">
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
                            <label class="form-label">متابعه</label>
                            <select name="status" class="form-control">
                                <option value="">جميع الحالات</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>متابعه</option>
                                <option value="تم بدء العمل" {{ request('status') == 'تم بدء العمل' ? 'selected' : '' }}>تم بدء العمل</option>
                                <option value="لم يرد" {{ request('status') == 'لم يرد' ? 'selected' : '' }}>لم يرد</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">الشركة</label>
                            <select name="company_id" class="form-control">
                                <option value="">جميع الشركات</option>
                                @foreach(\App\Models\Company::where('is_active', true)->get() as $company)
                                    <option value="{{ $company->id }}" {{ request('company_id') == $company->id ? 'selected' : ''
                                                                                                                                                            }}>
                                        {{ $company->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>



                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary me-2">تصفية</button>
                            <a href="{{ route('work_starts.index') }}" class="btn btn-light">مسح</a>
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
                            <h5 class="card-title mb-0">قائمه بدء العمل</h5>
                        </div>ب
                        <div class="card-body">
                            @if(session('success'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    {{ session('success') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            @endif

                            @if($workStarts->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>اسم المندوب</th>
                                                <th>المحافظة</th>
                                                <th>النوع</th>
                                                <th>الشركة</th>
                                                <th>الاوراق الناقصه</th>
                                                <th>التاريخ</th>
                                                <th>الإجراءات</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($workStarts as $workStart)
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="avatar-text avatar-sm rounded-circle bg-primary me-3">
                                                                <i class="feather-user"></i>
                                                            </div>
                                                            <div>
                                                                <h6 class="mb-0">
                                                                    <a
                                                                        href="{{ route('representatives-not-completed.show', $workStart->representative->id) }}">
                                                                        {{ $workStart->representative->name }}
                                                                    </a>
                                                                </h6>
                                                                <small
                                                                    class="text-muted">{{ $workStart->representative->phone }}</small>


                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            {{ $workStart->governorate->name }} <br>
                                                            {{ $workStart->location->name }}
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span
                                                            class="badge bg-info">{{ $workStart->message->type ?? 'غير محدد' }}</span>
                                                    </td>
                                                    <td>
                                                        <span
                                                            class="badge bg-info">{{ $workStart->representative->company->name ?? 'غير محدد' }}</span>
                                                    </td>
                                                    <td>
                                                        @if(count($workStart->representative->missingDocs()) > 0)
                                                            @foreach($workStart->representative->missingDocs() as $doc)
                                                                <span>{{ $doc }}</span><br>
                                                            @endforeach
                                                        @else
                                                            <span class="badge bg-success">كل الأوراق مكتملة</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        {{ $workStart->date }}
                                                    </td>
                                                    <td class="d-flex gap-2">

                                                    <div class="btn-group">
                                                            <button type="button" class="btn btn-sm btn-primary dropdown-toggle"
                                                                data-bs-toggle="dropdown">
                                                                {{ $workStart->status == 'pending' ? 'متابعة' : $workStart->status }}
                                                            </button>

                                                            <ul class="dropdown-menu">

                                                                <!-- بدء العمل -->
                                                                <li>
                                                                    <form method="POST"
                                                                        action="{{ route('work_starts.followup', $workStart->representative_id) }}">
                                                                        @csrf
                                                                        @method('POST')
                                                                        <input type="hidden" name="status" value="تم بدء العمل">
                                                                        <button class="dropdown-item">تم بدء العمل</button>
                                                                    </form>
                                                                </li>

                                                                <!-- لم يرد -->
                                                                {{-- <li>
                                                                    <button type="button"
                                                                        class="dropdown-item text-danger openAbsentModal"
                                                                        data-id="{{ $session->representative_id }}">
                                                                        لم يرد
                                                                    </button>
                                                                </li> --}}


                                                                <li>
                                                                    <form method="POST"
                                                                        action="{{ route('work_starts.followup', $workStart->representative_id) }}">
                                                                        @csrf
                                                                        @method('POST')
                                                                        <input type="hidden" name="status" value="لم يرد">
                                                                        <button class="dropdown-item">لم  يرد</button>
                                                                    </form>
                                                                </li>

                                                            </ul>
                                                        </div>


                                                        @if($workStart->representative->is_active)
                                                        <button type="button"
                                                            class="btn btn-sm btn-outline-{{ $workStart->representative->is_active ? 'danger' : 'success' }} uniform-btn"
                                                            data-bs-toggle="modal" data-bs-target="#reasonModal{{ $workStart->id }}">
                                                            {{ $workStart->representative->is_active ? 'استقاله' : 'تفعيل' }}
                                                        </button>
                                                        @endif




                                                       {{-- @if(!$workStart->representative->is_active)
                                                            <form
                                                                action="{{ route('training_sessions.activeResigne', $workStart->representative->id) }}"
                                                                method="POST"
                                                                onsubmit="return confirm('هل أنت متأكد من تفعيل هذا المندوب؟');">
                                                                @csrf
                                                                <button type="submit"
                                                                    class="btn btn-outline-success d-flex align-items-center"
                                                                    title="تفعيل">
                                                                    <i class="feather-play me-2"></i>
                                                                    <span>تفعيل</span>
                                                                </button>
                                                            </form>
                                                        @endif --}}

                                                        <button type="button" class="btn btn-sm btn-info uniform-btn" title="رسالة بدء العمل"
                                                            data-bs-toggle="modal" data-bs-target="#interviewModal"
                                                            data-id="{{ $workStart->representative_id}}">
                                                            <!--<i class="feather-clock"></i>-->
                                                            تعديل تاريخ  ببدء العمل
                                                        </button>
                                                    </td>

                                                </tr>

                                                 {{-- resign reason --}}
                                                <div class="modal fade" id="reasonModal{{ $workStart->id }}" tabindex="-1">
                                                    <div class="modal-dialog">
                                                        <form
                                                            action="{{ route('work_starts.toggle-status', $workStart->representative_id) }}"
                                                            method="POST">
                                                            @csrf
                                                            <div class="modal-content">

                                                                <div class="modal-header">
                                                                    <h5 class="modal-title">
                                                                        {{ $workStart->representative->is_active ? 'سبب الاستقالة' : 'سبب التفعيل' }}
                                                                    </h5>
                                                                    <button type="button" class="btn-close"
                                                                        data-bs-dismiss="modal"></button>
                                                                </div>

                                                                <div class="modal-body">
                                                                    <label class="form-label">السبب</label>
                                                                    <textarea name="reason" class="form-control" required
                                                                        placeholder="اكتب السبب هنا..."></textarea>
                                                                </div>

                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary"
                                                                        data-bs-dismiss="modal">إلغاء</button>
                                                                    <button type="submit" class="btn btn-primary">تأكيد</button>
                                                                </div>



                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>




                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Pagination -->
                                @if($workStarts->hasPages())
                                    <div class="d-flex justify-content-center mt-4">
                                        {{ $workStarts->links('pagination::bootstrap-5') }}
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




    <!-- Start work -->
    <div class="modal fade" id="interviewModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">رساله بدئ العمل</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="interviewForm" method="POST">
                    @csrf
                    <input type="hidden" name="representative_id">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">المحافظة</label>
                                <select name="government_id" id="interview_government_id" class="form-control" required>
                                    <option value="">اختر المحافظة</option>
                                    @foreach(\App\Models\Governorate::all() as $governorate)
                                        <option value="{{ $governorate->id }}">{{ $governorate->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">المنطقة (اختياري)</label>
                                <select name="location_id" id="interview_location_id" class="form-control">
                                    <option value="">اختر المنطقة (اختياري)</option>
                                </select>
                                <small class="text-muted">يمكن اختيار المحافظة فقط أو المحافظة والمنطقة معاً</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">الشركه</label>
                                <select name="company_id" id="company_id" class="form-control" required>
                                    <option value="">اختر الشركه</option>
                                    @foreach(\App\Models\Company::all() as $company)
                                        <option value="{{ $company->id }}">{{ $company->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">تايخ بدء العمل</label>
                                <input type="datetime-local" name="date" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">الرسالة</label>
                                <select name="message_id" id="interview_message_id" class="form-control select2" required>
                                    <option value="">اختر الرسالة</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">معاينة الرسالة</label>
                            <div id="messagePreview" class="border rounded p-3 bg-light">
                                <small class="text-muted">اختر المحافظة لعرض الرسائل المتاحة</small>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-warning" onclick="return confirm('هل أنت متأكد ؟')">
                            <i class="feather-calendar me-1"></i>رساله بدء العمل
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

        // Handle status change in follow-up modal
        /* document.addEventListener('DOMContentLoaded', function () {

            const interviewModal = document.getElementById('interviewModal');

            interviewModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget; // الزرار اللي فتح المودال
                const repId = button.getAttribute('data-id');

                // حط id جوه الفورم
                const hiddenInput = interviewModal.querySelector('input[name="representative_id"]');
                hiddenInput.value = repId;

                // حدّث الفورم عشان يبعت للـ route الصح
                const form = interviewModal.querySelector('form');
                form.action = "{{ route('work_starts.startRealRepresentative', ':id') }}"
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


        }); */



document.addEventListener('DOMContentLoaded', function () {

var interviewModal = document.getElementById('interviewModal');
var interviewForm  = document.getElementById('interviewForm');

// عند فتح المودال: ضبط الفورم + id
interviewModal.addEventListener('show.bs.modal', function (event) {
    var button = event.relatedTarget;
    var id   = button.getAttribute('data-id');

    document.querySelector('input[name="representative_id"]').value = id;

    interviewForm.action = "{{ route('representatives-not-completed.startRealRepresentative', ':id') }}"
        .replace(':id', id);
});


const govSelect = document.getElementById('interview_government_id');
const locSelect = document.getElementById('interview_location_id');
const companySelect = document.getElementById('company_id');
const messageSelect = document.getElementById('interview_message_id');
const messagePreview = document.getElementById('messagePreview');


// تحميل الرسائل
function loadMessages(governorateId, locationId = null, companyId = null) {

    if (!messageSelect) return;

    let url = `{{ url('getmessagesStartWork') }}?government_id=${governorateId}`;
    if (locationId) url += `&location_id=${locationId}`;
    if (companyId) url += `&company_id=${companyId}`;

    fetch(url)
        .then(res => res.json())
        .then(data => {
            messageSelect.innerHTML = '<option value="">اختر الرسالة</option>';
            data.forEach(msg => {
                messageSelect.innerHTML += `
                    <option value="${msg.id}">
                        ${msg.description}
                    </option>`;
            });

            messagePreview.innerHTML =
                '<small class="text-muted">اختر الرسالة لعرض المعاينة</small>';
        })
        .catch(err => {
            messageSelect.innerHTML = '<option value="">خطأ في التحميل</option>';
            messagePreview.innerHTML = '<small class="text-danger">خطأ في تحميل الرسائل</small>';
        });
}

// تغيير المحافظة: تحميل المناطق فقط
if (govSelect) {
    govSelect.addEventListener('change', function () {
        const governorateId = this.value;

        if (!governorateId) {
            locSelect.innerHTML = '<option value="">اختر المنطقة</option>';
            messageSelect.innerHTML = '<option value="">اختر الرسالة</option>';
            messagePreview.innerHTML = '<small class="text-muted">اختر المحافظة</small>';
            return;
        }

        fetch(`{{ url('getlocations') }}/${governorateId}`)
            .then(res => res.json())
            .then(data => {
                locSelect.innerHTML = '<option value="">اختر المنطقة (اختياري)</option>';
                data.forEach(loc => {
                    locSelect.innerHTML += `<option value="${loc.id}">${loc.name}</option>`;
                });

                messageSelect.innerHTML = '<option value="">اختر الرسالة</option>';
                messagePreview.innerHTML =
                    '<small class="text-muted">اختر المنطقة والشركة لعرض الرسائل</small>';
            })
            .catch(err => {
                locSelect.innerHTML = '<option value="">خطأ في تحميل المناطق</option>';
            });
    });
}

// عند تغيير المنطقة
if (locSelect) {
    locSelect.addEventListener('change', function () {
        const governorateId = govSelect.value;
        const locationId = this.value;
        const companyId = companySelect.value;

        if (governorateId && companyId) {
            loadMessages(governorateId, locationId, companyId);
        }
    });
}

// عند تغيير الشركة
if (companySelect) {
    companySelect.addEventListener('change', function () {
        const governorateId = govSelect.value;
        const locationId = locSelect.value;
        const companyId = this.value;

        if (governorateId && companyId) {
            loadMessages(governorateId, locationId, companyId);
        }
    });
}

// عرض المعاينة عند اختيار الرسالة
if (messageSelect) {
    messageSelect.addEventListener('change', function () {
        const messageId = this.value;

        if (!messageId) {
            messagePreview.innerHTML = '<small class="text-muted">اختر الرسالة لعرضها</small>';
            return;
        }

        fetch(`{{ url('getmessageStartWork') }}/${messageId}`)
            .then(res => res.json())
            .then(data => {
                messagePreview.innerHTML = `
                    <div><strong>الرسالة:</strong></div>
                    <div class="mt-2">${data.description}</div>
                    ${data.google_map_url
                        ? `<div class="mt-2"><strong>الخريطة:</strong> <a target="_blank" href="${data.google_map_url}">${data.google_map_url}</a></div>`
                        : ''
                    }
                `;
            })
            .catch(err => {
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
    </script>



<!--
    <script>
        document.querySelectorAll('.openAbsentModal').forEach(btn => {
            btn.addEventListener('click', function () {
                const id = this.dataset.id;
                const form = document.getElementById('absentForm');

                form.action =
                    "{{ route('representatives-not-completed.toggleTraining', ':id') }}"
                    .replace(':id', id);

                const myModal = new bootstrap.Modal(document.getElementById('absentModal'));
                myModal.show();
            });
        });
    </script> -->









@endpush

@extends('layouts.app')

@section('title', 'الحسابات البنكية')

@section('content')
<div class="nxl-content">
    <!-- [ page-header ] start -->
    <div class="page-header">
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
            <li class="breadcrumb-item">الحسابات البنكية</li>
        </ul>
        <div class="d-flex gap-2">
            <a href="{{ route('bank-accounts.export', request()->query()) }}" class="btn btn-success">
                <i class="feather-download me-1"></i>تصدير Excel
            </a>
        <a href="{{ route('bank-accounts.create') }}" class="btn btn-primary">
            <i class="feather-plus me-1"></i>إضافة حساب بنكي
        </a>
        </div>
    </div>
    <!-- [ page-header ] end -->



    <!-- Statistics Section -->
    <div class="card mb-4">
        <div class="card-body">
            <label class="fw-bold mb-3 d-block">الإحصائيات:</label>
            <div class="row g-3">
                <div class="col-lg-3 col-md-6">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center gap-3">
                                <div class="avatar-text avatar-xl rounded">
                                    <i class="feather-layers"></i>
                                </div>
                                <div>
                                    <span class="d-block fw-bold">الإجمالي</span>
                                    <span class="fs-24 fw-bolder">{{ $totalBankAccounts }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center gap-3">
                                <div class="avatar-text avatar-xl rounded bg-success text-white">
                                    <i class="feather-check-circle"></i>
                                </div>
                                <div>
                                    <span class="d-block fw-bold">يمتلك حساب</span>
                                    <span class="fs-24 fw-bolder">{{ $withAccountCount }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center gap-3">
                                <div class="avatar-text avatar-xl rounded bg-danger text-white">
                                    <i class="feather-x-circle"></i>
                                </div>
                                <div>
                                    <span class="d-block fw-bold">لا يمتلك حساب</span>
                                    <span class="fs-24 fw-bolder">{{ $withoutAccountCount }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center gap-3">
                                <div class="avatar-text avatar-xl rounded bg-warning text-white">
                                    <i class="feather-alert-circle"></i>
                                </div>
                                <div>
                                    <span class="d-block fw-bold">لا يمتلك كود</span>
                                    <span class="fs-24 fw-bolder">{{ $withoutCodeCount }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Filters Section -->
    <div class="collapse show" id="filterCollapse">
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('bank-accounts.index') }}" class="row g-3" id="filterForm">
                    <!-- البحث -->
                    <div class="col-md-2">
                        <label class="form-label" style="font-size: 11px;">البحث (الاسم / رقم الهاتف/ رقم الحساب)</label>
                        <input type="text" name="search" value="{{ request('search') }}" class="form-control"
                               placeholder="ابحث عن...">
                    </div>

                    <!-- فلتر البنك (Multiselect) -->
                    <div class="col-md-2">
                        <label class="form-label">البنك</label>
                        <select name="bank_ids[]" id="bank_ids" class="form-control select2" multiple>
                            <option value="" disabled>تحديد البنك</option>
                            @foreach($banks as $bank)
                                <option value="{{ $bank->id }}"
                                    {{ in_array($bank->id, request('bank_ids', [])) ? 'selected' : '' }}>
                                    {{ $bank->name }}
                                </option>
                            @endforeach
                        </select>
                        <div class="d-flex gap-2 mt-2">
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="selectAllBanks()">
                                <i class="feather-check-circle me-1"></i>تحديد الكل
                            </button>
                            <!-- <button type="button" class="btn btn-sm btn-outline-secondary" onclick="deselectAllBanks()">
                                <i class="feather-x-circle me-1"></i>إلغاء التحديد
                            </button> -->
                        </div>
                    </div>

                    <!-- فلتر المحافظة -->
                    <div class="col-md-2">
                        <label class="form-label">المحافظة</label>
                        <select name="governorate_id" id="governorate_id" class="form-control">
                            <option value="">جميع المحافظات</option>
                            @foreach($governorates as $governorate)
                                <option value="{{ $governorate->id }}"
                                    {{ request('governorate_id') == $governorate->id ? 'selected' : '' }}>
                                    {{ $governorate->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- فلتر المنطقة -->
                    <div class="col-md-2">
                        <label class="form-label">المنطقة</label>
                        <select name="location_id" id="location_id" class="form-control">
                            <option value="">جميع المناطق</option>
                            @foreach($locations as $location)
                                <option value="{{ $location->id }}"
                                    data-governorate="{{ $location->governorate_id }}"
                                    {{ request('location_id') == $location->id ? 'selected' : '' }}>
                                    {{ $location->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- فلتر الحالة -->
                    <div class="col-md-2">
                        <label class="form-label">الحالة</label>
                        <select name="status" class="form-control">
                            <option value="">جميع الحالات</option>
                            <option value="يمتلك حساب" {{ request('status') == 'يمتلك حساب' ? 'selected' : '' }}>يمتلك حساب</option>
                            <option value="لا يمتلك حساب" {{ request('status') == 'لا يمتلك حساب' ? 'selected' : '' }}>لا يمتلك حساب</option>
                        </select>
                    </div>

                    <!-- فلتر الكود -->
                    <div class="col-md-2">
                        <label class="form-label">كود المندوب</label>
                        <select name="code_status" class="form-control">
                            <option value="">الكل</option>
                            <option value="with" {{ request('code_status') == 'with' ? 'selected' : '' }}>يمتلك كود</option>
                            <option value="without" {{ request('code_status') == 'without' ? 'selected' : '' }}>لا يمتلك كود</option>
                        </select>
                    </div>



                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">تصفية</button>
                        <a href="{{ route('bank-accounts.index') }}" class="btn btn-light">مسح</a>
                    </div>
                </form>
            </div>
        </div>
    </div>





    <!-- Import Section -->
    <style>
        .import-card {
            border: 1px dashed #d0d5dd;
            background: #f9fafb;
        }
        .import-title {
            font-weight: 700;
            font-size: 14px;
        }
        .import-hint {
            font-size: 12px;
        }
    </style>
    <div class="card mb-4 import-card shadow-sm">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <div class="import-title">استيراد الحسابات البنكية من Excel</div>
                    <div class="text-muted import-hint">الأعمدة المطلوبة: الكود، اسم صاحب الحساب، البنك، رقم الحساب</div>
                </div>
                <span class="badge bg-primary-subtle text-primary">Excel / CSV</span>
            </div>
            <form method="POST" action="{{ route('bank-accounts.import') }}" enctype="multipart/form-data" class="row g-3 align-items-end">
                @csrf
                <div class="col-md-6">
                    <label class="form-label">ملف الاستيراد</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="feather-upload"></i></span>
                        <input type="file" name="file" class="form-control" accept=".xlsx,.xls,.csv" required>
                    </div>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-success w-100">
                        <i class="feather-upload me-1"></i>استيراد الآن
                    </button>
                </div>
            </form>
        </div>
    </div>



    <!-- [ Main Content ] start -->
    <div class="main-content">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">قائمة الحسابات البنكية</h5>
                        <a href="{{ route('bank-accounts.create') }}" class="btn btn-primary btn-sm">
                            <i class="feather-plus me-1"></i>إضافة حساب بنكي
                        </a>
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
                        @if(session('import_failures') && count(session('import_failures')) > 0)
                            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                                <strong>تم تخطي بعض الصفوف:</strong>
                                @foreach(session('import_failures') as $failure)
                                    <div>صف {{ $failure['row'] }}: {{ implode('، ', $failure['errors']) }}</div>
                                @endforeach
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if($bankAccounts->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>اسم المندوب / رقم الهاتف / رقم البطاقة</th>
                                            <th>كود المندوب</th>
                                            <th>المحافظة / المنطقة</th>
                                            <th>الحالة</th>
                                            <th>اسم صاحب الحساب</th>
                                            <th>اسم البنك</th>
                                            <th>رقم الحساب</th>
                                            <th>الإجراءات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($bankAccounts as $bankAccount)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-text avatar-sm rounded-circle bg-primary me-3">
                                                        <i class="feather-user"></i>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0">{{ $bankAccount->representative->name ?? 'غير محدد' }}</h6>
                                                        <small class="text-muted">{{ $bankAccount->representative->phone ?? '-' }}</small><br>
                                                        <small class="text-muted">{{ $bankAccount->representative->national_id ?? '-' }}</small>
                                                    </div>
                                                </div>
                                            </td>

                                            <td>
                                                {{ $bankAccount->representative->code ?? 'غير محدد' }}
                                            </td>

                                            <td>
                                                <div>
                                                    <strong>{{ $bankAccount->representative->governorate->name ?? 'غير محدد' }}</strong><br>
                                                    <small class="text-muted">{{ $bankAccount->representative->location->name ?? 'غير محدد' }}</small>
                                                </div>
                                            </td>

                                            <td>
                                                @if($bankAccount->status == 'يمتلك حساب')
                                                    <span class="badge bg-success">يمتلك حساب</span>
                                                @else
                                                    <span class="badge bg-danger">لا يمتلك حساب</span>
                                                @endif
                                            </td>

                                            <td>
                                                {{ $bankAccount->account_owner_name }}
                                            </td>

                                            <td>
                                                {{ $bankAccount->bank->name ?? 'غير محدد' }}
                                            </td>

                                            <td>
                                                <span>رقم الحساب: {{ $bankAccount->account_number }}</span> <br>
                                                <span class="text-muted ms-2">حساب المحفظة: {{ $bankAccount->representative->bank_account ?? 'غير محدد' }}</span>
                                            </td>

                                            <td>
                                                <div class="d-flex gap-2">
                                                    <a href="{{ route('bank-accounts.edit', $bankAccount->id) }}" class="btn btn-sm btn-outline-warning">
                                                        <i class="feather-edit"></i>
                                                    </a>
                                                    <form action="{{ route('bank-accounts.destroy', $bankAccount->id) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا الحساب البنكي؟');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                                            <i class="feather-trash-2"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach

                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            @if($bankAccounts->hasPages())
                                <div class="d-flex justify-content-center mt-4">
                                    {{ $bankAccounts->links('pagination::bootstrap-5') }}
                                </div>
                            @endif
                        @else
                            <div class="text-center py-5">
                                <div class="avatar-text avatar-xl mx-auto mb-3">
                                    <i class="feather-credit-card"></i>
                                </div>
                                <h5>لم يتم العثور على حسابات بنكية</h5>
                                <p class="text-muted">ابدأ بإضافة حساب بنكي أول.</p>
                                <a href="{{ route('bank-accounts.create') }}" class="btn btn-primary">
                                    <i class="feather-plus me-2"></i>إضافة حساب بنكي
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

@section('scripts')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        // Initialize Select2 for multiselect
        $('#bank_ids').select2({
            placeholder: "تحديد البنك",
            allowClear: true
        });

        // حفظ جميع خيارات المناطق الأصلية
        const allLocationOptions = $('#location_id').html();

        // Filter locations based on governorate
        $('#governorate_id').on('change', function() {
            const governorateId = $(this).val();
            const locationSelect = $('#location_id');

            // إعادة تعيين القائمة بجميع الخيارات الأصلية
            locationSelect.html(allLocationOptions);

            // إذا تم اختيار محافظة، قم بفلترة المناطق
            if (governorateId) {
                locationSelect.find('option').each(function() {
                    const option = $(this);
                    const optionGovernorate = option.data('governorate');

                    // إخفاء الخيارات التي لا تنتمي للمحافظة المختارة (عدا "جميع المناطق")
                    if (option.val() !== '' && optionGovernorate != governorateId) {
                        option.remove();
                    }
                });
            }

            // إعادة تعيين القيمة المختارة
            locationSelect.val('').trigger('change');
        });

        // Initialize location filter on page load if governorate is selected
        const initialGovernorateId = "{{ request('governorate_id') }}";
        const initialLocationId = "{{ request('location_id') }}";

        if (initialGovernorateId) {
            $('#governorate_id').trigger('change');
            if (initialLocationId) {
                setTimeout(function() {
                    $('#location_id').val(initialLocationId).trigger('change');
                }, 100);
            }
        }
    });

    function selectAllBanks() {
        $('#bank_ids option').prop('selected', true);
        $('#bank_ids').trigger('change');
    }

    function deselectAllBanks() {
        $('#bank_ids option').prop('selected', false);
        $('#bank_ids').trigger('change');
    }
</script>
@endsection

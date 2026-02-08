@extends('layouts.app')

@section('title', 'المحافظ الالكترونية')

@section('content')
<div class="nxl-content">
    <!-- [ page-header ] start -->
    <div class="page-header">
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
            <li class="breadcrumb-item">المحافظ الالكترونية</li>
        </ul>
    </div>
    <!-- [ page-header ] end -->

    <!-- Statistics Section -->
    <div class="card mb-4">
        <div class="card-body">
            <label class="fw-bold mb-3 d-block">الإحصائيات:</label>
            <div class="row g-3">
                <div class="col-lg-4 col-md-6">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center gap-3">
                                <div class="avatar-text avatar-xl rounded">
                                    <i class="feather-layers"></i>
                                </div>
                                <div>
                                    <span class="d-block fw-bold">الإجمالي</span>
                                    <span class="fs-24 fw-bolder">{{ $totalWallets }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center gap-3">
                                <div class="avatar-text avatar-xl rounded bg-success text-white">
                                    <i class="feather-check-circle"></i>
                                </div>
                                <div>
                                    <span class="d-block fw-bold">يمتلك محفظة</span>
                                    <span class="fs-24 fw-bolder">{{ $withWalletCount }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center gap-3">
                                <div class="avatar-text avatar-xl rounded bg-danger text-white">
                                    <i class="feather-x-circle"></i>
                                </div>
                                <div>
                                    <span class="d-block fw-bold">لا يمتلك محفظة</span>
                                    <span class="fs-24 fw-bolder">{{ $withoutWalletCount }}</span>
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
                <form method="GET" action="{{ route('wallet-accounts.index') }}" class="row g-3" id="filterForm">
                    <div class="col-md-3">
                        <label class="form-label" style="font-size: 11px;">البحث (الاسم / رقم الهاتف / الكود / رقم المحفظة)</label>
                        <input type="text" name="search" value="{{ request('search') }}" class="form-control"
                               placeholder="ابحث عن...">
                    </div>

                    <div class="col-md-3">
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

                    <div class="col-md-3">
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

                    <div class="col-md-3">
                        <label class="form-label">حالة المحفظة</label>
                        <select name="wallet_status" class="form-control">
                            <option value="">الكل</option>
                            <option value="with" {{ request('wallet_status') == 'with' ? 'selected' : '' }}>يمتلك محفظة</option>
                            <option value="without" {{ request('wallet_status') == 'without' ? 'selected' : '' }}>لا يمتلك محفظة</option>
                        </select>
                    </div>

                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">تصفية</button>
                        <a href="{{ route('wallet-accounts.index') }}" class="btn btn-light">مسح</a>
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
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">قائمة المحافظ الالكترونية</h5>
                    </div>
                    <div class="card-body">
                        @if($walletAccounts->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>اسم المندوب</th>
                                            <th>رقم الهاتف</th>
                                            <th>كود المندوب</th>
                                            <th>المحافظة / المنطقة</th>
                                            <th>رقم المحفظة</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($walletAccounts as $rep)
                                        <tr>
                                            <td>{{ $rep->name ?? 'غير محدد' }}</td>
                                            <td>{{ $rep->phone ?? '-' }}</td>
                                            <td>{{ $rep->code ?? 'غير محدد' }}</td>
                                            <td>
                                                <div>
                                                    <strong>{{ $rep->governorate->name ?? 'غير محدد' }}</strong><br>
                                                    <small class="text-muted">{{ $rep->location->name ?? 'غير محدد' }}</small>
                                                </div>
                                            </td>
                                            <td>{{ $rep->bank_account ?? 'غير محدد' }}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>

                            @if($walletAccounts->hasPages())
                                <div class="d-flex justify-content-center mt-4">
                                    {{ $walletAccounts->links('pagination::bootstrap-5') }}
                                </div>
                            @endif
                        @else
                            <div class="text-center py-5">
                                <div class="avatar-text avatar-xl mx-auto mb-3">
                                    <i class="feather-credit-card"></i>
                                </div>
                                <h5>لم يتم العثور على محافظ إلكترونية</h5>
                                <p class="text-muted">لا يوجد مندوبين لديهم محافظ إلكترونية بعد.</p>
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
<script>
    $(document).ready(function() {
        const allLocationOptions = $('#location_id').html();

        $('#governorate_id').on('change', function() {
            const governorateId = $(this).val();
            const locationSelect = $('#location_id');

            locationSelect.html(allLocationOptions);

            if (governorateId) {
                locationSelect.find('option').each(function() {
                    const option = $(this);
                    const optionGovernorate = option.data('governorate');

                    if (option.val() !== '' && optionGovernorate != governorateId) {
                        option.remove();
                    }
                });
            }

            locationSelect.val('').trigger('change');
        });

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
</script>
@endsection

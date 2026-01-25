@extends('layouts.app')

@section('title', 'إضافة حساب بنكي جديد')

@section('content')
    <div class="nxl-content">
        <!-- [ page-header ] start -->
        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">الحسابات البنكية</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('bank-accounts.index') }}">الحسابات البنكية</a></li>
                    <li class="breadcrumb-item">إنشاء</li>
                </ul>
            </div>
        </div>
        <!-- [ page-header ] end -->

        <!-- [ Main Content ] start -->
        <div class="main-content">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">إنشاء حساب بنكي جديد</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('bank-accounts.store') }}" method="POST">
                                @csrf

                                <div class="row">
                                    <!-- المندوب -->
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">المندوب <span class="text-danger">*</span></label>
                                        <select name="representative_id" id="representative_id" class="form-control @error('representative_id') is-invalid @enderror" required>
                                            <option value="">اختر المندوب</option>
                                            @foreach($representatives as $representative)
                                                <option value="{{ $representative->id }}" {{ old('representative_id') == $representative->id ? 'selected' : '' }}>
                                                    {{ $representative->name }} - {{ $representative->code }} - {{ $representative->phone }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('representative_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- البنك -->
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">البنك <span class="text-danger">*</span></label>
                                        <select name="bank_id" class="form-control @error('bank_id') is-invalid @enderror" required>
                                            <option value="">اختر البنك</option>
                                            @foreach($banks as $bank)
                                                <option value="{{ $bank->id }}" {{ old('bank_id') == $bank->id ? 'selected' : '' }}>
                                                    {{ $bank->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('bank_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- الحالة -->
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">الحالة <span class="text-danger">*</span></label>
                                        <select name="status" class="form-control @error('status') is-invalid @enderror" required>
                                            <option value="">اختر الحالة</option>
                                            <option value="يمتلك حساب" {{ old('status') == 'يمتلك حساب' ? 'selected' : '' }}>يمتلك حساب</option>
                                            <option value="لا يمتلك حساب" {{ old('status') == 'لا يمتلك حساب' ? 'selected' : '' }}>لا يمتلك حساب</option>
                                        </select>
                                        @error('status')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- اسم صاحب الحساب -->
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">اسم صاحب الحساب <span class="text-danger">*</span></label>
                                        <input type="text" name="account_owner_name" class="form-control @error('account_owner_name') is-invalid @enderror"
                                               placeholder="أدخل اسم صاحب الحساب" value="{{ old('account_owner_name') }}" required>
                                        @error('account_owner_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- رقم الحساب -->
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">رقم الحساب <span class="text-danger">*</span></label>
                                        <input type="text" name="account_number" class="form-control @error('account_number') is-invalid @enderror"
                                               placeholder="أدخل رقم الحساب" value="{{ old('account_number') }}" required>
                                        @error('account_number')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('bank-accounts.index') }}" class="btn btn-light">إلغاء</a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="feather-save me-2"></i> إنشاء الحساب البنكي
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('#representative_id').select2({
            placeholder: "اختر المندوب",
            allowClear: true
        });
    });
</script>
@endsection


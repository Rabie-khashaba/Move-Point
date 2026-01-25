@extends('layouts.app')

@section('title', 'إنشاء مندوب جديد')

@section('content')
<div class="nxl-content">
    <!-- [ page-header ] start -->
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">

            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('representatives.index') }}">المندوبين</a></li>
                @if($lead)
                    <li class="breadcrumb-item"><a href="{{ route('leads.show', $lead->id) }}">العميل المحتمل: {{ $lead->name }}</a></li>
                @endif
                <li class="breadcrumb-item">إنشاء مندوب جديد</li>
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
                        <h5 class="card-title mb-0">إنشاء مندوب جديد</h5>
                    </div>
                    <div class="card-body">
                        @if($lead)
                            <div class="alert alert-info mb-4">
                                <div class="d-flex align-items-center">
                                    <i class="feather-info me-2"></i>
                                    <div>
                                        <strong>إنشاء مندوب من عميل محتمل:</strong>
                                        <br>
                                        تم ملء البيانات الأساسية من العميل المحتمل "{{ $lead->name }}" ({{ $lead->phone }})
                                        <br>
                                        <small class="text-muted">يمكنك تعديل أي من هذه البيانات حسب الحاجة</small>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <form action="{{ route('representatives.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @if($lead)
                                <input type="hidden" name="lead_id" value="{{ $lead->id }}">
                            @endif

                            <div class="row">
                                <!-- الاسم -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">الاسم <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                           placeholder="أدخل الاسم" value="{{ old('name', $lead->name ?? '') }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- رقم التليفون -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">رقم التليفون <span class="text-danger">*</span></label>
                                    <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror"
                                           placeholder="أدخل رقم التليفون (11 رقم)" value="{{ old('phone', $lead->phone ?? '') }}"
                                           maxlength="11" pattern="[0-9]{11}" required>
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- العنوان -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">العنوان <span class="text-danger">*</span></label>
                                    <textarea name="address" class="form-control @error('address') is-invalid @enderror"
                                              placeholder="أدخل العنوان" rows="3" required>{{ old('address', $lead->address ?? '') }}</textarea>
                                    @error('address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- التواصل -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">التواصل <span class="text-danger">*</span></label>
                                    <input type="text" name="contact" class="form-control @error('contact') is-invalid @enderror"
                                           placeholder="أدخل رقم التواصل" value="{{ old('contact', $lead->phone ?? '') }}"
                                           maxlength="11" pattern="[0-9]{11}"
                                           oninput="this.value=this.value.replace(/[^0-9]/g,'');">
                                    @error('contact')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- رقم البطاقة -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">رقم البطاقة <span class="text-danger">*</span></label>
                                    <input type="text" name="national_id" class="form-control @error('national_id') is-invalid @enderror"
                                           placeholder="أدخل رقم البطاقة (14 رقم)" value="{{ old('national_id') }}"
                                           maxlength="14" pattern="[0-9]{14}" required>
                                    @error('national_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- المرتب -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">المرتب <span class="text-danger">*</span></label>
                                    <input type="number" name="salary" class="form-control @error('salary') is-invalid @enderror"
                                           placeholder="أدخل المرتب" value="{{ old('salary') }}" step="0.01" min="0" required>
                                    @error('salary')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- تاريخ بداية العمل -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">تاريخ بداية العمل <span class="text-danger">*</span></label>
                                    <input type="date" name="start_date" class="form-control @error('start_date') is-invalid @enderror"
                                           value="{{ old('start_date') }}" required>
                                    @error('start_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- الشركة -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">الشركة <span class="text-danger">*</span></label>
                                    <select name="company_id" class="form-control @error('company_id') is-invalid @enderror" required>
                                        <option value="">اختر الشركة</option>
                                        @foreach($companies as $company)
                                            <option value="{{ $company->id }}" {{ old('company_id') == $company->id ? 'selected' : '' }}>
                                                {{ $company->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('company_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- رقم محفظة او حساب بنكي -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">رقم محفظة او حساب بنكي </label>
                                    <input type="text" name="bank_account" class="form-control @error('bank_account') is-invalid @enderror"
                                           placeholder="أدخل رقم الحساب البنكي" value="{{ old('bank_account') }}" >
                                    @error('bank_account')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- كود المندوب -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">كود المندوب </label>
                                    <input type="text" name="code" class="form-control @error('code') is-invalid @enderror"
                                           placeholder="أدخل كود المندوب في الشركة" value="{{ old('code') }}" >
                                    @error('code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- المحافظة -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">المحافظة <span class="text-danger">*</span></label>
                                    <select name="governorate_id" id="governorate_id" class="form-control @error('governorate_id') is-invalid @enderror" required>
                                        <option value="">اختر المحافظة</option>
                                        @foreach($governorates as $governorate)
                                            <option value="{{ $governorate->id }}" {{ old('governorate_id', $lead->governorate_id ?? '') == $governorate->id ? 'selected' : '' }}>
                                                {{ $governorate->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('governorate_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- الموقع -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">المنطقة</label>
                                    <select name="location_id" id="location_id" class="form-control @error('location_id') is-invalid @enderror" >
                                        <option value="">اختر المنطقة</option>
                                        @foreach($locations as $location)
                                            <option value="{{ $location->id }}"
                                                    data-governorate="{{ $location->governorate_id }}"
                                                    {{ old('location_id', $lead->location_id ?? '') == $location->id ? 'selected' : '' }}>
                                                {{ $location->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('location_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>


                                <!-- المرفقات -->
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">المرفقات</label>
                                    <div class="row">
                                        <div class="col-md-4 mb-2">
                                            <label class="form-label small">البطاقة (وجه أول)</label>
                                            <input type="file" name="attachments[]" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <label class="form-label small">البطاقة (خلف)</label>
                                            <input type="file" name="attachments[]" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <label class="form-label small">فيش</label>
                                            <input type="file" name="attachments[]" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <label class="form-label small">شهادة ميلاد</label>
                                            <input type="file" name="attachments[]" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <label class="form-label small">إيصال الأمانة</label>
                                            <input type="file" name="attachments[]" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <label class="form-label small">رخصة القيادة</label>
                                            <input type="file" name="attachments[]" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <label class="form-label small">رخصة السيارة</label>
                                            <input type="file" name="attachments[]" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <label class="form-label small">إيصال مرافق (غاز أو مياه أو كهرباء)</label>
                                            <input type="file" name="attachments[]" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                                        </div>
                                    </div>
                                    @error('attachments.*')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- الاستعلام -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">الاستعلام</label>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="inquiry_checkbox" value="1"
                                               {{ old('inquiry_checkbox') ? 'checked' : '' }}>
                                        <label class="form-check-label">نعم</label>
                                    </div>
                                    <small class="text-muted">حدد إذا كان المندوب يحتاج إلى استعلام أو معلومات إضافية</small>
                                    @error('inquiry_checkbox')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- بيانات الاستعلام -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">بيانات الاستعلام</label>
                                    <textarea name="inquiry_data" class="form-control @error('inquiry_data') is-invalid @enderror"
                                              placeholder="أدخل بيانات الاستعلام أو المعلومات الإضافية المطلوبة" rows="2">{{ old('inquiry_data') }}</textarea>
                                    <small class="text-muted">أدخل تفاصيل الاستعلام أو المعلومات المطلوبة</small>
                                    @error('inquiry_data')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- لوكيشن المنزل -->
{{--                                <div class="col-md-12 mb-3">--}}
{{--                                    <span class="text-danger">*</span>--}}
{{--                                    <label class="form-label">لوكيشن المنزل </label>--}}
{{--                                    <input type="url" name="home_location" class="form-control @error('home_location') is-invalid @enderror"--}}
{{--                                           placeholder="أدخل رابط جوجل ماب للمنزل (مثال: https://maps.google.com/...)"--}}
{{--                                           value="{{ old('home_location') }}" required>--}}
{{--                                    <small class="text-muted">يجب إدخال رابط جوجل ماب صحيح للمنزل</small>--}}
{{--                                    @error('home_location')--}}
{{--                                        <div class="invalid-feedback">{{ $message }}</div>--}}
{{--                                    @enderror--}}
{{--                                </div>--}}

                                <div class="col-md-12 mb-3">
                                    <label class="form-label">لوكيشن المنزل</label>
                                    <input type="url" name="home_location"
                                           class="form-control @error('home_location') is-invalid @enderror"
                                           placeholder="أدخل رابط جوجل ماب للمنزل (مثال: https://maps.google.com/...)"
                                           value="{{ old('home_location') }}">
                                    <small class="text-muted">يمكن تركه فارغًا أو إدخال رابط جوجل ماب صحيح للمنزل</small>
                                    @error('home_location')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>



                                <!-- الحالة -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">الحالة</label>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="is_active" value="1" checked>
                                        <label class="form-check-label">نشط</label>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">تعيين كمشرف</label>

                                <div class="form-check form-switch">
                                    <input type="checkbox"
                                        class="form-check-input"
                                        id="isSupervisor"
                                        name="is_supervisor"
                                        value="1"
                                        {{ old('is_supervisor') ? 'checked' : '' }}>

                                    <label class="form-check-label" for="isSupervisor">مشرف</label>
                                </div>
                            </div>


                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('representatives.index') }}" class="btn btn-light">إلغاء</a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="feather-save me-2"></i>
                                    إنشاء المندوب
                                </button>
                            </div>
                        </form>
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
document.addEventListener('DOMContentLoaded', function() {
    // Handle governorate change to filter locations
    /* const governorateSelect = document.getElementById('governorate_id');
    const locationSelect = document.getElementById('location_id');

    // Store original location options for filtering
    const originalLocationOptions = Array.from(locationSelect.querySelectorAll('option')).map(option => ({
        element: option.cloneNode(true),
        governorateId: option.dataset.governorate
    }));

    // Function to filter locations
    function filterLocations(governorateId) {
        // Clear current options
        locationSelect.innerHTML = '<option value="">اختر الموقع</option>';

        if (governorateId) {
            // Add locations for selected governorate
            originalLocationOptions.forEach(locationData => {
                if (locationData.governorateId === governorateId) {
                    locationSelect.appendChild(locationData.element.cloneNode(true));
                }
            });
        }
    }

    governorateSelect.addEventListener('change', function() {
        filterLocations(this.value);
    }); */

    // If governorate is pre-selected (from lead), filter locations on page load
    if (governorateSelect.value) {
        filterLocations(governorateSelect.value);
    }

    // Phone number validation
    const phoneInput = document.querySelector('input[name="phone"]');
    phoneInput.addEventListener('input', function() {
        this.value = this.value.replace(/[^0-9]/g, '');
        if (this.value.length > 11) {
            this.value = this.value.slice(0, 11);
        }
    });

    // National ID validation
    const nationalIdInput = document.querySelector('input[name="national_id"]');
    nationalIdInput.addEventListener('input', function() {
        this.value = this.value.replace(/[^0-9]/g, '');
        if (this.value.length > 14) {
            this.value = this.value.slice(0, 14);
        }
    });
});
</script>



<script>
document.addEventListener("DOMContentLoaded", function () {

    const governorateSelect = document.getElementById("governorate_id");
    const locationSelect = document.getElementById("location_id");

    governorateSelect.addEventListener("change", function () {
        let id = this.value;

        locationSelect.innerHTML = `<option value="">جاري التحميل...</option>`;

        if (id) {
            fetch(`/get-locations/${id}`)
                .then(res => res.json())
                .then(data => {
                    locationSelect.innerHTML = `<option value="">اختر المنطقة</option>`;
                    data.forEach(loc => {
                        locationSelect.innerHTML += `<option value="${loc.id}">${loc.name}</option>`;
                    });
                });
        } else {
            locationSelect.innerHTML = `<option value="">اختر المنطقة</option>`;
        }
    });

    // Auto-load on page load (old values)
    if (governorateSelect.value) {
        governorateSelect.dispatchEvent(new Event("change"));
    }

});
</script>
@endsection

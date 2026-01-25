@extends('layouts.app')

@section('title', 'تعديل المندوب')

@section('content')
<div class="nxl-content">
    <!-- [ page-header ] start -->
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">

            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('representatives-not-completed.index') }}">المندوبين</a></li>
                <li class="breadcrumb-item">تعديل</li>
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
                        <h5 class="card-title mb-0">تعديل المندوب</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('representatives-not-completed.update', $representative->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                <!-- الاسم -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">الاسم <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                           placeholder="أدخل الاسم" value="{{ old('name', $representative->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- رقم التليفون -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">رقم التليفون <span class="text-danger">*</span></label>
                                    <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror"
                                           placeholder="أدخل رقم التليفون (11 رقم)" value="{{ old('phone', $representative->phone) }}"
                                           maxlength="11" pattern="[0-9]{11}" required>
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- العنوان -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">عنوان السكن الحالي <span class="text-danger">*</span></label>
                                    <textarea name="address" class="form-control @error('address') is-invalid @enderror"
                                              placeholder="أدخل العنوان" rows="3" required>{{ old('address', $representative->address) }}</textarea>
                                    @error('address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">عنوان السكن في البطاقة <span class="text-danger">*</span></label>
                                    <textarea name="address_in_card" class="form-control @error('address_in_card') is-invalid @enderror"
                                              placeholder="أدخل العنوان" rows="3" required>{{ old('address_in_card', $representative->address_in_card) }}</textarea>
                                    @error('address_in_card')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- التواصل -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">التواصل </label>
                                    <input type="text" name="contact" class="form-control @error('contact') is-invalid @enderror"
                                           placeholder="أدخل رقم التواصل" value="{{ old('contact', $representative->contact) }}"
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
                                           placeholder="أدخل رقم البطاقة (14 رقم)" value="{{ old('national_id', $representative->national_id) }}"
                                           maxlength="14" pattern="[0-9]{14}" required>
                                    @error('national_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- المرتب -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">المرتب </label>
                                    <input type="number" name="salary" class="form-control @error('salary') is-invalid @enderror"
                                           placeholder="أدخل المرتب" value="{{ old('salary', $representative->salary) }}" step="0.01" min="0" >
                                    @error('salary')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- تاريخ بداية العمل -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">تاريخ بداية العمل</label>
                                    <input type="date" name="start_date" class="form-control @error('start_date') is-invalid @enderror"
                                           value="{{ old('start_date', $representative->start_date ? $representative->start_date->format('Y-m-d') : '') }}">
                                    <small class="text-muted">اختياري - يمكن تركه فارغاً</small>
                                    @error('start_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>


                                <div class="col-md-3 mb-3">
                                    <label class="form-label">مسؤول المتابعة</label>
                                    <select name="employee_id" class="form-control" required>
                                        <option value="">اختر الموظف</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}"
                                                @if(old('employee_id') == $user->id || (isset($representative) && $representative->employee_id == $user->id))
                                                    selected
                                                @endif
                                            >
                                                {{ $user->employee_name }}
                                            </option>
                                        @endforeach
                                    </select>

                                    @error('employee_id')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- الشركة -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">الشركة </label>
                                    <select name="company_id" class="form-control @error('company_id') is-invalid @enderror" >
                                        <option value="">اختر الشركة</option>
                                        @foreach($companies as $company)
                                            <option value="{{ $company->id }}"
                                                    {{ old('company_id', $representative->company_id) == $company->id ? 'selected' : '' }}>
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
                                           placeholder="أدخل رقم الحساب البنكي" value="{{ old('bank_account', $representative->bank_account) }}" >
                                    @error('bank_account')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- كود المندوب -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">كود المندوب </label>
                                    <input type="text" name="code" class="form-control @error('code') is-invalid @enderror"
                                           placeholder="أدخل كود المندوب في الشركة" value="{{ old('code', $representative->code) }}" >
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
                                            <option value="{{ $governorate->id }}"
                                                    {{ old('governorate_id', $representative->governorate_id) == $governorate->id ? 'selected' : '' }}>
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
                                    <select name="location_id" id="location_id" class="form-control @error('location_id') is-invalid @enderror">
                                        <option value="">اختر المنطقة</option>
                                        @foreach($locations as $location)
                                            <option value="{{ $location->id }}"
                                                    data-governorate="{{ $location->governorate_id }}"
                                                    {{ old('location_id', $representative->location_id) == $location->id ? 'selected' : '' }}>
                                                {{ $location->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('location_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>


{{--
                                <!-- المرفقات الحالية -->
                                @php
                                    $attachments = null;
                                    if ($representative->attachments) {
                                        if (is_string($representative->attachments)) {
                                            $attachments = json_decode($representative->attachments, true);
                                        } elseif (is_array($representative->attachments)) {
                                            $attachments = $representative->attachments;
                                        }
                                    }
                                @endphp
                              @if($attachments && is_array($attachments) && count($attachments) > 0)
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label">المرفقات الحالية</label>
                                        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-3">
                                            @foreach($attachments as $index => $attachment)
                                            <div class="col">
                                                <div class="card h-100">
                                                    <div class="card-body d-flex flex-column justify-content-between">
                                                        <small class="text-muted mb-2">
                                                            @switch($index)
                                                                @case(0) البطاقة (وجه أول) @break
                                                                @case(1) البطاقة (خلف) @break
                                                                @case(2) فيش @break
                                                                @case(3) شهادة ميلاد @break
                                                                @case(4) إيصال الأمانة @break
                                                                @case(5) رخصة القيادة @break
                                                                @case(6) رخصة السيارة وجه أول @break
                                                                @case(7) رخصة السيارة وجه ثاني @break
                                                                @case(8) إيصال مرافق @break
                                                                @case(9) مرفق بيانات الاستعلام @break

                                                                @default مرفق {{ $index + 1 }}
                                                            @endswitch
                                                        </small>
                                                        <div class="text-center mt-auto">
                                                            <a href="{{ route('representatives_no.attachment.view', ['id' => $representative->id, 'index' => $index]) }}"
                                                            target="_blank" class="btn btn-sm btn-outline-primary w-100">
                                                                <i class="feather-eye me-1"></i> عرض
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    @endif
                                --}}


                                @if(isset($representative->attachments_with_urls) && count($representative->attachments_with_urls) > 0)
                            <div class="row mt-4">
                                <div class="col-12">
                                    <label class="fw-bold">المرفقات:</label>
                                    <div class="row mt-2">
                                        @foreach($representative->attachments_with_urls as $attachment)
                                            @php
                                                $extension = strtolower(pathinfo($attachment['path'], PATHINFO_EXTENSION));
                                                $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'gif']);
                                            @endphp
                                            <div class="col-md-2 mb-3">
                                                <div class="card">
                                                    <div class="card-body p-3">
                                                        <div class="d-flex align-items-center mb-2">
                                                            @if($isImage)
                                                                <i class="feather-image me-2 text-success"></i>
                                                            @else
                                                                <i class="feather-file-text me-2 text-primary"></i>
                                                            @endif
                                                            <small class="text-muted fw-bold">
                                                                {{ $attachment['type'] ?? 'مرفق' }}
                                                            </small>
                                                        </div>

                                                        @if($isImage)
                                                            <div class="mb-2">
                                                                <img src="{{ $attachment['url'] }}"
                                                                     alt="معاينة المرفق"
                                                                     class="img-fluid rounded"
                                                                     style="max-height: 150px; width: 100%; object-fit: cover;">
                                                            </div>
                                                        @endif


                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>


                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="row mt-4">
                                <div class="col-12">
                                    <label class="fw-bold">المرفقات:</label>
                                    <div class="mt-2">
                                        <span class="text-muted">لا توجد مرفقات</span>
                                    </div>
                                </div>
                            </div>
                        @endif

                                <!-- إضافة مرفقات جديدة -->
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">إضافة مرفقات جديدة</label>
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
                                            <label class="form-label small">رخصة السيارة وجه  أول</label>
                                            <input type="file" name="attachments[]" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <label class="form-label small">رخصة السيارة وجه  ثاني</label>
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
                                               {{ old('inquiry_checkbox', $representative->inquiry_checkbox) ? 'checked' : '' }}>
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
                                              placeholder="أدخل بيانات الاستعلام أو المعلومات الإضافية المطلوبة" rows="2">{{ old('inquiry_data', $representative->inquiry_data ?? '') }}</textarea>
                                    <small class="text-muted">أدخل تفاصيل الاستعلام أو المعلومات المطلوبة</small>
                                    <div class="col-md-4 mb-2">
                                            <label class="form-label small">مرفق بيانات الاستعلام</label>
                                            <input type="file" name="attachments[]" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                                        </div>
                                    @error('inquiry_data')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- لوكيشن المنزل -->
                                <div class="col-md-12 mb-3">
                                    {{--<span class="text-danger">*</span>--}}
                                    <label class="form-label">لوكيشن المنزل </label>
                                    <input type="url" name="home_location" class="form-control @error('home_location') is-invalid @enderror"
                                           placeholder="أدخل رابط جوجل ماب للمنزل (مثال: https://maps.google.com/...)"
                                           value="{{ old('home_location', $representative->home_location) }}" >
                                    <small class="text-muted">يجب إدخال رابط جوجل ماب صحيح للمنزل</small>
                                    @error('home_location')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- الحالة -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">الحالة</label>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="is_active" value="1"
                                               {{ old('is_active', $representative->is_active) ? 'checked' : '' }}>
                                        <label class="form-check-label">نشط</label>
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
                                            {{ isset($representative->user) && str_contains($representative->user->type, 'supervisor') ? 'checked' : '' }}>

                                        <label class="form-check-label" for="isSupervisor">مشرف</label>
                                    </div>
                                </div>

                            </div>

                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('representatives-not-completed.show', $representative->id) }}" class="btn btn-light">إلغاء</a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="feather-save me-2"></i>
                                    تحديث المندوب
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

    governorateSelect.addEventListener('change', function() {
        const governorateId = this.value;
        const locationOptions = locationSelect.querySelectorAll('option');

        // Reset location select
        locationSelect.innerHTML = '<option value="">اختر الموقع</option>';

        if (governorateId) {
            // Show only locations for selected governorate
            locationOptions.forEach(option => {
                if (option.dataset.governorate === governorateId) {
                    locationSelect.appendChild(option.cloneNode(true));
                }
            });
        }
    }); */

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

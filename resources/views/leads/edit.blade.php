@extends('layouts.app')

@section('content')
<div class="nxl-content">
    <!-- [ page-header ] start -->
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('leads.index') }}">الرئيسية</a></li>
                <li class="breadcrumb-item">تعديل العميل المحتمل</li>
            </ul>
        </div>
        <div class="d-md-none d-flex align-items-center">
            <a href="javascript:void(0)" class="page-header-right-open-toggle">
                <i class="feather-align-right fs-20"></i>
            </a>
        </div>
    </div>
    <!-- [ page-header ] end -->

    <!-- [ Main Content ] start -->
    <div class="main-content">
        <div class="row">
            <div class="col-lg-12">
                <div class="card stretch stretch-full">
                    <div class="card-body">
                        <form action="{{ route('leads.update', $lead->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <!-- Lead Status Section -->
                            <div class="mb-5">
                                <h5 class="fw-bold mb-3">حالة العميل المحتمل</h5>
                                <div class="row">
                                    <!-- Status -->
                                    <div class="col-lg-4 mb-4">
                                        <label class="form-label">الحالة</label>
                                        <select name="status" class="form-control" required>
                                            <option value="">اختر الحالة</option>
                                            <option value="جديد" {{ old('status', $lead->status) == 'جديد' ? 'selected' : '' }} style="color: green;">جديد</option>
                                            <option value="قديم" {{ old('status', $lead->status) == 'قديم' ? 'selected' : '' }} style="color: red;">قديم</option>
                                            <option value="متابعة" {{ old('status', $lead->status) == 'متابعة' ? 'selected' : '' }} style="color: blue;">متابعة</option>
                                            <option value="غير مهتم" {{ old('status', $lead->status) == 'غير مهتم' ? 'selected' : '' }} style="color: gray;">غير مهتم</option>
                                            <option value="تم التحويل" {{ old('status', $lead->status) == 'تم التحويل' ? 'selected' : '' }} style="color: green;">تم التحويل</option>
                                        </select>
                                        @error('status')<div class="text-danger">{{ $message }}</div>@enderror
                                    </div>

                                    <!-- Source -->
                                    <div class="col-lg-4 mb-4">
                                        <label class="form-label">المصدر</label>
                                        <select name="source_id" class="form-control" required>
                                            <option value="">اختر المصدر</option>
                                            @foreach($sources as $source)
                                                <option value="{{ $source->id }}" {{ old('source_id', $lead->source_id) == $source->id ? 'selected' : '' }}>{{ $source->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('source_id')<div class="text-danger">{{ $message }}</div>@enderror
                                    </div>

                                    <!-- Assigned to -->
                                    <div class="col-lg-4 mb-4">
                                        <label class="form-label">مسؤول المتابعة</label>
                                        <select name="assigned_to" class="form-control">
                                            <option value="">اختر الموظف</option>
                                            @foreach($users as $user)
                                                <option value="{{ $user->id }}" {{ old('assigned_to', $lead->assigned_to) == $user->id ? 'selected' : '' }}>{{ $user->employee_name }}</option>
                                            @endforeach
                                        </select>
                                        @error('assigned_to')<div class="text-danger">{{ $message }}</div>@enderror
                                    </div>

                                    <!-- Next Follow Up -->
                                    <div class="col-lg-4 mb-4">
                                        <label class="form-label">تاريخ المتابعة القادمة</label>
                                        <input type="date" name="next_follow_up" class="form-control" value="{{ old('next_follow_up', $lead->next_follow_up) }}">
                                        @error('next_follow_up')<div class="text-danger">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                            </div>

                            <hr class="mt-0">

                            <!-- Lead Information Section -->
                            <div class="mb-5">
                                <h5 class="fw-bold mb-3">بيانات العميل المحتمل</h5>
                                <div class="row">
                                    <!-- Name -->
                                    <div class="col-lg-6 mb-4">
                                        <label for="name" class="form-label">الاسم *</label>
                                        <div class="input-group">
                                            <div class="input-group-text"><i class="feather-user"></i></div>
                                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                                   id="name" name="name" placeholder="الاسم الكامل"
                                                   value="{{ old('name', $lead->name) }}" required>
                                        </div>
                                        @error('name')<div class="text-danger">{{ $message }}</div>@enderror
                                    </div>

                                    <!-- Governorate -->
                                    <div class="col-lg-6 mb-4">
                                        <label for="governorate_id" class="form-label">المحافظة *</label>
                                        <div class="input-group">
                                            <div class="input-group-text"><i class="feather-map-pin"></i></div>
                                            <select id="governorate_id" name="governorate_id" class="form-control @error('governorate_id') is-invalid @enderror" required>
                                                <option value="">اختر المحافظة</option>
                                                @foreach($governorates as $governorate)
                                                    <option value="{{ $governorate->id }}" {{ old('governorate_id', $lead->governorate_id) == $governorate->id ? 'selected' : '' }}>
                                                        {{ $governorate->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        @error('governorate_id')<div class="text-danger">{{ $message }}</div>@enderror
                                    </div>

                                    <!-- Phone -->
                                    <div class="col-lg-6 mb-4">
                                        <label for="phone" class="form-label">رقم الهاتف *</label>
                                        <div class="input-group">
                                            <div class="input-group-text"><i class="feather-phone"></i></div>
                                            <input type="text" class="form-control @error('phone') is-invalid @enderror"
                                                   id="phone" name="phone" placeholder="أدخل رقم الهاتف"
                                                   value="{{ old('phone', $lead->phone) }}" required>
                                        </div>
                                        @error('phone')<div class="text-danger">{{ $message }}</div>@enderror
                                    </div>

                                    <!-- Location -->
                                    <div class="col-lg-6 mb-4">
                                        <label for="location_id" class="form-label">المكان</label>
                                        <div class="input-group">
                                            <div class="input-group-text"><i class="feather-map-pin"></i></div>
                                            <select name="location_id" id="location_id" class="form-control @error('location_id') is-invalid @enderror">
                                                <option value="">اختر المكان</option>
                                            </select>
                                        </div>
                                        @error('location_id')<div class="text-danger">{{ $message }}</div>@enderror
                                    </div>

                                    <!-- Notes -->
                                    <div class="col-lg-12 mb-4">
                                        <label for="notes" class="form-label">ملاحظات</label>
                                        <div class="input-group">
                                            <div class="input-group-text"><i class="feather-type"></i></div>
                                            <textarea class="form-control" id="notes" name="notes"
                                                      cols="30" rows="5" placeholder="أدخل أي ملاحظات إضافية">{{ old('notes', $lead->notes) }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Buttons -->
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('leads.index') }}" class="btn btn-light">إلغاء</a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="feather-user-plus me-2"></i>
                                    تعديل العميل المحتمل
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


<script>
document.addEventListener('DOMContentLoaded', function() {
    // Phone validation
    const phoneInput = document.getElementById('phone');
    phoneInput.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value.length > 11) value = value.substring(0, 11);
        e.target.value = value;
    });

    const governorateSelect = document.getElementById('governorate_id');
    const locationSelect = document.getElementById('location_id');

    function loadLocations(governorateId, selectedLocation = null) {
        if (!governorateId) {
            locationSelect.innerHTML = '<option value="">اختر المكان</option>';
            return;
        }

        fetch(`{{ url('getlocations') }}/${governorateId}`)
            .then(res => res.json())
            .then(data => {
                locationSelect.innerHTML = '<option value="">اختر المكان</option>';
                data.forEach(loc => {
                    const selected = loc.id == selectedLocation ? 'selected' : '';
                    locationSelect.innerHTML += `<option value="${loc.id}" ${selected}>${loc.name}</option>`;
                });
            })
            .catch(err => {
                console.error(err);
                locationSelect.innerHTML = '<option value="">خطأ في تحميل البيانات</option>';
            });
    }

    governorateSelect.addEventListener('change', function() {
        loadLocations(this.value);
    });

    // Load locations for old value (editing existing lead)
    const oldGov = "{{ old('governorate_id', $lead->governorate_id) }}";
    const oldLoc = "{{ old('location_id', $lead->location_id) }}";
    if (oldGov) loadLocations(oldGov, oldLoc);
});
</script>

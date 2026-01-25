@extends('layouts.app')

@section('title', 'تعديل المشرف')

@section('content')
<div class="nxl-content">
    <!-- [ page-header ] start -->
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="m-b-10">المشرفين</h5>
            </div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('supervisors.index') }}">المشرفين</a></li>
                <li class="breadcrumb-item"><a href="{{ route('supervisors.show', $supervisor->id) }}">عرض</a></li>
                <li class="breadcrumb-item">تعديل</li>
            </ul>
        </div>
        <div class="page-header-right ms-auto">
            <div class="page-header-right-items">
                <a href="{{ route('supervisors.show', $supervisor->id) }}" class="btn btn-light-brand">
                    <i class="feather-arrow-left me-2"></i>
                    <span>العودة للمشرف</span>
                </a>
            </div>
        </div>
    </div>
    <!-- [ page-header ] end -->

    <!-- [ Main Content ] start -->
    <div class="main-content">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">تعديل المشرف</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('supervisors.update', $supervisor->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">الاسم <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                           placeholder="أدخل الاسم" value="{{ old('name', $supervisor->name) }}" required>
                                    @error('name')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">رقم التليفون <span class="text-danger">*</span></label>
                                    <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" 
                                           placeholder="أدخل رقم التليفون (11 رقم)" value="{{ old('phone', $supervisor->phone) }}" maxlength="11" required>
                                    @error('phone')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">التواصل <span class="text-danger">*</span></label>
                                    <input type="text" name="contact" class="form-control @error('contact') is-invalid @enderror" 
                                           placeholder="أدخل رقم التواصل (11 رقم)" value="{{ old('contact', $supervisor->contact) }}" maxlength="11" required>
                                    @error('contact')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">المحافظة</label>
                                    <select name="governorate_id" id="governorate_id" class="form-control @error('governorate_id') is-invalid @enderror" required>
                                        <option value="">اختر المحافظة</option>
                                        @foreach($governorates as $governorate)
                                            <option value="{{ $governorate->id }}" {{ old('governorate_id', $supervisor->governorate_id) == $governorate->id ? 'selected' : '' }}>
                                                {{ $governorate->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('governorate_id')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                   <label class="form-label">>المقر المسؤول عنه المشرف  <span class="text-danger">*</span></label>
                                   <select name="location_id" id="location_id" class="form-control @error('location_id') is-invalid @enderror" >
                                       <option value="">اختر المقر</option>
                                       @foreach($locations as $location)
                                           <option value="{{ $location->id }}" 
                                                   data-governorate="{{ $location->governorate_id }}"
                                                   {{ old('location_id', $supervisor->location_id) == $location->id ? 'selected' : '' }}>
                                               {{ $location->name }}
                                           </option>
                                       @endforeach
                                   </select>
                                   @error('location_id')
                                       <div class="text-danger">{{ $message }}</div>
                                   @enderror
                               </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">رقم البطاقة <span class="text-danger">*</span></label>
                                    <input type="text" name="national_id" class="form-control @error('national_id') is-invalid @enderror" 
                                           placeholder="أدخل رقم البطاقة (14 رقم)" value="{{ old('national_id', $supervisor->national_id) }}" maxlength="14" required>
                                    @error('national_id')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">المرتب <span class="text-danger">*</span></label>
                                    <input type="number" name="salary" class="form-control @error('salary') is-invalid @enderror" 
                                           placeholder="أدخل المرتب" value="{{ old('salary', $supervisor->salary) }}" min="0" step="0.01" required>
                                    @error('salary')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">تاريخ بداية العمل <span class="text-danger">*</span></label>
                                    <input type="date" name="start_date" class="form-control @error('start_date') is-invalid @enderror" 
                                           value="{{ old('start_date', $supervisor->start_date ? $supervisor->start_date->format('Y-m-d') : '') }}" required>
                                    @error('start_date')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-12 mb-3">
                                     <label class="form-label">المندوبين المسؤول عنهم</label>
                                     <select name="representative_ids[]" id="representative_ids" class="form-control select2" multiple>
                                         <option value="">اختر الممثلين</option>
                                         @foreach($supervisor->representatives as $rep)
                                             <option value="{{ $rep->id }}" selected>{{ $rep->name }}</option>
                                         @endforeach
                                     </select>
                                     <small class="form-text text-muted">يمكنك اختيار أكثر من ممثل (سيتم تصفية الممثلين حسب المحافظة والمقر المحددين)</small>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">الحالة</label>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="is_active" value="1" {{ old('is_active', $supervisor->is_active) ? 'checked' : '' }}>
                                        <label class="form-check-label">نشط</label>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('supervisors.show', $supervisor->id) }}" class="btn btn-light">إلغاء</a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="feather-save me-2"></i>
                                    تحديث المشرف
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

@push('scripts')
<script>
$(document).ready(function() {
    const $governorate = $('#governorate_id');
    const $location = $('#location_id');
    const $representative = $('#representative_ids');

    // Initialize Select2
    $representative.select2({
        placeholder: "اختر الممثلين",
        allowClear: true,
        width: '100%'
    });

    // Keep original location options
    const $allLocations = $location.find('option[data-governorate]').clone();

    function filterLocations(governorateId) {
        $location.html('<option value="">اختر المقر</option>');
        $allLocations.each(function() {
            if (!governorateId || $(this).data('governorate') == governorateId) {
                $location.append($(this).clone());
            }
        });
    }

    function populateRepresentatives(data) {
        $representative.empty();
        data.forEach(rep => {
            const option = new Option(rep.name, rep.id, false, false);
            $representative.append(option);
        });
        $representative.trigger('change');
    }

    async function fetchRepresentatives(governorateId, locationId) {
        let url = '';
        if(locationId) url = `/representatives/by-location/${locationId}`;
        else if(governorateId) url = `/representatives/by-governorate/${governorateId}`;
        else {
            $representative.empty().trigger('change');
            return;
        }

        try {
            const res = await fetch(url);
            if(!res.ok) throw new Error('Network response was not ok');
            const data = await res.json();
            populateRepresentatives(data);
        } catch(err) {
            console.error('Error fetching representatives:', err);
        }
    }

    $governorate.on('change', function() {
        filterLocations($(this).val());
        fetchRepresentatives($(this).val(), $location.val());
    });

    $location.on('change', function() {
        fetchRepresentatives($governorate.val(), $(this).val());
    });

    // Initial load
    if($governorate.val()) filterLocations($governorate.val());
    fetchRepresentatives($governorate.val(), $location.val());

    // Input validations
    ['phone','contact','national_id'].forEach(function(name){
        $(`input[name="${name}"]`).on('input', function() {
            this.value = this.value.replace(/[^0-9]/g,'');
            if(name == 'phone' || name == 'contact') this.value = this.value.slice(0,11);
            if(name == 'national_id') this.value = this.value.slice(0,14);
        });
    });
});
</script>
@endpush

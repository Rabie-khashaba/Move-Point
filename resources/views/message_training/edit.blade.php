@extends('layouts.app')

@section('title', 'تعديل الرسالة')

@section('content')
<div class="nxl-content">
    <!-- [ page-header ] start -->
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">

            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('messagesTraining.index') }}">الرسائل</a></li>
                <li class="breadcrumb-item">تعديل</li>
            </ul>
        </div>
        <div class="page-header-right ms-auto">
            <div class="page-header-right-items">
                <a href="{{ route('messagesTraining.index') }}" class="btn btn-light-brand">
                    <i class="feather-arrow-left me-2"></i>
                    <span>الرجوع الى الرسائل</span>
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
                        <h5 class="card-title mb-0">تعديل الرسالة</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('messagesTraining.update', $message->id) }}" method="POST">
                            @csrf
                            @method('PUT') <!-- To handle PUT request for update -->

                            <div class="row">
                                <!-- المحافظة -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">المحافظة <span class="text-danger">*</span></label>
                                    <select name="government_id" id="government_id" class="form-control @error('government_id') is-invalid @enderror" required>
                                        <option value="">اختر المحافظة</option>
                                        @foreach($governments as $governorate)
                                            <option value="{{ $governorate->id }}"
                                                    {{ old('government_id', $message->government_id) == $governorate->id ? 'selected' : '' }}>
                                                {{ $governorate->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('government_id')
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
                                                    {{ old('location_id', $message->location_id) == $location->id ? 'selected' : '' }}>
                                                {{ $location->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('location_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>


                                <div class="col-md-6 mb-3">
                                    <label class="form-label"> الشركة <span class="text-danger">*</span></label>
                                    <select name="company_id" id="company_id" class="form-control" required>
                                        <option value="">اختر الشركة</option>
                                        @foreach($companies as $company)
                                            <option value="{{ $company->id }}"
                                                {{ (old('company_id', $message->company_id ?? '') == $company->id) ? 'selected' : '' }}>
                                                {{ $company->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>


                                <!-- Description Field -->
                                <!-- الوصف -->
                                @if($message->type === 'أونلاين')
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label">الوصف</label>
                                        <textarea name="description"
                                                  class="form-control @error('description_training') is-invalid @enderror"
                                                  rows="4"
                                                  placeholder="أدخل الوصف">{{ old('description_training', $message->description_training) }}</textarea>
                                        @error('description_training')
                                        <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                @elseif($message->type === 'في المقر')
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label">الوصف</label>
                                        <textarea name="description"
                                                  class="form-control @error('description_location') is-invalid @enderror"
                                                  rows="4"
                                                  placeholder="أدخل الوصف">{{ old('description_location', $message->description_location) }}</textarea>
                                        @error('description_location')
                                        <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                @endif


                                <!-- Google Maps URL Field -->
                                @if($message->type === 'أونلاين')
                                    <!-- رابط التدريب -->
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label">رابط التدريب</label>
                                        <input type="url" name="google_map_url"
                                               class="form-control @error('link_training') is-invalid @enderror"
                                               placeholder="أدخل رابط التدريب"
                                               value="{{ old('link_training', $message->link_training) }}">
                                        @error('link_training')
                                        <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                @elseif($message->type === 'في المقر')
                                    <!-- رابط الخريطة -->
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label">رابط الخريطة</label>
                                        <input type="url" name="google_map_url"
                                               class="form-control @error('google_map_url') is-invalid @enderror"
                                               placeholder="أدخل رابط الخريطة"
                                               value="{{ old('google_map_url', $message->google_map_url) }}">
                                        @error('google_map_url')
                                        <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                @endif
                            </div>

                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('messagesTraining.index') }}" class="btn btn-light">إلغاء</a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="feather-save me-2"></i>
                                    حفظ التعديلات
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
    const governorateSelect = document.getElementById('government_id');
    const locationSelect = document.getElementById('location_id');

    // Function to filter locations
    function filterLocations(governorateId) {
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
    }

    governorateSelect.addEventListener('change', function() {
        filterLocations(this.value);
    });

    // If governorate is pre-selected (from lead), filter locations on page load
    if (governorateSelect.value) {
        filterLocations(governorateSelect.value);
    }
});
</script>
@endsection

@extends('layouts.app')

@section('title', 'إضافة رسالة جديدة')

@section('content')
<div class="nxl-content">
    <!-- [ page-header ] start -->
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="m-b-10">الرسائل</h5>
            </div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('messages.index') }}">الرسائل</a></li>
                <li class="breadcrumb-item">إنشاء</li>
            </ul>
        </div>
        <div class="page-header-right ms-auto">
            <div class="page-header-right-items">
                <a href="{{ route('messages.index') }}" class="btn btn-light-brand">
                    <i class="feather-arrow-left me-2"></i>
                    <span>Back to Messages</span>
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
                        <h5 class="card-title mb-0">إنشاء رسالة جديدة</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('messages.store') }}" method="POST">
                            @csrf
                            
                            <div class="row">
                                <!-- المحافظة -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">المحافظة <span class="text-danger">*</span></label>
                                    <select name="government_id" id="government_id" class="form-control @error('governorate_id') is-invalid @enderror" required>
                                        <option value="">اختر المحافظة</option>
                                        @foreach($governments as $governorate)
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

                                <!-- Description Field -->
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">الوصف</label>
                                    <textarea name="description" class="form-control" rows="4" placeholder="أدخل الوصف">{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Google Maps URL Field -->
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">رابط Google Maps</label>
                                    <input type="url" name="google_map_url" class="form-control @error('google_map_url') is-invalid @enderror" 
                                           placeholder="أدخل رابط الخريطة" value="{{ old('google_map_url') }}">
                                    @error('google_map_url')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('messages.index') }}" class="btn btn-light">إلغاء</a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="feather-save me-2"></i>
                                    إنشاء الرسالة
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
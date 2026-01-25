@extends('layouts.app')

@section('title', 'إضافة رسالة جديدة')

<style>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
</style>
@section('content')
    <div class="nxl-content">
        <!-- [ page-header ] start -->
        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">الرسائل</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('messagesTraining.index') }}">الرسائل</a></li>
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
                            <h5 class="card-title mb-0">إنشاء رسالة جديدة</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('messagesTraining.store') }}" method="POST">
                                @csrf

                                <!-- نوع التدريب -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">نوع التدريب <span class="text-danger">*</span></label>
                                    <select name="type" id="training_type" class="form-control" required>
                                        <option value="">اختر نوع التدريب</option>
                                        <option value="أونلاين">أونلاين</option>
                                        <option value="في المقر">في المقر</option>
                                    </select>
                                </div>

                                <!-- فورم الأونلاين -->
                                <div id="onlineForm" style="display:none;">

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">المحافظة <span class="text-danger">*</span></label>
                                            <select name="government_id" id="online_governorate_id" class="form-control ">
                                                <option value="">اختر المحافظة</option>
                                                @foreach($governments as $governorate)
                                                    <option value="{{ $governorate->id }}">{{ $governorate->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">المنطقة</label>
                                            <select name="location_id" id="online_location_id" class="form-control ">
                                                <option value="">اختر المنطقة</option>
                                                @foreach($locations as $location)
                                                    <option value="{{ $location->id }}"
                                                        data-governorate="{{ $location->governorate_id }}">
                                                        {{ $location->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label"> الشركة <span class="text-danger">*</span></label>
                                        <select name="company_id" id="company_id" class="form-control">
                                            <option value="">اختر الشركة</option>
                                            @foreach($companies as $company)
                                                <option value="{{ $company->id }}">{{ $company->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">رابط التدريب</label>
                                            <input type="url" name="link_training" class="form-control "
                                                placeholder="أدخل لينك التدريب">
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">الوصف</label>
                                            <textarea name="description_training" class="form-control" rows="2"
                                                placeholder="أدخل الوصف"></textarea>
                                        </div>
                                    </div>
                                </div>


                                <!-- فورم في المقر -->
                                <div id="offlineForm" style="display:none;">

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">المحافظة <span class="text-danger">*</span></label>
                                            <select name="government_id" id="offline_governorate_id" class="form-control ">
                                                <option value="">اختر المحافظة</option>
                                                @foreach($governments as $governorate)
                                                    <option value="{{ $governorate->id }}">{{ $governorate->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">المنطقة</label>
                                            <select name="location_id" id="offline_location_id" class="form-control ">
                                                <option value="">اختر المنطقة</option>
                                                @foreach($locations as $location)
                                                    <option value="{{ $location->id }}"
                                                        data-governorate="{{ $location->governorate_id }}">
                                                        {{ $location->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>


                                    <div class="col-md-6 mb-3">
                                        <label class="form-label"> الشركة <span class="text-danger">*</span></label>
                                        <select name="company_id" id="company_id" class="form-control">
                                            <option value="">اختر الشركة</option>
                                            @foreach($companies as $company)
                                                <option value="{{ $company->id }}">{{ $company->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="row">


                                        <div class="col-md-6 mb-3 ">
                                            <label class="form-label">رابط Google Maps</label>
                                            <input type="url" name="google_map_url" class="form-control "
                                                placeholder="أدخل رابط الخريطة">
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">الوصف</label>
                                            <textarea name="description_location" class="form-control" rows="2"
                                                placeholder="أدخل الوصف"></textarea>
                                        </div>
                                    </div>


                                </div>



                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('messagesTraining.index') }}" class="btn btn-light">إلغاء</a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="feather-save me-2"></i> إنشاء الرسالة
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

    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function () {
            // أي select عليه الكلاس ده هيتحول Select2
            $('.select2').select2({
                placeholder: "اختر من القائمة",
                allowClear: true
            });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const trainingType = document.getElementById('training_type');
            const onlineForm = document.getElementById('onlineForm');
            const offlineForm = document.getElementById('offlineForm');

            // Toggle forms
            trainingType.addEventListener('change', function () {
                if (this.value === 'أونلاين') {
                    toggleForm('أونلاين');
                } else if (this.value === 'في المقر') {
                    toggleForm('في المقر');
                } else {
                    onlineForm.style.display = 'none';
                    offlineForm.style.display = 'none';
                }
            });

            function toggleForm(type) {
                if (type === 'أونلاين') {
                    onlineForm.style.display = 'block';
                    offlineForm.style.display = 'none';

                    enableFields('#onlineForm');
                    disableFields('#offlineForm');
                } else {
                    onlineForm.style.display = 'none';
                    offlineForm.style.display = 'block';

                    enableFields('#offlineForm');
                    disableFields('#onlineForm');
                }
            }

            function enableFields(selector) {
                document.querySelectorAll(selector + ' select, ' + selector + ' input, ' + selector + ' textarea')
                    .forEach(el => el.disabled = false);
            }

            function disableFields(selector) {
                document.querySelectorAll(selector + ' select, ' + selector + ' input, ' + selector + ' textarea')
                    .forEach(el => el.disabled = true);
            }

            // فلترة المناطق حسب المحافظة (اونلاين فقط)
            document.getElementById('online_governorate_id')?.addEventListener('change', function () {
                filterLocations(this.value, 'online_location_id');
            });

            // فلترة المناطق حسب المحافظة (اوفلاين فقط)
            document.getElementById('offline_governorate_id')?.addEventListener('change', function () {
                filterLocations(this.value, 'offline_location_id');
            });

            function filterLocations(governorateId, locationSelectId) {
                const locationSelect = document.getElementById(locationSelectId);
                const allOptions = document.querySelectorAll(`#${locationSelectId} option`);

                locationSelect.innerHTML = '<option value="">اختر المنطقة</option>';
                allOptions.forEach(option => {
                    if (option.dataset.governorate === governorateId) {
                        locationSelect.appendChild(option.cloneNode(true));
                    }
                });
            }
        });
    </script>
@endsection
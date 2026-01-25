<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>تسجيل مندوب جديد - Black Horse</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg,rgb(255, 255, 255) 0%,rgb(255, 255, 255) 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .registration-container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
            margin: 2rem auto;
            max-width: 800px;
        }
        .form-header {
            background: linear-gradient(135deg,rgb(239, 105, 105) 0%,rgb(162, 75, 75) 100%);
            color: white;
            padding: 2rem;
            border-radius: 20px 20px 0 0;
            text-align: center;
        }
        .form-body {
            padding: 2rem;
        }
        .form-control:focus {
            border-color:rgb(234, 102, 102);
            box-shadow: 0 0 0 0.2rem rgba(234, 102, 102, 0.25);
        }
        .btn-primary {
            background: linear-gradient(135deg,rgb(234, 102, 102) 0%,rgb(157, 61, 61) 100%);
            border: none;
            padding: 12px 30px;
            border-radius: 25px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }
        .form-label {
            font-weight: 600;
            color: #333;
            margin-bottom: 0.5rem;
        }
        .required {
            color: #dc3545;
        }
        .file-upload-area {
            border: 2px dashed #667eea;
            border-radius: 10px;
            padding: 1rem;
            text-align: center;
            background: #f8f9ff;
            transition: all 0.3s ease;
        }
        .file-upload-area:hover {
            background: #e8edff;
            border-color: #5a67d8;
        }
        .file-preview {
            margin-top: 1rem;
            padding: 0.5rem;
            background: #e8f5e8;
            border-radius: 5px;
            font-size: 0.9rem;
        }
        .alert {
            border-radius: 10px;
            border: none;
        }
        .loading {
            display: none;
        }
        .spinner-border-sm {
            width: 1rem;
            height: 1rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="registration-container">
            <div class="form-header">
                <h2><i class="fas fa-user-plus me-2"></i>تسجيل مندوب جديد</h2>
                <p class="mb-0">انضم إلى فريق عملنا كمندوب </p>
            </div>

            <div class="form-body">
                <!-- Alert Container -->
                <div id="alertContainer"></div>

                <form id="representativeForm" enctype="multipart/form-data">
                    @csrf

                    <!-- Personal Information -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h5 class="text-primary mb-3"><i class="fas fa-user me-2"></i>البيانات الشخصية</h5>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">الاسم الكامل <span class="required">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" required>
                            <div class="invalid-feedback" id="name-error"></div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="phone" class="form-label">رقم الهاتف <span class="required">*</span></label>
                            <input type="tel" class="form-control" id="phone" name="phone" pattern="[0-9]{11}" maxlength="11" required>
                            <div class="invalid-feedback" id="phone-error"></div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="national_id" class="form-label">رقم البطاقة <span class="required">*</span></label>
                            <input type="text" class="form-control" id="national_id" name="national_id" pattern="[0-9]{14}" maxlength="14" required>
                            <div class="invalid-feedback" id="national_id-error"></div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="contact" class="form-label">رقم التواصل <span class="required">*</span></label>
                            <input type="tel" class="form-control" id="contact" name="contact" pattern="[0-9]{11}" maxlength="11" required>
                            <div class="invalid-feedback" id="contact-error"></div>
                        </div>

                        <div class="col-12 mb-3">
                            <label for="address" class="form-label">العنوان <span class="required">*</span></label>
                            <textarea class="form-control" id="address" name="address" rows="3" required></textarea>
                            <div class="invalid-feedback" id="address-error"></div>
                        </div>
                    </div>

                    <!-- Location Information -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h5 class="text-primary mb-3"><i class="fas fa-map-marker-alt me-2"></i>الموقع</h5>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="governorate_id" class="form-label">المحافظة <span class="required">*</span></label>
                            <select class="form-control" id="governorate_id" name="governorate_id" required>
                                <option value="">اختر المحافظة</option>
                                @foreach($governorates as $governorate)
                                    <option value="{{ $governorate->id }}">{{ $governorate->name }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback" id="governorate_id-error"></div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="location_id" class="form-label">المنطقة</label>
                            <select class="form-control" id="location_id" name="location_id">
                                <option value="">اختر المنطقة</option>
                                @foreach($locations as $location)
                                    <option value="{{ $location->id }}" data-governorate="{{ $location->governorate_id }}">
                                        {{ $location->name }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback" id="location_id-error"></div>
                        </div>

                        <div class="col-12 mb-3">
                            <label for="home_location" class="form-label">موقع المنزل (رابط خرائط جوجل) </label>
                            <input type="url" class="form-control" id="home_location" name="home_location" placeholder="https://maps.google.com/..." >
                            <div class="invalid-feedback" id="home_location-error"></div>
                        </div>
                    </div>

                    <!-- Work Information -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h5 class="text-primary mb-3"><i class="fas fa-briefcase me-2"></i>بيانات العمل</h5>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="company_id" class="form-label">الشركة <span class="required">*</span></label>
                            <select class="form-control" id="company_id" name="company_id" required>
                                <option value="">اختر الشركة</option>
                                @foreach($companies as $company)
                                    <option value="{{ $company->id }}">{{ $company->name }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback" id="company_id-error"></div>
                        </div>



                        <div class="col-md-6 mb-3">
                            <label for="bank_account" class="form-label">رقم المحفظة</label>
                            <input type="text" class="form-control" id="bank_account" name="bank_account">
                            <div class="invalid-feedback" id="bank_account-error"></div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="code" class="form-label">كود المندوب إذا وجد </label>
                            <input type="text" class="form-control" id="code" name="code">
                            <div class="invalid-feedback" id="code-error"></div>
                        </div>
                    </div>

                    <!-- File Attachments -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h5 class="text-primary mb-3"><i class="fas fa-paperclip me-2"></i>المرفقات</h5>
                            <p class="text-muted">يرجى رفع المرفقات المطلوبة (PDF, JPG, PNG - الحد الأقصى 2MB لكل ملف). البطاقة (وجه أول وخلف) مطلوبة.</p>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="attachments_0" class="form-label">البطاقة (وجه أول) <span class="required">*</span></label>
                            <div class="file-upload-area">
                                <input type="file" class="form-control" id="attachments_0" name="attachments[]" accept=".pdf,.jpg,.jpeg,.png" required>
                                <div class="file-preview" id="preview_0" style="display: none;"></div>
                            </div>
                            <div class="invalid-feedback" id="attachments.0-error"></div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="attachments_1" class="form-label">البطاقة (خلف) <span class="required">*</span></label>
                            <div class="file-upload-area">
                                <input type="file" class="form-control" id="attachments_1" name="attachments[]" accept=".pdf,.jpg,.jpeg,.png" required>
                                <div class="file-preview" id="preview_1" style="display: none;"></div>
                            </div>
                            <div class="invalid-feedback" id="attachments.1-error"></div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="attachments_2" class="form-label">(اختياري) فيش</label>
                            <div class="file-upload-area">
                                <input type="file" class="form-control" id="attachments_2" name="attachments[]" accept=".pdf,.jpg,.jpeg,.png">
                                <div class="file-preview" id="preview_2" style="display: none;"></div>
                            </div>
                            <div class="invalid-feedback" id="attachments.2-error"></div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="attachments_3" class="form-label">(اختياري) شهادة ميلاد</label>
                            <div class="file-upload-area">
                                <input type="file" class="form-control" id="attachments_3" name="attachments[]" accept=".pdf,.jpg,.jpeg,.png">
                                <div class="file-preview" id="preview_3" style="display: none;"></div>
                            </div>
                            <div class="invalid-feedback" id="attachments.3-error"></div>
                        </div>


                        <div class="col-md-4 mb-3">
                            <label for="attachments_3" class="form-label">ايصاال الامانه</label>
                            <div class="file-upload-area">
                                <input type="file" class="form-control" id="attachments_4" name="attachments[]" accept=".pdf,.jpg,.jpeg,.png">
                                <div class="file-preview" id="preview_4" style="display: none;"></div>
                            </div>
                            <div class="invalid-feedback" id="attachments.4-error"></div>
                        </div>


                        <div class="col-md-4 mb-3">
                            <label for="attachments_5" class="form-label">(اختياري) رخصة القيادة</label>
                            <div class="file-upload-area">
                                <input type="file" class="form-control" id="attachments_5" name="attachments[]" accept=".pdf,.jpg,.jpeg,.png">
                                <div class="file-preview" id="preview_5" style="display: none;"></div>
                            </div>
                            <div class="invalid-feedback" id="attachments.5-error"></div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="attachments_6" class="form-label">(اختياري) رخصة السيارة وجه أول</label>
                            <div class="file-upload-area">
                                <input type="file" class="form-control" id="attachments_6" name="attachments[]" accept=".pdf,.jpg,.jpeg,.png">
                                <div class="file-preview" id="preview_6" style="display: none;"></div>
                            </div>
                            <div class="invalid-feedback" id="attachments.6-error"></div>
                        </div>


                        <div class="col-md-4 mb-3">
                            <label for="attachments_7" class="form-label">(اختياري) رخصة السيارة وجه ثاني</label>
                            <div class="file-upload-area">
                                <input type="file" class="form-control" id="attachments_7" name="attachments[]" accept=".pdf,.jpg,.jpeg,.png">
                                <div class="file-preview" id="preview_7" style="display: none;"></div>
                            </div>
                            <div class="invalid-feedback" id="attachments.7-error"></div>
                        </div>
                    </div>



                    <!-- Submit Button -->
                    <div class="text-center">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <span class="loading">
                                <span class="spinner-border spinner-border-sm me-2" role="status"></span>
                                جاري الإرسال...
                            </span>
                            <span class="submit-text">
                                <i class="fas fa-paper-plane me-2"></i>إرسال الطلب
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('representativeForm');
            const alertContainer = document.getElementById('alertContainer');
            const submitBtn = form.querySelector('button[type="submit"]');
            const loadingSpan = submitBtn.querySelector('.loading');
            const submitTextSpan = submitBtn.querySelector('.submit-text');

            // Test alert container
            console.log('Alert container found:', !!alertContainer);
            if (alertContainer) {
                console.log('Alert container element:', alertContainer);
            }

            // Handle governorate change to filter locations (client-side)
            const governorateSelect = document.getElementById('governorate_id');
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
            });

            // If governorate is pre-selected, filter locations on page load
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

            // Handle file preview
            document.querySelectorAll('input[type="file"]').forEach(input => {
                input.addEventListener('change', function() {
                    const file = this.files[0];
                    const preview = document.getElementById('preview_' + this.id.split('_')[1]);

                    if (file) {
                        preview.textContent = `تم اختيار: ${file.name} (${(file.size / 1024 / 1024).toFixed(2)} MB)`;
                        preview.style.display = 'block';
                    } else {
                        preview.style.display = 'none';
                    }
                });
            });

            // Client-side validation function
            function validateForm() {
                let isValid = true;

                // Required fields validation
                const requiredFields = [
                    { id: 'name', message: 'الاسم مطلوب' },
                    { id: 'phone', message: 'رقم الهاتف مطلوب' },
                    { id: 'national_id', message: 'رقم البطاقة مطلوب' },
                    { id: 'contact', message: 'رقم التواصل مطلوب' },
                    { id: 'address', message: 'العنوان مطلوب' },
                    { id: 'governorate_id', message: 'المحافظة مطلوبة' },
                    { id: 'company_id', message: 'الشركة مطلوبة' }
                ];

                requiredFields.forEach(field => {
                    const element = document.getElementById(field.id);
                    const errorElement = document.getElementById(field.id + '-error');

                    if (!element.value.trim()) {
                        element.classList.add('is-invalid');
                        errorElement.textContent = field.message;
                        isValid = false;
                    }
                });

                // Phone number validation
                const phone = document.getElementById('phone').value;
                if (phone && !/^[0-9]{11}$/.test(phone)) {
                    const phoneElement = document.getElementById('phone');
                    const phoneError = document.getElementById('phone-error');
                    phoneElement.classList.add('is-invalid');
                    phoneError.textContent = 'رقم الهاتف يجب أن يكون 11 رقم';
                    isValid = false;
                }

                // National ID validation
                const nationalId = document.getElementById('national_id').value;
                if (nationalId && !/^[0-9]{14}$/.test(nationalId)) {
                    const nationalIdElement = document.getElementById('national_id');
                    const nationalIdError = document.getElementById('national_id-error');
                    nationalIdElement.classList.add('is-invalid');
                    nationalIdError.textContent = 'رقم البطاقة يجب أن يكون 14 رقم';
                    isValid = false;
                }

                // Contact validation
                const contact = document.getElementById('contact').value;
                if (contact && !/^[0-9]{11}$/.test(contact)) {
                    const contactElement = document.getElementById('contact');
                    const contactError = document.getElementById('contact-error');
                    contactElement.classList.add('is-invalid');
                    contactError.textContent = 'رقم التواصل يجب أن يكون 11 رقم';
                    isValid = false;
                }

                // Required file validation
                const requiredFiles = ['attachments_0', 'attachments_1'];
                requiredFiles.forEach(fileId => {
                    const fileElement = document.getElementById(fileId);
                    const fileError = document.getElementById(fileId.replace('_', '.') + '-error');

                    if (!fileElement.files.length) {
                        fileElement.classList.add('is-invalid');
                        fileError.textContent = 'هذا المرفق مطلوب';
                        isValid = false;
                    }
                });

                // File size validation
                document.querySelectorAll('input[type="file"]').forEach(input => {
                    if (input.files.length > 0) {
                        const file = input.files[0];
                        const maxSize = 2 * 1024 * 1024; // 2MB

                        if (file.size > maxSize) {
                            const errorElement = document.getElementById(input.id.replace('_', '.') + '-error');
                            input.classList.add('is-invalid');
                            errorElement.textContent = 'حجم الملف يجب أن يكون أقل من 2MB';
                            isValid = false;
                        }
                    }
                });

                if (!isValid) {
                    // Don't show generic alert here - let individual field errors show
                    console.log('Client-side validation failed');
                }

                return isValid;
            }

            // Form submission
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                // Clear previous errors
                clearErrors();

                // Client-side validation
                if (!validateForm()) {
                    return;
                }

                // Show loading state
                loadingSpan.style.display = 'inline';
                submitTextSpan.style.display = 'none';
                submitBtn.disabled = true;

                // Prepare form data
                const formData = new FormData(form);

                // Submit form with timeout
                const controller = new AbortController();
                const timeoutId = setTimeout(() => controller.abort(), 30000); // 30 second timeout

                fetch('{{ route("public.representative.store") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ||
                                      document.querySelector('input[name="_token"]').value
                    },
                    body: formData,
                    signal: controller.signal
                })
                .then(response => {
                    console.log('Response status:', response.status);
                    console.log('Response ok:', response.ok);

                    // Always try to parse JSON, even for error responses
                    return response.json().then(data => {
                        console.log('Parsed response data:', data);

                        // Check if response is ok
                        if (!response.ok) {
                            // For 422 errors, we still want to process the JSON data
                            if (response.status === 422) {
                                console.log('422 error - processing validation errors');
                                return { success: false, ...data };
                            }
                            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                        }

                        return data;
                    });
                })
                .then(data => {
                    if (data.success) {
                        showAlert('success', data.message);
                        form.reset();
                        document.getElementById('location_id').innerHTML = '<option value="">اختر المنطقة</option>';
                        document.querySelectorAll('.file-preview').forEach(preview => {
                            preview.style.display = 'none';
                        });

                        // Redirect to success page after 2 seconds
                        if (data.redirect_url) {
                            setTimeout(() => {
                                window.location.href = data.redirect_url;
                            }, 2000);
                        }
                    } else {
                        console.log('Error response from server:', data);
                        console.log('Error message from controller:', data.message);
                        console.log('Error count:', data.error_count);
                        console.log('Errors object:', data.errors);

                        // Handle different types of errors
                        if (data.errors && Object.keys(data.errors).length > 0) {
                            // Show field-specific errors first
                            const displayedErrorCount = showFieldErrors(data.errors);

                            // ALWAYS show the main error message from controller first
                            console.log('Controller message check:', {
                                hasMessage: !!data.message,
                                messageValue: data.message,
                                messageType: typeof data.message,
                                messageLength: data.message ? data.message.length : 0
                            });

                            if (data.message && data.message.trim() !== '') {
                                console.log('Showing controller message:', data.message);
                                showAlert('danger', data.message);
                            } else {
                                console.log('No controller message, showing generic');
                                showAlert('danger', 'يرجى تصحيح الأخطاء أدناه');
                            }

                            // Don't show error summary to avoid overriding main message
                            // if (data.error_count && data.error_count > 1) {
                            //     showErrorSummary(data.errors, data.error_count);
                            // }
                        } else if (data.error_type) {
                            // Handle specific error types with controller messages
                            let errorMessage = data.message || 'حدث خطأ أثناء إرسال الطلب. يرجى المحاولة مرة أخرى.';

                            // Use controller's specific error messages
                            if (data.error_type === 'database') {
                                if (data.error_code === 23000) {
                                    errorMessage = data.message || 'البيانات المدخلة مستخدمة من قبل. يرجى التحقق من البيانات وإعادة المحاولة.';
                                } else if (data.error_code === 22001) {
                                    errorMessage = data.message || 'أحد الحقول المدخلة أطول من المسموح به. يرجى تقصير النص.';
                                } else if (data.error_code === 1048) {
                                    errorMessage = data.message || 'يرجى ملء جميع الحقول المطلوبة.';
                                }
                            } else if (data.error_type.includes('PostTooLargeException')) {
                                errorMessage = data.message || 'حجم الملفات المرسلة كبير جداً. يرجى تقليل حجم الملفات أو إرسال ملفات أقل.';
                            } else if (data.error_type.includes('FileNotFoundException')) {
                                errorMessage = data.message || 'خطأ في رفع الملفات. يرجى التأكد من صحة الملفات وإعادة المحاولة.';
                            }

                            showAlert('danger', errorMessage);
                        } else {
                            // Show the controller's message
                            showAlert('danger', data.message || 'حدث خطأ أثناء إرسال الطلب. يرجى المحاولة مرة أخرى.');
                        }
                    }
                })
                .catch(error => {
                    console.error('Fetch error:', error);
                    console.error('Error message:', error.message);
                    console.error('Error name:', error.name);

                    // Handle different types of errors
                    let errorMessage = 'حدث خطأ في الاتصال. يرجى المحاولة مرة أخرى أو التواصل معنا عبر الهاتف.';

                    // Don't show generic 422 message since we handle it in the then block now
                    if (error.message.includes('HTTP 429')) {
                        errorMessage = 'تم إرسال طلبات كثيرة. يرجى الانتظار قليلاً قبل المحاولة مرة أخرى.';
                    } else if (error.message.includes('HTTP 500')) {
                        errorMessage = 'حدث خطأ في الخادم. يرجى المحاولة مرة أخرى أو التواصل معنا.';
                    } else if (error.message.includes('Failed to fetch')) {
                        errorMessage = 'فشل في الاتصال بالخادم. يرجى التحقق من اتصال الإنترنت والمحاولة مرة أخرى.';
                    } else if (error.name === 'AbortError') {
                        errorMessage = 'انتهت مهلة الطلب. يرجى المحاولة مرة أخرى.';
                    }

                    showAlert('danger', errorMessage);
                })
                .finally(() => {
                    // Clear timeout
                    clearTimeout(timeoutId);

                    // Hide loading state
                    loadingSpan.style.display = 'none';
                    submitTextSpan.style.display = 'inline';
                    submitBtn.disabled = false;
                });
            });

            function showAlert(type, message) {
                console.log('showAlert called with:', { type, message });
                alertContainer.innerHTML = `
                    <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                        <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-triangle'} me-2"></i>
                        ${message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                `;

                // Auto-hide after 5 seconds
                setTimeout(() => {
                    const alert = alertContainer.querySelector('.alert');
                    if (alert) {
                        alert.remove();
                    }
                }, 5000);
            }

            function showFieldErrors(errors) {
                console.log('Showing field errors from controller:', errors);

                // Clear previous errors
                clearErrors();

                let errorCount = 0;
                let displayedErrors = [];

                // Show field-specific errors
                Object.keys(errors).forEach(fieldName => {
                    let fieldId = fieldName;
                    let errorElementId = fieldName + '-error';

                    // Handle special field name mappings
                    if (fieldName.startsWith('attachments.')) {
                        // Convert attachments.0 to attachments_0
                        fieldId = fieldName.replace('.', '_');
                        errorElementId = fieldName + '-error';
                    }

                    const field = document.getElementById(fieldId);
                    const errorElement = document.getElementById(errorElementId);

                    console.log(`Processing error for field: ${fieldName}`, {
                        fieldId: fieldId,
                        errorElementId: errorElementId,
                        fieldFound: !!field,
                        errorElementFound: !!errorElement,
                        errorMessage: errors[fieldName][0]
                    });

                    if (field && errorElement) {
                        field.classList.add('is-invalid');
                        errorElement.textContent = errors[fieldName][0];
                        errorCount++;
                        displayedErrors.push(`${fieldName}: ${errors[fieldName][0]}`);
                        console.log(`Successfully displayed error for field: ${fieldName}`);
                    } else {
                        console.warn(`Could not find field or error element for: ${fieldName}`, {
                            fieldId: fieldId,
                            errorElementId: errorElementId,
                            field: field,
                            errorElement: errorElement
                        });
                    }
                });

                // Log all displayed errors for debugging
                console.log(`Displayed ${errorCount} field errors:`, displayedErrors);

                // Return the count of displayed errors
                return errorCount;
            }

            function showErrorSummary(errors, errorCount) {
                // Create error summary
                const errorList = Object.keys(errors).map(fieldName => {
                    const fieldLabel = getFieldLabel(fieldName);
                    return `• ${fieldLabel}: ${errors[fieldName][0]}`;
                }).join('<br>');

                const summaryMessage = `
                    <div class="mb-3">
                        <strong>ملخص الأخطاء:</strong><br>
                        ${errorList}
                    </div>
                `;

                // Show error summary alert
                showAlert('warning', summaryMessage);
            }

            function getFieldLabel(fieldName) {
                const fieldLabels = {
                    'name': 'الاسم الكامل',
                    'phone': 'رقم الهاتف',
                    'national_id': 'رقم البطاقة',
                    'contact': 'رقم التواصل',
                    'address': 'العنوان',
                    'governorate_id': 'المحافظة',
                    'location_id': 'المنطقة',
                    'home_location': 'موقع المنزل',
                    'company_id': 'الشركة',
                    'bank_account': 'رقم المحفظة',
                    'code': 'كود المندوب',
                    'attachments.0': 'البطاقة (وجه أول)',
                    'attachments.1': 'البطاقة (خلف)',
                    'attachments.2': 'الفيش',
                    'attachments.3': 'شهادة الميلاد',
                    'attachments.4': 'إيصال الأمانة',
                    'attachments.5': 'رخصة القيادة',
                    'attachments.6': 'رخصة السيارة وجه أول',
                    'attachments.7': 'رخصة السيارة وجه ثاني',
                };

                return fieldLabels[fieldName] || fieldName;
            }

            function clearErrors() {
                // Remove all error classes and messages
                form.querySelectorAll('.is-invalid').forEach(field => {
                    field.classList.remove('is-invalid');
                });

                form.querySelectorAll('.invalid-feedback').forEach(errorElement => {
                    errorElement.textContent = '';
                });
            }

        });
    </script>
</body>
</html>

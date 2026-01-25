<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>حذف الحساب - {{ config('app.name') }}</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Toastr CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">
    
    <style>
        body {
            background: #ffffff;
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .delete-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .delete-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            max-width: 500px;
            width: 100%;
        }
        
        .delete-header {
            background: linear-gradient(135deg, #ff6b6b, #ee5a52);
            color: white;
            border-radius: 20px 20px 0 0;
            padding: 30px;
            text-align: center;
        }
        
        .delete-header h1 {
            margin: 0;
            font-size: 1.8rem;
            font-weight: 600;
        }
        
        .delete-header p {
            margin: 10px 0 0 0;
            opacity: 0.9;
            font-size: 0.95rem;
        }
        
        .delete-body {
            padding: 40px;
        }
        
        .form-group {
            margin-bottom: 25px;
        }
        
        .form-label {
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
        }
        
        .form-label i {
            margin-left: 8px;
            color: #666;
        }
        
        .form-control {
            border: 2px solid #e9ecef;
            border-radius: 12px;
            padding: 12px 16px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: #ff6b6b;
            box-shadow: 0 0 0 0.2rem rgba(255, 107, 107, 0.25);
        }
        
        .btn-delete {
            background: linear-gradient(135deg, #ff6b6b, #ee5a52);
            border: none;
            border-radius: 12px;
            padding: 14px 30px;
            font-size: 1.1rem;
            font-weight: 600;
            color: white;
            width: 100%;
            transition: all 0.3s ease;
            margin-top: 10px;
        }
        
        .btn-delete:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(255, 107, 107, 0.4);
            color: white;
        }
        
        .btn-delete:disabled {
            opacity: 0.6;
            transform: none;
            box-shadow: none;
        }
        
        .warning-box {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 25px;
        }
        
        .warning-box h6 {
            color: #856404;
            font-weight: 600;
            margin-bottom: 10px;
        }
        
        .warning-box ul {
            margin: 0;
            padding-right: 20px;
            color: #856404;
        }
        
        .warning-box li {
            margin-bottom: 5px;
        }
        
        .user-info {
            background: #e3f2fd;
            border: 1px solid #bbdefb;
            border-radius: 12px;
            padding: 15px;
            margin-bottom: 20px;
            display: none;
        }
        
        .user-info.show {
            display: block;
        }
        
        .user-info h6 {
            color: #1976d2;
            margin-bottom: 8px;
        }
        
        .user-info p {
            margin: 0;
            color: #1976d2;
        }
        
        .loading {
            display: none;
        }
        
        .loading.show {
            display: inline-block;
        }
        
        .spinner-border-sm {
            width: 1rem;
            height: 1rem;
        }
        
        .back-link {
            text-align: center;
            margin-top: 20px;
        }
        
        .back-link a {
            color: #666;
            text-decoration: none;
            font-size: 0.9rem;
        }
        
        .back-link a:hover {
            color: #333;
        }
    </style>
</head>
<body>
    <div class="delete-container">
        <div class="delete-card">
            <div class="delete-header">
                <h1><i class="fas fa-user-times me-2"></i>حذف الحساب</h1>
                <p>هذه العملية لا يمكن التراجع عنها</p>
            </div>
            
            <div class="delete-body">
                <div class="warning-box">
                    <h6><i class="fas fa-exclamation-triangle me-2"></i>تحذير مهم</h6>
                    <ul>
                        <li>سيتم حذف جميع بياناتك نهائياً</li>
                        <li>لن تتمكن من استرداد أي معلومات</li>
                        <li>سيتم إلغاء جميع الطلبات والمواعيد</li>
                        <li>هذه العملية لا يمكن التراجع عنها</li>
                    </ul>
                </div>
                
                <div class="user-info" id="userInfo">
                    <h6><i class="fas fa-user me-2"></i>معلومات الحساب</h6>
                    <p id="userDetails"></p>
                </div>
                
                <form id="deleteAccountForm">
                    @csrf
                    
                    <div class="form-group">
                        <label class="form-label" for="phone">
                            <i class="fas fa-phone"></i>
                            رقم الهاتف
                        </label>
                        <input type="text" 
                               class="form-control" 
                               id="phone" 
                               name="phone" 
                               placeholder="أدخل رقم الهاتف"
                               required>
                        <div class="invalid-feedback" id="phoneError"></div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="password">
                            <i class="fas fa-lock"></i>
                            كلمة المرور
                        </label>
                        <input type="password" 
                               class="form-control" 
                               id="password" 
                               name="password" 
                               placeholder="أدخل كلمة المرور"
                               required>
                        <div class="invalid-feedback" id="passwordError"></div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="confirmation">
                            <i class="fas fa-exclamation-circle"></i>
                            تأكيد الحذف
                        </label>
                        <input type="text" 
                               class="form-control" 
                               id="confirmation" 
                               name="confirmation" 
                               placeholder="اكتب DELETE للتأكيد"
                               required>
                        <small class="form-text text-muted">
                            يجب كتابة "DELETE" بالضبط للتأكيد
                        </small>
                        <div class="invalid-feedback" id="confirmationError"></div>
                    </div>
                    
                    <button type="submit" class="btn btn-delete" id="deleteBtn">
                        <span class="loading" id="loading">
                            <span class="spinner-border spinner-border-sm me-2" role="status"></span>
                        </span>
                        <i class="fas fa-trash-alt me-2"></i>
                        حذف الحساب نهائياً
                    </button>
                </form>
                
                
            </div>
        </div>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Toastr JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <script>
        $(document).ready(function() {
            // Configure Toastr
            toastr.options = {
                "closeButton": true,
                "debug": false,
                "newestOnTop": false,
                "progressBar": true,
                "positionClass": "toast-top-center",
                "preventDuplicates": false,
                "onclick": null,
                "showDuration": "300",
                "hideDuration": "1000",
                "timeOut": "5000",
                "extendedTimeOut": "1000",
                "showEasing": "swing",
                "hideEasing": "linear",
                "showMethod": "fadeIn",
                "hideMethod": "fadeOut"
            };

            // Check phone number when user types
            let phoneCheckTimeout;
            $('#phone').on('input', function() {
                clearTimeout(phoneCheckTimeout);
                const phone = $(this).val().trim();
                
                if (phone.length >= 10) {
                    phoneCheckTimeout = setTimeout(function() {
                        checkPhoneNumber(phone);
                    }, 500);
                } else {
                    $('#userInfo').removeClass('show');
                }
            });

            // Form submission
            $('#deleteAccountForm').on('submit', function(e) {
                e.preventDefault();
                
                // Clear previous errors
                clearErrors();
                
                // Validate form
                if (!validateForm()) {
                    return;
                }
                
                // Show loading
                showLoading(true);
                
                // Submit form
                $.ajax({
                    url: '{{ route("public.delete-account") }}',
                    method: 'POST',
                    data: $(this).serialize(),
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        showLoading(false);
                        
                        if (response.success) {
                            toastr.success(response.message);
                            
                            // Clear form
                            $('#deleteAccountForm')[0].reset();
                            $('#userInfo').removeClass('show');
                            
                            // Show success message
                            setTimeout(function() {
                                alert('تم حذف الحساب بنجاح. سيتم إعادة توجيهك إلى صفحة تسجيل الدخول.');
                                window.location.href = '{{ route("login") }}';
                            }, 2000);
                        } else {
                            toastr.error(response.message);
                        }
                    },
                    error: function(xhr) {
                        showLoading(false);
                        
                        if (xhr.status === 422) {
                            // Validation errors
                            const errors = xhr.responseJSON.errors;
                            displayErrors(errors);
                        } else {
                            const response = xhr.responseJSON;
                            toastr.error(response?.message || 'حدث خطأ أثناء حذف الحساب');
                        }
                    }
                });
            });

            // Check phone number function
            function checkPhoneNumber(phone) {
                $.ajax({
                    url: '{{ route("public.check-phone") }}',
                    method: 'POST',
                    data: {
                        phone: phone,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.exists) {
                            const userType = response.type === 'employee' ? 'موظف' :
                                           response.type === 'representative' ? 'مندوب' :
                                           response.type === 'supervisor' ? 'مشرف' : response.type;
                            
                            $('#userDetails').html(`
                                <strong>الاسم:</strong> ${response.name}<br>
                                <strong>النوع:</strong> ${userType}
                            `);
                            $('#userInfo').addClass('show');
                        } else {
                            $('#userInfo').removeClass('show');
                        }
                    }
                });
            }

            // Validate form
            function validateForm() {
                let isValid = true;
                
                const phone = $('#phone').val().trim();
                const password = $('#password').val().trim();
                const confirmation = $('#confirmation').val().trim();
                
                if (phone.length < 10) {
                    showError('phone', 'رقم الهاتف يجب أن يكون 10 أرقام على الأقل');
                    isValid = false;
                }
                
                if (password.length < 6) {
                    showError('password', 'كلمة المرور يجب أن تكون 6 أحرف على الأقل');
                    isValid = false;
                }
                
                if (confirmation !== 'DELETE') {
                    showError('confirmation', 'يجب كتابة DELETE بالضبط للتأكيد');
                    isValid = false;
                }
                
                return isValid;
            }

            // Show error
            function showError(field, message) {
                $(`#${field}`).addClass('is-invalid');
                $(`#${field}Error`).text(message);
            }

            // Clear errors
            function clearErrors() {
                $('.form-control').removeClass('is-invalid');
                $('.invalid-feedback').text('');
            }

            // Display validation errors
            function displayErrors(errors) {
                Object.keys(errors).forEach(function(field) {
                    showError(field, errors[field][0]);
                });
            }

            // Show/hide loading
            function showLoading(show) {
                if (show) {
                    $('#loading').addClass('show');
                    $('#deleteBtn').prop('disabled', true);
                } else {
                    $('#loading').removeClass('show');
                    $('#deleteBtn').prop('disabled', false);
                }
            }
        });
    </script>
</body>
</html>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="انضم إلى فريق المندوبين المتميزين. فرصة رائعة للعمل مع شركة رائدة في مجال المبيعات والتسويق!">
    <meta name="keywords" content="مندوبين, مبيعات, تسويق, وظائف, عمل, فرص عمل">
    <meta name="author" content="شركتك">
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('assets/images/logo-sys.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('assets/images/logo-sys.png') }}">
    
    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="انضم إلى فريق المندوبين - {{ $slug ?? 'صفحة الهبوط' }}">
    <meta property="og:description" content="فرصة رائعة للعمل مع شركة رائدة في مجال المبيعات والتسويق!">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ request()->url() }}">
    <meta property="og:image" content="{{ asset('assets/images/logo-sys.png') }}">
    
    <title>{{ $slug ?? 'صفحة الهبوط' }} - انضم إلى فريق المندوبين</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #dc2626;
            --secondary-color: #b91c1c;
            --accent-color: #f59e0b;
            --text-color: #1f2937;
            --light-bg: #fef2f2;
            --border-color: #fecaca;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Cairo', sans-serif;
            line-height: 1.6;
            color: var(--text-color);
            background-color: white;
        }
        
        .hero-section {
            background: linear-gradient(135deg,rgb(255, 255, 255) 0%,rgb(255, 255, 255) 50%,rgb(255, 255, 255) 100%);
            color: white;
            padding: 80px 0;
            position: relative;
            overflow: hidden;
        }
        
        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="50" cy="50" r="1" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
            opacity: 0.3;
        }
        
        .hero-content {
            position: relative;
            z-index: 2;
        }
        
        .hero-title {
            font-size: 3.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
            color: black;
            text-shadow: 0px 0px 0px rgba(0,0,0,0.3);
        }
        
        .hero-subtitle {
            font-size: 1.25rem;
            margin-bottom: 2rem;
            color: #dc2626;
            font-weight: 600;
        }
        
        .cta-button {
            background: #dc2626;
            color: white;
            padding: 15px 40px;
            border: none;
            border-radius: 50px;
            font-size: 1.1rem;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
        }
        
        .cta-button:hover {
            background: #dc2626;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px #dc2626;
        }
        
        .features-section {
            padding: 80px 0;
            background: white;
        }
        
        .feature-card {
            text-align: center;
            padding: 2rem;
            border-radius: 15px;
            background: white;
            box-shadow: 0 5px 20px rgba(220, 38, 38, 0.1);
            border: 2px solid #dc2626;
            transition: transform 0.3s ease;
            height: 100%;
        }
        
        .feature-card:hover {
            transform: translateY(-5px);
        }
        
        .feature-icon {
            font-size: 3rem;
            color: #dc2626;
            margin-bottom: 1rem;
        }
        
        .form-section {
            padding: 80px 0;
            background: white;
        }
        
        .form-card {
            background: white;
            border-radius: 20px;
            padding: 3rem;
            box-shadow: 0 10px 40px rgba(220, 38, 38, 0.1);
            border: 2px solid #9a9a9a 
            ;
        }
        
        .form-title {
            color: #dc2626;
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 1rem;
            text-align: center;
        }
        
        .form-control {
            border: 2px solid ##9a9a9a 
            ;
            border-radius: 10px;
            padding: 12px 15px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: ##9a9a9a 
            ;
            box-shadow: 0 0 0 0.2rem rgba(220, 38, 38, 0.25);
        }
        
        .submit-btn {
            background: #dc2626;
            color: white;
            border: none;
            border-radius: 10px;
            padding: 15px 30px;
            font-size: 1.1rem;
            font-weight: 600;
            width: 100%;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(220, 38, 38, 0.3);
        }
        
        .submit-btn:hover {
            background: #b91c1c;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(220, 38, 38, 0.4);
        }
        
        .footer {
            background: white;
            color: #1f2937;
            padding: 40px 0;
            text-align: center;
            border-top: 2px solid #9a9a9a 
            ;
        }
        
        .social-links a {
            color: #dc2626;
            font-size: 1.5rem;
            margin: 0 10px;
            transition: color 0.3s ease;
        }
        
        .social-links a:hover {
            color: #b91c1c;
        }
        
        .floating-whatsapp {
            position: fixed;
            bottom: 30px;
            left: 30px;
            background: #25d366;
            color: white;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            text-decoration: none;
            box-shadow: 0 4px 20px rgba(37, 211, 102, 0.4);
            transition: all 0.3s ease;
            z-index: 1000;
        }
        
        .floating-whatsapp:hover {
            color: white;
            transform: scale(1.1);
            box-shadow: 0 6px 25px rgba(37, 211, 102, 0.6);
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
        
        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.5rem;
            }
            
            .hero-subtitle {
                font-size: 1.1rem;
            }
            
            .form-card {
                padding: 2rem;
                margin: 0 1rem;
            }
            
            .floating-whatsapp {
                bottom: 20px;
                left: 20px;
                width: 50px;
                height: 50px;
                font-size: 1.2rem;
            }
        }
    </style>
</head>
<body>
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 hero-content">
                    <h1 class="hero-title">انضم إلى فريق المندوبين</h1>
                    <p class="hero-subtitle">فرصة رائعة للعمل مع شركة movepoint</p>
                    <a href="#contact-form" class="cta-button">تقدم الآن</a>
                </div>
                <div class="col-lg-6 text-center">
                    <img src="{{ asset('assets/images/logo-sys.png') }}" alt="شعار النظام" class="img-fluid" style="max-width: 300px; opacity: 0.9;">
                </div>
            </div>
        </div>
    </section>

 

    <!-- Contact Form Section -->
    <section class="form-section" id="contact-form">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="form-card">
                        <h3 class="form-title">تقدم للعمل كمندوب</h3>
                        <p class="text-center text-muted mb-4">املأ النموذج التالي وسنتواصل معك لتحديد موعد المقابلة</p>
                        
                        <div id="alert-container"></div>
                        
                        <form id="lead-form">
                            <!-- Hidden fields for tracking -->
                            <input type="hidden" name="utm_source" value="{{ $utmSource }}">
                            <input type="hidden" name="utm_medium" value="{{ $utmMedium }}">
                            <input type="hidden" name="utm_campaign" value="{{ $utmCampaign }}">
                            <input type="hidden" name="referrer" value="{{ $referrer }}">
                            <input type="hidden" name="landing_page_slug" value="{{ $slug }}">
                            
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="name" class="form-label">الاسم الكامل *</label>
                                    <input type="text" class="form-control" id="name" name="name" required>
                                    <div class="invalid-feedback" id="name-error"></div>
                                </div>
                                <div class="col-md-6">
                                    <label for="phone" class="form-label">رقم الهاتف *</label>
                                    <input type="tel" class="form-control" id="phone" name="phone" required>
                                    <div class="invalid-feedback" id="phone-error"></div>
                                </div>
                            
                                <div class="col-md-6">
                                    <label for="governorate_id" class="form-label">المحافظة *</label>
                                    <select class="form-control" id="governorate_id" name="governorate_id" required>
                                        <option value="">اختر المحافظة</option>
                                        @foreach($governorates as $governorate)
                                            <option value="{{ $governorate->id }}">{{ $governorate->name }}</option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback" id="governorate_id-error"></div>
                                </div>

                                <div class="col-12">
                                    <label for="message" class="form-label">رسالة إضافية</label>
                                    <textarea class="form-control" id="message" name="message" rows="4" placeholder="اكتب أي تفاصيل إضافية هنا..."></textarea>
                                    <div class="invalid-feedback" id="message-error"></div>
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="submit-btn" id="submit-btn">
                                        <span class="btn-text">إرسال طلب التوظيف</span>
                                        <span class="loading">
                                            <span class="spinner-border spinner-border-sm me-2" role="status"></span>
                                            جاري الإرسال...
                                        </span>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h5>تواصل معنا</h5>
                    <p class="mb-3">نحن هنا لمساعدتك في تحقيق أهدافك</p>
                  
                    <hr class="my-3">
                    <p class="mb-0">&copy; {{ date('Y') }} جميع الحقوق محفوظة</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Floating WhatsApp Button -->
   

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('lead-form');
            const submitBtn = document.getElementById('submit-btn');
            const btnText = submitBtn.querySelector('.btn-text');
            const loading = submitBtn.querySelector('.loading');
            const alertContainer = document.getElementById('alert-container');

            // Phone number formatting - removed auto 20 prefix
            const phoneInput = document.getElementById('phone');
            phoneInput.addEventListener('input', function(e) {
                // Only allow numbers
                let value = e.target.value.replace(/\D/g, '');
                e.target.value = value;
            });

            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Clear previous errors
                clearErrors();
                
                // Show loading state
                btnText.style.display = 'none';
                loading.style.display = 'inline-block';
                submitBtn.disabled = true;

                // Collect form data
                const formData = new FormData(form);
                
                // Send AJAX request
                fetch('{{ route("landing-page.store") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(Object.fromEntries(formData))
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showAlert('success', 'تم إرسال طلبك بنجاح! سنتواصل معك قريباً لتحديد موعد المقابلة.');
                        form.reset();
                        
                        // Redirect to success page after 2 seconds
                        setTimeout(() => {
                            window.location.href = '{{ route("landing-page.success") }}';
                        }, 2000);
                    } else {
                        showAlert('danger', 'حدث خطأ أثناء إرسال الطلب. يرجى المحاولة مرة أخرى.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('danger', 'حدث خطأ في الاتصال. يرجى المحاولة مرة أخرى أو التواصل معنا عبر الهاتف.');
                })
                .finally(() => {
                    // Hide loading state
                    btnText.style.display = 'inline';
                    loading.style.display = 'none';
                    submitBtn.disabled = false;
                });
            });

            function showAlert(type, message) {
                alertContainer.innerHTML = `
                    <div class="alert alert-${type} alert-dismissible fade show" role="alert">
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

            function clearErrors() {
                // Remove all error classes and messages
                form.querySelectorAll('.is-invalid').forEach(field => {
                    field.classList.remove('is-invalid');
                });
                form.querySelectorAll('.invalid-feedback').forEach(feedback => {
                    feedback.textContent = '';
                });
            }

            // Smooth scrolling for anchor links
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                });
            });
        });
    </script>
</body>
</html>

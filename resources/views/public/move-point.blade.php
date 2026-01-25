<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>موف بوينت - Move Point</title>
    <meta name="description" content="تطبيق موف بوينت – الحل الإداري الذكي للمندوبين">
    <meta name="keywords" content="موف بوينت, Move Point, تطبيق إداري, مندوبين, طلبات إدارية">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Cairo', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        
        .hero-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 100px 0;
            text-align: center;
        }
        
        .hero-title {
            font-size: 3.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }
        
        .hero-subtitle {
            font-size: 1.5rem;
            font-weight: 300;
            margin-bottom: 2rem;
            opacity: 0.9;
        }
        
        .features-section {
            background: white;
            padding: 80px 0;
        }
        
        .feature-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            height: 100%;
            border: none;
        }
        
        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
        }
        
        .feature-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 2rem;
            color: white;
        }
        
        .feature-icon.primary { background: linear-gradient(135deg, #667eea, #764ba2); }
        .feature-icon.success { background: linear-gradient(135deg, #4facfe, #00f2fe); }
        .feature-icon.warning { background: linear-gradient(135deg, #43e97b, #38f9d7); }
        .feature-icon.info { background: linear-gradient(135deg, #fa709a, #fee140); }
        
        .feature-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 15px;
            color: #333;
        }
        
        .feature-description {
            color: #666;
            line-height: 1.6;
        }
        
        .description-section {
            background: #f8f9fa;
            padding: 80px 0;
        }
        
        .description-card {
            background: white;
            border-radius: 20px;
            padding: 50px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            border: none;
        }
        
        .description-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 30px;
            text-align: center;
        }
        
        .description-text {
            font-size: 1.2rem;
            line-height: 1.8;
            color: #555;
            margin-bottom: 30px;
        }
        
        .short-description {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            padding: 30px;
            border-radius: 15px;
            font-size: 1.3rem;
            font-weight: 500;
            text-align: center;
            margin-top: 30px;
        }
        
        .cta-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 80px 0;
            text-align: center;
        }
        
        .cta-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 20px;
        }
        
        .cta-subtitle {
            font-size: 1.3rem;
            margin-bottom: 40px;
            opacity: 0.9;
        }
        
        .btn-download {
            background: white;
            color: #667eea;
            border: none;
            padding: 15px 40px;
            font-size: 1.2rem;
            font-weight: 600;
            border-radius: 50px;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            margin: 10px;
        }
        
        .btn-download:hover {
            background: #f8f9fa;
            color: #667eea;
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        }
        
        .footer {
            background: #333;
            color: white;
            padding: 40px 0;
            text-align: center;
        }
        
        .sparkle {
            color: #ffd700;
            animation: sparkle 2s infinite;
        }
        
        @keyframes sparkle {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.5; transform: scale(1.1); }
        }
        
        .floating {
            animation: floating 3s ease-in-out infinite;
        }
        
        @keyframes floating {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
    </style>
</head>
<body>
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <h1 class="hero-title">
                        <span class="sparkle">✨</span> موف بوينت <span class="sparkle">✨</span>
                    </h1>
                    <p class="hero-subtitle">Move Point - الحل الإداري الذكي للمندوبين</p>
                    <div class="floating">
                        <i class="fas fa-mobile-alt fa-3x mb-3"></i>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="feature-card">
                        <div class="feature-icon primary">
                            <i class="fas fa-file-alt"></i>
                        </div>
                        <h4 class="feature-title">الطلبات الإدارية</h4>
                        <p class="feature-description">
                            تقديم طلبات إدارية بسرعة مثل طلب إجازة أو طلب سلفة، بدون الحاجة للرجوع ورقيًا أو انتظار طويل
                        </p>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="feature-card">
                        <div class="feature-icon success">
                            <i class="fas fa-camera"></i>
                        </div>
                        <h4 class="feature-title">تصوير الإيداع</h4>
                        <p class="feature-description">
                            رفع تصوير الإيداع اليومي مباشرة داخل التطبيق، مما يسهل متابعة العمليات المالية ويوفر سجلًا موثقًا
                        </p>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="feature-card">
                        <div class="feature-icon warning">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <h4 class="feature-title">تسجيل الأوردرات</h4>
                        <p class="feature-description">
                            تسجيل عدد الأوردرات المستلمة يوميًا لتبسيط عملية المتابعة ومراجعة الأداء
                        </p>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="feature-card">
                        <div class="feature-icon info">
                            <i class="fas fa-money-bill-wave"></i>
                        </div>
                        <h4 class="feature-title">تفاصيل الراتب</h4>
                        <p class="feature-description">
                            عرض تفاصيل الراتب بعد التفعيل، بحيث يمكن للمندوب متابعة استحقاقاته المالية بكل وضوح وشفافية
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Description Section -->
    <section class="description-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="description-card">
                        <h2 class="description-title">
                            <span class="sparkle">✨</span> وصف طويل <span class="sparkle">✨</span>
                        </h2>
                        
                        <p class="description-text">
                            تطبيق موف بوينت – Move Point هو الحل الإداري الذكي المصمم خصيصًا لتسهيل حياة المندوبين وتنظيم معاملاتهم اليومية مع الشركة.
                        </p>
                        
                        <p class="description-text">
                            من خلال واجهة بسيطة وسهلة الاستخدام، يتيح التطبيق للمندوبين إمكانية:
                        </p>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <ul class="list-unstyled">
                                    <li class="mb-3">
                                        <i class="fas fa-check-circle text-success me-2"></i>
                                        تقديم طلبات إدارية بسرعة مثل طلب إجازة أو طلب سلفة، بدون الحاجة للرجوع ورقيًا أو انتظار طويل.
                                    </li>
                                    <li class="mb-3">
                                        <i class="fas fa-check-circle text-success me-2"></i>
                                        رفع تصوير الإيداع اليومي مباشرة داخل التطبيق، مما يسهل متابعة العمليات المالية ويوفر سجلًا موثقًا.
                                    </li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <ul class="list-unstyled">
                                    <li class="mb-3">
                                        <i class="fas fa-check-circle text-success me-2"></i>
                                        تسجيل عدد الأوردرات المستلمة يوميًا لتبسيط عملية المتابعة ومراجعة الأداء.
                                    </li>
                                    <li class="mb-3">
                                        <i class="fas fa-check-circle text-success me-2"></i>
                                        عرض تفاصيل الراتب بعد التفعيل، بحيث يمكن للمندوب متابعة استحقاقاته المالية بكل وضوح وشفافية.
                                    </li>
                                </ul>
                            </div>
                        </div>
                        
                        <p class="description-text">
                            التطبيق يوفر بيئة آمنة وموثوقة لإدارة البيانات، ويعزز التواصل بين المندوب والإدارة بطريقة احترافية وسريعة.
                            مع موف بوينت، كل ما يحتاجه المندوب أصبح في مكان واحد – أسرع، أوضح، وأكثر تنظيمًا.
                        </p>
                        
                        <div class="short-description">
                            <p class="mb-0">
                                <strong>⚡ وصف قصير:</strong><br>
                                موف بوينت هو تطبيق إداري ذكي يساعد المندوبين على تقديم طلبات الإجازة والسلف، تسجيل الأوردرات، رفع الإيداعات اليومية، ومعرفة تفاصيل المرتب بسهولة وفي مكان واحد.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Call to Action Section -->
    <section class="cta-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8 text-center">
                    <h2 class="cta-title">ابدأ رحلتك مع موف بوينت اليوم</h2>
                    <p class="cta-subtitle">
                        انضم إلى آلاف المندوبين الذين يثقون في موف بوينت لإدارة أعمالهم اليومية
                    </p>
                    <a href="#" class="btn-download">
                        <i class="fas fa-download me-2"></i>
                        تحميل التطبيق
                    </a>
                    <a href="{{ route('public.representative.create') }}" class="btn-download">
                        <i class="fas fa-user-plus me-2"></i>
                        انضم كمندوب
                    </a>
                    <a href="#" class="btn-download">
                        <i class="fas fa-info-circle me-2"></i>
                        معرفة المزيد
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <p class="mb-0">
                        © 2024 موف بوينت - Move Point. جميع الحقوق محفوظة.
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Add some interactive animations
        document.addEventListener('DOMContentLoaded', function() {
            // Animate cards on scroll
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };
            
            const observer = new IntersectionObserver(function(entries) {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                    }
                });
            }, observerOptions);
            
            // Observe all feature cards
            document.querySelectorAll('.feature-card').forEach(card => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(30px)';
                card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
                observer.observe(card);
            });
        });
    </script>
</body>
</html>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('assets/images/logo-sys.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('assets/images/logo-sys.png') }}">
    
    <title>تم إرسال طلبك بنجاح! - انضم إلى فريق المندوبين</title>
    
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
            --success-color: #dc2626;
            --text-color: #1f2937;
            --light-bg: #fef2f2;
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
            background: white;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .success-container {
            background: white;
            border-radius: 20px;
            padding: 3rem;
            box-shadow: 0 20px 60px rgba(0,0,0,0.1);
            text-align: center;
            max-width: 600px;
            width: 90%;
            position: relative;
            overflow: hidden;
        }
        
        .success-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, var(--success-color), var(--primary-color));
        }
        
        .success-icon {
            width: 100px;
            height: 100px;
            background: var(--success-color);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 2rem;
            animation: bounceIn 0.8s ease-out;
        }
        
        .success-icon i {
            font-size: 3rem;
            color: white;
        }
        
        .success-title {
            color: var(--success-color);
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
            animation: fadeInUp 0.8s ease-out 0.2s both;
        }
        
        .success-message {
            color: var(--text-color);
            font-size: 1.2rem;
            margin-bottom: 2rem;
            animation: fadeInUp 0.8s ease-out 0.4s both;
        }
        
        .next-steps {
            background: white;
            border: 2px solid #dc2626;
            border-radius: 15px;
            padding: 2rem;
            margin: 2rem 0;
            text-align: right;
            animation: fadeInUp 0.8s ease-out 0.6s both;
            box-shadow: 0 5px 20px rgba(220, 38, 38, 0.1);
        }
        
        .next-steps h4 {
            color: var(--primary-color);
            margin-bottom: 1rem;
        }
        
        .next-steps ul {
            list-style: none;
            padding: 0;
        }
        
        .next-steps li {
            padding: 0.5rem 0;
            border-bottom: 1px solid #e2e8f0;
            position: relative;
            padding-right: 2rem;
        }
        
        .next-steps li:last-child {
            border-bottom: none;
        }
        
        .next-steps li::before {
            content: '✓';
            position: absolute;
            right: 0;
            color: var(--success-color);
            font-weight: bold;
        }
        
        .contact-info {
            background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
            color: white;
            border-radius: 15px;
            padding: 2rem;
            margin: 2rem 0;
            animation: fadeInUp 0.8s ease-out 0.8s both;
            box-shadow: 0 5px 20px rgba(220, 38, 38, 0.3);
        }
        
        .contact-info h4 {
            margin-bottom: 1rem;
        }
        
        .contact-method {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 1rem;
            margin: 1rem 0;
        }
        
        .contact-method i {
            font-size: 1.5rem;
            color: var(--accent-color);
        }
        
        .action-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
            margin-top: 2rem;
            animation: fadeInUp 0.8s ease-out 1s both;
        }
        
        .btn {
            padding: 12px 30px;
            border-radius: 50px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
        }
        
        .btn-primary {
            background: var(--primary-color);
            color: white;
        }
        
        .btn-primary:hover {
            background: var(--secondary-color);
            color: white;
            transform: translateY(-2px);
        }
        
        .btn-outline {
            background: transparent;
            color: var(--primary-color);
            border: 2px solid var(--primary-color);
        }
        
        .btn-outline:hover {
            background: var(--primary-color);
            color: white;
            transform: translateY(-2px);
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
        
        @keyframes bounceIn {
            0% {
                transform: scale(0.3);
                opacity: 0;
            }
            50% {
                transform: scale(1.05);
            }
            70% {
                transform: scale(0.9);
            }
            100% {
                transform: scale(1);
                opacity: 1;
            }
        }
        
        @keyframes fadeInUp {
            0% {
                opacity: 0;
                transform: translateY(30px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @media (max-width: 768px) {
            .success-container {
                padding: 2rem;
                margin: 1rem;
            }
            
            .success-title {
                font-size: 2rem;
            }
            
            .success-message {
                font-size: 1.1rem;
            }
            
            .action-buttons {
                flex-direction: column;
                align-items: center;
            }
            
            .btn {
                width: 100%;
                max-width: 300px;
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
    <div class="success-container">
        <div class="success-icon">
            <i class="fas fa-check"></i>
        </div>
        
        <h1 class="success-title">تم إرسال طلبك بنجاح!</h1>
        <p class="success-message">شكراً لك على اهتمامك بالانضمام إلى فريق المندوبين. سنقوم بمراجعة طلبك والتواصل معك قريباً.</p>
    

   
    
    <script>
        // Auto-redirect after 30 seconds
    
        // Add some interactive elements
        document.addEventListener('DOMContentLoaded', function() {
            // Add click effect to buttons
            document.querySelectorAll('.btn').forEach(button => {
                button.addEventListener('click', function(e) {
                    // Create ripple effect
                    const ripple = document.createElement('span');
                    const rect = this.getBoundingClientRect();
                    const size = Math.max(rect.width, rect.height);
                    const x = e.clientX - rect.left - size / 2;
                    const y = e.clientY - rect.top - size / 2;
                    
                    ripple.style.width = ripple.style.height = size + 'px';
                    ripple.style.left = x + 'px';
                    ripple.style.top = y + 'px';
                    ripple.classList.add('ripple');
                    
                    this.appendChild(ripple);
                    
                    setTimeout(() => {
                        ripple.remove();
                    }, 600);
                });
            });
        });
    </script>
</body>
</html>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>اختبار إعادة تعيين كلمة المرور</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { 
            background-color: #f8f9fa; 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .test-container {
            max-width: 500px;
            margin: 50px auto;
        }
        .result-box {
            min-height: 100px;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 15px;
            margin-top: 20px;
            background-color: white;
        }
    </style>
</head>
<body>
    <div class="container test-container">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">اختبار إعادة تعيين كلمة المرور</h4>
            </div>
            <div class="card-body">
                <form id="passwordResetForm">
                    <div class="mb-3">
                        <label for="phone" class="form-label">رقم الهاتف</label>
                        <input type="text" class="form-control" id="phone" name="phone" 
                               placeholder="أدخل رقم الهاتف (11 رقم)" maxlength="11" required>
                        <div class="form-text">أدخل رقم الهاتف المكون من 11 رقم</div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100">
                        <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                        إعادة تعيين كلمة المرور
                    </button>
                </form>

                <div class="result-box d-none" id="resultBox">
                    <h6>النتيجة:</h6>
                    <div id="resultContent"></div>
                </div>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">تعليمات الاختبار</h5>
            </div>
            <div class="card-body">
                <ul class="mb-0">
                    <li>أدخل رقم هاتف مستخدم موجود في النظام</li>
                    <li>سيتم إرسال كلمة مرور جديدة عبر WhatsApp</li>
                    <li>يمكنك مراجعة الطلبات في لوحة التحكم</li>
                    <li>API Endpoint: <code>/api/reset-password</code></li>
                </ul>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('passwordResetForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const phone = document.getElementById('phone').value;
            const submitBtn = document.querySelector('button[type="submit"]');
            const spinner = submitBtn.querySelector('.spinner-border');
            const resultBox = document.getElementById('resultBox');
            const resultContent = document.getElementById('resultContent');
            
            // Show loading state
            submitBtn.disabled = true;
            spinner.classList.remove('d-none');
            resultBox.classList.add('d-none');
            
            try {
                const response = await fetch('/api/reset-password', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    },
                    body: JSON.stringify({ phone: phone })
                });
                
                const result = await response.json();
                
                // Show result
                resultBox.classList.remove('d-none');
                if (result.success) {
                    resultContent.innerHTML = `
                        <div class="alert alert-success">
                            <strong>نجح!</strong> ${result.message}
                        </div>
                    `;
                } else {
                    resultContent.innerHTML = `
                        <div class="alert alert-danger">
                            <strong>فشل!</strong> ${result.message}
                        </div>
                    `;
                }
                
            } catch (error) {
                resultBox.classList.remove('d-none');
                resultContent.innerHTML = `
                    <div class="alert alert-danger">
                        <strong>خطأ!</strong> حدث خطأ في الاتصال: ${error.message}
                    </div>
                `;
            } finally {
                // Reset loading state
                submitBtn.disabled = false;
                spinner.classList.add('d-none');
            }
        });
    </script>
</body>
</html>

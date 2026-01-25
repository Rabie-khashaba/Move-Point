<!DOCTYPE html>
<html lang="ar" dir="rtl">

@include('partials.head')

<body>
    <main class="auth-creative-wrapper">
        <div class="auth-creative-inner">
            <div class="creative-card-wrapper">
                <div class="card my-4 overflow-hidden" style="z-index: 1">
                    <div class="row flex-1 g-0">
                        <!-- Left side: Form -->
                        <div class="col-lg-6 h-100 my-auto order-1 order-lg-0">
                            <div class="wd-50 bg-white p-2 rounded-circle shadow-lg position-absolute translate-middle top-50 start-50 d-none d-lg-block">
                                <img src="{{ asset('assets/images/logo-sys.png') }}" alt="شعار النظام" class="img-fluid">
                            </div>
                            <div class="creative-card-body card-body p-sm-5 text-end">
                                <h2 class="fs-20 fw-bolder mb-4">تسجيل الدخول</h2>
                                <p class="mb-4 text-muted">سجل الدخول إلى حسابك</p>

                                <!-- Errors -->
                                @if ($errors->any())
                                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                        <ul class="mb-0">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="إغلاق"></button>
                                    </div>
                                @endif

                                <!-- Success -->
                                @if (session('success'))
                                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                                        {{ session('success') }}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="إغلاق"></button>
                                    </div>
                                @endif

                                <!-- Error -->
                                @if (session('error'))
                                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                        {{ session('error') }}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="إغلاق"></button>
                                    </div>
                                @endif

                                <!-- Login Form -->
                                <form action="{{ route('login') }}" method="POST" class="w-100 mt-4 pt-2 text-end">
                                    @csrf
                                    <div class="mb-4">
                                        <input type="text" 
                                               name="phone" 
                                               class="form-control @error('phone') is-invalid @enderror" 
                                               placeholder="رقم الهاتف" 
                                               required>
                                        @error('phone')
                                            <div class="invalid-feedback text-end">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <input type="password" 
                                               name="password" 
                                               class="form-control @error('password') is-invalid @enderror" 
                                               placeholder="كلمة المرور" 
                                               required>
                                        @error('password')
                                            <div class="invalid-feedback text-end">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="d-flex justify-content-between mb-3">
                                        <div>
                                            <!-- يمكن إضافة "تذكرني" هنا -->
                                        </div>
                                        <div>
                                            <!-- يمكن إضافة "نسيت كلمة المرور؟" -->
                                        </div>
                                    </div>

                                    <div class="mt-5">
                                        <button type="submit" class="btn btn-lg btn-primary w-100">تسجيل الدخول</button>
                                    </div>

                                    <!-- Language Switch -->
                                    
                                </form>
                            </div>
                        </div>

                        <!-- Right side: Image -->
                        <div class="col-lg-6 bg-primary order-0 order-lg-1">
                            <div class="h-100 d-flex align-items-center justify-content-center">
                                <img src="{{ asset('assets/images/auth-user-1212.webp') }}" alt="صورة المستخدم" class="img-fluid">
                            </div>
                        </div>
                    </div>
                
                </div>
            </div>
        </div>
    </main>

    @include('partials.script')
</body>
</html>

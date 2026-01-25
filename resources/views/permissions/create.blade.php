@extends('layouts.app')

@section('title', 'إضافة صلاحية جديدة')

@section('content')
<div class="nxl-content">
    <div class="content-area" data-scrollbar-target="#psScrollbarInit">

        <div class="content-area-body p-3">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0">إضافة صلاحية جديدة</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('permissions.store') }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">اسم الصلاحية <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name') }}" 
                                           placeholder="مثال: view_users" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="display_name" class="form-label">الاسم المعروض</label>
                                    <input type="text" class="form-control @error('display_name') is-invalid @enderror" 
                                           id="display_name" name="display_name" value="{{ old('display_name') }}" 
                                           placeholder="مثال: عرض المستخدمين">
                                    @error('display_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="module" class="form-label">الوحدة</label>
                                    <select class="form-select @error('module') is-invalid @enderror" id="module" name="module">
                                        <option value="">اختر الوحدة</option>
                                        <option value="users" {{ old('module') == 'users' ? 'selected' : '' }}>المستخدمين</option>
                                        <option value="roles" {{ old('module') == 'roles' ? 'selected' : '' }}>الأدوار</option>
                                        <option value="permissions" {{ old('module') == 'permissions' ? 'selected' : '' }}>الصلاحيات</option>
                                        <option value="leads" {{ old('module') == 'leads' ? 'selected' : '' }}>العملاء المحتملين</option>
                                        <option value="representatives" {{ old('module') == 'representatives' ? 'selected' : '' }}>الممثلون</option>
                                        <option value="interviews" {{ old('module') == 'interviews' ? 'selected' : '' }}>المقابلات</option>
                                        <option value="messages" {{ old('module') == 'messages' ? 'selected' : '' }}>الرسائل</option>
                                        <option value="settings" {{ old('module') == 'settings' ? 'selected' : '' }}>الإعدادات</option>
                                        <option value="reports" {{ old('module') == 'reports' ? 'selected' : '' }}>التقارير</option>
                                        <option value="dashboard" {{ old('module') == 'dashboard' ? 'selected' : '' }}>لوحة التحكم</option>
                                    </select>
                                    @error('module')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="description" class="form-label">الوصف</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" 
                                              id="description" name="description" rows="3" 
                                              placeholder="وصف مختصر للصلاحية">{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('permissions.index') }}" class="btn btn-outline-secondary">إلغاء</a>
                            <button type="submit" class="btn btn-primary">حفظ الصلاحية</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

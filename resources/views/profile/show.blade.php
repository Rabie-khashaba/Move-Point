@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">تفاصيل الملف الشخصي</div>
                <div class="card-body">
                    {{-- Display Success Message --}}
                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif

                    {{-- Profile Form --}}
                    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                        @csrf

                        {{-- Name Field --}}
                        <div class="mb-3">
                            <label for="name" class="form-label">الاسم</label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                            @error('name')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Email Field --}}
                        <div class="mb-3">
                            <label for="email" class="form-label">البريد الإلكتروني</label>
                            <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                            @error('email')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Phone Field --}}
                        <div class="mb-3">
                            <label for="phone" class="form-label">رقم الهاتف</label>
                            <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone', $user->phone) }}">
                            @error('phone')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Avatar Field --}}
                        <div class="mb-3">
                            <label for="avatar" class="form-label">الصورة الشخصية</label>
                            <input type="file" class="form-control" id="avatar" name="avatar">
                            @if ($user->avatar)
                                <div class="mt-2">
                                    <img src="{{ asset('storage/app/public/' . $user->avatar) }}" alt="User Avatar" class="img-fluid rounded-circle" width="100">
                                </div>
                            @endif
                            @error('avatar')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Employee Name Field --}}
                        <div class="mb-3">
                            <label for="employee_name" class="form-label">اسم الموظف</label>
                            <input type="text" class="form-control" id="employee_name" name="employee_name" value="{{ old('employee_name', $employee->name ?? '') }}">
                            @error('employee_name')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Employee Phone Field --}}
                        <div class="mb-3">
                            <label for="employee_phone" class="form-label">هاتف الموظف</label>
                            <input type="text" class="form-control" id="employee_phone" name="employee_phone" value="{{ old('employee_phone', $employee->phone ?? '') }}">
                            @error('employee_phone')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Employee Address Field --}}
                        <div class="mb-3">
                            <label for="address" class="form-label">عنوان الموظف</label>
                            <input type="text" class="form-control" id="address" name="address" value="{{ old('address', $employee->address ?? '') }}">
                            @error('address')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Employee Salary Field --}}
                        <div class="mb-3">
                            <label for="salary" class="form-label">الراتب</label>
                            <input type="text" class="form-control" id="salary" name="salary" value="{{ old('salary', $employee->salary ?? '') }}">
                            @error('salary')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Save Button --}}
                        <button type="submit" class="btn btn-primary">حفظ التغييرات</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

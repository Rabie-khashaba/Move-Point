@extends('layouts.app')

@section('title', 'تعديل موظف')

@section('content')
<div class="nxl-content">
    <!-- [ page-header ] start -->
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('employees.index') }}">الموظفين</a></li>
                <li class="breadcrumb-item">تعديل</li>
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
                        <h5 class="card-title mb-0">تعديل بيانات الموظف</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('employees.update', $employee->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <!-- Employee Fields -->
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">الاسم</label>
                                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                           value="{{ old('name', $employee->name) }}" required>
                                    @error('name')<div class="text-danger">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">الهاتف</label>
                                    <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror"
                                           value="{{ old('phone', $employee->phone) }}">
                                    @error('phone')<div class="text-danger">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">رقم الواتس</label>
                                    <input type="text" name="whatsapp_phone" class="form-control @error('whatsapp_phone') is-invalid @enderror"
                                           value="{{ old('whatsapp_phone', $employee->whatsapp_phone) }}">
                                    @error('whatsapp_phone')<div class="text-danger">{{ $message }}</div>@enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">العنوان</label>
                                    <input type="text" name="address" class="form-control @error('address') is-invalid @enderror"
                                           value="{{ old('address', $employee->address) }}">
                                    @error('address')<div class="text-danger">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">جهة الاتصال الاسرة</label>
                                    <input type="text" name="contact" class="form-control @error('contact') is-invalid @enderror"
                                           value="{{ old('contact', $employee->contact) }}"
                                           minlength="11" maxlength="11" pattern="[0-9]{11}"
                                           oninput="this.value=this.value.replace(/[^0-9]/g,'');">
                                    @error('contact')<div class="text-danger">{{ $message }}</div>@enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">الرقم القومي</label>
                                    <input type="text" name="national_id" class="form-control @error('national_id') is-invalid @enderror"
                                           value="{{ old('national_id', $employee->national_id) }}" required
                                           minlength="14" maxlength="14" pattern="[0-9]{14}">
                                    @error('national_id')<div class="text-danger">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">الراتب</label>
                                    <input type="text" name="salary" class="form-control @error('salary') is-invalid @enderror"
                                           value="{{ old('salary', $employee->salary) }}">
                                    @error('salary')<div class="text-danger">{{ $message }}</div>@enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">تاريخ البداية</label>
                                    <input type="text" name="start_date" id="start_date" class="form-control @error('start_date') is-invalid @enderror"
                                           value="{{ old('start_date', $employee->start_date) }}" required>
                                    @error('start_date')<div class="text-danger">{{ $message }}</div>@enderror
                                </div>
                              <div class="col-md-6 mb-3">
                                    <label class="form-label">الدور</label>
                                    <select name="role" id="role" class="form-control" required>
                                        <option value="">اختر الدور</option>
                                        @foreach($roles as $role)
                                            <option value="{{ $role->id }}"
                                                {{ old('role', $employee->user->roles->first()->id ?? '') == $role->id ? 'selected' : '' }}>
                                                {{ $role->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('role_id')<div class="text-danger">{{ $message }}</div>@enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">الوردية</label>
                                    <select name="shift" class="form-control">
                                        <option value="مسائي" {{ old('shift', $employee->shift) == 'مسائي' ? 'selected' : '' }}>مسائي</option>
                                        <option value="صباحي" {{ old('shift', $employee->shift) == 'صباحي' ? 'selected' : '' }}>صباحي</option>
                                    </select>
                                    @error('shift')<div class="text-danger">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">أيام الإجازة</label>
                                    <input type="number" name="days_off" class="form-control @error('days_off') is-invalid @enderror"
                                           value="{{ old('days_off', $employee->days_off) }}" min="0">
                                    @error('days_off')<div class="text-danger">{{ $message }}</div>@enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">القسم</label>
                                    <select name="department_id" class="form-control" required>
                                        <option value="">اختر القسم</option>
                                        @foreach($departments as $department)
                                            <option value="{{ $department->id }}" {{ old('department_id', $employee->department_id) == $department->id ? 'selected' : '' }}>
                                                {{ $department->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('department_id')<div class="text-danger">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">كلمة المرور (اتركها فارغة إذا لا تريد تغييرها)</label>
                                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                                           placeholder="كلمة المرور الجديدة">
                                    @error('password')<div class="text-danger">{{ $message }}</div>@enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">تأكيد كلمة المرور</label>
                                    <input type="password" name="password_confirmation" class="form-control" placeholder="تأكيد كلمة المرور">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">المرفقات (يمكنك رفع ملفات إضافية)</label>
                                    <input type="file" name="attachments[]" class="form-control" multiple>
                                    @error('attachments')<div class="text-danger">{{ $message }}</div>@enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">الحالة</label>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="is_active" value="1" {{ old('is_active', $employee->is_active) ? 'checked' : '' }}>
                                        <label class="form-check-label">نشط</label>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('employees.index') }}" class="btn btn-light">إلغاء</a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="feather-save me-2"></i>
                                    تحديث الموظف
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- [ Main Content ] end -->
</div>
@endsection

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/vanillajs-datepicker@1.3.4/dist/css/datepicker.min.css">
<script src="https://cdn.jsdelivr.net/npm/vanillajs-datepicker@1.3.4/dist/js/datepicker.min.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const elem = document.querySelector('#start_date');
        new Datepicker(elem, {
            format: 'yyyy-mm-dd',
            autohide: true,
        });
    });
</script>

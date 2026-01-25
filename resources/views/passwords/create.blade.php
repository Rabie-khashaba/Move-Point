@extends('layouts.app')

@section('title', 'إنشاء طلب إعادة تعيين كلمة مرور')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">إنشاء طلب إعادة تعيين كلمة مرور جديد</h4>
                    <div class="card-tools">
                        <a href="{{ route('passwords.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-right"></i> العودة للقائمة
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('passwords.store') }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="phone" class="form-label">رقم الهاتف <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('phone') is-invalid @enderror" 
                                           id="phone" 
                                           name="phone" 
                                           value="{{ old('phone') }}" 
                                           placeholder="أدخل رقم الهاتف (11 رقم)"
                                           maxlength="11"
                                           required>
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">يجب أن يكون رقم الهاتف مكون من 11 رقم</small>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="notes" class="form-label">ملاحظات</label>
                                    <textarea class="form-control @error('notes') is-invalid @enderror" 
                                              id="notes" 
                                              name="notes" 
                                              rows="3" 
                                              placeholder="أدخل أي ملاحظات إضافية">{{ old('notes') }}</textarea>
                                    @error('notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i>
                                    <strong>ملاحظة:</strong> عند إنشاء طلب إعادة تعيين كلمة مرور، سيتم إرسال كلمة مرور جديدة عبر WhatsApp إلى الرقم المحدد.
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> إنشاء الطلب
                                </button>
                                <a href="{{ route('passwords.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> إلغاء
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Format phone number input
    $('#phone').on('input', function() {
        var value = $(this).val();
        // Remove any non-digit characters
        value = value.replace(/\D/g, '');
        // Limit to 11 digits
        if (value.length > 11) {
            value = value.substring(0, 11);
        }
        $(this).val(value);
    });

    // Form validation
    $('form').on('submit', function(e) {
        var phone = $('#phone').val();
        if (phone.length !== 11) {
            e.preventDefault();
            alert('يجب أن يكون رقم الهاتف مكون من 11 رقم');
            $('#phone').focus();
            return false;
        }
    });
});
</script>
@endpush

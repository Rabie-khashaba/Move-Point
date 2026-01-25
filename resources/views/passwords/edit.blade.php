@extends('layouts.app')

@section('title', 'تعديل طلب إعادة تعيين كلمة مرور')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">تعديل طلب إعادة تعيين كلمة مرور</h4>
                    <div class="card-tools">
                        <a href="{{ route('passwords.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-right"></i> العودة للقائمة
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('passwords.update', $resetRequest->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="phone" class="form-label">رقم الهاتف</label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="phone" 
                                           value="{{ $resetRequest->phone }}" 
                                           readonly>
                                    <small class="form-text text-muted">رقم الهاتف لا يمكن تعديله</small>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="status" class="form-label">الحالة <span class="text-danger">*</span></label>
                                    <select class="form-control @error('status') is-invalid @enderror" 
                                            id="status" 
                                            name="status" 
                                            required>
                                        <option value="pending" {{ old('status', $resetRequest->status) == 'pending' ? 'selected' : '' }}>في الانتظار</option>
                                        <option value="sent" {{ old('status', $resetRequest->status) == 'sent' ? 'selected' : '' }}>تم الإرسال</option>
                                        <option value="completed" {{ old('status', $resetRequest->status) == 'completed' ? 'selected' : '' }}>مكتمل</option>
                                        <option value="failed" {{ old('status', $resetRequest->status) == 'failed' ? 'selected' : '' }}>فشل</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="notes" class="form-label">ملاحظات</label>
                                    <textarea class="form-control @error('notes') is-invalid @enderror" 
                                              id="notes" 
                                              name="notes" 
                                              rows="3" 
                                              placeholder="أدخل أي ملاحظات إضافية">{{ old('notes', $resetRequest->notes) }}</textarea>
                                    @error('notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">معلومات الطلب</label>
                                    <div class="form-control-plaintext">
                                        <strong>تاريخ الإنشاء:</strong> {{ $resetRequest->created_at->format('Y-m-d H:i:s') }}<br>
                                        <strong>آخر تحديث:</strong> {{ $resetRequest->updated_at->format('Y-m-d H:i:s') }}<br>
                                        @if($resetRequest->user)
                                            <strong>المستخدم:</strong> {{ $resetRequest->user->name ?? 'غير محدد' }}
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> حفظ التغييرات
                                </button>
                                <a href="{{ route('passwords.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> إلغاء
                                </a>
                                <a href="{{ route('passwords.show', $resetRequest->id) }}" class="btn btn-info">
                                    <i class="fas fa-eye"></i> عرض التفاصيل
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
    // Form validation
    $('form').on('submit', function(e) {
        var status = $('#status').val();
        if (!status) {
            e.preventDefault();
            alert('يجب اختيار حالة للطلب');
            $('#status').focus();
            return false;
        }
    });
});
</script>
@endpush

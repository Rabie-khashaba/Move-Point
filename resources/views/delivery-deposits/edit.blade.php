@extends('layouts.app')

@section('title', 'تعديل إيداع التسليم')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('delivery-deposits.index') }}">إيداعات التسليم</a></li>
                        <li class="breadcrumb-item active">تعديل</li>
                    </ol>
                </div>
                <h4 class="page-title">تعديل إيداع التسليم</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('delivery-deposits.update', $deposit->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="representative_id" class="form-label">المندوب <span class="text-danger">*</span></label>
                                    <select name="representative_id" id="representative_id" class="form-select @error('representative_id') is-invalid @enderror" required>
                                        <option value="">اختر المندوب</option>
                                        @foreach($representatives as $representative)
                                            <option value="{{ $representative->id }}" 
                                                {{ (old('representative_id', $deposit->representative_id) == $representative->id) ? 'selected' : '' }}>
                                                {{ $representative->name }} - {{ $representative->phone }}
                                                @if($representative->governorate)
                                                    ({{ $representative->governorate->name }})
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('representative_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="amount" class="form-label">المبلغ <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" min="0" name="amount" id="amount" 
                                           class="form-control @error('amount') is-invalid @enderror" 
                                           value="{{ old('amount', $deposit->amount) }}" required>
                                    @error('amount')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="status" class="form-label">الحالة <span class="text-danger">*</span></label>
                                    <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                                        <option value="pending" {{ (old('status', $deposit->status) == 'pending') ? 'selected' : '' }}>في الانتظار</option>
                                        <option value="delivered" {{ (old('status', $deposit->status) == 'delivered') ? 'selected' : '' }}>تم التسليم</option>
                                        <option value="not_delivered" {{ (old('status', $deposit->status) == 'not_delivered') ? 'selected' : '' }}>لم يتم التسليم</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">تاريخ الإنشاء</label>
                                    <input type="text" class="form-control" value="{{ $deposit->created_at->format('Y-m-d H:i') }}" readonly>
                                </div>
                            </div>
                        </div>

                                                 @if($deposit->receipt_image)
                         <div class="row">
                             <div class="col-12">
                                 <div class="mb-3">
                                     <label class="form-label">إيصال الإيداع الحالي</label>
                                     <div class="text-center">
                                         <div class="receipt-image-container">
                                             @php
                                                 // Check if receipt_image is already a full URL
                                                 if (filter_var($deposit->receipt_image, FILTER_VALIDATE_URL)) {
                                                     // If it's an old storage URL format, convert it to the new format
                                                     if (strpos($deposit->receipt_image, '/storage/attachments/') !== false) {
                                                         $imageUrl = str_replace('/storage/attachments/', '/storage/app/public/attachments/', $deposit->receipt_image);
                                                     } elseif (strpos($deposit->receipt_image, '/storage/representatives/attachments/') !== false) {
                                                         $imageUrl = str_replace('/storage/representatives/attachments/', '/storage/app/public/representatives/attachments/', $deposit->receipt_image);
                                                     } elseif (strpos($deposit->receipt_image, '/storage/delivery-receipts/') !== false) {
                                                         $imageUrl = str_replace('/storage/delivery-receipts/', '/storage/app/public/delivery-receipts/', $deposit->receipt_image);
                                                     } elseif (strpos($deposit->receipt_image, '/storage/sliders/') !== false) {
                                                         $imageUrl = str_replace('/storage/sliders/', '/storage/app/public/sliders/', $deposit->receipt_image);
                                                     } else {
                                                         $imageUrl = $deposit->receipt_image;
                                                     }
                                                 } else {
                                                     $imageUrl = asset('storage/app/public/' . $deposit->receipt_image);
                                                 }
                                             @endphp
                                             <img src="{{ $imageUrl }}" 
                                                  alt="إيصال الإيداع" class="img-fluid rounded shadow-sm receipt-image" 
                                                  style="max-width: 100%; height: auto; max-height: 300px; object-fit: contain;">
                                         </div>
                                         <div class="mt-2">
                                             <a href="{{ $imageUrl }}" 
                                                target="_blank" class="btn btn-sm btn-outline-primary">
                                                 <i class="feather-external-link me-1"></i>عرض بالحجم الكامل
                                             </a>
                                         </div>
                                     </div>
                                 </div>
                             </div>
                         </div>
                         @endif

                        <div class="row">
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="notes" class="form-label">ملاحظات</label>
                                    <textarea name="notes" id="notes" rows="3" 
                                              class="form-control @error('notes') is-invalid @enderror">{{ old('notes', $deposit->notes) }}</textarea>
                                    @error('notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('delivery-deposits.index') }}" class="btn btn-secondary">
                                        <i class="feather-x me-1"></i>إلغاء
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="feather-save me-1"></i>حفظ التغييرات
                                    </button>
                                </div>
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
        // Initialize select2 for better UX
        $('#representative_id').select2({
            placeholder: 'اختر المندوب',
            allowClear: true,
            dir: 'rtl'
        });
    });
</script>
@endpush

@push('styles')
<style>
    .receipt-image-container {
        position: relative;
        display: inline-block;
        max-width: 100%;
    }
    
    .receipt-image {
        transition: transform 0.3s ease;
        cursor: pointer;
    }
    
    .receipt-image:hover {
        transform: scale(1.02);
    }
    
    @media (max-width: 768px) {
        .receipt-image {
            max-width: 100% !important;
            max-height: 250px !important;
        }
    }
    
    @media (max-width: 576px) {
        .receipt-image {
            max-height: 200px !important;
        }
    }
</style>
@endpush

@extends('layouts.app')

@section('title', 'إضافة إيداع تسليم جديد')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('delivery-deposits.index') }}">إيداعات التسليم</a></li>
                        <li class="breadcrumb-item active">إضافة جديد</li>
                    </ol>
                </div>
                <h4 class="page-title">إضافة إيداع تسليم جديد</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('delivery-deposits.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="representative_id" class="form-label">المندوب <span class="text-danger">*</span></label>
                                    <select name="representative_id" 
                                            id="representative_id" 
                                            class="form-select select2 @error('representative_id') is-invalid @enderror" 
                                            required>
                                        <option value="">اختر المندوب</option>
                                        @foreach($representatives as $representative)
                                            <option value="{{ $representative->id }}" {{ old('representative_id') == $representative->id ? 'selected' : '' }}>
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
                                    <label for="amount" class="form-label">المبلغ </label>
                                    <input type="number" step="0.01" min="0" name="amount" id="amount" 
                                           class="form-control @error('amount') is-invalid @enderror" 
                                           value="{{ old('amount') }}">
                                    @error('amount')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                             <div class="col-md-6">
                                 <div class="mb-3">
                                     <label for="receipt_image" class="form-label">صورة الإيصال</label>
                                     <input type="file" class="form-control @error('receipt_image') is-invalid @enderror" 
                                            id="receipt_image" name="receipt_image" accept="image/*">
                                     <div class="form-text">يجب أن تكون الصورة بصيغة JPEG, PNG, أو JPG وحجمها لا يتجاوز 2 ميجابايت</div>
                                     @error('receipt_image')
                                         <div class="invalid-feedback">{{ $message }}</div>
                                     @enderror
                                 </div>
                             </div>
                             
                             <div class="col-md-6">
                                 <div class="mb-3">
                                     <label for="notes" class="form-label">ملاحظات</label>
                                     <textarea name="notes" id="notes" rows="3" 
                                               class="form-control @error('notes') is-invalid @enderror">{{ old('notes') }}</textarea>
                                     @error('notes')
                                         <div class="invalid-feedback">{{ $message }}</div>
                                     @enderror
                                 </div>
                             </div>
                         </div>

                         <!-- Image Preview Area -->
                         <div class="row">
                             <div class="col-12">
                                 <div id="imagePreview" class="text-center" style="display: none;">
                                     <!-- Preview will be inserted here by JavaScript -->
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
                                        <i class="feather-save me-1"></i>حفظ
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

<!-- Lightbox for receipt images -->
<div id="receiptLightbox" class="lightbox">
    <span class="lightbox-close" onclick="closeLightbox()">&times;</span>
    <div class="lightbox-content">
        <img id="lightboxImg" class="lightbox-img" src="" alt="إيصال الإيداع">
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
            width: '100%',   // عشان يوسع على قد الفورم
            dir: "rtl",      // للاتجاه العربي
            dropdownParent: $('#representative_id').closest('.mb-3') // عشان ما يطلعش برة المودال/الكارد
        });

        
        // Preview image before upload
        $('#receipt_image').change(function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#imagePreview').html(`
                        <div class="receipt-image-container">
                            <img src="${e.target.result}" 
                                 alt="معاينة الإيصال" class="img-fluid rounded shadow-sm receipt-image" 
                                 style="max-width: 100%; height: auto; max-height: 300px; object-fit: contain;">
                        </div>
                        <div class="mt-2">
                            <small class="text-muted">معاينة الإيصال المحدد</small>
                        </div>
                    `);
                    $('#imagePreview').show();
                };
                reader.readAsDataURL(file);
            } else {
                $('#imagePreview').hide();
            }
        });
        
        // Lightbox functionality
        function openLightbox(imageSrc) {
            document.getElementById('lightboxImg').src = imageSrc;
            document.getElementById('receiptLightbox').style.display = 'block';
            document.body.style.overflow = 'hidden'; // Prevent scrolling
        }
        
        function closeLightbox() {
            document.getElementById('receiptLightbox').style.display = 'none';
            document.body.style.overflow = 'auto'; // Restore scrolling
        }
        
        // Close lightbox when clicking outside the image
        document.getElementById('receiptLightbox').addEventListener('click', function(e) {
            if (e.target === this) {
                closeLightbox();
            }
        });
        
        // Close lightbox with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeLightbox();
            }
        });
        
        // Add click event to receipt images (for preview)
        $(document).on('click', '.receipt-image', function() {
            openLightbox(this.src);
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
    
    /* Lightbox styles */
    .lightbox {
        display: none;
        position: fixed;
        z-index: 9999;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.9);
        backdrop-filter: blur(5px);
    }
    
    .lightbox-content {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        max-width: 90%;
        max-height: 90%;
    }
    
    .lightbox-img {
        width: 100%;
        height: auto;
        max-height: 90vh;
        object-fit: contain;
        border-radius: 8px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
    }
    
    .lightbox-close {
        position: absolute;
        top: 20px;
        right: 30px;
        color: #fff;
        font-size: 40px;
        font-weight: bold;
        cursor: pointer;
        z-index: 10000;
    }
    
    @media (max-width: 768px) {
        .receipt-image {
            max-width: 100% !important;
            max-height: 250px !important;
        }
        
        .lightbox-close {
            top: 10px;
            right: 20px;
            font-size: 30px;
        }
    }
    
    @media (max-width: 576px) {
        .receipt-image {
            max-height: 200px !important;
        }
    }
</style>
@endpush

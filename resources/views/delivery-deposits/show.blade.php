@extends('layouts.app')

@section('title', 'تفاصيل إيداع التسليم')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('delivery-deposits.index') }}">إيداعات التسليم</a></li>
                        <li class="breadcrumb-item active">تفاصيل</li>
                    </ol>
                </div>
                <h4 class="page-title">تفاصيل إيداع التسليم</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="card-title mb-3">معلومات الإيداع</h5>
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold">المندوب:</label>
                                <p class="form-control-plaintext">
                                    {{ $deposit->representative->name ?? 'غير محدد' }}
                                </p>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">رقم الهاتف:</label>
                                <p class="form-control-plaintext">
                                    {{ $deposit->representative->phone ?? 'غير محدد' }}
                                </p>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">المحافظة:</label>
                                <p class="form-control-plaintext">
                                    {{ $deposit->representative->governorate->name ?? 'غير محدد' }}
                                </p>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">المبلغ:</label>
                                <p class="form-control-plaintext text-primary fw-bold">
                                    {{ number_format($deposit->amount, 2) }} جنيه
                                </p>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <h5 class="card-title mb-3">معلومات الحالة</h5>
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold">الحالة:</label>
                                <div>
                                    @if($deposit->status == 'pending')
                                        <span class="badge bg-warning">في الانتظار</span>
                                    @elseif($deposit->status == 'delivered')
                                        <span class="badge bg-success">تم التسليم</span>
                                    @elseif($deposit->status == 'not_delivered')
                                        <span class="badge bg-danger">لم يتم التسليم</span>
                                    @endif
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">تاريخ الإنشاء:</label>
                                <p class="form-control-plaintext">
                                    {{ $deposit->created_at->format('Y-m-d H:i') }}
                                </p>
                            </div>

                            @if($deposit->delivered_at)
                            <div class="mb-3">
                                <label class="form-label fw-bold">تاريخ التسليم:</label>
                                <p class="form-control-plaintext">
                                    {{ $deposit->delivered_at->format('Y-m-d H:i') }}
                                </p>
                            </div>
                            @endif

                            @if($deposit->notes)
                            <div class="mb-3">
                                <label class="form-label fw-bold">ملاحظات:</label>
                                <p class="form-control-plaintext">
                                    {{ $deposit->notes }}
                                </p>
                            </div>
                            @endif
                        </div>
                    </div>

                                         @if($deposit->receipt_image)
                     <div class="row mt-4">
                         <div class="col-12">
                             <h5 class="card-title mb-3">إيصال الإيداع</h5>
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
                                          style="max-width: 100%; height: auto; max-height: 500px; object-fit: contain;">
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
                     @endif

                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('delivery-deposits.index') }}" class="btn btn-secondary">
                                    <i class="feather-arrow-right me-1"></i>العودة للقائمة
                                </a>
                                
                                @can('edit_delivery_deposits')
                                <a href="{{ route('delivery-deposits.edit', $deposit->id) }}" class="btn btn-primary">
                                    <i class="feather-edit me-1"></i>تعديل
                                </a>
                                @endcan

                                @if($deposit->status == 'pending')
                                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#uploadReceiptModal">
                                    <i class="feather-upload me-1"></i>رفع إيصال
                                </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if($deposit->status == 'pending')
<!-- Upload Receipt Modal -->
<div class="modal fade" id="uploadReceiptModal" tabindex="-1" aria-labelledby="uploadReceiptModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadReceiptModalLabel">رفع إيصال الإيداع</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('delivery-deposits.update-receipt', $deposit->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="receipt_image" class="form-label">صورة الإيصال <span class="text-danger">*</span></label>
                        <input type="file" class="form-control @error('receipt_image') is-invalid @enderror" 
                               id="receipt_image" name="receipt_image" accept="image/*" required>
                        <div class="form-text">يجب أن تكون الصورة بصيغة JPEG, PNG, أو JPG وحجمها لا يتجاوز 2 ميجابايت</div>
                        @error('receipt_image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-success">
                        <i class="feather-upload me-1"></i>رفع الإيصال
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<!-- Lightbox for receipt images -->
<div id="receiptLightbox" class="lightbox">
    <span class="lightbox-close" onclick="closeLightbox()">&times;</span>
    <div class="lightbox-content">
        <img id="lightboxImg" class="lightbox-img" src="" alt="إيصال الإيداع">
    </div>
</div>
@endsection

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
            max-height: 300px !important;
        }
        
        .lightbox-close {
            top: 10px;
            right: 20px;
            font-size: 30px;
        }
    }
    
    @media (max-width: 576px) {
        .receipt-image {
            max-height: 250px !important;
        }
    }
</style>
@endpush

@push('scripts')
<script>
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
    
    // Add click event to receipt images
    document.addEventListener('DOMContentLoaded', function() {
        const receiptImages = document.querySelectorAll('.receipt-image');
        receiptImages.forEach(function(img) {
            img.addEventListener('click', function() {
                openLightbox(this.src);
            });
        });
    });
</script>
@endpush
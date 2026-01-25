@extends('layouts.app')

@section('title', 'عرض إيصال الإيداع')

@section('content')
<div class="nxl-content">
    <div class="page-header">
        <h5 class="mb-0">إيصال إيداع - {{ $deposit->representative->name ?? 'غير محدد' }}</h5>
        <a href="{{ route('delivery-deposits.index') }}" class="btn btn-light ms-auto">
            <i class="feather-arrow-left me-2"></i> الرجوع
        </a>
    </div>

    <div class="card mt-4">
        <div class="card-body text-center">
            @if($deposit->receipt_image)
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
                     alt="إيصال الإيداع" 
                     class="img-fluid rounded shadow" 
                     style="max-height: 600px;">
                <div class="mt-3">
                    <span class="badge bg-primary">عدد الطلبات: {{ $deposit->orders_count ?? 'غير محدد' }}</span>
                </div>
            @else
                <p class="text-muted">لا يوجد إيصال لهذا الإيداع.</p>
            @endif
        </div>
    </div>
</div>
@endsection

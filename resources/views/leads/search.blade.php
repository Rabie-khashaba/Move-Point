@extends('layouts.app')

@section('title', 'استعلام حشلث')

@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">استعلام</h3>
            </div>
            <div class="card-body">
                <!-- Search Form -->
                <form method="GET" action="{{ route('leads.search') }}" class="mb-4">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="input-group">
                                <input type="text" name="phone" value="{{ $phone }}" class="form-control"
                                    placeholder="أدخل رقم الهاتف للبحث..." required>
                                <button class="btn btn-primary" type="submit">
                                    <i class="feather-search me-1"></i> بحث
                                </button>
                            </div>
                        </div>
                    </div>
                </form>

                <!-- Search Results -->
                @if($phone)
                    @if($lead)
                        <div class="">
                            <h5 class="alert-heading">تم العثور على العميل المحتمل</h5>
                            <div class="row">
                                <div class="col-md-4">
                                    <strong>الرقم:</strong> {{ $lead->id }}
                                </div>
                                <div class="col-md-4">
                                    <strong>الاسم:</strong> {{ $lead->name }}
                                </div>
                                <div class="col-md-4">
                                    <strong>رقم الهاتف:</strong> {{ $lead->phone }}
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-md-12">
                                    <strong>مُعين إلى:</strong>
                                    @if($lead->assignedTo)
                                        @php
                                            $assignedUser = $lead->assignedTo;
                                            $assignedName = $assignedUser->name;

                                            // If user has employee record, get name from employee table
                                            if ($assignedUser->employee) {
                                                $assignedName = $assignedUser->employee->name;
                                            }
                                        @endphp
                                        {{ $assignedName }}
                                    @else
                                        <span class="text-muted">غير مُعين</span>
                                    @endif
                                </div>
                            </div>

                            @if($representative)
                    <div class=" mt-3">
                        <h5 class="alert-heading">تم العثور على مندوب</h5>

                        <p>
                            <strong>اسم المندوب:</strong>
                            {{ $representative->name ?? 'غير محدد' }}
                        </p>

                        <p>
                            <strong>حالة المندوب:</strong>
                            @if($representative->status == 1)
                                <span class="badge bg-success">مندوب فعلي</span>
                            @else
                                <span class="badge bg-warning text-dark">مندوب غير مكتمل</span>
                            @endif
                        </p>
                    </div>
                @endif

                            <div class="mt-3">
                                <a href="{{ route('leads.show', $lead->id) }}" class="btn btn-outline-primary">
                                    <i class="feather-eye me-1"></i> عرض التفاصيل الكاملة
                                </a>
                            </div>
                        </div>
                    @else
                        <div>
                            <h5>لم يتم العثور على عميل محتمل</h5>
                            <p class="mb-0">لا يوجد عميل محتمل برقم الهاتف: <strong>{{ $phone }}</strong></p>
                        </div>
                    @endif
                @else
                    <div class="text-center text-muted">
                        <i class="feather-search" style="font-size: 48px; opacity: 0.3;"></i>
                        <p class="mt-2">أدخل رقم الهاتف للبحث عن العميل المحتمل</p>
                    </div>
                @endif

                
            </div>
        </div>
    </div>
@endsection
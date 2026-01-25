@extends('layouts.app')

@section('title', 'قائمة الانتظار - العملاء المحتملين')


@section('styles')
    <style>
        /* Ensure only one pagination is shown (hide any DataTables UI if injected) */
        .dataTables_wrapper .dataTables_paginate,
        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_info,
        .dataTables_wrapper .dataTables_filter {
            display: none !important;
        }

        /* Filters section styling */
        .filters-section {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            border: 1px solid #e9ecef;
        }

        .filters-section .form-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 8px;
        }

        .filters-section .form-control {
            border-radius: 6px;
            border: 1px solid #ced4da;
        }

        .filters-section .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }

        /* Active filter indicator */
        .filter-active {
            background-color: #e3f2fd;
            border-color: #2196f3;
        }

        /* Filter summary */
        .filter-summary {
            background: #e8f5e8;
            border: 1px solid #4caf50;
            border-radius: 6px;
            padding: 10px 15px;
            margin-bottom: 15px;
            font-size: 14px;
        }

        .filter-summary .badge {
            background-color: #4caf50;
            color: white;
            margin-right: 5px;
        }


    </style>
@endsection

@section('content')
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
                <li class="breadcrumb-item"><a href="{{ route('leads.index') }}">العملاء المحتملين</a></li>
                <li class="breadcrumb-item">قائمة الانتظار</li>
            </ul>
        </div>
        <div class="ms-auto">
            <a href="{{ route('leads.index') }}" class="btn btn-secondary">عودة إلى القائمة</a>
        </div>
    </div>

    <div class="container my-3">
        <div class="row">
            @foreach($governorates as $governorate)
                <div class="col-md-2 col-sm-6 mb-3">
                    <div style="font-size: 10px;" class="d-flex justify-content-between align-items-center p-3 border rounded shadow-sm bg-light">
                        <span class="fw-bold">{{ $governorate->name }}</span>
                        <span class="badge bg-primary rounded-pill">
                            {{ $governorate->leads_count }}
                        </span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>


    <!-- Filters Section -->
    <div class="filters-section ">
        @if(request('date_from') || request('date_to') || request('status') || request('search'))
            <div class="filter-summary">
                <strong>الفلاتر المطبقة:</strong>
                @if(request('search'))
                    <span class="badge">بحث: {{ request('search') }}</span>
                @endif
                @if(request('date_from') || request('date_to'))
                    <span class="badge">التاريخ: {{ request('date_from') ?? 'البداية' }} - {{ request('date_to') ?? 'النهاية' }}</span>
                @endif

            </div>
        @endif

        <div class="row g-3">
            <div class="col-md-3">
                <label class="form-label">بحث</label>
                <input type="text" class="form-control {{ request('search') ? 'filter-active' : '' }}" id="searchInput" placeholder="الاسم / الهاتف / المحافظة" value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label">من تاريخ</label>
                <input type="date" class="form-control {{ request('date_from') ? 'filter-active' : '' }}" id="dateFrom" value="{{ request('date_from', now()->toDateString()) }}">
            </div>
            <div class="col-md-2">
                <label class="form-label">إلى تاريخ</label>
                <input type="date" class="form-control {{ request('date_to') ? 'filter-active' : '' }}" id="dateTo" value="{{ request('date_to', now()->toDateString()) }}">
            </div>

            <div class="col-md-3 d-flex align-items-end">
                <div class="d-flex gap-2 w-100">
                    <button class="btn btn-primary flex-fill" onclick="applyFilters()">
                        <i class="feather-search me-1"></i>
                        تطبيق الفلتر
                    </button>
                    <button class="btn btn-outline-secondary" onclick="clearFilters()">
                        <i class="feather-refresh-cw me-1"></i>
                        مسح
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="card mt-3">
        <div class="card-header">
            <h5 class="mb-0">العملاء المحتملون في المحافظات/المناطق قيد الانتظار</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>العميل</th>
                            <th>الهاتف</th>
                            <th>المحافظة</th>
                            <th>المنطقة</th>
                            <th>المصدر</th>
                            <th>التاريخ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($leads as $lead)
                            <tr>
                                <td><a href="{{ route('leads.show', $lead->id) }}">{{ $lead->name }}</a></td>
                                <td><a href="tel:{{ $lead->phone }}">{{ $lead->phone }}</a></td>
                                <td>{{ $lead->governorate?->name ?? '-' }}</td>
                                <td>{{ $lead->location?->name ?? '-' }}</td>
                                <td>{{ $lead->source?->name ?? '-' }}</td>
                                <td>{{ $lead->created_at?->format('Y-m-d H:i') ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">لا توجد عناصر في قائمة الانتظار حالياً</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @if($leads->hasPages())
        <div class="d-flex justify-content-center mt-3">
            {{ $leads->appends(request()->query())->links('pagination::bootstrap-5') }}
        </div>
    @endif
@endsection




<script>

    document.addEventListener('DOMContentLoaded', function () {

        const urlParams = new URLSearchParams(window.location.search);
        if (!urlParams.has('date_from') && !urlParams.has('date_to') && !urlParams.has('search')) {
            const today = new Date().toISOString().split('T')[0];
            const url = new URL(window.location.href);
            url.searchParams.set('date_from', today);
            url.searchParams.set('date_to', today);
            window.location.href = url.toString();
            return; // Exit early to let the page reload with filters
        }

    // Apply filters function
    window.applyFilters = function() {
        const searchTerm = document.getElementById('searchInput').value;
        const dateFrom = document.getElementById('dateFrom').value;
        const dateTo = document.getElementById('dateTo').value;

        const url = new URL(window.location.href);

        // Clear existing filters
        url.searchParams.delete('date_from');
        url.searchParams.delete('date_to');

        // Add new filters
        if (searchTerm) {
            url.searchParams.set('search', searchTerm);
        }
        if (dateFrom) {
            url.searchParams.set('date_from', dateFrom);
        }
        if (dateTo) {
            url.searchParams.set('date_to', dateTo);
        }


        window.location.href = url.toString();
    };

    // Clear filters function
    window.clearFilters = function() {
        document.getElementById('searchInput').value = '';
        document.getElementById('dateFrom').value = '';
        document.getElementById('dateTo').value = '';


        const url = new URL(window.location.href);
        url.searchParams.delete('search');
        url.searchParams.delete('date_from');
        url.searchParams.delete('date_to');


        window.location.href = url.toString();
    };

    // Filter by date function (for dropdown)
    window.filterByDate = function(date) {
        const url = new URL(window.location.href);
        url.searchParams.set('date_from', date);
        url.searchParams.set('date_to', date);
        window.location.href = url.toString();
    };

    // Add enter key support for date inputs
    document.getElementById('dateFrom').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            applyFilters();
        }
    });

    document.getElementById('dateTo').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            applyFilters();
        }
    });

    // Add change event listeners for real-time filtering (optional)


    // Add date validation
    document.getElementById('dateFrom').addEventListener('change', function() {
        const dateFrom = this.value;
        const dateTo = document.getElementById('dateTo').value;

        if (dateFrom && dateTo && dateFrom > dateTo) {
            toastr.warning('تاريخ البداية يجب أن يكون قبل تاريخ النهاية');
            this.value = '';
        }
    });

    document.getElementById('dateTo').addEventListener('change', function() {
        const dateFrom = document.getElementById('dateFrom').value;
        const dateTo = this.value;

        if (dateFrom && dateTo && dateFrom > dateTo) {
            toastr.warning('تاريخ النهاية يجب أن يكون بعد تاريخ البداية');
            this.value = '';
        }
    });

    // Add loading state to filter buttons
    window.applyFilters = function() {
        const applyBtn = document.querySelector('button[onclick="applyFilters()"]');
        const originalText = applyBtn.innerHTML;

        applyBtn.disabled = true;
        applyBtn.innerHTML = '<i class="feather-loader spin me-1"></i>جاري التطبيق...';

        // Apply filters after a short delay to show loading state
        setTimeout(() => {
            const searchTerm = document.getElementById('searchInput').value;
            const dateFrom = document.getElementById('dateFrom').value;
            const dateTo = document.getElementById('dateTo').value;

            const url = new URL(window.location.href);

            // Clear existing filters
            url.searchParams.delete('search');
            url.searchParams.delete('date_from');
            url.searchParams.delete('date_to');
            url.searchParams.delete('page'); // Reset to first page

            // Add new filters
            if (searchTerm) {
                url.searchParams.set('search', searchTerm);
            }
            if (dateFrom) {
                url.searchParams.set('date_from', dateFrom);
            }
            if (dateTo) {
                url.searchParams.set('date_to', dateTo);
            }


            window.location.href = url.toString();
        }, 300);
    };
    });
</script>

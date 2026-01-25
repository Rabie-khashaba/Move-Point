@extends('layouts.app')

@section('title', 'لوحة التحكم')

@section('content')
<div class="nxl-content">
    <!-- الصف العلوي: الموظفين، الأقسام، الشركات، العملاء المحتملين -->
    <div class="row g-4">

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
                    <label class="form-label">من تاريخ</label>
                    <input type="date" class="form-control {{ request('date_from') ? 'filter-active' : '' }}" id="dateFrom" value="{{ request('date_from', now()->toDateString()) }}">
                </div>

                <div class="col-md-3">
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

        <!-- بطاقة الموظفين -->
        <div class="col-xxl-3 col-md-6">
            <div class="card stretch stretch-full">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between mb-4">
                        <div class="d-flex gap-4 align-items-center">
                            <div class="avatar-text avatar-lg bg-gray-200">
                                <i class="feather-users"></i>
                            </div>
                            <div>
                                <div class="fs-4 fw-bold text-dark"><span class="counter">{{ $employeeCount }}</span></div>
                                <h3 class="fs-13 fw-semibold text-truncate-1-line">الموظفين</h3>
                            </div>
                        </div>
                        <div class="dropdown">
                            <a href="javascript:void(0);" class="text-dark" id="employeeDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="feather-more-vertical"></i>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="employeeDropdown">
                                <li>
                                    <a class="dropdown-item" href="{{ route('employees.create') }}">
                                        <i class="feather-user me-2"></i> إضافة موظف
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- بطاقة الأقسام -->
        <div class="col-xxl-3 col-md-6">
            <div class="card stretch stretch-full">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between mb-4">
                        <div class="d-flex gap-4 align-items-center">
                            <div class="avatar-text avatar-lg bg-gray-200">
                                <i class="feather-layers"></i>
                            </div>
                            <div>
                                <div class="fs-4 fw-bold text-dark"><span class="counter">{{ $departmentCount }}</span></div>
                                <h3 class="fs-13 fw-semibold text-truncate-1-line">الأقسام</h3>
                            </div>
                        </div>
                        <div class="dropdown">
                            <a href="javascript:void(0);" class="text-dark" id="departmentDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="feather-more-vertical"></i>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="departmentDropdown">
                                <li>
                                    <a class="dropdown-item" href="{{ route('departments.create') }}">
                                        <i class="feather-layers me-2"></i> إضافة قسم
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xxl-3 col-md-6">
            <div class="card stretch stretch-full">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between mb-4">
                        <div class="d-flex gap-4 align-items-center">
                            <div class="avatar-text avatar-lg bg-gray-200">
                                <i class="feather-user-plus"></i>
                            </div>
                            <div>
                                <div class="fs-4 fw-bold text-dark"><span class="counter">{{ $recentHires }}</span></div>
                                <h3 class="fs-13 fw-semibold text-truncate-1-line">الشركات</h3>
                            </div>
                        </div>
                        <div class="dropdown">
                            <a href="javascript:void(0);" class="text-dark" id="companyDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="feather-more-vertical"></i>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="companyDropdown">
                                <li>
                                    <a class="dropdown-item" href="{{ route('companies.create') }}">
                                        <i class="feather-user-plus me-2"></i> إضافة شركة
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- بطاقة العملاء المحتملين -->
        <div class="col-xxl-3 col-md-6">
            <div class="card stretch stretch-full">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between mb-4">
                        <div class="d-flex gap-4 align-items-center">
                            <div class="avatar-text avatar-lg bg-gray-200">
                                <i class="feather-target"></i>
                            </div>
                            <div>
                                <div class="fs-4 fw-bold text-dark"><span class="counter">{{ $leadCount }}</span></div>
                                <h3 class="fs-13 fw-semibold text-truncate-1-line">العملاء المحتملين</h3>
                            </div>
                        </div>
                        <div class="dropdown">
                            <a href="javascript:void(0);" class="text-dark" id="leadDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="feather-more-vertical"></i>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="leadDropdown">
                                <li>
                                    <a class="dropdown-item" href="{{ route('leads.create') }}">
                                        <i class="feather-target me-2"></i> إضافة عميل محتمل
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mt-4">
        <div class="col-xxl-8 col-lg-12">


            <div class="card stretch stretch-full">
                <div class="card-header">
                    <h5 class="mb-0">أخر العملاء المحتملين</h5> <!-- هذا هو العنوان أعلى الكارد -->
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                    <table class="table table-hover" id="dashboardLeadList">
                            <thead>
                                <tr>
                                    <th class="wd-30">
                                        <div class="btn-group mb-1">
                                            <div class="custom-control custom-checkbox ms-1">
                                                <input type="checkbox" class="custom-control-input" id="checkAllDashboardLead">
                                                <label class="custom-control-label" for="checkAllDashboardLead"></label>
                                            </div>
                                        </div>
                                    </th>
                                    <th>العميل</th>
                                    <th>الهاتف</th>
                                    <th>المحافظة</th>
                                    <th>المصدر</th>
                                    <th>الموظف المخصص</th>
                                    <th>الحالة</th>

                                </tr>
                            </thead>
                            <tbody>
                                @foreach($leads as $lead)
                                    <tr class="single-item" id="lead-{{ $lead->id }}">
                                        <td>
                                            <div class="item-checkbox ms-1">
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input dashboard-lead-checkbox" id="checkBox_{{ $lead->id }}" value="{{ $lead->id }}">
                                                    <label class="custom-control-label" for="checkBox_{{ $lead->id }}"></label>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <a href="{{ route('leads.show', $lead->id) }}" class="hstack gap-3">
                                                <div>
                                                    <span class="text-truncate-1-line">{{ $lead->name }}</span>
                                                </div>
                                            </a>
                                        </td>
                                        <td><a href="tel:{{ $lead->phone }}">{{ $lead->phone }}</a></td>
                                        <td>{{ $lead->governorate?->name ?? '-' }}</td>
                                        <td>
                                            <div class="hstack gap-2">
                                                <div class="avatar-text avatar-sm">
                                                    <i class="feather-{{ strtolower($lead->source?->name ?? 'help-circle') }}"></i>
                                                </div>
                                                <a href="javascript:void(0);">{{ $lead->source?->name ?? '-' }}</a>
                                            </div>
                                        </td>
                                        <td>{{ $lead->employee->employee->name ?? '-' }}</td>
                                        <td>
                                             @php
                                                 $statusColors = [
                                                     'جديد' => 'bg-soft-dark text-dark',
                                                     'متابعة' => 'bg-soft-primary text-primary',
                                                     'مقابلة' => 'bg-soft-warning text-warning',
                                                     'غير مهتم' => 'bg-soft-danger text-danger',
                                                     'عمل مقابلة' => 'bg-soft-info text-info',
                                                     'مفاوضات' => 'bg-soft-warning text-warning',
                                                     'مغلق' => 'bg-soft-success text-success',
                                                     'خسر' => 'bg-soft-danger text-danger',
                                                     'قديم' => 'bg-soft-secondary text-secondary'
                                                 ];
                                                 $statusColor = $statusColors[$lead->status] ?? 'bg-soft-secondary text-secondary';
                                             @endphp
                                             <div class="badge {{ $statusColor }}">{{ $lead->status }}</div>
                                         </td>

                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xxl-4 col-lg-12">
            <div class="card stretch stretch-full overflow-hidden">
                <div class="bg-primary text-white">
                    <div class="p-4">
                        <span class="badge bg-light text-primary text-dark float-end">العملاء حسب المصدر</span>
                        <div class="text-start">
                            <h4 class="text-reset">{{ $leadCount }}</h4>
                            <p class="text-reset m-0">إجمالي العملاء المحتملين</p>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @php
                        use Carbon\Carbon;
                        use App\Models\Lead;

                        // استقبل الفلتر من الريكوست
                        $dateFrom = request()->filled('date_from')
                            ? Carbon::parse(request()->input('date_from'))->startOfDay()
                            : null;

                        $dateTo = request()->filled('date_to')
                            ? Carbon::parse(request()->input('date_to'))->endOfDay()
                            : null;

                        // ابني الكويري
                        $query = Lead::query();

                        if ($dateFrom && $dateTo) {
                            $query->whereBetween('created_at', [$dateFrom, $dateTo]);
                        } elseif ($dateFrom) {
                            $query->where('created_at', '>=', $dateFrom);
                        } elseif ($dateTo) {
                            $query->where('created_at', '<=', $dateTo);
                        }

                        $sources = $query->with('source')
                            ->get()
                            ->groupBy('source_id')
                            ->sortByDesc(fn($leads) => $leads->count());
                    @endphp


                @foreach($sources as $sourceId => $leadsBySource)
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="hstack gap-3">
                                <div class="avatar-text avatar-sm rounded bg-gray-200 p-2">
                                    <i class="feather-{{ strtolower($leadsBySource->first()->source?->name ?? 'help-circle') }}"></i>
                                </div>
                                <div>
                                    <span class="d-block">{{ $leadsBySource->first()->source?->name ?? 'غير معروف' }}</span>
                                    <span class="fs-12 text-muted">عدد العملاء</span>
                                </div>
                            </div>
                            <div class="text-end">
                                <div class="fw-bold text-dark">{{ $leadsBySource->count() }}</div>
                                <div class="fs-12">عدد</div>
                            </div>
                        </div>
                    @endforeach


                </div>

                <a href="{{ route('leads.index') }}" class="card-footer fs-11 fw-bold text-uppercase text-center py-4">عرض التفاصيل كاملة</a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <!-- Chart -->
            <canvas id="leadsSourceChart" width="400" height="250"></canvas>

        </div>
    </div>



</div>
@endsection

@section('styles')
    <style>
        .text-blue {
            color: #007bff !important; /* Bootstrap primary blue */
        }
        .text-red {
            color: #dc3545 !important; /* Bootstrap danger red */
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

        /* Loading overlay styles */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.7);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }

        .loading-content {
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            text-align: center;
            position: relative;
            min-width: 300px;
        }

        .loading-spinner {
            width: 50px;
            height: 50px;
            border: 4px solid #f3f3f3;
            border-top: 4px solid #007bff;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 20px;
        }

        .loading-text {
            font-size: 16px;
            color: #333;
            margin-bottom: 10px;
        }

        /* Spin animation */
        .spin {
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Button disabled state */
        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        /* Toastr customization */
        .toastr {
            z-index: 10000;
        }
    </style>
@endsection

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<!-- لازم تضيف مكتبة Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>



    <script>
        // Configure toastr
        toastr.options = {
            "closeButton": true,
            "debug": false,
            "newestOnTop": false,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "preventDuplicates": false,
            "onclick": null,
            "showDuration": "300",
            "hideDuration": "1000",
            "timeOut": "5000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        };

        // Function to hide loading overlay
        function hideLoadingOverlay() {
            const loadingOverlay = document.getElementById('loadingOverlay');
            if (loadingOverlay) {
                loadingOverlay.style.display = 'none';
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            // Select all functionality
            const checkAll = document.getElementById('checkAllDashboardLead');
            checkAll.addEventListener('change', function () {
                document.querySelectorAll('.dashboard-lead-checkbox').forEach(cb => cb.checked = checkAll.checked);
            });

            // Status color mapping
            const colorMap = {
                'متابعة': 'bg-white text-blue',
                'غير مهتم': 'bg-white text-red',
                'مقابلة': 'bg-white text-black'
            };

            // Update status color and AJAX
            document.querySelectorAll('.lead-status').forEach(select => {
                // Set initial color based on selected option
                const selectedOption = select.options[select.selectedIndex];
                select.className = 'form-control ' + (colorMap[selectedOption.value] || '');

                select.addEventListener('change', function () {
                    const leadId = this.dataset.leadId;
                    const status = this.value;
                    const newColor = colorMap[status] || '';
                    this.className = 'form-control ' + newColor;

                    fetch(`{{ url('leads') }}/${leadId}/status`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ status: status })
                    })
                    .then(res => {
                        if (!res.ok) {
                            throw new Error('فشل الاتصال بالخادم: ' + res.status);
                        }
                        return res.json();
                    })
                    .then(data => {
                        if (data.success) {
                            toastr.success(data.message);
                            location.reload(); // Reload after status change
                        } else {
                            toastr.error(data.message || 'فشل تحديث الحالة');
                        }
                    })
                    .catch(err => {
                        console.error('Status update error:', err);
                        toastr.error(err.message || 'حدث خطأ أثناء الاتصال بالخادم');
                    });
                });
            });

            // Bulk assign function
            const bulkAssignBtn = document.getElementById('bulkAssignBtn');
            if (bulkAssignBtn) {
                bulkAssignBtn.addEventListener('click', function() {
                    const employeeId = document.getElementById('bulkAssignEmployee').value;
                    const selectedLeads = Array.from(document.querySelectorAll('.dashboard-lead-checkbox:checked')).map(cb => cb.value);

                    if (selectedLeads.length === 0) {
                        toastr.error('يرجى اختيار العملاء المحتملين');
                        return;
                    }

                    // Show confirmation for automatic assignment
                    let confirmMessage = 'هل أنت متأكد من تعيين العملاء المحددين؟';
                    if (!employeeId) {
                        confirmMessage = 'سيتم تعيين العملاء تلقائياً إلى الموظف الأقل في عدد العملاء. هل أنت متأكد؟';
                    }

                    if (!confirm(confirmMessage)) {
                        return;
                    }

                    // Show loading overlay
                    const loadingOverlay = document.getElementById('loadingOverlay');
                    if (loadingOverlay) {
                        loadingOverlay.style.display = 'flex';
                        const loadingText = loadingOverlay.querySelector('.loading-text');
                        if (loadingText) {
                            loadingText.textContent = `جاري تعيين ${selectedLeads.length} عميل محتمل...`;
                        }
                    }

                    // Disable button and show loading state
                    bulkAssignBtn.disabled = true;
                    const originalText = bulkAssignBtn.innerHTML;
                    bulkAssignBtn.innerHTML = '<i class="feather-loader spin me-2"></i>جاري التعيين...';

                    // Set timeout to force hide overlay after 10 seconds
                    const overlayTimeout = setTimeout(() => {
                        if (loadingOverlay) {
                            loadingOverlay.style.display = 'none';
                        }
                        bulkAssignBtn.disabled = false;
                        bulkAssignBtn.innerHTML = originalText;
                        toastr.error('انتهت مهلة العملية. يرجى المحاولة مرة أخرى.');
                    }, 10000);

                    console.log('Bulk assigning leads:', selectedLeads, 'to employee:', employeeId || 'auto-assign');

                    fetch(`{{ route('leads.bulkAssign') }}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ employee_id: employeeId, leads: selectedLeads })
                    })
                    .then(res => {
                        clearTimeout(overlayTimeout);
                        if (!res.ok) {
                            throw new Error('فشل الاتصال بالخادم: ' + res.status);
                        }
                        return res.json();
                    })
                    .then(data => {
                        clearTimeout(overlayTimeout);

                        // Clear checkboxes
                        document.querySelectorAll('.dashboard-lead-checkbox:checked').forEach(cb => cb.checked = false);
                        document.getElementById('checkAllDashboardLead').checked = false;

                        // Clear any existing toastr messages
                        toastr.clear();

                        if (data.success) {
                            // Show success message
                            toastr.success(data.message || 'تم تعيين العملاء بنجاح');

                            // Hide overlay after 1 second
                            setTimeout(() => {
                                if (loadingOverlay) {
                                    loadingOverlay.style.display = 'none';
                                }
                                bulkAssignBtn.disabled = false;
                                bulkAssignBtn.innerHTML = originalText;

                                // Show reload message and reload page
                                toastr.info('جاري إعادة تحميل الصفحة...');
                                setTimeout(() => {
                                    location.reload();
                                }, 2000);
                            }, 1000);
                        } else {
                            // Hide overlay and show error
                            if (loadingOverlay) {
                                loadingOverlay.style.display = 'none';
                            }
                            bulkAssignBtn.disabled = false;
                            bulkAssignBtn.innerHTML = originalText;
                            toastr.error(data.message || 'فشل تعيين العملاء');
                        }
                    })
                    .catch(err => {
                        clearTimeout(overlayTimeout);
                        console.error('Bulk assign error:', err);

                        // Hide overlay and show error
                        if (loadingOverlay) {
                            loadingOverlay.style.display = 'none';
                        }
                        bulkAssignBtn.disabled = false;
                        bulkAssignBtn.innerHTML = originalText;
                        toastr.error(err.message || 'حدث خطأ أثناء الاتصال بالخادم');
                    });
                });
            }

            // // Filter by status function
            // window.filterByStatus = function(status) {
            //     const url = new URL(window.location.href);
            //     if (status === 'all') {
            //         url.searchParams.delete('status');
            //     } else {
            //         url.searchParams.set('status', status);
            //     }
            //     window.location.href = url.toString();
            //};

            // Apply filters function
            window.applyFilters = function() {
                const dateFrom = document.getElementById('dateFrom').value;
                const dateTo = document.getElementById('dateTo').value;


                const url = new URL(window.location.href);

                // Clear existing filters
                url.searchParams.delete('date_from');
                url.searchParams.delete('date_to');


                // Add new filters
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
                document.getElementById('dateFrom').value = '';
                document.getElementById('dateTo').value = '';


                const url = new URL(window.location.href);
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
            document.getElementById('statusFilter').addEventListener('change', function() {
                // Uncomment the line below if you want real-time filtering
                // applyFilters();
            });

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
                    const dateFrom = document.getElementById('dateFrom').value;
                    const dateTo = document.getElementById('dateTo').value;

                    const url = new URL(window.location.href);

                    // Clear existing filters
                    url.searchParams.delete('date_from');
                    url.searchParams.delete('date_to');

                    url.searchParams.delete('page'); // Reset to first page

                    // Add new filters
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


        //charts

    </script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const ctx = document.getElementById('leadsSourceChart').getContext('2d');

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: @json($leadsByGovernorate->pluck('governorate')),
                datasets: [{
                    label: 'عدد العملاء',
                    data: @json($leadsByGovernorate->pluck('total')),
                    backgroundColor: '#36A2EB'
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false },
                    title: {
                        display: true,
                        text: 'عدد العملاء في كل محافظة'
                    }
                }
            }
        });
    });
</script>







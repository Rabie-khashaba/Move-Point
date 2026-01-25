@extends('layouts.app')

@section('title', 'المشرفين')

@section('content')
<div class="nxl-content">
    <!-- [ page-header ] start -->
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="m-b-10">المشرفين</h5>
            </div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
                <li class="breadcrumb-item">المشرفين</li>
            </ul>
        </div>
        <div class="page-header-right ms-auto">
            <div class="page-header-right-items">
                <div class="d-flex d-md-none">
                    <a href="javascript:void(0)" class="page-header-right-close-toggle">
                        <i class="feather-arrow-left me-2"></i>
                        <span>رجوع</span>
                    </a>
                </div>
                <div class="d-flex align-items-center gap-2 page-header-right-items-wrapper">
                    <a href="javascript:void(0);" class="btn btn-icon btn-light-brand" data-bs-toggle="collapse" data-bs-target="#filterCollapse">
                        <i class="feather-filter"></i>
                    </a>
                    @can('view_supervisors')
                    <a href="{{ route('supervisor-transfer-logs.index') }}" class="btn btn-info me-2">
                        <i class="feather-activity me-2"></i>
                        <span>سجل النقل</span>
                    </a>
                    @endcan
                    @can('create_supervisors')
                    <a href="{{ route('supervisors.create') }}" class="btn btn-primary">
                        <i class="feather-plus me-2"></i>
                        <span>إضافة مشرف</span>
                    </a>
                    @endcan
                </div>
            </div>
            <div class="d-md-none d-flex align-items-center">
                <a href="javascript:void(0)" class="page-header-right-open-toggle">
                    <i class="feather-align-right fs-20"></i>
                </a>
            </div>
        </div>
    </div>
    <!-- [ page-header ] end -->

    <!-- Filter Collapse -->
    <div class="collapse" id="filterCollapse">
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('supervisors.index') }}" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">البحث</label>
                        <input type="text" name="search" class="form-control" placeholder="البحث في المشرفين..." value="{{ request('search') }}">
                    </div>
                                         <div class="col-md-3">
                         <label class="form-label">المحافظة</label>
                         <select name="governorate_id" id="filterGovernorate" class="form-control">
                             <option value="">جميع المحافظات</option>
                             @foreach(\App\Models\Governorate::all() as $governorate)
                                 <option value="{{ $governorate->id }}" {{ request('governorate_id') == $governorate->id ? 'selected' : '' }}>
                                     {{ $governorate->name }}
                                 </option>
                             @endforeach
                         </select>
                     </div>
                                           <div class="col-md-3">
                          <label class="form-label">المقر</label>
                          <select name="location_id" id="filterLocation" class="form-control">
                              <option value="">جميع المقار</option>
                          </select>
                      </div>
                    <div class="col-md-3">
                        <label class="form-label">الحالة</label>
                        <select name="status" class="form-control">
                            <option value="">جميع الحالات</option>
                            <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>نشط</option>
                            <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>غير نشط</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">تصفية</button>
                        <a href="{{ route('supervisors.index') }}" class="btn btn-light">مسح</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- [ Main Content ] start -->
    <div class="main-content">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">قائمة المشرفين</h5>
                    </div>
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if($supervisors->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>اسم المشرف</th>
                                            <th>رقم التليفون</th>
                                            <th>المقر المسؤول عنه</th>
                                            <th>عدد المندوبين</th>
                                            <th>الحالة</th>
                                            <th>تاريخ الإضافة</th>
                                            <th>الإجراءات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($supervisors as $supervisor)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-text avatar-sm rounded-circle bg-warning me-3">
                                                        <i class="feather-user-check"></i>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0">{{ $supervisor->name }}</h6>
                                                        <small class="text-muted">رقم البطاقة: {{ $supervisor->national_id }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <i class="feather-phone me-2 text-muted"></i>
                                                    <a href="tel:{{ $supervisor->phone }}" class="text-decoration-none">{{ $supervisor->phone }}</a>
                                                </div>
                                            </td>
                                                                                         <td>
                                                 <div>
                                                     <span class="badge bg-info">{{ $supervisor->location_name ?? $supervisor->location->name ?? 'غير محدد' }}</span>
                                                     @if($supervisor->governorate)
                                                         <br><small class="text-muted">{{ $supervisor->governorate->name }}</small>
                                                     @endif
                                                 </div>
                                             </td>
                                            <td>
                                                <span class="badge bg-primary">{{ $supervisor->representatives->count() }} ممثل</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $supervisor->is_active ? 'success' : 'danger' }}">
                                                    {{ $supervisor->is_active ? 'نشط' : 'غير نشط' }}
                                                </span>
                                            </td>
                                            <td>
                                                <small class="text-muted">{{ $supervisor->created_at->format('d M, Y') }}</small>
                                            </td>
                                            <td>
                                                <div class="d-flex gap-2">
                                                    @can('view_supervisors')
                                                    <a href="{{ route('supervisors.show', $supervisor->id) }}" class="btn btn-sm btn-outline-primary" title="عرض">
                                                        <i class="feather-eye"></i>
                                                    </a>
                                                    @endcan
                                                    @can('edit_supervisors')
                                                    <a href="{{ route('supervisors.edit', $supervisor->id) }}" class="btn btn-sm btn-outline-warning" title="تعديل">
                                                        <i class="feather-edit"></i>
                                                    </a>
                                                    @endcan
                                                    @can('edit_supervisors')
                                                    <button type="button" class="btn btn-sm btn-outline-info" 
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#representativesModal" 
                                                            data-supervisor-id="{{ $supervisor->id }}"
                                                            data-supervisor-name="{{ $supervisor->name }}"
                                                            title="عرض المندوبين">
                                                        <i class="feather-users"></i>
                                                    </button>
                                                    @endcan
                                                    @can('edit_supervisors')
                                                    <form action="{{ route('supervisors.toggle-status', $supervisor->id) }}" method="POST" style="display: inline;">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-outline-{{ $supervisor->is_active ? 'danger' : 'success' }}" 
                                                                data-toggle-status="{{ $supervisor->is_active ? 'deactivate' : 'activate' }}"
                                                                title="{{ $supervisor->is_active ? 'إيقاف' : 'تفعيل' }}">
                                                            <i class="feather-{{ $supervisor->is_active ? 'pause' : 'play' }}"></i>
                                                        </button>
                                                    </form>
                                                    @endcan
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            @if($supervisors->hasPages())
                                <div class="d-flex justify-content-center mt-4">
                                    {{ $supervisors->links('pagination::bootstrap-5') }}
                                </div>
                            @endif
                        @else
                            <div class="text-center py-5">
                                <div class="avatar-text avatar-xl mx-auto mb-3">
                                    <i class="feather-user-check"></i>
                                </div>
                                <h5>لا توجد مشرفين</h5>
                                <p class="text-muted">ابدأ بإضافة أول مشرف.</p>
                                <a href="{{ route('supervisors.create') }}" class="btn btn-primary">
                                    <i class="feather-plus me-2"></i>إضافة مشرف
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- [ Main Content ] end -->
</div>

<!-- Representatives Modal -->
<div class="modal fade" id="representativesModal" tabindex="-1" aria-labelledby="representativesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="representativesModalLabel">مندوبين المشرف</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="representativesList">
                    <div class="text-center">
                        <div class="spinner-border" role="status">
                            <span class="visually-hidden">جاري التحميل...</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
// Set up CSRF token for all AJAX requests
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

$(document).ready(function() {
    // Handle status toggle confirmation
    $('form[action*="toggle-status"]').on('submit', function(e) {
        e.preventDefault();
        
        var form = $(this);
        var button = form.find('button[type="submit"]');
        var action = button.data('toggle-status');
        var actionText = action === 'activate' ? 'تفعيل' : 'إيقاف';
        
        if (confirm('هل أنت متأكد من ' + actionText + ' هذا المشرف؟')) {
            form.off('submit').submit();
        }
    });

    // Handle governorate filter change
    $('#filterGovernorate').on('change', function() {
        var governorateId = $(this).val();
        loadFilterLocations(governorateId);
    });

    function loadFilterLocations(governorateId) {
        if (!governorateId) {
            $('#filterLocation').empty().append('<option value="">جميع المقار</option>');
            return;
        }

        $.ajax({
            url: '/getlocations/' + governorateId,
            type: 'GET',
            success: function(response) {
                $('#filterLocation').empty().append('<option value="">جميع المقار</option>');
                
                response.forEach(function(location) {
                    var isSelected = location.id == {{ request('location_id') ?? 'null' }};
                    var option = new Option(location.name, location.id, isSelected, isSelected);
                    $('#filterLocation').append(option);
                });
            },
            error: function() {
                $('#filterLocation').empty().append('<option value="">خطأ في تحميل المقار</option>');
            }
        });
    }

    // Load locations on page load if governorate is selected
    var initialGovernorateId = $('#filterGovernorate').val();
    if (initialGovernorateId) {
        loadFilterLocations(initialGovernorateId);
    }

    // Handle representatives modal
    $('#representativesModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var supervisorId = button.data('supervisor-id');
        var supervisorName = button.data('supervisor-name');
        
        // Update modal title
        $('#representativesModalLabel').text('مندوبين المشرف: ' + supervisorName);
        
        // Load representatives
        $.ajax({
            url: '{{ url("supervisors") }}/' + supervisorId + '/representatives',
            type: 'GET',
            success: function(response) {
                var html = '';
                if (response.length > 0) {
                                                               html = '<div class="table-responsive"><table class="table table-hover">';
                     html += '<thead><tr><th>اسم المندوب</th><th>رقم التليفون</th><th>الشركة</th><th>المقر</th><th>المحافظة</th><th>كود المندوب</th><th>تاريخ الإضافة</th></tr></thead><tbody>';
                      
                     response.forEach(function(representative) {
                         html += '<tr>';
                         html += '<td><strong>' + representative.name + '</strong></td>';
                         html += '<td><a href="tel:' + representative.phone + '">' + representative.phone + '</a></td>';
                         html += '<td><span class="badge bg-info">' + (representative.company ? representative.company.name : 'غير محدد') + '</span></td>';
                         html += '<td><span class="badge bg-secondary">' + (representative.location ? representative.location.name : 'غير محدد') + '</span></td>';
                         html += '<td><span class="badge bg-warning">' + (representative.governorate ? representative.governorate.name : 'غير محدد') + '</span></td>';
                         html += '<td><small class="text-muted">' + (representative.code || 'غير محدد') + '</small></td>';
                         html += '<td><small class="text-muted">' + new Date(representative.created_at).toLocaleDateString('ar-EG') + '</small></td>';
                         html += '</tr>';
                     });
                    
                     html += '</tbody></table></div>';
                } else {
                    html = '<div class="text-center py-4"><p class="text-muted">لا يوجد ممثلين لهذا المشرف</p></div>';
                }
                
                $('#representativesList').html(html);
            },
            error: function() {
                $('#representativesList').html('<div class="text-center py-4"><p class="text-danger">حدث خطأ أثناء تحميل البيانات</p></div>');
            }
        });
    });
});
</script>
@endpush

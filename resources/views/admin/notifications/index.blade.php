@extends('layouts.app')

@section('title', 'إدارة الإشعارات')

@section('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css"/>
<style>
    .notification-btn {
        transition: all 0.3s ease;
        border-radius: 10px;
        padding: 20px;
        height: 120px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        text-align: center;
    }
    
    .notification-btn:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.15);
    }
    
    .notification-btn i {
        font-size: 2rem;
        margin-bottom: 10px;
    }
    
    .notification-btn small {
        font-size: 0.85rem;
        opacity: 0.8;
    }
    
    .modal-header {
        border-radius: 10px 10px 0 0;
    }
    
    .modal-content {
        border-radius: 10px;
        border: none;
        box-shadow: 0 10px 30px rgba(0,0,0,0.2);
    }
    
    .form-control:focus, .form-select:focus {
        border-color: #007bff;
        box-shadow: 0 0 0 0.2rem rgba(0,123,255,0.25);
    }
    
    .alert {
        border-radius: 8px;
        border: none;
    }
    
    .btn {
        border-radius: 8px;
        font-weight: 500;
    }
    
    .spinner-border-sm {
        width: 1rem;
        height: 1rem;
    }
    
    /* Pagination Styles */
    .pagination {
        direction: rtl;
    }
    
    .pagination .page-link {
        color: #007bff;
        border-color: #dee2e6;
        padding: 0.5rem 0.75rem;
        margin: 0 2px;
        border-radius: 6px;
        font-weight: 500;
    }
    
    .pagination .page-link:hover {
        color: #0056b3;
        background-color: #e9ecef;
        border-color: #adb5bd;
    }
    
    .pagination .page-item.active .page-link {
        background-color: #007bff;
        border-color: #007bff;
        color: white;
    }
    
    .pagination .page-item.disabled .page-link {
        color: #6c757d;
        background-color: #fff;
        border-color: #dee2e6;
    }
    
    .pagination .page-item:first-child .page-link {
        border-top-right-radius: 6px;
        border-bottom-right-radius: 6px;
        border-top-left-radius: 0;
        border-bottom-left-radius: 0;
    }
    
    .pagination .page-item:last-child .page-link {
        border-top-left-radius: 6px;
        border-bottom-left-radius: 6px;
        border-top-right-radius: 0;
        border-bottom-right-radius: 0;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">إدارة الإشعارات</h4>
                </div>
                <div class="card-body">
                    <!-- Send Notification Buttons -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">إرسال الإشعارات</h5>
                                </div>
                                <div class="card-body text-center">
                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <button type="button" class="btn btn-primary btn-lg w-100 notification-btn" data-bs-toggle="modal" data-bs-target="#sendToAllModal">
                                                <i class="feather-users"></i>
                                                إرسال للجميع
                                                <small>جميع المستخدمين</small>
                                            </button>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <button type="button" class="btn btn-success btn-lg w-100 notification-btn" data-bs-toggle="modal" data-bs-target="#sendToUserTypeModal">
                                                <i class="feather-user-check"></i>
                                                إرسال لنوع مستخدم
                                                <small>موظفين، مندوبين، مشرفين</small>
                                            </button>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <button type="button" class="btn btn-warning btn-lg w-100 notification-btn" data-bs-toggle="modal" data-bs-target="#sendToUsersModal">
                                                <i class="feather-user-plus"></i>
                                                إرسال لمستخدمين محددين
                                                <small>اختيار المستخدمين</small>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Statistics -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4 class="mb-0" id="totalNotifications">-</h4>
                                            <p class="mb-0">إجمالي الإشعارات</p>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="feather-bell font-large-2"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4 class="mb-0" id="unreadNotifications">-</h4>
                                            <p class="mb-0">غير مقروءة</p>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="feather-mail font-large-2"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4 class="mb-0" id="notificationsToday">-</h4>
                                            <p class="mb-0">اليوم</p>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="feather-calendar font-large-2"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4 class="mb-0" id="usersWithTokens">-</h4>
                                            <p class="mb-0">مستخدمين مفعلين</p>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="feather-users font-large-2"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Forms -->
                    <!-- Send to All Users Modal -->
                    <div class="modal fade" id="sendToAllModal" tabindex="-1" aria-labelledby="sendToAllModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header bg-primary text-white">
                                    <h5 class="modal-title" id="sendToAllModalLabel">
                                        <i class="feather-users me-2"></i>إرسال إشعار لجميع المستخدمين
                                    </h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <form id="sendToAllForm">
                                    <div class="modal-body">
                                        @csrf
                                        <div class="mb-3">
                                            <label class="form-label">العنوان <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="title" required placeholder="أدخل عنوان الإشعار">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">الرسالة <span class="text-danger">*</span></label>
                                            <textarea class="form-control" name="body" rows="4" required placeholder="أدخل محتوى الإشعار"></textarea>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">نوع الإشعار</label>
                                            <select class="form-select" name="type">
                                                <option value="general">عام</option>
                                                <option value="announcement">إعلان</option>
                                                <option value="leave_request">طلب إجازة</option>
                                                <option value="advance_request">طلب سلفة</option>
                                                <option value="delivery_deposit">إيداع تسليم</option>
                                            </select>
                                        </div>
                                        <div class="alert alert-info">
                                            <i class="feather-info me-2"></i>
                                            سيتم إرسال هذا الإشعار لجميع المستخدمين في النظام
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="feather-send me-1"></i> إرسال للجميع
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Send to User Type Modal -->
                    <div class="modal fade" id="sendToUserTypeModal" tabindex="-1" aria-labelledby="sendToUserTypeModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header bg-success text-white">
                                    <h5 class="modal-title" id="sendToUserTypeModalLabel">
                                        <i class="feather-user-check me-2"></i>إرسال إشعار لنوع مستخدم
                                    </h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <form id="sendToUserTypeForm">
                                    <div class="modal-body">
                                        @csrf
                                        <div class="mb-3">
                                            <label class="form-label">نوع المستخدم <span class="text-danger">*</span></label>
                                            <select class="form-select" name="user_type" required>
                                                <option value="">اختر نوع المستخدم</option>
                                                @foreach($userTypes as $type)
                                                    <option value="{{ $type }}">
                                                        @switch($type)
                                                            @case('employee') الموظفين @break
                                                            @case('representative') المندوبين @break
                                                            @case('supervisor') المشرفين @break
                                                        @endswitch
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">العنوان <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="title" required placeholder="أدخل عنوان الإشعار">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">الرسالة <span class="text-danger">*</span></label>
                                            <textarea class="form-control" name="body" rows="4" required placeholder="أدخل محتوى الإشعار"></textarea>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">نوع الإشعار</label>
                                            <select class="form-select" name="type">
                                                <option value="general">عام</option>
                                                <option value="announcement">إعلان</option>
                                                <option value="leave_request">طلب إجازة</option>
                                                <option value="advance_request">طلب سلفة</option>
                                                <option value="delivery_deposit">إيداع تسليم</option>
                                            </select>
                                        </div>
                                        <div class="alert alert-success">
                                            <i class="feather-info me-2"></i>
                                            سيتم إرسال هذا الإشعار لجميع المستخدمين من النوع المحدد
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                                        <button type="submit" class="btn btn-success">
                                            <i class="feather-send me-1"></i> إرسال للنوع
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Send to Specific Users Modal -->
                    <div class="modal fade" id="sendToUsersModal" tabindex="-1" aria-labelledby="sendToUsersModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header bg-warning text-dark">
                                    <h5 class="modal-title" id="sendToUsersModalLabel">
                                        <i class="feather-user-plus me-2"></i>إرسال إشعار لمستخدمين محددين
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <form id="sendToUsersForm">
                                    <div class="modal-body">
                                        @csrf
                                        <div class="mb-3">
                                            <label class="form-label">المستخدمين <span class="text-danger">*</span></label>
                                            <div class="mb-2">
                                                <input type="text" id="userSearchInput" class="form-control" placeholder="ابحث عن المستخدمين...">
                                            </div>
                                            <select class="form-select" name="user_ids[]" id="userSelect" multiple required size="8">
                                                @foreach($users as $user)
                                                    <option value="{{ $user->id }}" data-name="{{ $user->name }}" data-type="{{ $user->type }}">
                                                        {{ $user->name ?? $user->employee->name ?? $user->representative->name ?? $user->supervisor->name ?? 'غير محدد' }} ({{ $user->type }})
                                                    </option>
                                                @endforeach
                                            </select>
                                            <div class="form-text">اضغط Ctrl لاختيار عدة مستخدمين</div>
                                            <div id="selectedUsersCount" class="text-info small mt-2"></div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">العنوان <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="title" required placeholder="أدخل عنوان الإشعار">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">الرسالة <span class="text-danger">*</span></label>
                                            <textarea class="form-control" name="body" rows="4" required placeholder="أدخل محتوى الإشعار"></textarea>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">نوع الإشعار</label>
                                            <select class="form-select" name="type">
                                                <option value="general">عام</option>
                                                <option value="announcement">إعلان</option>
                                                <option value="leave_request">طلب إجازة</option>
                                                <option value="advance_request">طلب سلفة</option>
                                                <option value="delivery_deposit">إيداع تسليم</option>
                                            </select>
                                        </div>
                                        <div class="alert alert-warning">
                                            <i class="feather-info me-2"></i>
                                            سيتم إرسال هذا الإشعار للمستخدمين المحددين فقط
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                                        <button type="submit" class="btn btn-warning">
                                            <i class="feather-send me-1"></i> إرسال للمحددين
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Notifications List -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">قائمة الإشعارات</h5>
                            <div class="card-tools">
                                <button class="btn btn-danger btn-sm" id="bulkDeleteBtn" disabled>
                                    <i class="feather-trash-2 me-1"></i> حذف المحدد
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>
                                                <input type="checkbox" id="selectAll" class="form-check-input">
                                            </th>
                                            <th>المستخدم</th>
                                            <th>العنوان</th>
                                            <th>الرسالة</th>
                                            <th>النوع</th>
                                            <th>الحالة</th>
                                            <th>التاريخ</th>
                                            <th>الإجراءات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($notifications as $notification)
                                            <tr>
                                                <td>
                                                    <input type="checkbox" class="form-check-input notification-checkbox" 
                                                           value="{{ $notification->id }}">
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar bg-light-primary me-1">
                                                            <span class="avatar-content">{{ substr($notification->user->name ?? 'U', 0, 1) }}</span>
                                                        </div>
                                                        <div>
                                                            <h6 class="mb-0">{{ $notification->user->name ?? 'غير محدد' }}</h6>
                                                            <small class="text-muted">{{ $notification->user->type ?? '' }}</small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>{{ $notification->title }}</td>
                                                <td>{{ Str::limit($notification->body, 50) }}</td>
                                                <td>
                                                    <span class="badge bg-{{ $notification->type === 'general' ? 'primary' : 'secondary' }}">
                                                        {{ $notification->type }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @if($notification->is_read)
                                                        <span class="badge bg-success">مقروء</span>
                                                    @else
                                                        <span class="badge bg-warning">غير مقروء</span>
                                                    @endif
                                                </td>
                                                <td>{{ $notification->created_at->format('Y-m-d H:i') }}</td>
                                                <td>
                                                    <button class="btn btn-sm btn-danger" onclick="deleteNotification({{ $notification->id }})">
                                                        <i class="feather-trash-2"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-center">لا توجد إشعارات</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            
                            <!-- Pagination Info and Controls -->
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <div class="text-muted">
                                        <i class="feather-info me-1"></i>
                                        عرض {{ $notifications->firstItem() ?? 0 }} إلى {{ $notifications->lastItem() ?? 0 }} 
                                        من أصل {{ $notifications->total() }} إشعار
                                        @if($notifications->hasPages())
                                            (الصفحة {{ $notifications->currentPage() }} من {{ $notifications->lastPage() }})
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    @if($notifications->hasPages())
                                        <div class="d-flex justify-content-end align-items-center">
                                            <div class="me-3">
                                                <small class="text-muted">عدد الإشعارات في الصفحة:</small>
                                                <select class="form-select form-select-sm d-inline-block w-auto ms-1" id="perPageSelect">
                                                    <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                                                    <option value="20" {{ request('per_page') == 20 || !request('per_page') ? 'selected' : '' }}>20</option>
                                                    <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                                                    <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                                                </select>
                                            </div>
                                            <nav aria-label="صفحات الإشعارات">
                                                {{ $notifications->appends(request()->query())->links('pagination::bootstrap-4') }}
                                            </nav>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Load statistics
    loadStats();
    
    // Send to all users
    $('#sendToAllForm').on('submit', function(e) {
        e.preventDefault();
        sendNotification('all', $(this).serialize(), 'sendToAllModal');
    });
    
    // Send to user type
    $('#sendToUserTypeForm').on('submit', function(e) {
        e.preventDefault();
        sendNotification('user-type', $(this).serialize(), 'sendToUserTypeModal');
    });
    
    // Send to specific users
    $('#sendToUsersForm').on('submit', function(e) {
        e.preventDefault();
        sendNotification('users', $(this).serialize(), 'sendToUsersModal');
    });
    
    // Select all checkbox
    $('#selectAll').on('change', function() {
        $('.notification-checkbox').prop('checked', $(this).is(':checked'));
        updateBulkDeleteButton();
    });
    
    // Individual checkboxes
    $('.notification-checkbox').on('change', function() {
        updateBulkDeleteButton();
    });
    
    // Bulk delete
    $('#bulkDeleteBtn').on('click', function() {
        const selectedIds = $('.notification-checkbox:checked').map(function() {
            return $(this).val();
        }).get();
        
        if (selectedIds.length > 0) {
            bulkDeleteNotifications(selectedIds);
        }
    });

    // Modal form enhancements
    // Clear forms when modals are hidden
    $('.modal').on('hidden.bs.modal', function() {
        $(this).find('form')[0].reset();
        $(this).find('.alert').hide();
    });

    // Show user count in specific users modal
    $('#sendToUsersModal').on('show.bs.modal', function() {
        const userCount = $(this).find('select[name="user_ids[]"] option').length;
        $(this).find('.alert-warning').html(`
            <i class="feather-info me-2"></i>
            سيتم إرسال هذا الإشعار للمستخدمين المحددين فقط (${userCount} مستخدم متاح)
        `);
        updateSelectedUsersCount();
    });
    
    // User search functionality
    $('#userSearchInput').on('input', function() {
        const searchTerm = $(this).val().toLowerCase();
        $('#userSelect option').each(function() {
            const option = $(this);
            const name = option.data('name').toLowerCase();
            const type = option.data('type').toLowerCase();
            
            if (name.includes(searchTerm) || type.includes(searchTerm)) {
                option.show();
            } else {
                option.hide();
            }
        });
    });
    
    // Update selected users count
    $('#userSelect').on('change', function() {
        updateSelectedUsersCount();
    });
    
    function updateSelectedUsersCount() {
        const selectedCount = $('#userSelect').val() ? $('#userSelect').val().length : 0;
        if (selectedCount > 0) {
            const selectedUsers = [];
            $('#userSelect option:selected').each(function() {
                selectedUsers.push($(this).data('name'));
            });
            $('#selectedUsersCount').html(`
                <strong>المستخدمين المحددين (${selectedCount}):</strong><br>
                ${selectedUsers.join(', ')}
            `);
        } else {
            $('#selectedUsersCount').html('');
        }
    }

    // Form validation
    $('form[id$="Form"]').on('submit', function(e) {
        const form = $(this);
        let isValid = true;
        
        // Clear previous validation
        form.find('.is-invalid').removeClass('is-invalid');
        form.find('.invalid-feedback').remove();
        
        // Validate required fields
        form.find('[required]').each(function() {
            if (!$(this).val().trim()) {
                $(this).addClass('is-invalid');
                $(this).after('<div class="invalid-feedback">هذا الحقل مطلوب</div>');
                isValid = false;
            }
        });
        
        // Validate user selection for specific users form
        if (form.attr('id') === 'sendToUsersForm') {
            const selectedUsers = form.find('select[name="user_ids[]"]').val();
            if (!selectedUsers || selectedUsers.length === 0) {
                form.find('select[name="user_ids[]"]').addClass('is-invalid');
                form.find('select[name="user_ids[]"]').after('<div class="invalid-feedback">يرجى اختيار مستخدم واحد على الأقل</div>');
                isValid = false;
            }
        }
        
        if (!isValid) {
            e.preventDefault();
            toastr.error('يرجى ملء جميع الحقول المطلوبة');
        }
    });

    // Handle per-page selector
    $('#perPageSelect').on('change', function() {
        const perPage = $(this).val();
        const currentUrl = new URL(window.location);
        currentUrl.searchParams.set('per_page', perPage);
        currentUrl.searchParams.delete('page'); // Reset to first page
        window.location.href = currentUrl.toString();
    });
});

function sendNotification(type, data, modalId) {
    const url = type === 'all' ? '/admin-notifications/send-to-all' :
                type === 'user-type' ? '/admin-notifications/send-to-user-type' :
                '/admin-notifications/send-to-users';
    
    console.log('Sending notification to:', url);
    console.log('Data:', data);
    
    // Show loading state
    const submitBtn = $(`#${modalId} button[type="submit"]`);
    const originalText = submitBtn.html();
    submitBtn.prop('disabled', true).html('<i class="spinner-border spinner-border-sm me-1"></i> جاري الإرسال...');
    
    $.ajax({
        url: url,
        method: 'POST',
        data: data,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            console.log('Response:', response);
            submitBtn.prop('disabled', false).html(originalText);
            
            if (response.success) {
                toastr.success(response.message);
                loadStats();
                // Clear form and close modal
                $(`#${modalId} form`)[0].reset();
                $(`#${modalId}`).modal('hide');
                // Reload after a short delay to show new notifications
                setTimeout(() => location.reload(), 1000);
            } else {
                toastr.error(response.message);
            }
        },
        error: function(xhr) {
            console.error('Error:', xhr);
            submitBtn.prop('disabled', false).html(originalText);
            
            const response = xhr.responseJSON;
            let errorMessage = 'حدث خطأ أثناء إرسال الإشعار';
            
            if (response && response.message) {
                errorMessage = response.message;
            } else if (xhr.status === 422) {
                errorMessage = 'يرجى التحقق من البيانات المدخلة';
            } else if (xhr.status === 500) {
                errorMessage = 'خطأ في الخادم، يرجى المحاولة مرة أخرى';
            }
            
            toastr.error(errorMessage);
        }
    });
}

function deleteNotification(id) {
    if (confirm('هل أنت متأكد من حذف هذا الإشعار؟')) {
        $.ajax({
            url: `/admin-notifications/${id}`,
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    location.reload();
                } else {
                    toastr.error(response.message);
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                toastr.error(response?.message || 'حدث خطأ أثناء حذف الإشعار');
            }
        });
    }
}

function bulkDeleteNotifications(ids) {
    if (confirm(`هل أنت متأكد من حذف ${ids.length} إشعار؟`)) {
        $.ajax({
            url: '/admin-notifications/bulk-delete',
            method: 'POST',
            data: {
                notification_ids: ids,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    location.reload();
                } else {
                    toastr.error(response.message);
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                toastr.error(response?.message || 'حدث خطأ أثناء حذف الإشعارات');
            }
        });
    }
}

function updateBulkDeleteButton() {
    const selectedCount = $('.notification-checkbox:checked').length;
    $('#bulkDeleteBtn').prop('disabled', selectedCount === 0);
    if (selectedCount > 0) {
        $('#bulkDeleteBtn').html(`<i class="feather-trash-2 me-1"></i> حذف المحدد (${selectedCount})`);
    } else {
        $('#bulkDeleteBtn').html('<i class="feather-trash-2 me-1"></i> حذف المحدد');
    }
}

function loadStats() {
    $.ajax({
        url: '/admin-notifications/stats',
        method: 'GET',
        success: function(response) {
            $('#totalNotifications').text(response.total_notifications);
            $('#unreadNotifications').text(response.unread_notifications);
            $('#notificationsToday').text(response.notifications_today);
            $('#usersWithTokens').text(response.users_with_tokens);
        }
    });
}
</script>

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script>
    // Configure toastr
    toastr.options = {
        "closeButton": true,
        "debug": false,
        "newestOnTop": true,
        "progressBar": true,
        "positionClass": "toast-top-left",
        "preventDuplicates": false,
        "onclick": null,
        "showDuration": "300",
        "hideDuration": "1000",
        "timeOut": "5000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut",
        "rtl": true
    };
</script>
@endsection
@endpush


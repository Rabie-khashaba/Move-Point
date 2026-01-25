@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">
                        <i class="feather-plus me-2"></i>
                        إنشاء إشعار جديد
                    </h4>
                    <a href="{{ route('notifications.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="feather-arrow-right me-1"></i>
                        العودة للإشعارات
                    </a>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('notifications.store') }}">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="title" class="form-label">عنوان الإشعار <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                           id="title" name="title" value="{{ old('title') }}" required>
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="type" class="form-label">نوع الإشعار <span class="text-danger">*</span></label>
                                    <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                                        <option value="">اختر نوع الإشعار</option>
                                        <option value="general" {{ old('type') === 'general' ? 'selected' : '' }}>عام</option>
                                        <option value="leave_request" {{ old('type') === 'leave_request' ? 'selected' : '' }}>طلب إجازة</option>
                                        <option value="advance_request" {{ old('type') === 'advance_request' ? 'selected' : '' }}>طلب سلفة</option>
                                        <option value="resignation_request" {{ old('type') === 'resignation_request' ? 'selected' : '' }}>طلب استقالة</option>
                                        <option value="delivery_deposit" {{ old('type') === 'delivery_deposit' ? 'selected' : '' }}>إيداع تسليم</option>
                                    </select>
                                    @error('type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="body" class="form-label">محتوى الإشعار <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('body') is-invalid @enderror" 
                                      id="body" name="body" rows="4" required 
                                      placeholder="اكتب محتوى الإشعار هنا...">{{ old('body') }}</textarea>
                            <div class="form-text">
                                <small class="text-muted">
                                    <span id="char-count">0</span> / 1000 حرف
                                </small>
                            </div>
                            @error('body')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <!-- Notification Templates -->
                        <div class="mb-3">
                            <label class="form-label">قوالب الإشعارات السريعة</label>
                            <div class="row">
                                <div class="col-md-6">
                                    <button type="button" class="btn btn-outline-info btn-sm w-100 mb-2" onclick="useTemplate('welcome')">
                                        <i class="feather-user-plus me-1"></i>
                                        ترحيب جديد
                                    </button>
                                    <button type="button" class="btn btn-outline-warning btn-sm w-100 mb-2" onclick="useTemplate('reminder')">
                                        <i class="feather-clock me-1"></i>
                                        تذكير مهم
                                    </button>
                                </div>
                                <div class="col-md-6">
                                    <button type="button" class="btn btn-outline-success btn-sm w-100 mb-2" onclick="useTemplate('announcement')">
                                        <i class="feather-megaphone me-1"></i>
                                        إعلان عام
                                    </button>
                                    <button type="button" class="btn btn-outline-danger btn-sm w-100 mb-2" onclick="useTemplate('urgent')">
                                        <i class="feather-alert-triangle me-1"></i>
                                        عاجل
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Priority Level -->
                        <div class="mb-3">
                            <label for="priority" class="form-label">أولوية الإشعار</label>
                            <select class="form-select @error('priority') is-invalid @enderror" id="priority" name="priority">
                                <option value="normal" {{ old('priority') === 'normal' ? 'selected' : '' }}>عادي</option>
                                <option value="high" {{ old('priority') === 'high' ? 'selected' : '' }}>عالي</option>
                                <option value="urgent" {{ old('priority') === 'urgent' ? 'selected' : '' }}>عاجل</option>
                            </select>
                            @error('priority')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <!-- Schedule Notification -->
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="schedule_notification" 
                                       name="schedule_notification" value="1" {{ old('schedule_notification') ? 'checked' : '' }}>
                                <label class="form-check-label" for="schedule_notification">
                                    جدولة الإشعار للإرسال لاحقاً
                                </label>
                            </div>
                        </div>
                        
                        <div id="schedule_section" class="mb-3" style="display: none;">
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="scheduled_date" class="form-label">تاريخ الإرسال</label>
                                    <input type="date" class="form-control @error('scheduled_date') is-invalid @enderror" 
                                           id="scheduled_date" name="scheduled_date" value="{{ old('scheduled_date') }}">
                                    @error('scheduled_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="scheduled_time" class="form-label">وقت الإرسال</label>
                                    <input type="time" class="form-control @error('scheduled_time') is-invalid @enderror" 
                                           id="scheduled_time" name="scheduled_time" value="{{ old('scheduled_time') }}">
                                    @error('scheduled_time')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">المستهدفون <span class="text-danger">*</span></label>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="target_type" 
                                               id="specific_users" value="specific_users" 
                                               {{ old('target_type') === 'specific_users' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="specific_users">
                                            مستخدمين محددين
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="target_type" 
                                               id="user_types" value="user_types" 
                                               {{ old('target_type') === 'user_types' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="user_types">
                                            أنواع المستخدمين
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="target_type" 
                                               id="all_users" value="all_users" 
                                               {{ old('target_type') === 'all_users' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="all_users">
                                            جميع المستخدمين
                                        </label>
                                    </div>
                                </div>
                            </div>
                            @error('target_type')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <!-- Specific Users Selection -->
                        <div id="specific_users_section" class="mb-3" style="display: none;">
                            <div class="alert alert-info">
                                <i class="feather-info me-2"></i>
                                سيتم إرسال هذا الإشعار للمستخدمين المحددين فقط
                            </div>
                            
                            <!-- User Search -->
                            <div class="mb-3 user-search-container">
                                <label for="userSearchInput" class="form-label">البحث في المستخدمين</label>
                                <input type="text" class="form-control" id="userSearchInput" 
                                       placeholder="ابحث بالاسم أو النوع...">
                                <i class="feather-search search-icon"></i>
                            </div>
                            
                            <!-- Selected Users Display -->
                            <div id="selectedUsersDisplay" class="mb-3" style="display: none;">
                                <label class="form-label">المستخدمون المحددون</label>
                                <div id="selectedUsersList" class="d-flex flex-wrap gap-2"></div>
                            </div>
                            
                            <label for="user_ids" class="form-label">اختر المستخدمين <span class="text-danger">*</span></label>
                            <select class="form-select @error('user_ids') is-invalid @enderror" 
                                    id="user_ids" name="user_ids[]" multiple required size="8">
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" 
                                            {{ in_array($user->id, old('user_ids', [])) ? 'selected' : '' }}>
                                        {{ $user->name ?? $user->employee->name ?? $user->representative->name ?? $user->supervisor->name ?? 'غير محدد' }} ({{ $user->type }})
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text user-count-display">
                                <span id="selectedUsersCount" class="text-primary">0</span> مستخدم محدد من أصل <span id="totalUsersCount">{{ $users->count() }}</span>
                            </div>
                            @error('user_ids')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <!-- User Types Selection -->
                        <div id="user_types_section" class="mb-3" style="display: none;">
                            <label for="user_types" class="form-label">اختر أنواع المستخدمين</label>
                            <div class="row">
                                @foreach($userTypes as $userType)
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="user_types[]" 
                                                   id="user_type_{{ $userType }}" value="{{ $userType }}"
                                                   {{ in_array($userType, old('user_types', [])) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="user_type_{{ $userType }}">
                                                @switch($userType)
                                                    @case('admin') مدير @break
                                                    @case('supervisor') مشرف @break
                                                    @case('representative') مندوب @break
                                                    @case('employee') موظف @break
                                                @endswitch
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @error('user_types')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-info" onclick="previewNotification()">
                                <i class="feather-eye me-1"></i>
                                معاينة الإشعار
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="feather-send me-1"></i>
                                إرسال الإشعار
                            </button>
                            <a href="{{ route('notifications.index') }}" class="btn btn-outline-secondary">
                                <i class="feather-x me-1"></i>
                                إلغاء
                            </a>
                        </div>
                    </form>
                    
                    <!-- Notification Preview Modal -->
                    <div class="modal fade" id="previewModal" tabindex="-1" aria-labelledby="previewModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="previewModalLabel">
                                        <i class="feather-eye me-2"></i>
                                        معاينة الإشعار
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="notification-preview">
                                        <div class="d-flex align-items-start">
                                            <div class="notification-icon me-3">
                                                <i class="feather-bell text-primary"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="notification-title mb-1" id="preview-title">عنوان الإشعار</h6>
                                                <p class="notification-body mb-2" id="preview-body">محتوى الإشعار</p>
                                                <div class="notification-meta">
                                                    <span class="badge bg-secondary me-2" id="preview-type">عام</span>
                                                    <span class="badge bg-warning me-2" id="preview-priority">عادي</span>
                                                    <small class="text-muted" id="preview-time">الآن</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                                    <button type="button" class="btn btn-primary" onclick="submitForm()">إرسال الإشعار</button>
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
document.addEventListener('DOMContentLoaded', function() {
    const targetTypeRadios = document.querySelectorAll('input[name="target_type"]');
    const specificUsersSection = document.getElementById('specific_users_section');
    const userTypesSection = document.getElementById('user_types_section');
    const scheduleSection = document.getElementById('schedule_section');
    const scheduleCheckbox = document.getElementById('schedule_notification');
    const bodyTextarea = document.getElementById('body');
    const charCount = document.getElementById('char-count');
    
    // Character counter
    function updateCharCount() {
        const count = bodyTextarea.value.length;
        charCount.textContent = count;
        
        if (count > 1000) {
            charCount.parentElement.classList.add('text-danger');
        } else {
            charCount.parentElement.classList.remove('text-danger');
        }
    }
    
    bodyTextarea.addEventListener('input', updateCharCount);
    updateCharCount(); // Initial count
    
    // Toggle sections based on target type
    function toggleSections() {
        const selectedType = document.querySelector('input[name="target_type"]:checked')?.value;
        
        // Hide all sections first
        specificUsersSection.style.display = 'none';
        userTypesSection.style.display = 'none';
        
        // Show relevant section
        if (selectedType === 'specific_users') {
            specificUsersSection.style.display = 'block';
        } else if (selectedType === 'user_types') {
            userTypesSection.style.display = 'block';
        }
    }
    
    // Toggle schedule section
    function toggleScheduleSection() {
        if (scheduleCheckbox.checked) {
            scheduleSection.style.display = 'block';
            // Set default date to tomorrow
            const tomorrow = new Date();
            tomorrow.setDate(tomorrow.getDate() + 1);
            document.getElementById('scheduled_date').value = tomorrow.toISOString().split('T')[0];
            // Set default time to 9 AM
            document.getElementById('scheduled_time').value = '09:00';
        } else {
            scheduleSection.style.display = 'none';
        }
    }
    
    // Add event listeners
    targetTypeRadios.forEach(radio => {
        radio.addEventListener('change', toggleSections);
    });
    
    scheduleCheckbox.addEventListener('change', toggleScheduleSection);
    
    // Initial calls
    toggleSections();
    toggleScheduleSection();
    
    // Initialize user selection functionality
    initializeUserSelection();
});

// User search and selection functionality
function initializeUserSelection() {
    const userSelect = document.getElementById('user_ids');
    const userSearchInput = document.getElementById('userSearchInput');
    const selectedUsersCount = document.getElementById('selectedUsersCount');
    const selectedUsersDisplay = document.getElementById('selectedUsersDisplay');
    const selectedUsersList = document.getElementById('selectedUsersList');
    
    if (!userSelect || !userSearchInput) return;
    
    // User search functionality
    userSearchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const options = userSelect.querySelectorAll('option');
        
        options.forEach(option => {
            const userText = option.textContent.toLowerCase();
            if (userText.includes(searchTerm) || searchTerm === '') {
                option.style.display = 'block';
            } else {
                option.style.display = 'none';
            }
        });
    });
    
    // Update selected users display
    function updateSelectedUsersDisplay() {
        const selectedOptions = Array.from(userSelect.selectedOptions);
        const count = selectedOptions.length;
        
        selectedUsersCount.textContent = count;
        
        if (count > 0) {
            selectedUsersDisplay.style.display = 'block';
            selectedUsersList.innerHTML = '';
            
            selectedOptions.forEach(option => {
                const userBadge = document.createElement('span');
                userBadge.className = 'badge bg-primary me-2 mb-2';
                userBadge.innerHTML = `
                    ${option.textContent}
                    <button type="button" class="btn-close btn-close-white ms-1" 
                            onclick="removeUserSelection(${option.value})" 
                            style="font-size: 0.7em;"></button>
                `;
                selectedUsersList.appendChild(userBadge);
            });
        } else {
            selectedUsersDisplay.style.display = 'none';
        }
    }
    
    // Add event listener for selection changes
    userSelect.addEventListener('change', updateSelectedUsersDisplay);
    
    // Initial update
    updateSelectedUsersDisplay();
    
    // Make functions globally available
    window.removeUserSelection = function(userId) {
        const option = userSelect.querySelector(`option[value="${userId}"]`);
        if (option) {
            option.selected = false;
            updateSelectedUsersDisplay();
        }
    };
}

// Notification templates
const templates = {
    welcome: {
        title: 'مرحباً بك في النظام',
        body: 'نرحب بك في نظام إدارة الموارد البشرية. نتمنى لك تجربة ممتعة ومفيدة.',
        type: 'general',
        priority: 'normal'
    },
    reminder: {
        title: 'تذكير مهم',
        body: 'هذا تذكير مهم يرجى الانتباه إليه واتخاذ الإجراء المناسب في أقرب وقت ممكن.',
        type: 'general',
        priority: 'high'
    },
    announcement: {
        title: 'إعلان عام',
        body: 'نود إعلامكم بإعلان عام مهم يخص جميع الموظفين. يرجى الاطلاع على التفاصيل.',
        type: 'general',
        priority: 'normal'
    },
    urgent: {
        title: 'إشعار عاجل',
        body: 'هذا إشعار عاجل يتطلب انتباهكم الفوري. يرجى اتخاذ الإجراء المناسب فوراً.',
        type: 'general',
        priority: 'urgent'
    }
};

function useTemplate(templateName) {
    const template = templates[templateName];
    if (template) {
        document.getElementById('title').value = template.title;
        document.getElementById('body').value = template.body;
        document.getElementById('type').value = template.type;
        document.getElementById('priority').value = template.priority;
        
        // Update character count
        document.getElementById('char-count').textContent = template.body.length;
        
        // Show success message
        showToast('تم تطبيق القالب بنجاح', 'success');
    }
}

function previewNotification() {
    const title = document.getElementById('title').value || 'عنوان الإشعار';
    const body = document.getElementById('body').value || 'محتوى الإشعار';
    const type = document.getElementById('type').value || 'general';
    const priority = document.getElementById('priority').value || 'normal';
    
    // Update preview content
    document.getElementById('preview-title').textContent = title;
    document.getElementById('preview-body').textContent = body;
    
    // Update type badge
    const typeTexts = {
        'general': 'عام',
        'leave_request': 'طلب إجازة',
        'advance_request': 'طلب سلفة',
        'resignation_request': 'طلب استقالة',
        'delivery_deposit': 'إيداع تسليم'
    };
    document.getElementById('preview-type').textContent = typeTexts[type] || 'عام';
    
    // Update priority badge
    const priorityTexts = {
        'normal': 'عادي',
        'high': 'عالي',
        'urgent': 'عاجل'
    };
    const priorityColors = {
        'normal': 'bg-secondary',
        'high': 'bg-warning',
        'urgent': 'bg-danger'
    };
    const priorityElement = document.getElementById('preview-priority');
    priorityElement.textContent = priorityTexts[priority] || 'عادي';
    priorityElement.className = `badge ${priorityColors[priority] || 'bg-secondary'} me-2`;
    
    // Update time
    const scheduleCheckbox = document.getElementById('schedule_notification');
    if (scheduleCheckbox.checked) {
        const scheduledDate = document.getElementById('scheduled_date').value;
        const scheduledTime = document.getElementById('scheduled_time').value;
        if (scheduledDate && scheduledTime) {
            document.getElementById('preview-time').textContent = `مجدول: ${scheduledDate} ${scheduledTime}`;
        } else {
            document.getElementById('preview-time').textContent = 'مجدول للإرسال لاحقاً';
        }
    } else {
        document.getElementById('preview-time').textContent = 'الآن';
    }
    
    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('previewModal'));
    modal.show();
}

function submitForm() {
    // Close modal
    const modal = bootstrap.Modal.getInstance(document.getElementById('previewModal'));
    modal.hide();
    
    // Submit form
    document.querySelector('form').submit();
}

function showToast(message, type = 'info') {
    // Create toast element
    const toast = document.createElement('div');
    toast.className = `toast align-items-center text-white bg-${type} border-0`;
    toast.setAttribute('role', 'alert');
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">${message}</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    `;
    
    // Add to container
    let toastContainer = document.querySelector('.toast-container');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
        document.body.appendChild(toastContainer);
    }
    
    toastContainer.appendChild(toast);
    
    // Show toast
    const bsToast = new bootstrap.Toast(toast);
    bsToast.show();
    
    // Remove after hide
    toast.addEventListener('hidden.bs.toast', () => {
        toast.remove();
    });
}
</script>

<style>
.notification-preview {
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 20px;
    margin: 10px 0;
}

.notification-icon {
    width: 40px;
    height: 40px;
    background: #e3f2fd;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
}

.notification-title {
    font-weight: 600;
    color: #333;
}

.notification-body {
    color: #666;
    line-height: 1.5;
}

.notification-meta {
    margin-top: 10px;
}

.char-count-warning {
    color: #dc3545 !important;
}

.template-btn {
    transition: all 0.3s ease;
}

.template-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.user-search-container {
    position: relative;
}

.user-search-container .form-control {
    padding-right: 40px;
}

.user-search-container .search-icon {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: #6c757d;
    pointer-events: none;
}

.selected-user-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    margin: 2px;
}

.selected-user-badge .btn-close {
    background-size: 0.7em;
    opacity: 0.8;
}

.selected-user-badge .btn-close:hover {
    opacity: 1;
}

.user-count-display {
    font-size: 0.875rem;
    color: #6c757d;
}

.user-count-display .text-primary {
    color: #0d6efd !important;
    font-weight: 600;
}
</style>
@endpush

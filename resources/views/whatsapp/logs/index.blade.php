@extends('layouts.app')

@section('title', 'سجل رسائل الواتساب')

@section('content')
<style>
    .logs-container {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        min-height: 100vh;
        padding: 20px 0;
    }
    
    .main-card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        overflow: hidden;
    }
    
    .header-section {
        background: linear-gradient(135deg, #25D366 0%, #128C7E 100%);
        color: white;
        padding: 30px;
        text-align: center;
        position: relative;
        overflow: hidden;
    }
    
    .header-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="whatsapp-pattern" patternUnits="userSpaceOnUse" width="20" height="20"><circle cx="10" cy="10" r="2" fill="rgba(255,255,255,0.1)"/></pattern></defs><rect width="100" height="100" fill="url(%23whatsapp-pattern)"/></svg>');
        opacity: 0.3;
    }
    
    .header-content {
        position: relative;
        z-index: 1;
    }
    
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin: 30px 0;
    }
    
    .stat-card {
        background: white;
        border-radius: 12px;
        padding: 25px;
        text-align: center;
        box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
        border-left: 4px solid;
    }
    
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.15);
    }
    
    .stat-card.total { border-left-color: #17a2b8; }
    .stat-card.sent { border-left-color: #28a745; }
    .stat-card.failed { border-left-color: #dc3545; }
    .stat-card.pending { border-left-color: #ffc107; }
    
    .stat-number {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 10px;
    }
    
    .stat-label {
        font-size: 1rem;
        color: #6c757d;
        font-weight: 500;
    }
    
    .filters-section {
        background: white;
        padding: 25px;
        border-radius: 12px;
        margin-bottom: 25px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.08);
    }
    
    .filter-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
        align-items: end;
    }
    
    .form-group {
        margin-bottom: 0;
    }
    
    .form-control {
        border-radius: 8px;
        border: 2px solid #e9ecef;
        padding: 12px 15px;
        transition: all 0.3s ease;
    }
    
    .form-control:focus {
        border-color: #25D366;
        box-shadow: 0 0 0 0.2rem rgba(37, 211, 102, 0.25);
    }
    
    .btn-whatsapp {
        background: linear-gradient(135deg, #25D366 0%, #128C7E 100%);
        border: none;
        color: white;
        padding: 12px 25px;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .btn-whatsapp:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(37, 211, 102, 0.4);
        color: white;
    }
    
    .btn-danger-custom {
        background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
        border: none;
        color: white;
        padding: 8px 16px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 500;
        transition: all 0.3s ease;
    }
    
    .btn-danger-custom:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(220, 53, 69, 0.4);
        color: white;
    }
    
    .btn-warning-custom {
        background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%);
        border: none;
        color: #212529;
        padding: 8px 16px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 500;
        transition: all 0.3s ease;
    }
    
    .btn-warning-custom:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(255, 193, 7, 0.4);
        color: #212529;
    }
    
    .logs-table {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 5px 20px rgba(0,0,0,0.08);
    }
    
    .table-header {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        padding: 20px 25px;
        border-bottom: 2px solid #25D366;
    }
    
    .table {
        margin-bottom: 0;
    }
    
    .table thead th {
        background: #25D366;
        color: white;
        border: none;
        font-weight: 600;
        padding: 15px 12px;
        font-size: 14px;
    }
    
    .table tbody td {
        padding: 15px 12px;
        vertical-align: middle;
        border-color: #f1f3f4;
    }
    
    .table tbody tr:hover {
        background-color: #f8f9fa;
    }
    
    .status-badge {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .status-sent {
        background: #d4edda;
        color: #155724;
    }
    
    .status-failed {
        background: #f8d7da;
        color: #721c24;
    }
    
    .status-pending {
        background: #fff3cd;
        color: #856404;
    }
    
    .status-waiting {
        background: #d1ecf1;
        color: #0c5460;
    }
    
    .phone-link {
        color: #25D366;
        text-decoration: none;
        font-weight: 600;
    }
    
    .phone-link:hover {
        color: #128C7E;
        text-decoration: underline;
    }
    
    .message-preview {
        max-width: 200px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        cursor: pointer;
        color: #6c757d;
    }
    
    .message-preview:hover {
        color: #25D366;
    }
    
    .action-buttons {
        display: flex;
        gap: 5px;
        flex-wrap: wrap;
    }
    
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #6c757d;
    }
    
    .empty-state i {
        font-size: 48px;
        color: #25D366;
        margin-bottom: 20px;
    }
    
    .bulk-actions {
        background: #f8f9fa;
        padding: 15px 25px;
        border-top: 1px solid #e9ecef;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
    }
    
    .pagination-wrapper {
        background: white;
        padding: 20px;
        border-radius: 0 0 12px 12px;
    }
    
    .loading-spinner {
        display: inline-block;
        width: 20px;
        height: 20px;
        border: 3px solid #f3f3f3;
        border-top: 3px solid #25D366;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    
    .fade-in {
        animation: fadeIn 0.5s ease-in;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>

<div class="logs-container">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="main-card fade-in">
                    <!-- Header Section -->
                    <div class="header-section">
                        <div class="header-content">
                            <h1 class="mb-3">
                                <i class="fab fa-whatsapp"></i> سجل رسائل الواتساب
                            </h1>
                            <p class="mb-0">إدارة ومراقبة جميع رسائل الواتساب المرسلة</p>
                        </div>
                    </div>
                    
                    <!-- Statistics Section -->
                    <div class="container-fluid" style="padding: 0 30px;">
                        <div class="stats-grid">
                            <div class="stat-card total">
                                <div class="stat-number text-info">{{ $stats['total'] }}</div>
                                <div class="stat-label">إجمالي الرسائل</div>
                            </div>
                            <div class="stat-card sent">
                                <div class="stat-number text-success">{{ $stats['sent'] }}</div>
                                <div class="stat-label">مرسلة بنجاح</div>
                            </div>
                            <div class="stat-card failed">
                                <div class="stat-number text-danger">{{ $stats['failed'] }}</div>
                                <div class="stat-label">فشل في الإرسال</div>
                            </div>
                            <div class="stat-card pending">
                                <div class="stat-number text-warning">{{ $stats['pending'] }}</div>
                                <div class="stat-label">معلقة</div>
                            </div>
                        </div>
                        
                        <!-- Filters Section -->
                        <div class="filters-section">
                            <form method="GET" id="filter-form">
                                <div class="filter-row">
                                    <div class="form-group">
                                        <label for="status">الحالة</label>
                                        <select name="status" id="status" class="form-control">
                                            <option value="">جميع الحالات</option>
                                            <option value="sent" {{ request('status') == 'sent' ? 'selected' : '' }}>مرسل</option>
                                            <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>فشل</option>
                                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>معلق</option>
                                            <option value="waiting" {{ request('status') == 'waiting' ? 'selected' : '' }}>في الانتظار</option>
                                        </select>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="phone">رقم الهاتف</label>
                                        <input type="text" name="phone" id="phone" class="form-control" 
                                               value="{{ request('phone') }}" placeholder="ابحث برقم الهاتف">
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="service">الخدمة</label>
                                        <select name="service" id="service" class="form-control">
                                            <option value="">جميع الخدمات</option>
                                            <option value="simulation" {{ request('service') == 'simulation' ? 'selected' : '' }}>محاكاة</option>
                                            <option value="twilio" {{ request('service') == 'twilio' ? 'selected' : '' }}>Twilio</option>
                                            <option value="whatsapp_business" {{ request('service') == 'whatsapp_business' ? 'selected' : '' }}>WhatsApp Business</option>
                                            <option value="wapilot" {{ request('service') == 'wapilot' ? 'selected' : '' }}>WAPilot</option>
                                        </select>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="date_from">من تاريخ</label>
                                        <input type="date" name="date_from" id="date_from" class="form-control" 
                                               value="{{ request('date_from') }}">
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="date_to">إلى تاريخ</label>
                                        <input type="date" name="date_to" id="date_to" class="form-control" 
                                               value="{{ request('date_to') }}">
                                    </div>
                                    
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-whatsapp w-100">
                                            <i class="fas fa-search"></i> بحث
                                        </button>
                                    </div>
                                    
                                    <div class="form-group">
                                        <a href="{{ route('whatsapp.logs.index') }}" class="btn btn-secondary w-100">
                                            <i class="fas fa-times"></i> مسح الفلاتر
                                        </a>
                                    </div>
                                </div>
                            </form>
                        </div>
                        
                        <!-- Logs Table -->
                        <div class="logs-table">
                            <div class="table-header">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h4 class="mb-0">
                                        <i class="fas fa-list"></i> سجل الرسائل
                                        <span class="badge badge-info ml-2">{{ $logs->total() }}</span>
                                    </h4>
                                    <div>
                                        @can('manage_whatsapp_logs')
                                        <button class="btn btn-danger-custom" onclick="bulkDelete()">
                                            <i class="fas fa-trash"></i> حذف محدد
                                        </button>
                                        <button class="btn btn-warning-custom" onclick="clearOldLogs()">
                                            <i class="fas fa-broom"></i> مسح القديمة
                                        </button>
                                        @endcan
                                        <a href="{{ route('whatsapp.logs.export', request()->query()) }}" class="btn btn-info">
                                            <i class="fas fa-download"></i> تصدير
                                        </a>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th width="50">
                                                <input type="checkbox" id="select-all" onchange="toggleSelectAll()">
                                            </th>
                                            <th>الهاتف</th>
                                            <th>الرسالة</th>
                                            <th>الحالة</th>
                                            <th>الخدمة</th>
                                            <th>المحاولات</th>
                                            <th>التاريخ</th>
                                            <th>الإجراءات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($logs as $log)
                                        <tr>
                                            <td>
                                                <input type="checkbox" class="log-checkbox" value="{{ $log->id }}">
                                            </td>
                                            <td>
                                                <a href="{{ $log->whatsapp_link }}" target="_blank" class="phone-link">
                                                    {{ $log->phone }}
                                                </a>
                                                <br>
                                                <small class="text-muted">{{ $log->time_ago }}</small>
                                            </td>
                                            <td>
                                                <div class="message-preview" onclick="showLogDetails({{ $log->id }})" 
                                                     title="{{ $log->message }}">
                                                    {{ $log->message_preview }}
                                                </div>
                                            </td>
                                            <td>
                                                <span class="status-badge status-{{ $log->status }}">
                                                    {{ $log->status_text }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge badge-info">{{ $log->service }}</span>
                                            </td>
                                            <td>
                                                <span class="badge badge-secondary">{{ $log->attempts }}</span>
                                            </td>
                                            <td>
                                                <small class="text-muted">{{ $log->created_at->format('Y-m-d H:i') }}</small>
                                            </td>
                                            <td>
                                                <div class="action-buttons">
                                                    @can('resend_whatsapp_messages')
                                                    @if($log->canResend())
                                                    <button class="btn btn-success btn-sm" onclick="resendMessage({{ $log->id }})">
                                                        <i class="fas fa-redo"></i>
                                                    </button>
                                                    @endif
                                                    @endcan
                                                    
                                                    
                                                    @can('manage_whatsapp_logs')
                                                    <button class="btn btn-danger btn-sm" onclick="deleteLog({{ $log->id }})">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                    @endcan
                                                </div>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="8" class="empty-state">
                                                <i class="fas fa-inbox"></i>
                                                <h4>لا توجد سجلات</h4>
                                                <p>لم يتم العثور على أي سجلات مطابقة للبحث</p>
                                            </td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            
                            @if($logs->hasPages())
                            <div class="pagination-wrapper">
                                {{ $logs->appends(request()->query())->links() }}
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Log Details Modal -->
<div class="modal fade" id="logModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <i class="fab fa-whatsapp"></i> تفاصيل السجل
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body" id="log-content">
                <!-- Content will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">إغلاق</button>
                <button type="button" class="btn btn-success" id="resend-from-modal">
                    <i class="fas fa-redo"></i> إعادة إرسال
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
let currentLogId = null;

// Auto-submit form on filter change
document.getElementById('filter-form').addEventListener('change', function() {
    this.submit();
});

function toggleSelectAll() {
    const selectAll = document.getElementById('select-all');
    const checkboxes = document.querySelectorAll('.log-checkbox');
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAll.checked;
    });
}

function showLogDetails(logId) {
    currentLogId = logId;
    
    // Fetch log details
    fetch(`/whatsapp-logs/${logId}`)
        .then(response => response.text())
        .then(html => {
            document.getElementById('log-content').innerHTML = html;
            $('#logModal').modal('show');
        })
        .catch(error => {
            console.error('Error:', error);
            toastr.error('حدث خطأ أثناء جلب تفاصيل السجل');
        });
}

function resendMessage(logId) {
    if (!confirm('هل أنت متأكد من إعادة إرسال هذه الرسالة؟')) {
        return;
    }
    
    const button = event.target.closest('button');
    const originalContent = button.innerHTML;
    
    button.innerHTML = '<span class="loading-spinner"></span>';
    button.disabled = true;
    
    fetch(`/whatsapp-logs/${logId}/resend`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            toastr.success(data.message);
            setTimeout(() => location.reload(), 1000);
        } else {
            toastr.error(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        toastr.error('حدث خطأ أثناء إعادة الإرسال');
    })
    .finally(() => {
        button.innerHTML = originalContent;
        button.disabled = false;
    });
}

function deleteLog(logId) {
    if (!confirm('هل أنت متأكد من حذف هذا السجل؟')) {
        return;
    }
    
    fetch(`/whatsapp-logs/${logId}`, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            toastr.success(data.message);
            setTimeout(() => location.reload(), 1000);
        } else {
            toastr.error(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        toastr.error('حدث خطأ أثناء الحذف');
    });
}

function bulkDelete() {
    const checkboxes = document.querySelectorAll('.log-checkbox:checked');
    
    if (checkboxes.length === 0) {
        toastr.warning('يرجى تحديد السجلات المراد حذفها');
        return;
    }
    
    if (!confirm(`هل أنت متأكد من حذف ${checkboxes.length} سجل؟`)) {
        return;
    }
    
    const ids = Array.from(checkboxes).map(cb => cb.value);
    
    fetch('/whatsapp-logs/bulk-delete', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ ids: ids })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            toastr.success(data.message);
            setTimeout(() => location.reload(), 1000);
        } else {
            toastr.error(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        toastr.error('حدث خطأ أثناء الحذف');
    });
}

function clearOldLogs() {
    const days = prompt('كم يوم من السجلات القديمة تريد حذفها؟', '30');
    
    if (!days || isNaN(days)) {
        return;
    }
    
    if (!confirm(`هل أنت متأكد من حذف السجلات الأقدم من ${days} يوم؟`)) {
        return;
    }
    
    fetch('/whatsapp-logs/clear-old', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ days: parseInt(days) })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            toastr.success(data.message);
            setTimeout(() => location.reload(), 1000);
        } else {
            toastr.error(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        toastr.error('حدث خطأ أثناء المسح');
    });
}

// Initialize toastr
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
    "hideMethod": "fadeOut"
};
</script>
@endsection

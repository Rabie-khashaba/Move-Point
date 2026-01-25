@extends('layouts.app')

@section('title', 'إدارة رسائل الواتساب')

@section('content')
<style>
    .whatsapp-card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        overflow: hidden;
        transition: transform 0.3s ease;
    }
    
    .whatsapp-card:hover {
        transform: translateY(-5px);
    }
    
    .whatsapp-header {
        background: linear-gradient(135deg, #25D366 0%, #128C7E 100%);
        border: none;
        padding: 20px 25px;
    }
    
    .whatsapp-header h3 {
        color: white;
        font-weight: 600;
        margin: 0;
        font-size: 1.5rem;
    }
    
    .whatsapp-header .card-tools {
        display: flex;
        gap: 10px;
    }
    
    .whatsapp-header .btn {
        border-radius: 8px;
        font-weight: 500;
        transition: all 0.3s ease;
    }
    
    .whatsapp-header .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    }
    
    .stats-card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
        overflow: hidden;
    }
    
    .stats-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    }
    
    .info-box {
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 20px;
        transition: all 0.3s ease;
    }
    
    .info-box:hover {
        transform: scale(1.02);
    }
    
    .info-box-icon {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        color: white;
    }
    
    .info-box-content {
        margin-right: 15px;
    }
    
    .info-box-text {
        font-size: 14px;
        font-weight: 500;
        color: #6c757d;
        margin-bottom: 5px;
    }
    
    .info-box-number {
        font-size: 28px;
        font-weight: 700;
        color: #2c3e50;
    }
    
    .table-card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        overflow: hidden;
    }
    
    .table-card .card-header {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-bottom: 2px solid #25D366;
        padding: 20px 25px;
    }
    
    .table-card .card-title {
        color: #2c3e50;
        font-weight: 600;
        margin: 0;
        font-size: 1.2rem;
    }
    
    .table-responsive {
        border-radius: 0 0 12px 12px;
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
    
    .btn-resend {
        background: linear-gradient(135deg, #25D366 0%, #128C7E 100%);
        border: none;
        color: white;
        padding: 8px 16px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 500;
        transition: all 0.3s ease;
    }
    
    .btn-resend:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(37, 211, 102, 0.4);
        color: white;
    }
    
    .btn-resend-all {
        background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%);
        border: none;
        color: #212529;
        padding: 10px 20px;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .btn-resend-all:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(255, 193, 7, 0.4);
        color: #212529;
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
    
    .empty-state h4 {
        color: #2c3e50;
        margin-bottom: 10px;
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
    
    .error-text {
        color: #dc3545;
        font-size: 12px;
        max-width: 150px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    
    .attempts-badge {
        background: #e9ecef;
        color: #495057;
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 600;
    }
    
    .service-badge {
        background: #17a2b8;
        color: white;
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 600;
    }
</style>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card whatsapp-card fade-in">
                <div class="card-header whatsapp-header">
                    <h3 class="card-title mb-0">
                        <i class="fab fa-whatsapp"></i> إدارة رسائل الواتساب
                    </h3>
                    <div class="card-tools">
                        <button class="btn btn-light btn-sm" onclick="refreshData()" id="refresh-btn">
                            <i class="fas fa-sync-alt"></i> تحديث
                        </button>
                        <a href="/whatsapp-dashboard" class="btn btn-light btn-sm" target="_blank">
                            <i class="fas fa-chart-bar"></i> لوحة الإحصائيات
                        </a>
                    </div>
                </div>
                
                <div class="card-body">
                    <!-- Statistics Cards -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="info-box bg-info stats-card">
                                <span class="info-box-icon"><i class="fas fa-paper-plane"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">إجمالي الرسائل</span>
                                    <span class="info-box-number" id="total-messages">{{ $stats['total'] ?? 0 }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box bg-success stats-card">
                                <span class="info-box-icon"><i class="fas fa-check"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">مرسلة بنجاح</span>
                                    <span class="info-box-number" id="sent-messages">{{ $stats['sent'] ?? 0 }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box bg-danger stats-card">
                                <span class="info-box-icon"><i class="fas fa-times"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">فشل في الإرسال</span>
                                    <span class="info-box-number" id="failed-messages">{{ $stats['failed'] ?? 0 }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box bg-warning stats-card">
                                <span class="info-box-icon"><i class="fas fa-clock"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">في الانتظار</span>
                                    <span class="info-box-number" id="pending-messages">{{ $stats['pending_messages'] ?? 0 }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pending Messages Table -->
                    <div class="card table-card">
                        <div class="card-header">
                            <h4 class="card-title">
                                <i class="fas fa-exclamation-triangle text-warning"></i> 
                                الرسائل المعلقة والفاشلة
                            </h4>
                            <div class="card-tools">
                                <button class="btn btn-resend-all btn-sm" onclick="resendAllPending()" id="resend-all-btn">
                                    <i class="fas fa-redo"></i> إعادة إرسال الكل
                                </button>
                            </div>
                        </div>
                        
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover" id="pending-messages-table">
                                    <thead>
                                        <tr>
                                            <th>الهاتف</th>
                                            <th>الرسالة</th>
                                            <th>الحالة</th>
                                            <th>الخدمة</th>
                                            <th>التاريخ</th>
                                            <th>المحاولات</th>
                                            <th>الخطأ</th>
                                            <th>الإجراءات</th>
                                        </tr>
                                    </thead>
                                    <tbody id="pending-messages-body">
                                        @forelse($pendingMessages as $message)
                                        <tr data-message-id="{{ $message['id'] }}">
                                            <td>
                                                <a href="https://wa.me/{{ str_replace('+', '', $message['phone']) }}" 
                                                   target="_blank" class="phone-link">
                                                    {{ $message['phone'] }}
                                                </a>
                                                <br>
                                                <small class="text-muted">{{ $message['timestamp'] }}</small>
                                            </td>
                                            <td>
                                                <div class="message-preview" onclick="showMessageDetails('{{ $message['id'] }}')" 
                                                     title="{{ $message['message'] }}">
                                                    {{ Str::limit($message['message'], 50) }}
                                                </div>
                                            </td>
                                            <td>
                                                <span class="status-badge status-{{ $message['status'] }}">
                                                    {{ $message['status'] }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="service-badge">{{ $message['service'] }}</span>
                                            </td>
                                            <td>
                                                <small class="text-muted">{{ $message['timestamp'] }}</small>
                                            </td>
                                            <td>
                                                <span class="attempts-badge">{{ $message['attempts'] ?? 0 }}</span>
                                            </td>
                                            <td>
                                                <div class="error-text" title="{{ $message['error'] ?? '' }}">
                                                    {{ Str::limit($message['error'] ?? 'لا يوجد خطأ', 30) }}
                                                </div>
                                            </td>
                                            <td>
                                                <button class="btn btn-resend btn-sm" 
                                                        onclick="resendMessage('{{ $message['id'] }}')"
                                                        data-message-id="{{ $message['id'] }}">
                                                    <i class="fas fa-redo"></i> إعادة إرسال
                                                </button>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="8" class="empty-state">
                                                <i class="fas fa-check-circle"></i>
                                                <h4>لا توجد رسائل معلقة</h4>
                                                <p>جميع الرسائل تم إرسالها بنجاح!</p>
                                            </td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Success Rate Card -->
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="card stats-card">
                                <div class="card-body text-center">
                                    <h5 class="card-title text-success">
                                        <i class="fas fa-chart-line"></i> معدل النجاح
                                    </h5>
                                    <div class="row text-center">
                                        <div class="col-6">
                                            <h4 class="text-success" id="success-rate">{{ $stats['success_rate'] ?? 0 }}%</h4>
                                            <small class="text-muted">معدل النجاح</small>
                                        </div>
                                        <div class="col-6">
                                            <h4 class="text-info" id="total-today">{{ $stats['total'] ?? 0 }}</h4>
                                            <small class="text-muted">إجمالي اليوم</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Message Modal -->
<div class="modal fade" id="messageModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <i class="fab fa-whatsapp"></i> تفاصيل الرسالة
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="message-content"></div>
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
let currentMessageId = null;

// Refresh data every 30 seconds
setInterval(refreshData, 30000);

function refreshData() {
    const refreshBtn = document.getElementById('refresh-btn');
    const originalContent = refreshBtn.innerHTML;
    
    // Show loading spinner
    refreshBtn.innerHTML = '<span class="loading-spinner"></span> تحديث...';
    refreshBtn.disabled = true;
    
    // Refresh statistics
    fetch('/whatsapp-dashboard-stats')
        .then(response => response.json())
        .then(data => {
            if (data.statistics) {
                updateStatistics(data.statistics);
            }
        })
        .catch(error => console.error('Error refreshing stats:', error));
    
    // Refresh pending messages
    fetch('/whatsapp-pending-messages')
        .then(response => response.json())
        .then(data => {
            if (data.messages) {
                updatePendingMessagesTable(data.messages);
            }
        })
        .catch(error => console.error('Error refreshing pending messages:', error))
        .finally(() => {
            // Restore button
            refreshBtn.innerHTML = originalContent;
            refreshBtn.disabled = false;
        });
}

function updateStatistics(stats) {
    document.getElementById('total-messages').textContent = stats.total || 0;
    document.getElementById('sent-messages').textContent = stats.sent || 0;
    document.getElementById('failed-messages').textContent = stats.failed || 0;
    document.getElementById('pending-messages').textContent = stats.pending_messages || 0;
    document.getElementById('success-rate').textContent = (stats.success_rate || 0) + '%';
    document.getElementById('total-today').textContent = stats.total || 0;
}

function updatePendingMessagesTable(messages) {
    const tbody = document.getElementById('pending-messages-body');
    
    if (messages.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="8" class="empty-state">
                    <i class="fas fa-check-circle"></i>
                    <h4>لا توجد رسائل معلقة</h4>
                    <p>جميع الرسائل تم إرسالها بنجاح!</p>
                </td>
            </tr>
        `;
        return;
    }
    
    tbody.innerHTML = messages.map(message => `
        <tr data-message-id="${message.id}">
            <td>
                <a href="https://wa.me/${message.phone.replace('+', '')}" 
                   target="_blank" class="phone-link">
                    ${message.phone}
                </a>
                <br>
                <small class="text-muted">${message.timestamp}</small>
            </td>
            <td>
                <div class="message-preview" onclick="showMessageDetails('${message.id}')" 
                     title="${message.message}">
                    ${message.message.length > 50 ? message.message.substring(0, 50) + '...' : message.message}
                </div>
            </td>
            <td>
                <span class="status-badge status-${message.status}">
                    ${message.status}
                </span>
            </td>
            <td>
                <span class="service-badge">${message.service}</span>
            </td>
            <td>
                <small class="text-muted">${message.timestamp}</small>
            </td>
            <td>
                <span class="attempts-badge">${message.attempts || 0}</span>
            </td>
            <td>
                <div class="error-text" title="${message.error || ''}">
                    ${message.error ? (message.error.length > 30 ? message.error.substring(0, 30) + '...' : message.error) : 'لا يوجد خطأ'}
                </div>
            </td>
            <td>
                <button class="btn btn-resend btn-sm" 
                        onclick="resendMessage('${message.id}')"
                        data-message-id="${message.id}">
                    <i class="fas fa-redo"></i> إعادة إرسال
                </button>
            </td>
        </tr>
    `).join('');
}

function showMessageDetails(messageId) {
    currentMessageId = messageId;
    
    // Find the message data
    const row = document.querySelector(`tr[data-message-id="${messageId}"]`);
    if (!row) return;
    
    const phone = row.cells[0].querySelector('.phone-link').textContent.trim();
    const message = row.cells[1].querySelector('.message-preview').getAttribute('title');
    const status = row.cells[2].querySelector('.status-badge').textContent.trim();
    const service = row.cells[3].querySelector('.service-badge').textContent.trim();
    const timestamp = row.cells[4].textContent.trim();
    const attempts = row.cells[5].querySelector('.attempts-badge').textContent.trim();
    const error = row.cells[6].querySelector('.error-text').getAttribute('title');
    
    const content = `
        <div class="row">
            <div class="col-md-6">
                <h6><i class="fas fa-phone text-success"></i> رقم الهاتف:</h6>
                <p class="text-muted">${phone}</p>
                
                <h6><i class="fas fa-cog text-info"></i> الخدمة:</h6>
                <p class="text-muted">${service}</p>
                
                <h6><i class="fas fa-calendar text-primary"></i> التاريخ:</h6>
                <p class="text-muted">${timestamp}</p>
            </div>
            <div class="col-md-6">
                <h6><i class="fas fa-info-circle text-warning"></i> الحالة:</h6>
                <p><span class="status-badge status-${status}">${status}</span></p>
                
                <h6><i class="fas fa-redo text-secondary"></i> المحاولات:</h6>
                <p class="text-muted">${attempts}</p>
                
                ${error ? `
                <h6><i class="fas fa-exclamation-triangle text-danger"></i> الخطأ:</h6>
                <p class="text-danger">${error}</p>
                ` : ''}
            </div>
        </div>
        <hr>
        <h6><i class="fas fa-comment text-success"></i> نص الرسالة:</h6>
        <div class="alert alert-light" style="white-space: pre-wrap; font-family: monospace;">${message}</div>
    `;
    
    document.getElementById('message-content').innerHTML = content;
    $('#messageModal').modal('show');
}

function resendMessage(messageId) {
    const button = document.querySelector(`button[data-message-id="${messageId}"]`);
    const originalContent = button.innerHTML;
    
    // Show loading state
    button.innerHTML = '<span class="loading-spinner"></span> جاري الإرسال...';
    button.disabled = true;
    
    fetch('/whatsapp-resend-message', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            message_id: messageId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            toastr.success(data.message);
            // Refresh data after successful resend
            setTimeout(refreshData, 1000);
        } else {
            toastr.error(data.message || 'فشل في إعادة الإرسال');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        toastr.error('حدث خطأ أثناء إعادة الإرسال');
    })
    .finally(() => {
        // Restore button
        button.innerHTML = originalContent;
        button.disabled = false;
    });
}

function resendAllPending() {
    const button = document.getElementById('resend-all-btn');
    const originalContent = button.innerHTML;
    
    if (!confirm('هل أنت متأكد من إعادة إرسال جميع الرسائل المعلقة؟')) {
        return;
    }
    
    // Show loading state
    button.innerHTML = '<span class="loading-spinner"></span> جاري الإرسال...';
    button.disabled = true;
    
    fetch('/whatsapp-pending-messages')
        .then(response => response.json())
        .then(data => {
            if (data.messages && data.messages.length > 0) {
                let completed = 0;
                let successCount = 0;
                
                data.messages.forEach(message => {
                    fetch('/whatsapp-resend-message', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            message_id: message.id
                        })
                    })
                    .then(response => response.json())
                    .then(result => {
                        if (result.success) successCount++;
                        completed++;
                        
                        if (completed === data.messages.length) {
                            toastr.success(`تم إعادة إرسال ${successCount} من ${data.messages.length} رسالة بنجاح`);
                            // Refresh data
                            setTimeout(refreshData, 1000);
                        }
                    })
                    .catch(error => {
                        console.error('Error resending message:', error);
                        completed++;
                        
                        if (completed === data.messages.length) {
                            toastr.success(`تم إعادة إرسال ${successCount} من ${data.messages.length} رسالة بنجاح`);
                            setTimeout(refreshData, 1000);
                        }
                    });
                });
            } else {
                toastr.info('لا توجد رسائل معلقة لإعادة الإرسال');
            }
        })
        .catch(error => {
            console.error('Error fetching pending messages:', error);
            toastr.error('حدث خطأ أثناء جلب الرسائل المعلقة');
        })
        .finally(() => {
            // Restore button
            button.innerHTML = originalContent;
            button.disabled = false;
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
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>رسائل الواتساب - WhatsApp Messages</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Toastr -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">
    
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .main-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            margin: 20px;
            overflow: hidden;
        }
        
        .header {
            background: linear-gradient(135deg, #25D366 0%, #128C7E 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        .header h1 {
            font-size: 2.5rem;
            margin-bottom: 10px;
        }
        
        .stats-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
        }
        
        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .stat-label {
            color: #666;
            font-size: 0.9rem;
        }
        
        .message-item {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 15px;
            border-left: 5px solid #25D366;
            transition: all 0.3s ease;
        }
        
        .message-item:hover {
            transform: translateX(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .message-item.failed {
            border-left-color: #dc3545;
            background: #fff5f5;
        }
        
        .message-item.waiting {
            border-left-color: #ffc107;
            background: #fffbf0;
        }
        
        .status-badge {
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .status-sent {
            background: #d4edda;
            color: #155724;
        }
        
        .status-failed {
            background: #f8d7da;
            color: #721c24;
        }
        
        .status-waiting {
            background: #fff3cd;
            color: #856404;
        }
        
        .btn-whatsapp {
            background: #25D366;
            border-color: #25D366;
            color: white;
        }
        
        .btn-whatsapp:hover {
            background: #128C7E;
            border-color: #128C7E;
            color: white;
        }
        
        .loading {
            text-align: center;
            padding: 50px;
            color: #666;
        }
        
        .no-messages {
            text-align: center;
            padding: 50px;
            color: #28a745;
        }
        
        .message-preview {
            max-height: 100px;
            overflow: hidden;
            margin-bottom: 10px;
        }
        
        .error-text {
            color: #dc3545;
            font-size: 0.8rem;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <div class="main-container">
        <div class="header">
            <h1><i class="fab fa-whatsapp"></i> رسائل الواتساب</h1>
            <p>إدارة الرسائل المعلقة والفاشلة</p>
        </div>
        
        <div class="container-fluid p-4">
            <!-- Statistics Row -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="stats-card">
                        <div class="stat-number text-primary" id="total-messages">{{ $stats['total'] ?? 0 }}</div>
                        <div class="stat-label">إجمالي الرسائل</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-card">
                        <div class="stat-number text-success" id="sent-messages">{{ $stats['sent'] ?? 0 }}</div>
                        <div class="stat-label">مرسلة بنجاح</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-card">
                        <div class="stat-number text-danger" id="failed-messages">{{ $stats['failed'] ?? 0 }}</div>
                        <div class="stat-label">فشل في الإرسال</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-card">
                        <div class="stat-number text-warning" id="pending-messages">{{ $stats['pending_messages'] ?? 0 }}</div>
                        <div class="stat-label">في الانتظار</div>
                    </div>
                </div>
            </div>
            
            <!-- Action Buttons -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="d-flex gap-2 flex-wrap">
                        <button class="btn btn-whatsapp" onclick="refreshData()">
                            <i class="fas fa-sync-alt"></i> تحديث البيانات
                        </button>
                        <button class="btn btn-warning" onclick="resendAllPending()">
                            <i class="fas fa-redo"></i> إعادة إرسال الكل
                        </button>
                        <button class="btn btn-info" onclick="sendTestMessage()">
                            <i class="fas fa-paper-plane"></i> إرسال رسالة تجريبية
                        </button>
                        <a href="/whatsapp-dashboard" class="btn btn-secondary" target="_blank">
                            <i class="fas fa-chart-bar"></i> لوحة الإحصائيات
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Pending Messages -->
            <div class="row">
                <div class="col-12">
                    <h4 class="mb-3">
                        <i class="fas fa-exclamation-triangle text-warning"></i> 
                        الرسائل المعلقة والفاشلة
                    </h4>
                    
                    <div id="messages-container">
                        @forelse($pendingMessages as $message)
                        <div class="message-item {{ $message['status'] }}" data-message-id="{{ $message['id'] }}">
                            <div class="row align-items-center">
                                <div class="col-md-2">
                                    <strong>{{ $message['phone'] }}</strong>
                                    <br>
                                    <small class="text-muted">{{ \Carbon\Carbon::parse($message['timestamp'])->diffForHumans() }}</small>
                                </div>
                                <div class="col-md-4">
                                    <div class="message-preview">
                                        {{ Str::limit($message['message'], 150) }}
                                    </div>
                                    @if($message['error'])
                                        <div class="error-text">
                                            <strong>خطأ:</strong> {{ Str::limit($message['error'], 100) }}
                                        </div>
                                    @endif
                                </div>
                                <div class="col-md-2">
                                    <span class="status-badge status-{{ $message['status'] }}">
                                        @if($message['status'] == 'failed')
                                            فشل
                                        @elseif($message['status'] == 'waiting')
                                            انتظار
                                        @else
                                            {{ $message['status'] }}
                                        @endif
                                    </span>
                                    <br>
                                    <small class="text-muted">{{ $message['service'] }}</small>
                                </div>
                                <div class="col-md-2">
                                    <small class="text-muted">المحاولات: {{ $message['attempts'] }}</small>
                                </div>
                                <div class="col-md-2">
                                    <div class="btn-group" role="group">
                                        <button class="btn btn-success btn-sm" onclick="resendMessage('{{ $message['id'] }}')">
                                            <i class="fas fa-redo"></i> إعادة إرسال
                                        </button>
                                        <button class="btn btn-info btn-sm" onclick="showFullMessage('{{ $message['id'] }}', '{{ addslashes($message['message']) }}')">
                                            <i class="fas fa-eye"></i> عرض
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="no-messages">
                            <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                            <h4>لا توجد رسائل معلقة أو فاشلة</h4>
                            <p>جميع الرسائل تم إرسالها بنجاح!</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Message Modal -->
    <div class="modal fade" id="messageModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">تفاصيل الرسالة</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="message-content"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                    <button type="button" class="btn btn-success" id="resend-from-modal">إعادة إرسال</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    
    <script>
        let currentMessageId = null;
        
        // Configure Toastr
        toastr.options = {
            "closeButton": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "timeOut": "5000"
        };
        
        // Auto-refresh every 30 seconds
        setInterval(refreshData, 30000);
        
        function refreshData() {
            fetch('/whatsapp-dashboard-stats')
                .then(response => response.json())
                .then(data => {
                    if (data.statistics) {
                        updateStatistics(data.statistics);
                    }
                })
                .catch(error => console.error('Error refreshing stats:', error));
            
            fetch('/whatsapp-pending-messages')
                .then(response => response.json())
                .then(data => {
                    if (data.messages) {
                        updateMessagesTable(data.messages);
                    }
                })
                .catch(error => console.error('Error refreshing messages:', error));
        }
        
        function updateStatistics(stats) {
            document.getElementById('total-messages').textContent = stats.total || 0;
            document.getElementById('sent-messages').textContent = stats.sent || 0;
            document.getElementById('failed-messages').textContent = stats.failed || 0;
            document.getElementById('pending-messages').textContent = stats.pending_messages || 0;
        }
        
        function updateMessagesTable(messages) {
            const container = document.getElementById('messages-container');
            
            if (messages.length === 0) {
                container.innerHTML = `
                    <div class="no-messages">
                        <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                        <h4>لا توجد رسائل معلقة أو فاشلة</h4>
                        <p>جميع الرسائل تم إرسالها بنجاح!</p>
                    </div>
                `;
                return;
            }
            
            container.innerHTML = messages.map(message => `
                <div class="message-item ${message.status}" data-message-id="${message.id}">
                    <div class="row align-items-center">
                        <div class="col-md-2">
                            <strong>${message.phone}</strong>
                            <br>
                            <small class="text-muted">${getTimeAgo(message.timestamp)}</small>
                        </div>
                        <div class="col-md-4">
                            <div class="message-preview">
                                ${message.message.substring(0, 150)}${message.message.length > 150 ? '...' : ''}
                            </div>
                            ${message.error ? `<div class="error-text"><strong>خطأ:</strong> ${message.error.substring(0, 100)}${message.error.length > 100 ? '...' : ''}</div>` : ''}
                        </div>
                        <div class="col-md-2">
                            <span class="status-badge status-${message.status}">
                                ${getStatusText(message.status)}
                            </span>
                            <br>
                            <small class="text-muted">${message.service}</small>
                        </div>
                        <div class="col-md-2">
                            <small class="text-muted">المحاولات: ${message.attempts}</small>
                        </div>
                        <div class="col-md-2">
                            <div class="btn-group" role="group">
                                <button class="btn btn-success btn-sm" onclick="resendMessage('${message.id}')">
                                    <i class="fas fa-redo"></i> إعادة إرسال
                                </button>
                                <button class="btn btn-info btn-sm" onclick="showFullMessage('${message.id}', '${message.message.replace(/'/g, "\\'")}')">
                                    <i class="fas fa-eye"></i> عرض
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `).join('');
        }
        
        function getStatusText(status) {
            switch(status) {
                case 'failed': return 'فشل';
                case 'waiting': return 'انتظار';
                case 'pending': return 'معلق';
                default: return status;
            }
        }
        
        function getTimeAgo(timestamp) {
            const now = new Date();
            const time = new Date(timestamp);
            const diff = now - time;
            const minutes = Math.floor(diff / 60000);
            const hours = Math.floor(minutes / 60);
            const days = Math.floor(hours / 24);
            
            if (days > 0) return `${days} يوم`;
            if (hours > 0) return `${hours} ساعة`;
            if (minutes > 0) return `${minutes} دقيقة`;
            return 'الآن';
        }
        
        function resendMessage(messageId) {
            if (!confirm('هل أنت متأكد من إعادة إرسال هذه الرسالة؟')) {
                return;
            }
            
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
                    toastr.success('تم إعادة إرسال الرسالة بنجاح!');
                    refreshData();
                } else {
                    toastr.error('فشل في إعادة إرسال الرسالة: ' + data.message);
                }
            })
            .catch(error => {
                toastr.error('خطأ في إعادة إرسال الرسالة: ' + error.message);
            });
        }
        
        function resendAllPending() {
            if (!confirm('هل أنت متأكد من إعادة إرسال جميع الرسائل المعلقة؟')) {
                return;
            }
            
            fetch('/whatsapp-pending-messages')
                .then(response => response.json())
                .then(data => {
                    if (data.messages && data.messages.length > 0) {
                        let completed = 0;
                        const total = data.messages.length;
                        
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
                                completed++;
                                if (completed === total) {
                                    toastr.success(`تم إعادة إرسال ${total} رسالة!`);
                                    refreshData();
                                }
                            })
                            .catch(error => {
                                completed++;
                                console.error('Error resending message:', error);
                            });
                        });
                    } else {
                        toastr.info('لا توجد رسائل معلقة لإعادة الإرسال');
                    }
                })
                .catch(error => {
                    toastr.error('خطأ في جلب الرسائل المعلقة: ' + error.message);
                });
        }
        
        function showFullMessage(messageId, messageText) {
            document.getElementById('message-content').innerHTML = `
                <div class="alert alert-info">
                    <strong>الرسالة:</strong><br>
                    <pre style="white-space: pre-wrap; font-family: inherit;">${messageText}</pre>
                </div>
            `;
            currentMessageId = messageId;
            new bootstrap.Modal(document.getElementById('messageModal')).show();
        }
        
        function sendTestMessage() {
            if (confirm('هل تريد إرسال رسالة تجريبية إلى +201100667988؟')) {
                fetch('/whatsapp-send-test')
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            toastr.success('تم إرسال الرسالة التجريبية بنجاح!');
                            refreshData();
                        } else {
                            toastr.error('فشل في إرسال الرسالة التجريبية: ' + data.message);
                        }
                    })
                    .catch(error => {
                        toastr.error('خطأ في إرسال الرسالة التجريبية: ' + error.message);
                    });
            }
        }
        
        // Resend from modal
        document.getElementById('resend-from-modal').addEventListener('click', function() {
            if (currentMessageId) {
                bootstrap.Modal.getInstance(document.getElementById('messageModal')).hide();
                resendMessage(currentMessageId);
            }
        });
    </script>
</body>
</html>

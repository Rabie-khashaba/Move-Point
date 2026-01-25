<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>سجل رسائل الواتساب - WhatsApp Message Logs</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #25D366 0%, #128C7E 50%, #075E54 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .header {
            background: linear-gradient(135deg, #25D366 0%, #128C7E 50%, #075E54 100%);
            color: white;
            padding: 40px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="whatsapp" patternUnits="userSpaceOnUse" width="20" height="20"><circle cx="10" cy="10" r="2" fill="rgba(255,255,255,0.1)"/></pattern></defs><rect width="100" height="100" fill="url(%23whatsapp)"/></svg>');
            opacity: 0.3;
        }
        
        .header h1 {
            position: relative;
            z-index: 1;
        }
        
        .header p {
            position: relative;
            z-index: 1;
        }
        
        .header h1 {
            font-size: 2.5rem;
            margin-bottom: 10px;
        }
        
        .header p {
            font-size: 1.1rem;
            opacity: 0.9;
        }
        
        .controls {
            padding: 20px 30px;
            background: #f8f9fa;
            border-bottom: 1px solid #e9ecef;
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            align-items: center;
        }
        
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn-primary {
            background: #25D366;
            color: white;
        }
        
        .btn-primary:hover {
            background: #128C7E;
            transform: translateY(-2px);
        }
        
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        
        .btn-secondary:hover {
            background: #545b62;
        }
        
        .btn-danger {
            background: #dc3545;
            color: white;
        }
        
        .btn-danger:hover {
            background: #c82333;
        }
        
        .stats {
            padding: 20px 30px;
            background: #f8f9fa;
            border-bottom: 1px solid #e9ecef;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }
        
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            color: #25D366;
            margin-bottom: 5px;
        }
        
        .stat-label {
            color: #666;
            font-size: 0.9rem;
        }
        
        .logs-container {
            padding: 30px;
            max-height: 600px;
            overflow-y: auto;
        }
        
        .log-item {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 15px;
            border-left: 5px solid #25D366;
            transition: all 0.3s ease;
        }
        
        .log-item:hover {
            transform: translateX(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .log-item.failed {
            border-left-color: #dc3545;
            background: #fff5f5;
        }
        
        .log-item.waiting {
            border-left-color: #ffc107;
            background: #fffbf0;
        }
        
        .log-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
        
        .log-status {
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
        
        .log-phone {
            font-weight: bold;
            color: #333;
            font-size: 1.1rem;
        }
        
        .log-timestamp {
            color: #666;
            font-size: 0.9rem;
        }
        
        .log-service {
            background: #e9ecef;
            padding: 3px 8px;
            border-radius: 5px;
            font-size: 0.8rem;
            color: #495057;
        }
        
        .log-message {
            margin-top: 10px;
            padding: 10px;
            background: white;
            border-radius: 5px;
            font-size: 0.9rem;
            line-height: 1.5;
            max-height: 100px;
            overflow-y: auto;
        }
        
        .log-error {
            margin-top: 10px;
            padding: 10px;
            background: #f8d7da;
            border-radius: 5px;
            font-size: 0.8rem;
            color: #721c24;
        }
        
        .loading {
            text-align: center;
            padding: 50px;
            color: #666;
        }
        
        .error {
            background: #f8d7da;
            color: #721c24;
            padding: 20px;
            border-radius: 10px;
            margin: 20px;
        }
        
        @media (max-width: 768px) {
            .header h1 {
                font-size: 2rem;
            }
            
            .controls {
                flex-direction: column;
                align-items: stretch;
            }
            
            .btn {
                text-align: center;
            }
            
            .log-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📱 سجل رسائل الواتساب</h1>
            <p>WhatsApp Message Logs & Statistics</p>
        </div>
        
        <div class="controls">
            <button class="btn btn-primary" onclick="loadLogs()">🔄 تحديث السجلات</button>
            <button class="btn btn-secondary" onclick="loadStats()">📊 الإحصائيات</button>
            <button class="btn btn-danger" onclick="clearLogs()">🗑️ مسح السجلات القديمة</button>
            <a href="/whatsapp-link" class="btn btn-primary" target="_blank">🔗 رابط الواتساب</a>
            <a href="/whatsapp-send-test" class="btn btn-secondary" target="_blank">📤 إرسال رسالة تجريبية</a>
        </div>
        
        <div class="stats" id="stats-container" style="display: none;">
            <h3 style="margin-bottom: 20px; color: #333;">📊 إحصائيات الرسائل</h3>
            <div class="stats-grid" id="stats-grid">
                <!-- Stats will be loaded here -->
            </div>
        </div>
        
        <div class="logs-container">
            <div id="loading" class="loading">
                <h3>🔄 جاري تحميل السجلات...</h3>
                <p>Loading message logs...</p>
            </div>
            
            <div id="error" class="error" style="display: none;">
                <h3>❌ خطأ في تحميل السجلات</h3>
                <p id="error-message"></p>
            </div>
            
            <div id="logs-container">
                <!-- Logs will be loaded here -->
            </div>
        </div>
    </div>

    <script>
        // Load logs on page load
        window.onload = function() {
            loadLogs();
            loadStats();
        };
        
        function loadLogs() {
            document.getElementById('loading').style.display = 'block';
            document.getElementById('error').style.display = 'none';
            document.getElementById('logs-container').innerHTML = '';
            
            fetch('/whatsapp-logs')
                .then(response => response.json())
                .then(data => {
                    document.getElementById('loading').style.display = 'none';
                    
                    if (data.logs && data.logs.length > 0) {
                        displayLogs(data.logs);
                    } else {
                        document.getElementById('logs-container').innerHTML = 
                            '<div class="loading"><h3>📭 لا توجد سجلات</h3><p>No message logs found</p></div>';
                    }
                })
                .catch(error => {
                    document.getElementById('loading').style.display = 'none';
                    document.getElementById('error').style.display = 'block';
                    document.getElementById('error-message').textContent = error.message;
                });
        }
        
        function loadStats() {
            fetch('/whatsapp-stats')
                .then(response => response.json())
                .then(data => {
                    if (data.statistics) {
                        displayStats(data.statistics);
                    }
                })
                .catch(error => {
                    console.error('Error loading stats:', error);
                });
        }
        
        function displayStats(stats) {
            const statsContainer = document.getElementById('stats-container');
            const statsGrid = document.getElementById('stats-grid');
            
            statsContainer.style.display = 'block';
            
            statsGrid.innerHTML = `
                <div class="stat-card">
                    <div class="stat-number">${stats.total}</div>
                    <div class="stat-label">إجمالي الرسائل</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">${stats.sent}</div>
                    <div class="stat-label">مرسلة بنجاح</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">${stats.failed}</div>
                    <div class="stat-label">فشل في الإرسال</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">${stats.waiting}</div>
                    <div class="stat-label">في الانتظار</div>
                </div>
            `;
        }
        
        function displayLogs(logs) {
            const container = document.getElementById('logs-container');
            
            logs.forEach(log => {
                const logItem = document.createElement('div');
                logItem.className = `log-item ${log.status}`;
                
                const statusClass = `status-${log.status}`;
                const statusText = {
                    'sent': 'تم الإرسال',
                    'failed': 'فشل',
                    'waiting': 'انتظار',
                    'pending': 'معلق'
                }[log.status] || log.status;
                
                logItem.innerHTML = `
                    <div class="log-header">
                        <div>
                            <div class="log-phone">${log.phone}</div>
                            <div class="log-timestamp">${log.timestamp}</div>
                        </div>
                        <div style="display: flex; gap: 10px; align-items: center;">
                            <span class="log-service">${log.service}</span>
                            <span class="log-status ${statusClass}">${statusText}</span>
                        </div>
                    </div>
                    <div class="log-message">${log.message}</div>
                    ${log.error ? `<div class="log-error"><strong>خطأ:</strong> ${log.error}</div>` : ''}
                `;
                
                container.appendChild(logItem);
            });
        }
        
        function clearLogs() {
            if (confirm('هل أنت متأكد من مسح السجلات القديمة؟')) {
                fetch('/whatsapp-clear-logs')
                    .then(response => response.json())
                    .then(data => {
                        alert(data.message);
                        loadLogs();
                        loadStats();
                    })
                    .catch(error => {
                        alert('خطأ في مسح السجلات: ' + error.message);
                    });
            }
        }
        
        // Auto-refresh every 30 seconds
        setInterval(() => {
            loadLogs();
            loadStats();
        }, 30000);
    </script>
</body>
</html>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    @include('partials.head')
    @yield('styles')
</head>
<body>
    <div class="app-wrapper">
        <!-- Sidebar -->
        @include('partials.left-sidebar')
        
        <!-- Main Content -->
        <div class="content-wrapper">
            <!-- Header -->
            @include('partials.header')
            
            <!-- Page Content -->
            <main class="page-content p-4">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="إغلاق"></button>
                    </div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="إغلاق"></button>
                    </div>
                @endif
                @yield('content')
            </main>
            
            <!-- Footer -->
            @include('partials.footer')
        </div>
    </div>
    
    <!-- Mobile Overlay -->
    <div class="mobile-overlay" id="mobileOverlay"></div>
    
    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js"></script>
    @include('partials.script')
   

    @stack('scripts')
    
    <!-- Custom JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof feather !== 'undefined') {
                feather.replace();
            }

            const sidebar = document.getElementById('sidebar');
            
            // Load notification counts
            loadNotificationCounts();
            
            // Refresh notification counts every 30 seconds
            setInterval(loadNotificationCounts, 30000);
            
            // Load notification dropdown when opened
            loadNotificationDropdown();
            
            // Setup notification dropdown event listeners
            setupNotificationDropdown();
        });
        
        // Function to load notification counts
        function loadNotificationCounts() {
            fetch('/notifications/unread-count')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        updateNotificationBadges(data.count);
                    }
                })
                .catch(error => {
                    console.error('Error loading notification counts:', error);
                });
        }
        
        // Function to update notification badges
        function updateNotificationBadges(totalCount) {
            // Update individual request type counts
            fetch('/notifications/recent')
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.notifications) {
                        const counts = {
                            'leave_request': 0,
                            'advance_request': 0,
                            'resignation_request': 0,
                            'delivery_deposit': 0
                        };
                        
                        // Count notifications by type
                        data.notifications.forEach(notification => {
                            if (counts.hasOwnProperty(notification.type)) {
                                counts[notification.type]++;
                            }
                        });
                        
                        // Update individual badges
                        updateBadge('leave-requests-count', counts.leave_request);
                        updateBadge('advance-requests-count', counts.advance_request);
                        updateBadge('resignation-requests-count', counts.resignation_request);
                        updateBadge('delivery-deposits-count', counts.delivery_deposit);
                        
                        // Update total requests badge
                        const totalRequests = counts.leave_request + counts.advance_request + counts.resignation_request + counts.delivery_deposit;
                        updateBadge('requests-notification-count', totalRequests);
                        
                        // Update all notifications badge
                        updateBadge('all-notifications-count', totalCount);
                        updateBadge('header-notification-count', totalCount);
                    }
                })
                .catch(error => {
                    console.error('Error loading recent notifications:', error);
                });
        }
        
        // Function to update individual badge
        function updateBadge(badgeId, count) {
            const badge = document.getElementById(badgeId);
            if (badge) {
                if (count > 0) {
                    badge.textContent = count;
                    badge.style.display = 'inline-flex';
                } else {
                    badge.style.display = 'none';
                }
            }
        }
        
        // Function to setup notification dropdown event listeners
        function setupNotificationDropdown() {
            const notificationDropdown = document.querySelector('.nxl-header-notification');
            if (notificationDropdown) {
                const dropdownToggle = notificationDropdown.querySelector('[data-bs-toggle="dropdown"]');
                const dropdownMenu = notificationDropdown.querySelector('.dropdown-menu');
                
                if (dropdownToggle && dropdownMenu) {
                    dropdownToggle.addEventListener('click', function() {
                        // Load notifications when dropdown is opened
                        setTimeout(() => {
                            if (dropdownMenu.classList.contains('show')) {
                                loadNotificationDropdown();
                            }
                        }, 100);
                    });
                }
            }
        }
        
        // Function to load notification dropdown content
        function loadNotificationDropdown() {
            const notificationList = document.getElementById('notification-dropdown-list');
            if (!notificationList) return;
            
            fetch('/notifications/recent?limit=10')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        displayNotificationDropdown(data.notifications);
                    } else {
                        notificationList.innerHTML = '<div class="text-center p-3 text-muted">فشل في تحميل الإشعارات</div>';
                    }
                })
                .catch(error => {
                    console.error('Error loading notification dropdown:', error);
                    notificationList.innerHTML = '<div class="text-center p-3 text-muted">خطأ في تحميل الإشعارات</div>';
                });
        }
        
        // Function to display notifications in dropdown
        function displayNotificationDropdown(notifications) {
            const notificationList = document.getElementById('notification-dropdown-list');
            if (!notificationList) return;
            
            if (notifications.length === 0) {
                notificationList.innerHTML = '<div class="text-center p-3 text-muted">لا توجد إشعارات</div>';
                return;
            }
            
            notificationList.innerHTML = notifications.map(notification => `
                <div class="notification-item ${notification.is_read ? '' : 'unread'}">
                    <div class="notification-content">
                        <div class="notification-icon ${notification.type}">
                            <i class="feather-${getNotificationIcon(notification.type)}"></i>
                        </div>
                        <div class="notification-text">
                            <div class="notification-title">${notification.title}</div>
                            <div class="notification-body">${notification.body}</div>
                            <div class="notification-time">${formatTimeAgo(notification.created_at)}</div>
                            ${!notification.is_read ? `
                                <div class="notification-actions">
                                    <button class="btn btn-sm btn-outline-primary" onclick="markAsRead(${notification.id})">
                                        <i class="feather-check"></i> مقروء
                                    </button>
                                </div>
                            ` : ''}
                        </div>
                    </div>
                </div>
            `).join('');
        }
        
        // Function to get notification icon based on type
        function getNotificationIcon(type) {
            const icons = {
                'leave_request': 'calendar',
                'advance_request': 'dollar-sign',
                'resignation_request': 'user-minus',
                'delivery_deposit': 'truck',
                'general': 'bell'
            };
            return icons[type] || 'bell';
        }
        
        // Function to format time ago
        function formatTimeAgo(dateString) {
            const date = new Date(dateString);
            const now = new Date();
            const diffInSeconds = Math.floor((now - date) / 1000);
            
            if (diffInSeconds < 60) {
                return 'الآن';
            } else if (diffInSeconds < 3600) {
                const minutes = Math.floor(diffInSeconds / 60);
                return `منذ ${minutes} دقيقة`;
            } else if (diffInSeconds < 86400) {
                const hours = Math.floor(diffInSeconds / 3600);
                return `منذ ${hours} ساعة`;
            } else {
                const days = Math.floor(diffInSeconds / 86400);
                return `منذ ${days} يوم`;
            }
        }
        
        // Function to mark notification as read (from dropdown)
        function markAsRead(notificationId) {
            fetch(`/notifications/${notificationId}/mark-read`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Reload notification dropdown
                    loadNotificationDropdown();
                    // Update sidebar counts
                    loadNotificationCounts();
                }
            })
            .catch(error => {
                console.error('Error marking notification as read:', error);
            });
        }
        
        // Function to mark all notifications as read (from dropdown)
        function markAllAsRead() {
            fetch('/notifications/mark-all-read', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Reload notification dropdown
                    loadNotificationDropdown();
                    // Update sidebar counts
                    loadNotificationCounts();
                }
            })
            .catch(error => {
                console.error('Error marking all notifications as read:', error);
            });
        }
    </script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarToggle = document.getElementById('sidebarToggle');
            const mobileOverlay = document.getElementById('mobileOverlay');
            const submenuLinks = document.querySelectorAll('.sidebar-nav .has-submenu > .nav-link');

            // Toggle sidebar on desktop
            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('collapsed');
                    document.querySelector('.content-wrapper').classList.toggle('sidebar-collapsed');
                });
            }

            // Toggle sidebar on mobile
            if (window.innerWidth < 992) { // Bootstrap's 'lg' breakpoint
                sidebar.classList.add('collapsed');
                document.querySelector('.content-wrapper').classList.add('sidebar-collapsed');
            }

            // Mobile overlay for sidebar
            if (sidebarToggle && mobileOverlay) {
                sidebarToggle.addEventListener('click', function() {
                    if (window.innerWidth < 992) {
                        sidebar.classList.toggle('collapsed');
                        mobileOverlay.classList.toggle('active');
                    }
                });

                mobileOverlay.addEventListener('click', function() {
                    sidebar.classList.add('collapsed');
                    mobileOverlay.classList.remove('active');
                });
            }

            // Submenu toggle
            submenuLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const submenu = this.nextElementSibling;
                    if (submenu && submenu.classList.contains('submenu')) {
                        // Close other open submenus at the same level
                        submenuLinks.forEach(otherLink => {
                            if (otherLink !== this) {
                                const otherSubmenu = otherLink.nextElementSibling;
                                if (otherSubmenu && otherSubmenu.classList.contains('submenu') && otherSubmenu.style.display === 'block') {
                                    otherSubmenu.style.display = 'none';
                                    otherLink.querySelector('.nav-arrow i').classList.remove('rotated');
                                }
                            }
                        });

                        // Toggle current submenu
                        if (submenu.style.display === 'block') {
                            submenu.style.display = 'none';
                            this.querySelector('.nav-arrow i').classList.remove('rotated');
                        } else {
                            submenu.style.display = 'block';
                            this.querySelector('.nav-arrow i').classList.add('rotated');
                        }
                    }
                });
            });

            // Ensure active submenu is open on load
            document.querySelectorAll('.sidebar-nav .nav-item.has-submenu.active > .submenu').forEach(submenu => {
                submenu.style.display = 'block';
                submenu.previousElementSibling.querySelector('.nav-arrow i').classList.add('rotated');
            });
        });

        window.showAlert = function(message, type = 'success') {
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
            alertDiv.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="إغلاق"></button>
            `;
            
            // Insert at the top of the content wrapper
            const contentWrapper = document.querySelector('.content-wrapper');
            if (contentWrapper) {
                contentWrapper.insertBefore(alertDiv, contentWrapper.firstChild);
            }
            
            // Auto-hide after 5 seconds
            setTimeout(() => {
                if (alertDiv.parentNode) {
                    alertDiv.remove();
                }
            }, 5000);
        };

        window.confirmDelete = function(message = 'هل أنت متأكد أنك تريد حذف هذا العنصر؟') {
            return confirm(message);
        };

        // Initialize tooltips
        document.addEventListener('DOMContentLoaded', function() {
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });

        // Auto-hide alerts
        // document.addEventListener('DOMContentLoaded', function() {
        //     setTimeout(function() {
        //         const alerts = document.querySelectorAll('.alert');
        //         alerts.forEach(function(alert) {
        //             if (alert.classList.contains('alert-persist')) {
        //                 return;
        //             }
        //             if (alert.parentNode) {
        //                 alert.remove();
        //             }
        //         });
        //     }, 5000);
        // });

        // Form validation enhancement
        document.addEventListener('DOMContentLoaded', function() {
            const forms = document.querySelectorAll('form');
            forms.forEach(function(form) {
                form.addEventListener('submit', function(e) {
                    const requiredFields = form.querySelectorAll('[required]');
                    let isValid = true;
                    
                    requiredFields.forEach(function(field) {
                        if (!field.value.trim()) {
                            isValid = false;
                            field.classList.add('is-invalid');
                        } else {
                            field.classList.remove('is-invalid');
                        }
                    });
                    
                    if (!isValid) {
                        e.preventDefault();
                        showAlert('الرجاء تعبئة جميع الحقول المطلوبة.', 'danger');
                    }
                });
            });
        });

        // Phone number formatting
        document.addEventListener('DOMContentLoaded', function() {
            const phoneInputs = document.querySelectorAll('input[name="phone"]');
            phoneInputs.forEach(function(input) {
                input.addEventListener('input', function(e) {
                    let value = e.target.value.replace(/\D/g, '');
                    if (value.length > 11) {
                        value = value.substring(0, 11);
                    }
                    e.target.value = value;
                });
            });
        });

        // Status toggle confirmation
        document.addEventListener('DOMContentLoaded', function() {
            const toggleButtons = document.querySelectorAll('[data-toggle-status]');
            toggleButtons.forEach(function(button) {
                button.addEventListener('click', function(e) {
                    const action = this.getAttribute('data-toggle-status');
                    if (!confirm(`هل أنت متأكد أنك تريد ${action} هذا العنصر؟`)) {
                        e.preventDefault();
                    }
                });
            });
        });
    </script>
    
    @yield('scripts')
</body>
</html>

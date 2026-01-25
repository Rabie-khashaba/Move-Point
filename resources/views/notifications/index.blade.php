@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">
                        <i class="feather-bell me-2"></i>
                        إشعاراتي
                    </h4>
                    <div class="d-flex gap-2">
                        @can('create_notifications')
                        <a href="{{ route('notifications.create') }}" class="btn btn-primary btn-sm">
                            <i class="feather-plus me-1"></i>
                            إنشاء إشعار
                        </a>
                        @endcan
                        <button class="btn btn-success btn-sm" onclick="markAllAsRead()">
                            <i class="feather-check me-1"></i>
                            تحديد الكل كمقروء
                        </button>
                        <a href="{{ route('notifications.index') }}" class="btn btn-primary btn-sm">
                            <i class="feather-refresh-cw me-1"></i>
                            تحديث
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Statistics Cards -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body text-center">
                                    <h3>{{ $stats['total'] ?? 0 }}</h3>
                                    <p class="mb-0">إجمالي الإشعارات</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body text-center">
                                    <h3>{{ $stats['unread'] ?? 0 }}</h3>
                                    <p class="mb-0">غير مقروء</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <h3>{{ $stats['read'] ?? 0 }}</h3>
                                    <p class="mb-0">مقروء</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body text-center">
                                    <h3>{{ $stats['today'] ?? 0 }}</h3>
                                    <p class="mb-0">اليوم</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Filters -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <form method="GET" action="{{ route('notifications.index') }}" class="d-flex gap-2">
                                <select name="type" class="form-select" style="width: auto;">
                                    <option value="">جميع الأنواع</option>
                                    <option value="leave_request" {{ $type === 'leave_request' ? 'selected' : '' }}>طلبات الإجازة</option>
                                    <option value="advance_request" {{ $type === 'advance_request' ? 'selected' : '' }}>طلبات السلفة</option>
                                    <option value="resignation_request" {{ $type === 'resignation_request' ? 'selected' : '' }}>طلبات الاستقالة</option>
                                    <option value="delivery_deposit" {{ $type === 'delivery_deposit' ? 'selected' : '' }}>إيداعات التسليم</option>
                                    <option value="general" {{ $type === 'general' ? 'selected' : '' }}>عام</option>
                                </select>
                                <select name="status" class="form-select" style="width: auto;">
                                    <option value="">جميع الحالات</option>
                                    <option value="unread" {{ $status === 'unread' ? 'selected' : '' }}>غير مقروء</option>
                                    <option value="read" {{ $status === 'read' ? 'selected' : '' }}>مقروء</option>
                                </select>
                                <button type="submit" class="btn btn-outline-primary">
                                    <i class="feather-filter me-1"></i>
                                    تصفية
                                </button>
                                <a href="{{ route('notifications.index') }}" class="btn btn-outline-secondary">
                                    <i class="feather-x me-1"></i>
                                    إعادة تعيين
                                </a>
                            </form>
                        </div>
                    </div>

                    <!-- Notifications List -->
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>النوع</th>
                                    <th>العنوان</th>
                                    <th>الرسالة</th>
                                    <th>التاريخ</th>
                                    <th>الحالة</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($notifications as $notification)
                                <tr class="{{ $notification->is_read ? '' : 'table-warning' }}">
                                    <td>
                                        <span class="badge bg-{{ $notification->type === 'leave_request' ? 'info' : ($notification->type === 'advance_request' ? 'success' : ($notification->type === 'resignation_request' ? 'danger' : ($notification->type === 'delivery_deposit' ? 'warning' : 'secondary'))) }}">
                                            @switch($notification->type)
                                                @case('leave_request')
                                                    طلب إجازة
                                                    @break
                                                @case('advance_request')
                                                    طلب سلفة
                                                    @break
                                                @case('resignation_request')
                                                    طلب استقالة
                                                    @break
                                                @case('delivery_deposit')
                                                    إيداع تسليم
                                                    @break
                                                @default
                                                    عام
                                            @endswitch
                                        </span>
                                    </td>
                                    <td>{{ $notification->title }}</td>
                                    <td>{{ $notification->body }}</td>
                                    <td>{{ $notification->created_at->format('Y-m-d H:i') }}</td>
                                    <td>
                                        <span class="badge bg-{{ $notification->is_read ? 'success' : 'warning' }}">
                                            {{ $notification->is_read ? 'مقروء' : 'غير مقروء' }}
                                        </span>
                                    </td>
                                    <td>
                                        @if(!$notification->is_read)
                                            <button class="btn btn-sm btn-outline-primary" onclick="markAsRead({{ $notification->id }})">
                                                <i class="feather-check"></i>
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted">لا توجد إشعارات</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($notifications->hasPages())
                        <div class="d-flex justify-content-center mt-4">
                            {{ $notifications->links('pagination::bootstrap-5') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
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
            // Reload the page to update the UI
            window.location.reload();
        }
    })
    .catch(error => {
        console.error('Error marking notification as read:', error);
    });
}

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
            // Reload the page to update the UI
            window.location.reload();
        }
    })
    .catch(error => {
        console.error('Error marking all notifications as read:', error);
    });
}
</script>
@endpush

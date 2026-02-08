<?php

namespace App\Services;

use App\Models\AppNotification;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class NotificationService
{
    /**
     * Create a notification for specific users
     */
    public function createNotification($userIds, $title, $body, $type = 'general', $data = null)
    {
        $notifications = [];

        foreach ((array) $userIds as $userId) {
            $notifications[] = AppNotification::create([
                'user_id' => $userId,
                'title' => $title,
                'body' => $body,
                'type' => $type,
                'data' => $data,
                'is_read' => false,
            ]);
        }

        return $notifications;
    }

    /**
     * Create notification for all admins
     */
    public function notifyAdmins($title, $body, $type = 'general', $data = null)
    {
        $adminIds = User::where('type', 'admin')->pluck('id')->toArray();
        return $this->createNotification($adminIds, $title, $body, $type, $data);
    }

    /**
     * Create notification for all supervisors
     */
    public function notifySupervisors($title, $body, $type = 'general', $data = null)
    {
        $supervisorIds = User::where('type', 'supervisor')->pluck('id')->toArray();
        return $this->createNotification($supervisorIds, $title, $body, $type, $data);
    }

    /**
     * Create notification for specific user types
     */
    public function notifyUserTypes($userTypes, $title, $body, $type = 'general', $data = null)
    {
        $userIds = User::whereIn('type', (array) $userTypes)->pluck('id')->toArray();
        return $this->createNotification($userIds, $title, $body, $type, $data);
    }

    /**
     * Get unread notifications count for current user
     */
    public function getUnreadCount($userId = null)
    {
        $userId = $userId ?? Auth::id();

        // Security: ensure user can only access their own notifications
        if ($userId !== Auth::id()) {
            throw new \Exception('غير مصرح بالوصول إلى إشعارات المستخدمين الآخرين');
        }

        return AppNotification::where('user_id', $userId)
            ->where('is_read', false)
            ->count();
    }

    /**
     * Get recent notifications for current user
     */
    public function getRecentNotifications($userId = null, $limit = 10)
    {
        $userId = $userId ?? Auth::id();

        // Security: ensure user can only access their own notifications
        if ($userId !== Auth::id()) {
            throw new \Exception('غير مصرح بالوصول إلى إشعارات المستخدمين الآخرين');
        }

        return AppNotification::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Mark notification as read
     */
    public function markAsRead($notificationId, $userId = null)
    {
        $userId = $userId ?? Auth::id();

        $notification = AppNotification::where('id', $notificationId)
            ->where('user_id', $userId)
            ->first();

        if ($notification) {
            $notification->markAsRead();
            return true;
        }

        return false;
    }

    /**
     * Mark all notifications as read for current user
     */
    public function markAllAsRead($userId = null)
    {
        $userId = $userId ?? Auth::id();

        // Security: ensure user can only access their own notifications
        if ($userId !== Auth::id()) {
            throw new \Exception('غير مصرح بالوصول إلى إشعارات المستخدمين الآخرين');
        }

        return AppNotification::where('user_id', $userId)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
    }

    /**
     * Get notification statistics
     */
    public function getNotificationStats($userId = null)
    {
        $userId = $userId ?? Auth::id();

        // Security: ensure user can only access their own notifications
        if ($userId !== Auth::id()) {
            throw new \Exception('غير مصرح بالوصول إلى إشعارات المستخدمين الآخرين');
        }

        $total = AppNotification::where('user_id', $userId)->count();
        $unread = AppNotification::where('user_id', $userId)->where('is_read', false)->count();
        $read = $total - $unread;

        return [
            'total' => $total,
            'unread' => $unread,
            'read' => $read,
        ];
    }

    /**
     * Create notification for leave request
     */
    public function notifyLeaveRequest($leaveRequest, $action = 'created')
    {
        $requesterName = $this->getRequesterName($leaveRequest);

        $title = match($action) {
            'created' => 'طلب إجازة جديد',
            'approved' => 'تمت الموافقة على طلب إجازة',
            'rejected' => 'تم رفض طلب إجازة',
            default => 'تحديث في طلب إجازة'
        };

        $body = match($action) {
            'created' => "تم تقديم طلب إجازة من {$requesterName}",
            'approved' => "تمت الموافقة على طلب إجازة من {$requesterName}",
            'rejected' => "تم رفض طلب إجازة من {$requesterName}",
            default => "تم تحديث طلب إجازة من {$requesterName}"
        };

        $data = [
            'leave_request_id' => $leaveRequest->id,
            'requester_name' => $requesterName,
            'action' => $action,
            'start_date' => $leaveRequest->start_date,
            'end_date' => $leaveRequest->end_date,
        ];

        if ($action === 'created') {
            // Only notify admins for new requests
            $this->notifyUserTypes(['admin'], $title, $body, 'leave_request', $data);
        } else {
            // Notify the requester for approval/rejection
            $requesterId = $this->getRequesterId($leaveRequest);
            if ($requesterId) {
                $this->createNotification([$requesterId], $title, $body, 'leave_request', $data);
            }
        }
    }

    /**
     * Create notification for advance request
     */
    public function notifyAdvanceRequest($advanceRequest, $action = 'created')
    {
        $requesterName = $this->getRequesterName($advanceRequest);

        $title = match($action) {
            'created' => 'طلب سلفة جديد',
            'approved' => 'تمت الموافقة على طلب سلفة',
            'rejected' => 'تم رفض طلب سلفة',
            default => 'تحديث في طلب سلفة'
        };

        $body = match($action) {
            'created' => "تم تقديم طلب سلفة من {$requesterName} بمبلغ {$advanceRequest->amount}",
            'approved' => "تمت الموافقة على طلب سلفة من {$requesterName} بمبلغ {$advanceRequest->amount} ",
            'rejected' => "تم رفض طلب سلفة من {$requesterName} بمبلغ {$advanceRequest->amount} ",
            default => "تم تحديث طلب سلفة من {$requesterName}"
        };

        $data = [
            'advance_request_id' => $advanceRequest->id,
            'requester_name' => $requesterName,
            'action' => $action,
            'amount' => $advanceRequest->amount,
        ];

        if ($action === 'created') {
            // Only notify admins for new requests
            $this->notifyUserTypes(['admin'], $title, $body, 'advance_request', $data);
        } else {
            // Notify the requester for approval/rejection
            $requesterId = $this->getRequesterId($advanceRequest);
            if ($requesterId) {
                $this->createNotification([$requesterId], $title, $body, 'advance_request', $data);
            }
        }
    }

    /**
     * Create notification for resignation request
     */
    public function notifyResignationRequest($resignationRequest, $action = 'created')
    {
        $requesterName = $this->getRequesterName($resignationRequest);

        $title = match($action) {
            'created' => 'طلب استقالة جديد',
            'approved' => 'تمت الموافقة على طلب استقالة',
            'rejected' => 'تم رفض طلب استقالة',
            default => 'تحديث في طلب استقالة'
        };

        $body = match($action) {
            'created' => "تم تقديم طلب استقالة من {$requesterName}",
            'approved' => "تمت الموافقة على طلب استقالة من {$requesterName}",
            'rejected' => "تم رفض طلب استقالة من {$requesterName}",
            default => "تم تحديث طلب استقالة من {$requesterName}"
        };

        $data = [
            'resignation_request_id' => $resignationRequest->id,
            'requester_name' => $requesterName,
            'action' => $action,
            'resignation_date' => $resignationRequest->resignation_date,
        ];

        if ($action === 'created') {
            // Only notify admins for new requests
            $this->notifyUserTypes(['admin'], $title, $body, 'resignation_request', $data);
        } else {
            // Notify the requester for approval/rejection
            $requesterId = $this->getRequesterId($resignationRequest);
            if ($requesterId) {
                $this->createNotification([$requesterId], $title, $body, 'resignation_request', $data);
            }
        }
    }

    /**
     * Create notification for delivery deposit
     */
    public function notifyDeliveryDeposit($deliveryDeposit, $action = 'created')
    {
        $requesterName = $this->getRequesterName($deliveryDeposit);

        $title = match($action) {
            'created' => 'إيداع تسليم جديد',
            'approved' => 'تمت الموافقة على إيداع تسليم',
            'rejected' => 'تم رفض إيداع تسليم',
            default => 'تحديث في إيداع تسليم'
        };

        $body = match($action) {
            'created' => "تم تقديم إيداع تسليم من {$requesterName}  {$deliveryDeposit->amount} ",
            'approved' => "تمت الموافقة على إيداع تسليم من {$requesterName}  {$deliveryDeposit->amount} ",
            'rejected' => "تم رفض إيداع تسليم من {$requesterName}  {$deliveryDeposit->amount}",
            default => "تم تحديث إيداع تسليم من {$requesterName}"
        };

        $data = [
            'delivery_deposit_id' => $deliveryDeposit->id,
            'requester_name' => $requesterName,
            'action' => $action,
            'amount' => $deliveryDeposit->amount,
            'orders_count' => $deliveryDeposit->orders_count ?? null,
        ];

        if ($action === 'created') {
            // Only notify admins for new requests
            $this->notifyUserTypes(['admin'], $title, $body, 'delivery_deposit', $data);
        } else {
            // Notify the requester for approval/rejection
            $requesterId = $this->getRequesterId($deliveryDeposit);
            if ($requesterId) {
                $this->createNotification([$requesterId], $title, $body, 'delivery_deposit', $data);
            }
        }
    }

    /**
     * Get requester name from request object
     */
    private function getRequesterName($request)
    {
        // Handle LeaveRequest, AdvanceRequest, ResignationRequest
        if ($request->employee) {
            return $request->employee->name ?? 'موظف غير معروف';
        } elseif ($request->representative) {
            return $request->representative->name ?? 'مندوب غير معروف';
        } elseif ($request->supervisor) {
            return $request->supervisor->name ?? 'مشرف غير معروف';
        }

        // Handle DeliveryDeposit
        if (method_exists($request, 'employee') && $request->employee) {
            return $request->employee->name ?? 'موظف غير معروف';
        } elseif (method_exists($request, 'representative') && $request->representative) {
            return $request->representative->name ?? 'مندوب غير معروف';
        } elseif (method_exists($request, 'supervisor') && $request->supervisor) {
            return $request->supervisor->name ?? 'مشرف غير معروف';
        }

        return 'مستخدم غير معروف';
    }

    /**
     * Get requester ID from request object
     */
    private function getRequesterId($request)
    {
        // Handle LeaveRequest, AdvanceRequest, ResignationRequest
        if ($request->employee) {
            return $request->employee->user_id ?? null;
        } elseif ($request->representative) {
            return $request->representative->user_id ?? null;
        } elseif ($request->supervisor) {
            return $request->supervisor->user_id ?? null;
        }

        // Handle DeliveryDeposit
        if (method_exists($request, 'employee') && $request->employee) {
            return $request->employee->user_id ?? null;
        } elseif (method_exists($request, 'representative') && $request->representative) {
            return $request->representative->user_id ?? null;
        } elseif (method_exists($request, 'supervisor') && $request->supervisor) {
            return $request->supervisor->user_id ?? null;
        }

        return null;
    }
}
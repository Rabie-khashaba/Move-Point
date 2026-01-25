<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AppNotification;
use App\Services\FirebaseNotificationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class NotificationController extends Controller
{
    protected $firebaseService;

    public function __construct(FirebaseNotificationService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

    /**
     * Get user notifications
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        
        $notifications = $user->notifications()
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'data' => $notifications->items(),
            'pagination' => [
                'current_page' => $notifications->currentPage(),
                'last_page' => $notifications->lastPage(),
                'per_page' => $notifications->perPage(),
                'total' => $notifications->total(),
            ]
        ]);
    }

    /**
     * Get unread notifications count
     */
    public function unreadCount(Request $request): JsonResponse
    {
        $user = $request->user();
        
        $count = $user->notifications()->unread()->count();

        return response()->json([
            'unread_count' => $count
        ]);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(Request $request, $id): JsonResponse
    {
        $user = $request->user();
        
        $notification = $user->notifications()->findOrFail($id);
        $notification->markAsRead();

        return response()->json([
            'message' => 'تم تحديد الإشعار كمقروء'
        ]);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead(Request $request): JsonResponse
    {
        $user = $request->user();
        
        $user->notifications()->unread()->update([
            'is_read' => true,
            'read_at' => now(),
        ]);

        return response()->json([
            'message' => 'تم تحديد جميع الإشعارات كمقروءة'
        ]);
    }

    /**
     * Register device token
     */
    public function registerToken(Request $request): JsonResponse
    {
        $request->validate([
            'device_token' => 'required|string',
        ]);

        $user = $request->user();
        $this->firebaseService->addDeviceToken($user, $request->device_token);

        return response()->json([
            'message' => 'تم تسجيل رمز الجهاز بنجاح'
        ]);
    }

    /**
     * Unregister device token
     */
    public function unregisterToken(Request $request): JsonResponse
    {
        $request->validate([
            'device_token' => 'required|string',
        ]);

        $user = $request->user();
        $this->firebaseService->removeDeviceToken($user, $request->device_token);

        return response()->json([
            'message' => 'تم إلغاء تسجيل رمز الجهاز'
        ]);
    }

    /**
     * Toggle notifications
     */
    public function toggleNotifications(Request $request): JsonResponse
    {
        $request->validate([
            'enabled' => 'required|boolean',
        ]);

        $user = $request->user();
        $user->update(['notifications_enabled' => $request->enabled]);

        return response()->json([
            'message' => $request->enabled ? 'تم تفعيل الإشعارات' : 'تم إلغاء تفعيل الإشعارات',
            'notifications_enabled' => $user->notifications_enabled
        ]);
    }

    /**
     * Get notification settings
     */
    public function settings(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'notifications_enabled' => $user->notifications_enabled,
            'device_tokens_count' => count($user->device_tokens ?? [])
        ]);
    }

    /**
     * Delete notification
     */
    public function destroy(Request $request, $id): JsonResponse
    {
        $user = $request->user();
        
        $notification = $user->notifications()->findOrFail($id);
        $notification->delete();

        return response()->json([
            'message' => 'تم حذف الإشعار'
        ]);
    }
}

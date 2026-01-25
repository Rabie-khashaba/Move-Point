<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
        
        // Ensure all notification methods are user-specific
        $this->middleware(function ($request, $next) {
            // Additional security: ensure user can only access their own notifications
            if ($request->route('id')) {
                $notificationId = $request->route('id');
                $notification = AppNotification::find($notificationId);
                
                if ($notification && $notification->user_id !== Auth::id()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'غير مصرح بالوصول إلى هذا الإشعار'
                    ], 403);
                }
            }
            
            return $next($request);
        });
    }

    /**
     * Get unread notifications count
     */
    public function getUnreadCount()
    {
        $count = $this->notificationService->getUnreadCount();
        
        return response()->json([
            'success' => true,
            'count' => $count
        ]);
    }

    /**
     * Get recent notifications
     */
    public function getRecentNotifications(Request $request)
    {
        $limit = $request->get('limit', 10);
        $notifications = $this->notificationService->getRecentNotifications(null, $limit);
        
        return response()->json([
            'success' => true,
            'notifications' => $notifications
        ]);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(Request $request, $id)
    {
        $success = $this->notificationService->markAsRead($id);
        
        return response()->json([
            'success' => $success,
            'message' => $success ? 'تم تحديد الإشعار كمقروء' : 'فشل في تحديث الإشعار'
        ]);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        $updated = $this->notificationService->markAllAsRead();
        
        return response()->json([
            'success' => true,
            'message' => "تم تحديد {$updated} إشعار كمقروء",
            'updated_count' => $updated
        ]);
    }

    /**
     * Get notification statistics
     */
    public function getStats()
    {
        $stats = $this->notificationService->getNotificationStats();
        
        // Add today's count
        $stats['today'] = AppNotification::where('user_id', Auth::id())
            ->whereDate('created_at', today())
            ->count();
        
        return response()->json([
            'success' => true,
            'statistics' => $stats
        ]);
    }

    /**
     * Show notifications page
     */
    public function index(Request $request)
    {
        $userId = Auth::id(); // Always use authenticated user's ID
        
        // Additional security check: ensure user can only access their own notifications
        if ($request->has('user_id') && $request->user_id != $userId) {
            abort(403, 'غير مصرح بالوصول إلى إشعارات المستخدمين الآخرين');
        }
        
        $perPage = $request->get('per_page', 20);
        $type = $request->get('type');
        $status = $request->get('status'); // 'read', 'unread', or null for all
        
        $query = AppNotification::where('user_id', $userId);
        
        // Filter by type
        if ($type) {
            $query->where('type', $type);
        }
        
        // Filter by read status
        if ($status === 'read') {
            $query->where('is_read', true);
        } elseif ($status === 'unread') {
            $query->where('is_read', false);
        }
        
        $notifications = $query->orderBy('created_at', 'desc')
            ->paginate($perPage);
            
        $stats = $this->notificationService->getNotificationStats($userId);
        
        // Add today's count
        $stats['today'] = AppNotification::where('user_id', $userId)
            ->whereDate('created_at', today())
            ->count();
        
        return view('notifications.index', compact('notifications', 'stats', 'type', 'status'));
    }

    /**
     * Show admin notification creation form
     */
    public function create()
    {
        // Check if user is admin
        if (Auth::user()->type !== 'admin') {
            abort(403, 'غير مصرح بالوصول إلى هذه الصفحة');
        }
        
        $users = \App\Models\User::get();
        $userTypes = ['admin', 'supervisor', 'representative', 'employee'];
        
        return view('notifications.create', compact('users', 'userTypes'));
    }

    /**
     * Store admin-created notification
     */
    public function store(Request $request)
    {
        // Check if user is admin
        if (Auth::user()->type !== 'admin') {
            abort(403, 'غير مصرح بالوصول إلى هذه الصفحة');
        }
        
        // Debug: Log all request data
        \Log::info('Notification request data', [
            'all_data' => $request->all(),
            'method' => $request->method(),
            'url' => $request->url()
        ]);
        
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'body' => 'required|string|max:1000',
                'type' => 'required|in:general,leave_request,advance_request,resignation_request,delivery_deposit',
                'priority' => 'nullable|in:normal,high,urgent',
                'target_type' => 'required|in:specific_users,user_types,all_users',
                'user_ids' => 'required_if:target_type,specific_users|array',
                'user_ids.*' => 'exists:users,id',
                'user_types' => 'required_if:target_type,user_types|array',
                'user_types.*' => 'in:admin,supervisor,representative,employee',
                'schedule_notification' => 'nullable|boolean',
                'scheduled_date' => 'nullable|date|after_or_equal:today',
                'scheduled_time' => 'nullable|date_format:H:i',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation failed', [
                'errors' => $e->errors(),
                'request_data' => $request->all()
            ]);
            throw $e;
        }

        try {
            // Debug: Log the validated data
            \Log::info('Notification creation attempt', [
                'validated_data' => $validated,
                'user_id' => Auth::id(),
                'user_type' => Auth::user()->type
            ]);
            
            // Prepare notification data
            $notificationData = [
                'priority' => $validated['priority'] ?? 'normal',
                'created_by' => Auth::id(),
            ];

            // Handle scheduling
            if (($validated['schedule_notification'] ?? false) && $validated['scheduled_date'] && $validated['scheduled_time']) {
                $scheduledDateTime = $validated['scheduled_date'] . ' ' . $validated['scheduled_time'];
                $notificationData['scheduled_at'] = $scheduledDateTime;
                $notificationData['status'] = 'scheduled';
            } else {
                $notificationData['status'] = 'sent';
            }

            if ($validated['target_type'] === 'specific_users') {
                \Log::info('Creating notification for specific users', ['user_ids' => $validated['user_ids']]);
                $result = $this->notificationService->createNotification(
                    $validated['user_ids'],
                    $validated['title'],
                    $validated['body'],
                    $validated['type'],
                    $notificationData
                );
                \Log::info('Notification creation result', ['result' => $result]);
                $message = "تم إرسال الإشعار إلى " . count($validated['user_ids']) . " مستخدم";
            } elseif ($validated['target_type'] === 'user_types') {
                \Log::info('Creating notification for user types', ['user_types' => $validated['user_types']]);
                $result = $this->notificationService->notifyUserTypes(
                    $validated['user_types'],
                    $validated['title'],
                    $validated['body'],
                    $validated['type'],
                    $notificationData
                );
                \Log::info('Notification creation result', ['result' => $result]);
                $message = "تم إرسال الإشعار إلى أنواع المستخدمين المحددة";
            } else {
                \Log::info('Creating notification for all admins');
                $result = $this->notificationService->notifyAdmins(
                    $validated['title'],
                    $validated['body'],
                    $validated['type'],
                    $notificationData
                );
                \Log::info('Notification creation result', ['result' => $result]);
                $message = "تم إرسال الإشعار إلى جميع المستخدمين";
            }

            // Add scheduling info to message
            if (($validated['schedule_notification'] ?? false) && $validated['scheduled_date'] && $validated['scheduled_time']) {
                $message .= " (مجدول للإرسال في " . $validated['scheduled_date'] . " " . $validated['scheduled_time'] . ")";
            }

            return redirect()->route('notifications.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            \Log::error('Failed to create admin notification: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);
            return back()->withErrors(['error' => 'فشل في إنشاء الإشعار: ' . $e->getMessage()])
                ->withInput();
        }
    }
}

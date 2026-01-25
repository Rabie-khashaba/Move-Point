<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\AppNotification;
use App\Services\FirebaseNotificationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;

class NotificationController extends Controller
{
    protected $firebaseService;

    public function __construct(FirebaseNotificationService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

    /**
     * Show notification management page
     */
    public function index(): View
    {
        // Get per_page parameter with default of 20
        $perPage = request('per_page', 20);
        
        // Validate per_page value
        $allowedPerPage = [10, 20, 50, 100];
        if (!in_array($perPage, $allowedPerPage)) {
            $perPage = 20;
        }
        
        // Get notifications with pagination
        $notifications = AppNotification::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        $userTypes = ['employee', 'representative', 'supervisor'];
        
        // Get all users (notifications_enabled is optional)
        $users = User::whereIn('type', $userTypes)
            ->where(function($query) {
                $query->where('notifications_enabled', true)
                      ->orWhereNull('notifications_enabled');
            })
            ->select('id', 'name', 'type', 'phone')
            ->get();

        return view('admin.notifications.index', compact('notifications', 'userTypes', 'users'));
    }

    /**
     * Send notification to all users
     */
    public function sendToAll(Request $request): JsonResponse
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string|max:1000',
            'type' => 'nullable|string|in:general,leave_request,advance_request,delivery_deposit,announcement',
            'data' => 'nullable|array',
        ]);

        try {
            $userTypes = ['employee', 'representative', 'supervisor'];
            $successCount = 0;
            $totalUsers = 0;

            foreach ($userTypes as $userType) {
                // Get all users of this type (device tokens are optional for now)
                $users = User::where('type', $userType)
                    ->where(function($query) {
                        $query->where('notifications_enabled', true)
                              ->orWhereNull('notifications_enabled');
                    })
                    ->get();

                $totalUsers += $users->count();

                foreach ($users as $user) {
                    $success = false;
                    
                    Log::info('Sending notification to user', [
                        'user_id' => $user->id,
                        'user_name' => $user->name,
                        'user_type' => $user->type,
                        'has_device_tokens' => !empty($user->device_tokens),
                        'device_tokens_count' => is_array($user->device_tokens) ? count($user->device_tokens) : 0
                    ]);
                    
                    // Send Firebase notification if user has device tokens
                    if ($user->device_tokens && is_array($user->device_tokens) && count($user->device_tokens) > 0) {
                        foreach ($user->device_tokens as $token) {
                            if (!empty($token)) {
                                Log::info('Sending Firebase notification', [
                                    'user_id' => $user->id,
                                    'token' => substr($token, 0, 20) . '...',
                                    'title' => $request->title,
                                    'body' => $request->body
                                ]);
                                
                                // Ensure data is properly formatted for Firebase
                                $firebaseData = $this->formatDataForFirebase($request->data ?? []);
                                
                                Log::info('About to send Firebase notification', [
                                    'user_id' => $user->id,
                                    'token' => substr($token, 0, 20) . '...',
                                    'title' => $request->title,
                                    'body' => $request->body,
                                    'original_request_data' => $request->data,
                                    'formatted_firebase_data' => $firebaseData,
                                    'data_types' => [
                                        'original_type' => gettype($request->data),
                                        'formatted_type' => gettype($firebaseData),
                                        'formatted_count' => count($firebaseData)
                                    ]
                                ]);
                                
                                $firebaseSuccess = $this->firebaseService->sendToDevice(
                                    $token,
                                    $request->title,
                                    $request->body,
                                    $firebaseData
                                );
                                
                                Log::info('Firebase notification result', [
                                    'user_id' => $user->id,
                                    'success' => $firebaseSuccess
                                ]);
                                
                                if ($firebaseSuccess) {
                                    $success = true;
                                }
                            }
                        }
                    } else {
                        Log::info('User has no device tokens', [
                            'user_id' => $user->id,
                            'device_tokens' => $user->device_tokens
                        ]);
                    }
                    
                    // Always save to database for tracking
                    try {
                        AppNotification::create([
                            'user_id' => $user->id,
                            'title' => $request->title,
                            'body' => $request->body,
                            'type' => $request->type ?? 'general',
                            'data' => $request->data ?? [],
                            'is_read' => false
                        ]);
                        $success = true; // Count as success if saved to database
                    } catch (\Exception $e) {
                        // Table doesn't exist, skip saving
                    }
                    
                    if ($success) {
                        $successCount++;
                    }
                }
            }

            return response()->json([
                'success' => true,
                'message' => "تم إرسال الإشعار بنجاح إلى {$successCount} من أصل {$totalUsers} مستخدم",
                'sent_count' => $successCount,
                'total_count' => $totalUsers
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء إرسال الإشعار: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send notification to specific user type
     */
    public function sendToUserType(Request $request): JsonResponse
    {
        $request->validate([
            'user_type' => 'required|string|in:employee,representative,supervisor',
            'title' => 'required|string|max:255',
            'body' => 'required|string|max:1000',
            'type' => 'nullable|string|in:general,leave_request,advance_request,delivery_deposit,announcement',
            'data' => 'nullable|array',
        ]);

        try {
            $successCount = 0;
            $totalUsers = 0;
            
            // Get users of the specified type
            $users = User::where('type', $request->user_type)
                ->where(function($query) {
                    $query->where('notifications_enabled', true)
                          ->orWhereNull('notifications_enabled');
                })
                ->get();
            
            $totalUsers = $users->count();
            
            // Send notifications to each user
            foreach ($users as $user) {
                $success = false;
                
                // Send Firebase notification if user has device tokens
                if ($user->device_tokens && is_array($user->device_tokens) && count($user->device_tokens) > 0) {
                    foreach ($user->device_tokens as $token) {
                        if (!empty($token)) {
                            // Ensure data is properly formatted for Firebase
                            $firebaseData = $this->formatDataForFirebase($request->data ?? []);
                            
                            Log::info('About to send Firebase notification (UserType)', [
                                'user_id' => $user->id,
                                'user_type' => $request->user_type,
                                'token' => substr($token, 0, 20) . '...',
                                'title' => $request->title,
                                'body' => $request->body,
                                'original_request_data' => $request->data,
                                'formatted_firebase_data' => $firebaseData,
                                'data_types' => [
                                    'original_type' => gettype($request->data),
                                    'formatted_type' => gettype($firebaseData),
                                    'formatted_count' => count($firebaseData)
                                ]
                            ]);
                            
                            $firebaseSuccess = $this->firebaseService->sendToDevice(
                                $token,
                                $request->title,
                                $request->body,
                                $firebaseData
                            );
                            if ($firebaseSuccess) {
                                $success = true;
                            }
                        }
                    }
                }
                
                // Always save to database for tracking
                try {
                    AppNotification::create([
                        'user_id' => $user->id,
                        'title' => $request->title,
                        'body' => $request->body,
                        'type' => $request->type ?? 'general',
                        'data' => $request->data ?? [],
                        'is_read' => false
                    ]);
                    $success = true; // Count as success if saved to database
                } catch (\Exception $e) {
                    // Table doesn't exist, skip saving
                }
                
                if ($success) {
                    $successCount++;
                }
            }

            $userTypeLabel = match($request->user_type) {
                'employee' => 'الموظفين',
                'representative' => 'المندوبين',
                'supervisor' => 'المشرفين',
                default => $request->user_type
            };

            return response()->json([
                'success' => true,
                'message' => "تم إرسال الإشعار بنجاح إلى {$successCount} من أصل {$totalUsers} {$userTypeLabel}",
                'sent_count' => $successCount,
                'total_count' => $totalUsers
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء إرسال الإشعار: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send notification to specific users
     */
    public function sendToUsers(Request $request): JsonResponse
    {
        $request->validate([
            'user_ids' => 'required|array|min:1',
            'user_ids.*' => 'integer|exists:users,id',
            'title' => 'required|string|max:255',
            'body' => 'required|string|max:1000',
            'type' => 'nullable|string|in:general,leave_request,advance_request,delivery_deposit,announcement',
            'data' => 'nullable|array',
        ]);

        try {
            $successCount = 0;
            $totalUsers = count($request->user_ids);
            
            // Send notifications to each selected user
            foreach ($request->user_ids as $userId) {
                $user = User::find($userId);
                if (!$user) continue;
                
                $success = false;
                
                // Send Firebase notification if user has device tokens
                if ($user->device_tokens && is_array($user->device_tokens) && count($user->device_tokens) > 0) {
                    foreach ($user->device_tokens as $token) {
                        if (!empty($token)) {
                            // Ensure data is properly formatted for Firebase
                            $firebaseData = $this->formatDataForFirebase($request->data ?? []);
                            
                            Log::info('About to send Firebase notification (UserType)', [
                                'user_id' => $user->id,
                                'user_type' => $request->user_type,
                                'token' => substr($token, 0, 20) . '...',
                                'title' => $request->title,
                                'body' => $request->body,
                                'original_request_data' => $request->data,
                                'formatted_firebase_data' => $firebaseData,
                                'data_types' => [
                                    'original_type' => gettype($request->data),
                                    'formatted_type' => gettype($firebaseData),
                                    'formatted_count' => count($firebaseData)
                                ]
                            ]);
                            
                            $firebaseSuccess = $this->firebaseService->sendToDevice(
                                $token,
                                $request->title,
                                $request->body,
                                $firebaseData
                            );
                            if ($firebaseSuccess) {
                                $success = true;
                            }
                        }
                    }
                }
                
                // Always save to database for tracking
                try {
                    AppNotification::create([
                        'user_id' => $userId,
                        'title' => $request->title,
                        'body' => $request->body,
                        'type' => $request->type ?? 'general',
                        'data' => $request->data ?? [],
                        'is_read' => false
                    ]);
                    $success = true; // Count as success if saved to database
                } catch (\Exception $e) {
                    // Table doesn't exist, skip saving
                }
                
                if ($success) {
                    $successCount++;
                }
            }

            return response()->json([
                'success' => true,
                'message' => "تم إرسال الإشعار بنجاح إلى {$successCount} من أصل {$totalUsers} مستخدم",
                'sent_count' => $successCount,
                'total_count' => $totalUsers
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء إرسال الإشعار: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get users for dropdown
     */
    public function getUsers(Request $request): JsonResponse
    {
        $userType = $request->get('type');
        
        $query = User::where(function($q) {
                $q->where('notifications_enabled', true)
                  ->orWhereNull('notifications_enabled');
            })
            ->select('id', 'name', 'type', 'phone');

        if ($userType) {
            $query->where('type', $userType);
        }

        $users = $query->get();

        return response()->json([
            'users' => $users
        ]);
    }

    /**
     * Get notification statistics
     */
    public function getStats(): JsonResponse
    {
        try {
            $stats = [
                'total_notifications' => AppNotification::count(),
                'unread_notifications' => AppNotification::where('is_read', false)->count(),
                'notifications_today' => AppNotification::whereDate('created_at', today())->count(),
                'users_with_tokens' => User::whereNotNull('device_tokens')
                    ->where('notifications_enabled', true)
                    ->count(),
                'notifications_by_type' => AppNotification::selectRaw('type, COUNT(*) as count')
                    ->groupBy('type')
                    ->get()
                    ->pluck('count', 'type'),
            ];
        } catch (\Exception $e) {
            // Tables/columns don't exist, return default stats
            $stats = [
                'total_notifications' => 0,
                'unread_notifications' => 0,
                'notifications_today' => 0,
                'users_with_tokens' => User::count(),
                'notifications_by_type' => [],
            ];
        }

        return response()->json($stats);
    }

    /**
     * Delete notification
     */
    public function destroy($id): JsonResponse
    {
        try {
            $notification = AppNotification::findOrFail($id);
            $notification->delete();

            return response()->json([
                'success' => true,
                'message' => 'تم حذف الإشعار بنجاح'
            ]);
        } catch (\Exception $e) {
            // Table doesn't exist or notification not found
            return response()->json([
                'success' => false,
                'message' => 'لا يمكن حذف الإشعار - الجدول غير موجود'
            ], 500);
        }
    }

    /**
     * Bulk delete notifications
     */
    public function bulkDelete(Request $request): JsonResponse
    {
        $request->validate([
            'notification_ids' => 'required|array|min:1',
            'notification_ids.*' => 'integer|exists:app_notifications,id',
        ]);

        try {
            $deletedCount = AppNotification::whereIn('id', $request->notification_ids)->delete();

            return response()->json([
                'success' => true,
                'message' => "تم حذف {$deletedCount} إشعار بنجاح"
            ]);
        } catch (\Exception $e) {
            // Table doesn't exist
            return response()->json([
                'success' => false,
                'message' => 'لا يمكن حذف الإشعارات - الجدول غير موجود'
            ], 500);
        }
    }

    /**
     * Format data for Firebase V1 API - ensure all values are strings
     */
    private function formatDataForFirebase($data): array
    {
        if (!is_array($data)) {
            $data = [];
        }
    
        $formattedData = [];
        foreach ($data as $key => $value) {
            $stringKey = (string) $key;
    
            // Flatten arrays/lists instead of passing them directly
            if (is_array($value)) {
                foreach ($value as $subKey => $subValue) {
                    $formattedData[$stringKey . '_' . $subKey] = (string) $subValue;
                }
            } elseif (is_object($value)) {
                $formattedData[$stringKey] = json_encode($value);
            } elseif (is_bool($value)) {
                $formattedData[$stringKey] = $value ? 'true' : 'false';
            } elseif (is_null($value)) {
                $formattedData[$stringKey] = '';
            } else {
                $formattedData[$stringKey] = (string) $value;
            }
        }
        
        // Always ensure we have at least some data for Firebase
        if (empty($formattedData)) {
            $formattedData = [
                'notification_type' => 'admin_notification',
                'sent_at' => now()->toISOString()
            ];
        }
    
        return $formattedData;
    }
    
}


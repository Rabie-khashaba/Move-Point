<?php

namespace App\Services;

use App\Models\User;
use App\Models\AppNotification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FirebaseNotificationService
{
    private $projectId;
    private $accessToken;
    private $fcmUrl;

    public function __construct()
    {
        $this->projectId = config('firebase.project_id');
        $this->fcmUrl = "https://fcm.googleapis.com/v1/projects/{$this->projectId}/messages:send";
        
        // Get access token for V1 API
        $this->accessToken = $this->getAccessToken();
        
        Log::info('FirebaseNotificationService V1 initialized', [
            'project_id' => $this->projectId,
            'fcm_url' => $this->fcmUrl,
            'access_token_configured' => !empty($this->accessToken),
            'config_file_exists' => file_exists(config_path('firebase.php'))
        ]);
    }

    /**
     * Get access token for Firebase V1 API
     */
    private function getAccessToken()
    {
        $serviceAccountPath = config('firebase.service_account_path');
        
        if (!$serviceAccountPath || !file_exists($serviceAccountPath)) {
            Log::error('Firebase service account file not found', [
                'path' => $serviceAccountPath
            ]);
            return null;
        }
        
        $serviceAccount = json_decode(file_get_contents($serviceAccountPath), true);
        
        if (!$serviceAccount) {
            Log::error('Invalid service account file');
            return null;
        }
        
        // Create JWT token
        $header = json_encode(['typ' => 'JWT', 'alg' => 'RS256']);
        $now = time();
        $payload = json_encode([
            'iss' => $serviceAccount['client_email'],
            'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
            'aud' => 'https://oauth2.googleapis.com/token',
            'iat' => $now,
            'exp' => $now + 3600
        ]);
        
        $base64Header = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
        $base64Payload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));
        
        $signature = '';
        openssl_sign($base64Header . '.' . $base64Payload, $signature, $serviceAccount['private_key'], 'SHA256');
        $base64Signature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));
        
        $jwt = $base64Header . '.' . $base64Payload . '.' . $base64Signature;
        
        // Exchange JWT for access token
        $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion' => $jwt
        ]);
        
        if ($response->successful()) {
            $data = $response->json();
            return $data['access_token'] ?? null;
        }
        
        Log::error('Failed to get Firebase access token', [
            'response' => $response->body()
        ]);
        
        return null;
    }

    /**
     * Send notification to a single user
     */
    public function sendToUser(User $user, string $title, string $body, array $data = [], string $type = 'general'): bool
    {
        if (!$user->notifications_enabled || empty($user->device_tokens)) {
            Log::info('Notification not sent - user notifications disabled or no device tokens', [
                'user_id' => $user->id,
                'notifications_enabled' => $user->notifications_enabled,
                'device_tokens_count' => is_array($user->device_tokens) ? count($user->device_tokens) : 0
            ]);
            return false;
        }

        // Save notification to database
        $notification = AppNotification::create([
            'user_id' => $user->id,
            'title' => $title,
            'body' => $body,
            'type' => $type,
            'data' => $data,
        ]);

        // Send to all device tokens
        $success = true;
        $deviceTokens = is_array($user->device_tokens) ? $user->device_tokens : [];
        
        Log::info('Sending notification to user', [
            'user_id' => $user->id,
            'device_tokens_count' => count($deviceTokens),
            'title' => $title
        ]);
        
        foreach ($deviceTokens as $token) {
            if (!$this->sendToDevice($token, $title, $body, $data)) {
                $success = false;
            }
        }

        return $success;
    }

    /**
     * Send notification to multiple users
     */
    public function sendToUsers(array $userIds, string $title, string $body, array $data = [], string $type = 'general'): bool
    {
        $users = User::whereIn('id', $userIds)
            ->where('notifications_enabled', true)
            ->whereNotNull('device_tokens')
            ->get();

        $success = true;
        foreach ($users as $user) {
            if (!$this->sendToUser($user, $title, $body, $data, $type)) {
                $success = false;
            }
        }

        return $success;
    }

    /**
     * Send notification to all users of a specific type
     */
    public function sendToUserType(string $userType, string $title, string $body, array $data = [], string $type = 'general'): bool
    {
        $users = User::where('type', $userType)
            ->where('notifications_enabled', true)
            ->whereNotNull('device_tokens')
            ->get();

        $success = true;
        foreach ($users as $user) {
            if (!$this->sendToUser($user, $title, $body, $data, $type)) {
                $success = false;
            }
        }

        return $success;
    }

    /**
     * Send notification to a specific device token using Firebase V1 API
     */
    public function sendToDevice(string $token, string $title, string $body, array $data = []): bool
    {
        if (!$this->accessToken) {
            Log::error('FCM V1 - No access token available');
            return false;
        }

        // V1 API payload structure - ensure all data values are strings
        $stringData = [];
        if (is_array($data) && !empty($data)) {
            foreach ($data as $key => $value) {
                // Ensure key is a string
                $stringKey = (string) $key;
                
                // Convert value to string, handling different data types
                if (is_array($value) || is_object($value)) {
                    $stringData[$stringKey] = json_encode($value);
                } elseif (is_bool($value)) {
                    $stringData[$stringKey] = $value ? 'true' : 'false';
                } elseif (is_null($value)) {
                    $stringData[$stringKey] = '';
                } else {
                    $stringData[$stringKey] = (string) $value;
                }
            }
        }
        
        // If no data, add a default notification data
        if (empty($stringData)) {
            $stringData = [
                'notification_type' => 'general',
                'timestamp' => now()->toISOString()
            ];
        }
        
        $payload = [
            'message' => [
                'token' => $token,
                'notification' => [
                    'title' => $title,
                    'body' => $body
                ],
                'data' => $stringData,
                'android' => [
                    'priority' => 'high',
                    'notification' => [
                        'sound' => 'default',
                        'notification_priority' => 'PRIORITY_HIGH'
                    ]
                ],
                'apns' => [
                    'headers' => [
                        'apns-priority' => '10'
                    ],
                    'payload' => [
                        'aps' => [
                            'sound' => 'default',
                            'badge' => 1
                        ]
                    ]
                ]
            ]
        ];
        Log::info('FCM Payload: ' . json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));


        Log::info('FCM V1 Notification - Final payload before sending', [
            'token' => $token,
            'title' => $title,
            'body' => $body,
            'original_data' => $data,
            'original_data_type' => gettype($data),
            'converted_data' => $stringData,
            'converted_data_type' => gettype($stringData),
            'converted_data_count' => count($stringData),
            'final_payload' => $payload,
            'payload_json' => json_encode($payload),
            'fcm_url' => $this->fcmUrl,
            'access_token_available' => !empty($this->accessToken),
            'access_token_length' => strlen($this->accessToken ?? '')
        ]);

        try {
            $headers = [
                'Authorization' => 'Bearer ' . $this->accessToken,
                'Content-Type' => 'application/json',
            ];
            
            Log::info('FCM V1 Notification - Making HTTP request', [
                'url' => $this->fcmUrl,
                'headers' => $headers,
                'payload_size' => strlen(json_encode($payload)),
                'method' => 'POST'
            ]);
            
            $response = Http::withHeaders($headers)->post($this->fcmUrl, $payload);

            Log::info('FCM V1 Notification - HTTP Response', [
                'status_code' => $response->status(),
                'headers' => $response->headers(),
                'body' => $response->body(),
                'successful' => $response->successful()
            ]);

            if ($response->successful()) {
                $responseData = $response->json();
                
                Log::info('FCM V1 Notification - Success', [
                    'token' => $token,
                    'response' => $responseData,
                    'message_name' => $responseData['name'] ?? 'unknown'
                ]);
                
                return true;
            }

            Log::error('FCM V1 notification HTTP failed', [
                'token' => $token,
                'status' => $response->status(),
                'response' => $response->body(),
                'headers' => $response->headers()
            ]);

            return false;
        } catch (\Exception $e) {
            Log::error('FCM V1 notification exception', [
                'token' => $token,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return false;
        }
    }

    /**
     * Add device token to user
     */
    public function addDeviceToken(User $user, string $token): bool
    {
        $tokens = $user->device_tokens ?? [];
        
        if (!in_array($token, $tokens)) {
            $tokens[] = $token;
            $user->update(['device_tokens' => $tokens]);
        }

        return true;
    }

    /**
     * Remove device token from user
     */
    public function removeDeviceToken(User $user, string $token): bool
    {
        $tokens = $user->device_tokens ?? [];
        $tokens = array_values(array_filter($tokens, fn($t) => $t !== $token));
        
        $user->update(['device_tokens' => $tokens]);
        return true;
    }

    /**
     * Send leave request notification
     */
    public function sendLeaveRequestNotification(User $user, array $leaveData): bool
    {
        $title = 'طلب إجازة جديد';
        $body = "تم تقديم طلب إجازة من {$user->name}";
        
        return $this->sendToUser($user, $title, $body, [
            'type' => 'leave_request',
            'leave_id' => $leaveData['id'] ?? null,
            'start_date' => $leaveData['start_date'] ?? null,
            'end_date' => $leaveData['end_date'] ?? null,
        ], 'leave_request');
    }

    /**
     * Send advance request notification
     */
    public function sendAdvanceRequestNotification(User $user, array $advanceData): bool
    {
        $title = 'طلب سلفة جديد';
        $body = "تم تقديم طلب سلفة من {$user->name} بمبلغ {$advanceData['amount']} جنيه";
        
        return $this->sendToUser($user, $title, $body, [
            'type' => 'advance_request',
            'advance_id' => $advanceData['id'] ?? null,
            'amount' => $advanceData['amount'] ?? null,
        ], 'advance_request');
    }

    /**
     * Send delivery deposit notification
     */
    public function sendDeliveryDepositNotification(User $user, array $depositData): bool
    {
        $title = 'إيداع تسليم جديد';
        $body = "تم تقديم إيداع تسليم من {$user->name} بمبلغ {$depositData['amount']} جنيه";
        
        return $this->sendToUser($user, $title, $body, [
            'type' => 'delivery_deposit',
            'deposit_id' => $depositData['id'] ?? null,
            'amount' => $depositData['amount'] ?? null,
        ], 'delivery_deposit');
    }

    /**
     * Send leave request approval notification
     */
    public function sendLeaveRequestApprovalNotification(User $user, array $leaveData): bool
    {
        $title = 'تمت الموافقة على طلب الإجازة';
        $body = "تمت الموافقة على طلب إجازتك من {$leaveData['start_date']} إلى {$leaveData['end_date']}";
        
        return $this->sendToUser($user, $title, $body, [
            'type' => 'leave_request_approved',
            'leave_id' => $leaveData['id'] ?? null,
            'start_date' => $leaveData['start_date'] ?? null,
            'end_date' => $leaveData['end_date'] ?? null,
            'status' => 'approved',
        ], 'leave_request_approved');
    }

    /**
     * Send leave request rejection notification
     */
    public function sendLeaveRequestRejectionNotification(User $user, array $leaveData, string $reason): bool
    {
        $title = 'تم رفض طلب الإجازة';
        $body = "تم رفض طلب إجازتك. السبب: {$reason}";
        
        return $this->sendToUser($user, $title, $body, [
            'type' => 'leave_request_rejected',
            'leave_id' => $leaveData['id'] ?? null,
            'start_date' => $leaveData['start_date'] ?? null,
            'end_date' => $leaveData['end_date'] ?? null,
            'status' => 'rejected',
            'rejection_reason' => $reason,
        ], 'leave_request_rejected');
    }

    /**
     * Send advance request approval notification
     */
    public function sendAdvanceRequestApprovalNotification(User $user, array $advanceData): bool
    {
        $title = 'تمت الموافقة على طلب السلفة';
        $body = "تمت الموافقة على طلب سلفتك بمبلغ {$advanceData['amount']} جنيه";
        
        return $this->sendToUser($user, $title, $body, [
            'type' => 'advance_request_approved',
            'advance_id' => $advanceData['id'] ?? null,
            'amount' => $advanceData['amount'] ?? null,
            'status' => 'approved',
        ], 'advance_request_approved');
    }

    /**
     * Send advance request rejection notification
     */
    public function sendAdvanceRequestRejectionNotification(User $user, array $advanceData, string $reason): bool
    {
        $title = 'تم رفض طلب السلفة';
        $body = "تم رفض طلب سلفتك. السبب: {$reason}";
        
        return $this->sendToUser($user, $title, $body, [
            'type' => 'advance_request_rejected',
            'advance_id' => $advanceData['id'] ?? null,
            'amount' => $advanceData['amount'] ?? null,
            'status' => 'rejected',
            'rejection_reason' => $reason,
        ], 'advance_request_rejected');
    }

    /**
     * Send resignation request approval notification
     */
    public function sendResignationRequestApprovalNotification(User $user, array $resignationData): bool
    {
        $title = 'تمت الموافقة على طلب الاستقالة';
        $body = "تمت الموافقة على طلب استقالتك. تاريخ الاستقالة: {$resignationData['resignation_date']}";
        
        return $this->sendToUser($user, $title, $body, [
            'type' => 'resignation_request_approved',
            'resignation_id' => $resignationData['id'] ?? null,
            'resignation_date' => $resignationData['resignation_date'] ?? null,
            'status' => 'approved',
        ], 'resignation_request_approved');
    }

    /**
     * Send resignation request rejection notification
     */
    public function sendResignationRequestRejectionNotification(User $user, array $resignationData, string $reason): bool
    {
        $title = 'تم رفض طلب الاستقالة';
        $body = "تم رفض طلب استقالتك. السبب: {$reason}";
        
        return $this->sendToUser($user, $title, $body, [
            'type' => 'resignation_request_rejected',
            'resignation_id' => $resignationData['id'] ?? null,
            'resignation_date' => $resignationData['resignation_date'] ?? null,
            'status' => 'rejected',
            'rejection_reason' => $reason,
        ], 'resignation_request_rejected');
    }
}

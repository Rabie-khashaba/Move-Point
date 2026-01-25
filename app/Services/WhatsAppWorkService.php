<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\WhatsAppLog;

class WhatsAppWorkService
{
    /**
     * Send WhatsApp message using the configured service
     */
    public function send($phone, $message, $interviewDate = null, $googleMapUrl = null , $trainingDate = null  )
    {
        $service = config('services.whatsapp.service', 'simulation');

        // Create log entry
        $log = WhatsAppLog::create([
            'phone' => $phone,
            'message' => $this->formatMessage($message, $interviewDate, $googleMapUrl),
            'status' => 'waiting',
            'service' => $service,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'metadata' => [
                'interview_date' => $interviewDate,
                'google_map_url' => $googleMapUrl,
                'original_message' => $message
            ]
        ]);

        try {
            // Format the message
            $formattedMessage = $this->formatMessage($message, $interviewDate, $googleMapUrl ,$trainingDate );

            $result = false;
            $response = null;
            $error = null;

            switch ($service) {
                case 'twilio':
                    $result = $this->sendViaTwilio($phone, $formattedMessage);
                    break;
                case 'whatsapp_business':
                    $result = $this->sendViaWhatsAppBusiness($phone, $formattedMessage);
                    break;
                case 'wapilot':
                    $result = $this->sendViaWAPilot($phone, $formattedMessage);
                    break;
                case 'custom':
                    $result = $this->sendViaCustomAPI($phone, $formattedMessage);
                    break;
                default:
                    $result = $this->sendViaSimulation($phone, $formattedMessage);
                    break;
            }

            // Update log based on result
            if ($result) {
                $log->markAsSent('Message sent successfully');
            } else {
                $log->markAsFailed('Failed to send message');
            }

            // Also log to file for backup
            $this->logMessageStatus($phone, $formattedMessage, $result ? 'sent' : 'failed', $service, $response, $error);

            return $result;

        } catch (\Exception $e) {
            $error = $e->getMessage();
            $log->markAsFailed($error);

            // Log to file for backup
            $this->logMessageStatus($phone, $message, 'failed', $service, null, $error);

            Log::error('WhatsApp service error: ' . $e->getMessage(), [
                'phone' => $phone,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Format the message with interview details
     */
    private function formatMessage($messageText, $interviewDate = null, $googleMapUrl = null, $trainingDate = null)
    {
        $message = "مرحباً،\n\n";
        $message .= $messageText . "\n\n";

        if ($trainingDate) {
            $formattedTrainingDate = \Carbon\Carbon::parse($trainingDate)->format('d/m/Y H:i');
            $message .= "📚 تاريخ بدء التدريب: " . $formattedTrainingDate . "\n\n";

            if ($googleMapUrl) {
                $message .= "📍 مكان بدء التدريب على الخريطة:\n";
                $message .= $googleMapUrl . "\n\n";
            }
        }

        if ($interviewDate) {
            $formattedDate = \Carbon\Carbon::parse($interviewDate)->format('d/m/Y H:i');
            $message .= "🗓️ تاريخ بدء العمل: " . $formattedDate . "\n\n";

            if ($googleMapUrl) {
                $message .= "📍 مكان بدء العمل على الخريطة:\n";
                $message .= $googleMapUrl . "\n\n";
            }
        }

        $message .= "شكراً لكم";

        return $message;
    }


    private function sendViaTwilio($phone, $message)
    {
        try {
            $accountSid = config('services.twilio.sid');
            $authToken = config('services.twilio.token');
            $fromNumber = config('services.twilio.whatsapp_from');

            if (!$accountSid || !$authToken || !$fromNumber) {
                Log::error('Twilio configuration missing');
                return false;
            }

            $client = new \Twilio\Rest\Client($accountSid, $authToken);

            $result = $client->messages->create(
                "whatsapp:+{$phone}",
                [
                    'from' => "whatsapp:{$fromNumber}",
                    'body' => $message
                ]
            );

            Log::info('Twilio WhatsApp message sent successfully', [
                'message_sid' => $result->sid,
                'phone' => $phone
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Twilio WhatsApp error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send via WhatsApp Business API
     */
    private function sendViaWhatsAppBusiness($phone, $message)
    {
        try {
            $accessToken = config('services.whatsapp.access_token');
            $phoneNumberId = config('services.whatsapp.phone_number_id');

            if (!$accessToken || !$phoneNumberId) {
                Log::error('WhatsApp Business API configuration missing');
                return false;
            }

            $url = "https://graph.facebook.com/v17.0/{$phoneNumberId}/messages";

            $data = [
                'messaging_product' => 'whatsapp',
                'to' => $phone,
                'type' => 'text',
                'text' => [
                    'body' => $message
                ]
            ];

            $response = Http::withHeaders([
                'Authorization' => "Bearer {$accessToken}",
                'Content-Type' => 'application/json'
            ])->post($url, $data);

            if ($response->successful()) {
                Log::info('WhatsApp Business API message sent successfully', [
                    'phone' => $phone,
                    'response' => $response->json()
                ]);
                return true;
            } else {
                Log::error('WhatsApp Business API error', [
                    'phone' => $phone,
                    'response' => $response->json()
                ]);
                return false;
            }
        } catch (\Exception $e) {
            Log::error('WhatsApp Business API error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send via WAPilot API
     */
    private function sendViaWAPilot($phone, $message)
    {
        try {
            $instanceId = config('services.whatsapp.wapilot_instance_id');
            $apiToken = config('services.whatsapp.wapilot_api_token');
            $baseUrl = config('services.whatsapp.wapilot_base_url', 'https://api.wapilot.com');

            if (!$instanceId || !$apiToken) {
                Log::error('WAPilot configuration missing');
                return false;
            }

            // Format phone number with +2 prefix
            $formattedPhone = $this->formatPhoneNumber($phone);

            // Use the correct WAPilot API endpoint format
            $url = "{$baseUrl}/v1/{$instanceId}/send-message";

            $headers = [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ];

            // Try different phone number formats for chat_id
            $phoneFormats = [
                $formattedPhone,                    // +201234567890
                ltrim($formattedPhone, '+'),        // 201234567890
                ltrim($formattedPhone, '+') . '@c.us', // 201234567890@c.us (WhatsApp format)
                $formattedPhone . '@c.us',          // +201234567890@c.us
                '2' . ltrim($formattedPhone, '+'),  // 2201234567890 (double country code)
                $phone,                             // Original format
                '2' . $phone                        // 2 + original
            ];

            foreach ($phoneFormats as $chatId) {
                $data = [
                    'token' => $apiToken,
                    'chat_id' => $chatId,
                    'text' => $message
                ];

                Log::info('Trying WAPilot API', [
                    'url' => $url,
                    'chat_id' => $chatId,
                    'phone' => $formattedPhone,
                    'instance_id' => $instanceId
                ]);

                $response = Http::withHeaders($headers)->post($url, $data);

                if ($response->successful()) {
                    $responseData = $response->json();
                    Log::info('WAPilot WhatsApp message sent successfully', [
                        'phone' => $formattedPhone,
                        'chat_id_used' => $chatId,
                        'response' => $responseData,
                        'message_id' => $responseData['message_id'] ?? null
                    ]);
                    return true;
                } else {
                    Log::warning('WAPilot attempt failed', [
                        'phone' => $formattedPhone,
                        'chat_id' => $chatId,
                        'response' => $response->json(),
                        'status_code' => $response->status()
                    ]);
                }
            }

            // If all attempts failed
            Log::error('WAPilot WhatsApp API error - all phone formats failed', [
                'phone' => $formattedPhone,
                'phone_formats_tried' => $phoneFormats,
                'last_response' => $response->json(),
                'last_status_code' => $response->status()
            ]);

            return false;

        } catch (\Exception $e) {
            Log::error('WAPilot WhatsApp error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Format phone number for WAPilot API
     */
    private function formatPhoneNumber($phone)
    {
        // Remove any non-digit characters except +
        $phone = preg_replace('/[^0-9+]/', '', $phone);

        // Remove + if present
        $phone = ltrim($phone, '+');

        // If it doesn't start with country code, add Egypt country code (+2)
        if (!preg_match('/^2\d{1,14}$/', $phone)) {
            // Add Egypt country code (+2)
            $phone = '2' . $phone;
        }

        return $phone;
    }

    /**
     * Send via Custom API (WAMR, etc.)
     */
    private function sendViaCustomAPI($phone, $message)
    {
        try {
            $apiUrl = config('services.whatsapp.custom_api_url');
            $apiKey = config('services.whatsapp.custom_api_key');

            if (!$apiUrl || !$apiKey) {
                Log::error('Custom WhatsApp API configuration missing');
                return false;
            }

            $response = Http::withHeaders([
                'Authorization' => "Bearer {$apiKey}",
                'Content-Type' => 'application/json'
            ])->post($apiUrl, [
                'phone' => $phone,
                'message' => $message
            ]);

            if ($response->successful()) {
                Log::info('Custom WhatsApp API message sent successfully', [
                    'phone' => $phone,
                    'response' => $response->json()
                ]);
                return true;
            } else {
                Log::error('Custom WhatsApp API error', [
                    'phone' => $phone,
                    'response' => $response->json()
                ]);
                return false;
            }
        } catch (\Exception $e) {
            Log::error('Custom WhatsApp API error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Simulation method for testing
     */
    private function sendViaSimulation($phone, $message)
    {
        Log::info('SIMULATION: WhatsApp message would be sent to ' . $phone, [
            'message' => $message,
            'status' => 'simulated_success',
            'timestamp' => now()
        ]);

        // Simulate API delay
        sleep(1);

        return true;
    }

    /**
     * Test method to send a simple message
     */
    public function testMessage($phone)
    {
        $simpleMessage = "مرحباً، هذا اختبار للرسائل. " . now()->format('H:i:s');
        return $this->sendViaWAPilot($phone, $simpleMessage);
    }

    /**
     * Send test message to specific number
     */
    public function sendTestToNumber($phone = '+201100667988')
    {
        $testMessage = "مرحباً،\n\nهذا اختبار لنظام الرسائل في نظام إدارة الموارد البشرية.\n\nالوقت: " . now()->format('Y-m-d H:i:s') . "\n\nشكراً لكم";

        Log::info('Sending test WhatsApp message', [
            'phone' => $phone,
            'message' => $testMessage
        ]);

        return $this->send($phone, $testMessage);
    }

    /**
     * Generate WhatsApp direct link
     */
    public function generateWhatsAppLink($phone, $message = '')
    {
        // Format phone number (remove + and any spaces)
        $formattedPhone = preg_replace('/[^0-9]/', '', $phone);

        // Create WhatsApp link
        $baseUrl = "https://wa.me/{$formattedPhone}";

        if (!empty($message)) {
            $encodedMessage = urlencode($message);
            $baseUrl .= "?text={$encodedMessage}";
        }

        return $baseUrl;
    }

    /**
     * Log message status to database and file
     */
    private function logMessageStatus($phone, $message, $status, $service = null, $response = null, $error = null)
    {
        $logData = [
            'phone' => $phone,
            'message' => $message,
            'status' => $status, // 'sent', 'failed', 'waiting', 'pending'
            'service' => $service ?? config('services.whatsapp.service', 'simulation'),
            'response' => $response,
            'error' => $error,
            'timestamp' => now()->toDateTimeString(),
            'ip' => request()->ip() ?? 'system',
            'user_agent' => request()->userAgent() ?? 'system'
        ];

        // Log to Laravel log file
        if ($status === 'sent') {
            Log::info('WhatsApp Message Sent Successfully', $logData);
        } elseif ($status === 'failed') {
            Log::error('WhatsApp Message Failed', $logData);
        } elseif ($status === 'waiting') {
            Log::warning('WhatsApp Message Waiting/Queued', $logData);
        } else {
            Log::info('WhatsApp Message Status Update', $logData);
        }

        // Log to custom WhatsApp log file
        $this->logToWhatsAppFile($logData);

        return $logData;
    }

    /**
     * Log to dedicated WhatsApp log file
     */
    private function logToWhatsAppFile($logData)
    {
        $logFile = storage_path('logs/whatsapp-messages.log');
        $logEntry = json_encode($logData, JSON_UNESCAPED_UNICODE) . "\n";

        file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
    }

    /**
     * Get all WhatsApp message logs
     */
    public function getMessageLogs($limit = 100)
    {
        $logFile = storage_path('logs/whatsapp-messages.log');

        if (!file_exists($logFile)) {
            return [];
        }

        $lines = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $logs = [];

        // Get last N lines
        $lines = array_slice($lines, -$limit);

        foreach ($lines as $line) {
            $logData = json_decode($line, true);
            if ($logData) {
                $logs[] = $logData;
            }
        }

        return array_reverse($logs); // Most recent first
    }

    /**
     * Get message statistics
     */
    public function getMessageStats()
    {
        $logs = $this->getMessageLogs(1000); // Get last 1000 messages for stats

        $stats = [
            'total' => count($logs),
            'sent' => 0,
            'failed' => 0,
            'waiting' => 0,
            'pending' => 0,
            'by_service' => [],
            'by_hour' => [],
            'recent_activity' => []
        ];

        foreach ($logs as $log) {
            $status = $log['status'] ?? 'unknown';
            $service = $log['service'] ?? 'unknown';
            $hour = date('H', strtotime($log['timestamp'] ?? 'now'));

            // Count by status
            if (isset($stats[$status])) {
                $stats[$status]++;
            }

            // Count by service
            if (!isset($stats['by_service'][$service])) {
                $stats['by_service'][$service] = 0;
            }
            $stats['by_service'][$service]++;

            // Count by hour
            if (!isset($stats['by_hour'][$hour])) {
                $stats['by_hour'][$hour] = 0;
            }
            $stats['by_hour'][$hour]++;

            // Recent activity (last 10)
            if (count($stats['recent_activity']) < 10) {
                $stats['recent_activity'][] = [
                    'phone' => $log['phone'],
                    'status' => $status,
                    'service' => $service,
                    'timestamp' => $log['timestamp'],
                    'error' => $log['error'] ?? null
                ];
            }
        }

        return $stats;
    }

    /**
     * Clear old logs (keep last 1000 entries)
     */
    public function clearOldLogs()
    {
        $logFile = storage_path('logs/whatsapp-messages.log');

        if (!file_exists($logFile)) {
            return true;
        }

        $lines = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        if (count($lines) > 1000) {
            $keepLines = array_slice($lines, -1000);
            file_put_contents($logFile, implode("\n", $keepLines) . "\n");

            Log::info('WhatsApp logs cleared, kept last 1000 entries');
            return true;
        }

        return false;
    }

    /**
     * Get pending/failed messages for resending
     */
    public function getPendingMessages($limit = 50)
    {
        return WhatsAppLog::pending()
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(function($log) {
                return [
                    'id' => $log->id,
                    'phone' => $log->phone,
                    'message' => $log->message,
                    'status' => $log->status,
                    'service' => $log->service,
                    'timestamp' => $log->created_at->format('Y-m-d H:i:s'),
                    'attempts' => $log->attempts,
                    'error' => $log->error
                ];
            })
            ->toArray();
    }

    /**
     * Get number of attempts for a specific message
     */
    private function getMessageAttempts($phone, $timestamp)
    {
        $logs = $this->getMessageLogs(1000);
        $attempts = 0;

        foreach ($logs as $log) {
            if ($log['phone'] === $phone &&
                abs(strtotime($log['timestamp']) - strtotime($timestamp)) < 60) { // Within 1 minute
                $attempts++;
            }
        }

        return $attempts;
    }

    /**
     * Resend a failed message
     */
    public function resendMessage($messageId, $phone = null, $message = null)
    {
        // If phone and message are provided directly, use them
        if ($phone && $message) {
            $this->logMessageStatus($phone, $message, 'resending', config('services.whatsapp.service', 'simulation'));
            return $this->send($phone, $message);
        }

        // Otherwise, find the message by ID from logs
        $logs = $this->getMessageLogs(1000);

        foreach ($logs as $log) {
            $logId = md5($log['phone'] . $log['timestamp'] . $log['message']);
            if ($logId === $messageId && in_array($log['status'], ['failed', 'waiting', 'pending'])) {
                $this->logMessageStatus($log['phone'], $log['message'], 'resending', $log['service']);
                return $this->send($log['phone'], $log['message']);
            }
        }

        return false;
    }

    /**
     * Get message statistics for dashboard
     */
    public function getDashboardStats()
    {
        $total = WhatsAppLog::count();
        $sent = WhatsAppLog::sent()->count();
        $failed = WhatsAppLog::failed()->count();
        $pending = WhatsAppLog::pending()->count();

        $successRate = $total > 0 ? round(($sent / $total) * 100, 2) : 0;

        return [
            'total' => $total,
            'sent' => $sent,
            'failed' => $failed,
            'pending_messages' => $pending,
            'success_rate' => $successRate
        ];
    }
}

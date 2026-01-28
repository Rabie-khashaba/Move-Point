<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppServicebyair
{
    protected string $apiUrl;
    protected ?string $defaultToken;

    public function __construct()
    {
        $this->defaultToken = env('WHATSAPP_API_TOKEN');
        $this->apiUrl = rtrim(env('WHATSAPP_API_URL'), '/');
    }

    /**
     * تنسيق رقم الهاتف المصري
     */
    private function formatPhone(string $phone): string
    {
        // إزالة أي مسافات أو رموز
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // إزالة الصفر من البداية
        $phone = ltrim($phone, '0');

        // إزالة +20 أو 20 لو موجودين
        $phone = preg_replace('/^(20|\+20)/', '', $phone);

        // إضافة 20
        return '20' . $phone;
    }

    /**
     * تجهيز نص الرسالة بنفس الاستايل
     */
    private function formatMessage(string $messageText, ?string $interviewDate = null, ?string $googleMapUrl = null): string
    {
        $message = "مرحباً،\n\n";
        $message .= $messageText . "\n\n";

        if ($interviewDate) {
            $formattedDate = Carbon::parse($interviewDate)->format('d/m/Y H:i');
            $message .= "تاريخ المقابلة: " . $formattedDate . "\n\n";
        }

        if ($googleMapUrl) {
            $message .= "📍 موقع المقابلة على الخريطة:\n";
            $message .= $googleMapUrl . "\n\n";
        }

        $message .= "شكراً لكم";

        return $message;
    }

    /**
     * إرسال رسالة واتساب
     */
    public function send(
        string $phone,
        string $message,
        ?string $interviewDate = null,
        ?string $googleMapUrl = null,
        ?string $deviceToken = null
    ): array {
        $token = $deviceToken ?? $this->defaultToken;

        if (!$token) {
            return [
                'success' => false,
                'message' => 'التوكن غير متوفر لإرسال الرسالة.'
            ];
        }

        $formattedPhone = $this->formatPhone($phone);
        $formattedMessage = $this->formatMessage($message, $interviewDate, $googleMapUrl);

        try {
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$token}",
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ])
            ->timeout(90)
            ->retry(2, 100)
            ->post($this->apiUrl . '/api/send-message', [
                'phone' => $formattedPhone,
                'message' => $formattedMessage
            ]);

            if ($response->successful()) {
                Log::info('WhatsApp message sent', [
                    'phone' => $formattedPhone,
                    'response' => $response->json()
                ]);

                return [
                    'success' => true,
                    'message' => '✅ تم إرسال الرسالة بنجاح.',
                    'response' => $response->json()
                ];
            }

            Log::error('WhatsApp send failed', [
                'status' => $response->status(),
                'body' => $response->body(),
                'phone' => $formattedPhone
            ]);

            return [
                'success' => false,
                'message' => '❌ فشل إرسال الرسالة.'
            ];

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('WhatsApp Connection Error', [
                'error' => $e->getMessage(),
                'phone' => $formattedPhone
            ]);

            return [
                'success' => false,
                'message' => 'مشكلة في الاتصال بخدمة WhatsApp'
            ];

        } catch (\Exception $e) {
            Log::error('WhatsApp Unexpected Error', [
                'error' => $e->getMessage(),
                'phone' => $formattedPhone
            ]);

            return [
                'success' => false,
                'message' => '⚠️ حدث خطأ غير متوقع أثناء الإرسال.'
            ];
        }
    }

    /**
     * إرسال OTP
     */
    public function sendOtp(string $phone, string $code, ?string $deviceToken = null): array
    {
        return $this->send($phone, "كود التحقق الخاص بك هو : {$code}", null, null, $deviceToken);
    }
}

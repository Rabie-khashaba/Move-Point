<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class WhatsAppService2
{
    protected string $apiUrl;

    public function __construct()
    {
        $this->apiUrl = 'https://api.fekrawhats.com/send';
    }

    /**
     * Send WhatsApp message to a single number
     *
     * @param string $phone رقم الهاتف
     * @param string $message نص الرسالة
     * @param string|null $interviewDate تاريخ العمل
     * @param string|null $googleMapUrl رابط المكان
     * @param string|null $deviceToken التوكن (لو متساب يستخدم التوكن الافتراضي)
     * @return array
     */
    public function send(
        string $phone,
        string $message,
        ?string $interviewDate = null,
        ?string $googleMapUrl = null,
        ?string $deviceToken = null
    )
    {


        // استخدم التوكن المرسل أو الافتراضي
        $deviceToken = $deviceToken ?? 'BDv6ogWwsK38RzY7oqcF';

        // تنظيف الرقم
        $recipient = preg_replace('/[^0-9]/', '', $phone);
        if (strlen($recipient) != 11) {
            return [
                'success' => false,
                'message' => 'رقم غير صالح. يجب أن يكون 11 رقمًا.'
            ];
        }

        $formattedPhone = '2' . $recipient;
        $formattedMessage = $this->formatMessage($message, $interviewDate, $googleMapUrl);

        $data = [
            'token'    => $deviceToken,
            'receiver' => $formattedPhone,
            'msgtext'  => $formattedMessage,
        ];

        try {
            $response = $this->sendRequest($data, $this->apiUrl);

            $json = json_decode($response, true);

            if (isset($json['success']) && $json['success'] === true) {
                return [
                    'success' => true,
                    'message' => '✅ تم إرسال الرسالة بنجاح.',
                    'response' => $json,
                ];
            }

            return [
                'success' => false,
                'message' => '❌ فشل الإرسال: ' . $response,
            ];

        } catch (\Exception $e) {
            Log::error('WhatsApp API exception: ' . $e->getMessage(), [
                'phone' => $formattedPhone,
                'device' => $deviceToken,
            ]);

            return [
                'success' => false,
                'message' => '⚠️ حدث خطأ أثناء الإرسال: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Prepare the message text
     */
    private function formatMessage($messageText, $interviewDate = null, $googleMapUrl = null)
    {
        $message = "مرحباً،\n\n";
        $message .= $messageText . "\n\n";

        if ($interviewDate) {
            $formattedDate = \Carbon\Carbon::parse($interviewDate)->format('d/m/Y H:i');
            $message .= "تاريخ المقابلة: " . $formattedDate . "\n\n";
        }

        // Add Google Maps URL if provided
        if ($googleMapUrl) {
            $message .= "📍 موقع المقابلة على الخريطة:\n";
            $message .= $googleMapUrl . "\n\n";
        }

        $message .= "شكراً لكم";

        return $message;
    }

    /**
     * Send HTTP request using cURL
     */
    protected function sendRequest(array $data, string $url): string
    {
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_HTTPHEADER => ['Content-Type: application/x-www-form-urlencoded'],
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS => http_build_query($data),
            CURLOPT_URL => $url,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0,
        ]);

        $response = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            throw new \Exception('cURL error: ' . $error);
        }

        return $response;
    }
}

<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class FekraWhatsService
{
    protected $apiUrl;
    protected $token;

    public function __construct()
    {
        $this->apiUrl = 'https://api.fekrawhats.com/send';
        $this->token = env('FEKRAWHATS_TOKEN', 'aB6nMC8OQWaLnr1L6JqI'); // ضع التوكن في .env
    }

    /**
     * Send simple text message (with optional buttons/media)
     */
    public function send($to, $message, $fromNumber = 1, $buttons = [])
    {
        $nodeurl = 'https://api.fekrawhats.com/send';
        $mediaurl = 'https://share.google/images/gPHsQ2Mnhm4WpY4MC';

        // اختر التوكن حسب الرقم المحدد
        $tokenKey = 'FEKRAWHATS_TOKEN_' . $fromNumber;
        $token = config('services.fekrawhats.' . $tokenKey) ?? env($tokenKey);

        if (is_array($to)) {
            foreach ($to as $value) {
                $data = [
                    'receiver' => '+2' . $value,
                    'msgtext'  => $message,
                    'token'    => $token,
                    'mediaurl' => $mediaurl,
                    'buttons'  => $buttons,
                ];
                $this->sendRequest($data, $nodeurl);
            }
        } else {
            $data = [
                'receiver' => '+2' . $to,
                'msgtext'  => $message,
                'token'    => $token,
                'mediaurl' => $mediaurl,
                'buttons'  => $buttons,
            ];
            $this->sendRequest($data, $nodeurl);
        }
    }

protected function sendRequest($data, $nodeurl)
{
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_HTTPHEADER => ['Content-Type: application/x-www-form-urlencoded'],
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POSTFIELDS => http_build_query($data),
        CURLOPT_URL => $nodeurl,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_SSL_VERIFYHOST => 0,
        CURLOPT_SSL_VERIFYPEER => 0,
    ]);
    $response = curl_exec($ch);
    curl_close($ch);
    return $response;
}


    /**
     * Send media message (image, pdf, etc.)
     */
    public function sendMedia($to, $mediaUrl, $caption = '', $fromNumber = 1)
    {
        $nodeurl = 'https://api.fekrawhats.com/send';

        // اختَر التوكن بناءً على الرقم المختار
        $tokenKey = 'FEKRAWHATS_TOKEN_' . $fromNumber;
        $token = env($tokenKey);

        $data = [
            'receiver' => '+2' . $to,
            'msgtext'  => $caption,
            'token'    => $token,
            'mediaurl' => $mediaUrl,
        ];

        return $this->sendRequest($data, $nodeurl);
    }

    /**
     * Common HTTP request handler
     */
    /* protected function sendRequest(array $data)
    { */
       /*  $response = Http::asForm()
            ->withoutVerifying()
            ->timeout(30)
            ->post($this->apiUrl, $data);

        return [
            'status' => $response->status(),
            'body'   => $response->body(),
        ]; */


       /*  $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_HTTPHEADER => ['Content-Type: application/x-www-form-urlencoded'],
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS => http_build_query($data),
            CURLOPT_URL => $nodeurl,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0,
        ]);
        $response = curl_exec($ch);
        curl_close($ch);
        return $response; */
    //}



}

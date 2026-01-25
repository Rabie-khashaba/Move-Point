<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FekraWhatsService;

class WhatsAppController extends Controller
{
    public function sendTest()
    {
        // الرقم الذي تريد التجربة عليه (بدون +2، لأنه يضيفه Service تلقائياً)
        $phone = '01008781912';

        // الرسالة التي تريد إرسالها
        $message = 'مرحباً، هذه رسالة اختبار من النظام التجريبي.';

        // instance من Service
        $whatsApp = new FekraWhatsService();

        // إرسال الرسالة
        $result = $whatsApp->send($phone, $message);

        // إرجاع النتيجة
        return response()->json($result);


    }
}

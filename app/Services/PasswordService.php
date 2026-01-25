<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PasswordService
{
    protected $whatsappService;

    public function __construct(WhatsAppService $whatsappService)
    {
        $this->whatsappService = $whatsappService;
    }

    /**
     * Generate a new password based on name and phone
     */
    public function generatePassword($name, $phone)
    {
        $cleanName   = preg_replace('/[^a-zA-Z0-9]/', '', $name);
        $phoneSuffix = substr($phone, -4);

        return strtolower(substr($cleanName, 0, 3)) . $phoneSuffix . Str::random(3);
    }

    /**
     * Reset password for a user and send via WhatsApp
     */
    public function resetPassword($phone)
    {
        try {
            $user = User::where('phone', $phone)->first();

            if (!$user) {
                return ['success' => false, 'message' => 'لم يتم العثور على مستخدم بهذا الرقم'];
            }

            $relatedModel = $this->getRelatedModel($user);
            if (!$relatedModel) {
                return ['success' => false, 'message' => 'لم يتم العثور على بيانات المستخدم'];
            }

            $newPassword = $this->generatePassword($relatedModel->name, $phone);
            $message     = $this->formatPasswordResetMessage($relatedModel->name, $newPassword);

            $whatsappResult = $this->whatsappService->send($phone, $message);

            if ($whatsappResult) {
                $user->update([
                    'password'            => Hash::make($newPassword),
                    'forget_password'     => false,
                    'last_plain_password' => $newPassword, // نخزن الباسورد العادي عشان نقدر نعيد الإرسال
                    'last_reset_status'   => 'sent',
                    'last_reset_at'       => now(),
                ]);

                return [
                    'success'  => true,
                    'message'  => 'تم إرسال كلمة المرور الجديدة عبر WhatsApp بنجاح',
                    'password' => $newPassword,
                ];
            } else {
                $user->update([
                    'forget_password'   => false,
                    'last_reset_status' => 'failed',
                    'last_reset_at'     => now(),
                ]);

                return ['success' => false, 'message' => 'فشل في إرسال الرسالة عبر WhatsApp'];
            }
        } catch (\Exception $e) {
            \Log::error('Password reset error', [
                'phone' => $phone,
                'error' => $e->getMessage(),
            ]);

            return ['success' => false, 'message' => 'حدث خطأ أثناء إعادة تعيين كلمة المرور'];
        }
    }

    /**
     * Get the related model (representative, supervisor, employee)
     */
    private function getRelatedModel(User $user)
    {
        return match ($user->type) {
            'representative' => $user->representative,
            'supervisor'     => $user->supervisor,
            'employee'       => $user->employee,
            default          => null,
        };
    }

    /**
     * Format the password reset message for WhatsApp
     */
    private function formatPasswordResetMessage($name, $password)
    {
        return "مرحباً {$name}،\n\n" .
            "تم إعادة تعيين كلمة المرور الخاصة بك.\n\n" .
            "كلمة المرور الجديدة: {$password}\n\n" .
            "شكراً لكم";
    }

    /**
     * Get all password reset requests
     */
    public function getAllResetRequests()
    {
        return User::where('type', '!=', 'admin')
            ->where(function($q){
                $q->whereNotNull('last_reset_status')
                  ->orWhere('forget_password', 1);
            })
            ->orderBy('updated_at', 'desc')
            ->paginate(20);
    }

    /**
     * Get password reset requests by status
     */
    public function getResetRequestsByStatus($status)
    {
        return User::where('type', '!=', 'admin')
            ->where('last_reset_status', $status)
            ->orderBy('updated_at', 'desc')
            ->paginate(20);
    }

    /**
     * Mark password reset as completed
     */
    public function markAsCompleted($userId)
    {
        $user = User::find($userId);

        if ($user) {
            $user->update([
                'forget_password' => false,
                'last_reset_status' => 'completed',
            ]);
            return true;
        }

        return false;
    }

    /**
     * Resend password reset via WhatsApp
     */
    public function resendPasswordReset($userId)
    {
        $user = User::find($userId);

        if (!$user || !$user->last_plain_password) {
            return false;
        }

        $relatedModel = $this->getRelatedModel($user);
        $message      = $this->formatPasswordResetMessage($relatedModel->name, $user->last_plain_password);

        $whatsappResult = $this->whatsappService->send($user->phone, $message);

        if ($whatsappResult) {
            $user->update([
                'forget_password'   => false,
                'last_reset_status' => 'resent',
                'last_reset_at'     => now(),
            ]);
            return true;
        }

        return false;
    }
}

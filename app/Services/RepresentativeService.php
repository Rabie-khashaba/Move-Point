<?php
namespace App\Services;
use App\Repositories\RepresentativeRepository;
use App\Models\User;
use App\Services\PasswordService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class RepresentativeService
{
    protected $repository;
    protected $passwordService;

    public function __construct(RepresentativeRepository $repository, PasswordService $passwordService)
    {
        $this->repository = $repository;
        $this->passwordService = $passwordService;
    }

    public function all()
    {
        return $this->repository->all()->load(['user', 'company', 'governorate', 'location']);
    }

    public function paginated($perPage = 20)
    {
        return $this->repository->query()->with(['user', 'company', 'governorate', 'location'])->paginate($perPage);
    }

    public function find($id)
    {
        return $this->repository->find($id);
    }

    public function create(array $data)
    {
        Log::info('RepresentativeService@create started', [
            'name' => $data['name'] ?? 'N/A',
            'phone' => $data['phone'] ?? 'N/A',
            'company_id' => $data['company_id'] ?? 'N/A',
            'attachments_count' => count($data['attachments'] ?? []),
            'created_by' => auth()->id() ?? 'public_registration'
        ]);

        try {
            // Generate password automatically
            $generatedPassword = $this->passwordService->generatePassword($data['name'], $data['phone']);

            Log::info('Password generated for representative', [
                'phone' => $data['phone'],
                'password_length' => strlen($generatedPassword)
            ]);

            // Create user account for mobile app
            $user = User::create([
                'phone' => $data['phone'],
                'password' => Hash::make($generatedPassword),
                'type' => 'representative',
                'forget_password' => false,
            ]);

            Log::info('User account created successfully', [
                'user_id' => $user->id,
                'phone' => $data['phone'],
                'type' => 'representative'
            ]);

            // Handle file uploads for 5 attachments
            $attachments = [];
            if (isset($data['attachments']) && is_array($data['attachments'])) {
                Log::info('Processing attachments', [
                    'total_attachments' => count($data['attachments']),
                    'user_id' => $user->id
                ]);

                foreach ($data['attachments'] as $index => $file) {
                    if ($file && $file->isValid()) {
                        $fileName = time() . '_' . $index . '.' . $file->getClientOriginalExtension();
                        $path = $file->storeAs('representatives/attachments', $fileName, 'public');
                        $attachments[] = $path;

                        Log::info('Attachment uploaded successfully', [
                            'attachment_index' => $index,
                            'file_name' => $fileName,
                            'path' => $path,
                            'file_size' => $file->getSize()
                        ]);
                    } else {
                        Log::warning('Invalid attachment file', [
                            'attachment_index' => $index,
                            'file_valid' => $file ? $file->isValid() : false,
                            'file_error' => $file ? $file->getError() : 'No file'
                        ]);
                    }
                }
            }

            Log::info('Attachments processing completed', [
                'successful_uploads' => count($attachments),
                'total_attachments' => count($data['attachments'] ?? [])
            ]);

            $representativeData = [
                'user_id' => $user->id,
                'name' => $data['name'],
                'phone' => $data['phone'],
                'address' => $data['address'],
                'contact' => $data['contact'],
                'national_id' => $data['national_id'],
                'salary' => $data['salary'],
                'start_date' => $data['start_date'],
                'company_id' => $data['company_id'],
                'bank_account' => $data['bank_account'],
                'code' => $data['code'],
                'attachments' => json_encode($attachments),
                'inquiry_checkbox' => $data['inquiry_checkbox'] ?? false,
                'governorate_id' => $data['governorate_id'],
                'location_id' => $data['location_id'],
                'home_location' => $data['home_location'] ?? null,
                'inquiry_data' => $data['inquiry_data'] ?? null,
                'is_active' => $data['is_active'] ?? true,
                'created_by' => auth()->id(), // Set the current user as creator
            ];

            Log::info('Creating representative record', [
                'user_id' => $user->id,
                'representative_data_keys' => array_keys($representativeData)
            ]);

            $representative = $this->repository->create($representativeData);

            Log::info('Representative record created successfully', [
                'representative_id' => $representative->id,
                'user_id' => $user->id,
                'name' => $data['name'],
                'phone' => $data['phone']
            ]);

            // Auto-create training record
            try {
                \App\Models\RepresentativeTraining::firstOrCreate([
                    'representative_id' => $representative->id,
                ], [
                    'is_completed' => false,
                    'completed_at' => null,
                ]);

                Log::info('Training record created for representative', [
                    'representative_id' => $representative->id,
                    'training_status' => 'pending'
                ]);
            } catch (\Exception $e) {
                Log::warning('Failed to create training record for representative', [
                    'representative_id' => $representative->id,
                    'error' => $e->getMessage(),
                ]);
            }

            // Send WhatsApp notification with credentials
            try {
                $message = "مرحباً {$data['name']}،\n\n" .
                          "تم إنشاء حسابك بنجاح في النظام.\n\n" .
                          "بيانات تسجيل الدخول:\n" .
                          "رقم الهاتف: {$data['phone']}\n" .
                          "كلمة المرور: {$generatedPassword}\n\n" .
                          "تحميل التطبيق:\n" .
                          "https://play.google.com/store/apps/details?id=com.tripple.move_point\n\n" .
                          "فيديو لمعرفة كيفية طلب سلفتك:\n" .
                          "https://movepoint.site/storage/app/public/videos/representative-welcome.mp4\n\n" .
                          "للتواصل فون الأرقام التالية وأي رقم آخر لا يعتد به:\n" .
                          "منه / 01111266019\n" .
                          "يوسف / 01026768707\n" .
                          "مؤمن / 01044446905\n\n" .

                          "اطلب سلفتك الآن من التطبيق";

                $whatsappService = app(\App\Services\WhatsAppService::class);
                $whatsappService->send($data['phone'], $message);
            } catch (\Exception $e) {
                Log::error('Failed to send WhatsApp notification for representative: ' . $e->getMessage());
            }

            Log::info('RepresentativeService@create completed successfully', [
                'representative_id' => $representative->id,
                'user_id' => $user->id,
                'name' => $data['name'],
                'phone' => $data['phone'],
                'attachments_count' => count($attachments)
            ]);

            return $representative;
        } catch (\Exception $e) {
            Log::error('RepresentativeService@create failed: ' . $e->getMessage(), [
                'phone' => $data['phone'] ?? 'N/A',
                'name' => $data['name'] ?? 'N/A',
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

   public function update($id, array $data)
{
    $representative = $this->repository->find($id);

    // Decode existing attachments
    $existingAttachments = [];
    if ($representative->attachments) {
        $existingAttachments = is_string($representative->attachments)
            ? json_decode($representative->attachments, true) ?? []
            : $representative->attachments;
    }
    $user = User::find($representative->user_id);
    $user->update([
        'name' => $data['name'],
        'phone' => $data['phone'],
    ]);

    // Handle new file uploads (partial replacement)
    if (isset($data['attachments']) && is_array($data['attachments'])) {
        foreach ($data['attachments'] as $index => $file) {
            if ($file && $file->isValid()) {
                // Delete old attachment at this index if exists
                if (isset($existingAttachments[$index]) && Storage::disk('public')->exists($existingAttachments[$index])) {
                    Storage::disk('public')->delete($existingAttachments[$index]);
                }

                // Store new file
                $fileName = time() . '_' . $index . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('representatives/attachments', $fileName, 'public');
                $existingAttachments[$index] = $path;
            }
        }
    }

    $data['attachments'] = json_encode($existingAttachments);

    return $this->repository->update($representative, $data);
}

    public function changePassword($id, $password)
    {
        $representative = $this->repository->find($id);
        $representative->user->update(['password' => Hash::make($password)]);
    }

    public function delete($id)
    {
        $representative = $this->repository->find($id);

        // Delete attachments from storage
        if ($representative->attachments) {
            $attachments = [];
            if (is_string($representative->attachments)) {
                $attachments = json_decode($representative->attachments, true) ?? [];
            } elseif (is_array($representative->attachments)) {
                $attachments = $representative->attachments;
            }

            foreach ($attachments as $attachment) {
                if (Storage::disk('public')->exists($attachment)) {
                    Storage::disk('public')->delete($attachment);
                }
            }
        }

        // Delete user account
        if ($representative->user) {
            $representative->user->delete();
        }

        $this->repository->delete($representative);
    }

    public function getLocationsByGovernorate($governorateId)
    {
        return \App\Models\Location::where('governorate_id', $governorateId)->get();
    }
}

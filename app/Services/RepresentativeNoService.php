<?php
namespace App\Services;

use App\Models\Lead;
use App\Models\Supervisor;
use App\Repositories\RepresentativeRepository;
use App\Models\User;
use App\Services\PasswordService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class RepresentativeNoService
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


        Log::info('RepresentativeNoService@create started', [
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

            $type = ['representative']; // النوع الأساسي

            if (!empty($data['is_supervisor']) && $data['is_supervisor']) {
                $type[] = 'supervisor';
            }

            // نحول المصفوفة لسلسلة
            $userType = implode(',', $type);

            $user = User::create([
                'phone' => $data['phone'],
                'password' => Hash::make($generatedPassword),
                'type' => 'supervisor', // ← هنا ندعم مندوب + مشرف

            ]);

            // Create user account for mobile app
            /* $user = User::create([
                'phone' => $data['phone'],
                'password' => Hash::make($generatedPassword),
                'type' => 'representative',
                'forget_password' => false,
            ]); */


            Log::info('User account created successfully', [
                'user_id' => $user->id,
                'phone' => $data['phone'],
                'type' => 'representative'
            ]);

            // Handle file uploads for 5 attachments
            $requiredDocs = [
                0 => 'البطاقة (وجه أول)',
                1 => 'البطاقة (خلف)',
                2 => 'فيش',
                3 => 'شهادة ميلاد',
                4 => 'إيصال الأمانة',
                5 => 'رخصة القيادة',
                6 => 'رخصة السيارة وجه أول',
                7 => 'رخصة السيارة وجه ثاني',
                8 => 'إيصال مرافق (غاز أو مياه أو كهرباء)',
                9 => 'مرفق بيانات الاستعلام',

            ];

            $attachments = [];
            if (isset($data['attachments']) && is_array($data['attachments'])) {
                Log::info('Processing attachments', [
                    'total_attachments' => count($data['attachments']),
                    'user_id' => $user->id
                ]);

                foreach ($data['attachments'] as $index => $file) {
                    if ($file && $file->isValid()) {
                        // Check MIME type to avoid .htm uploads
                        $mime = $file->getMimeType(); // ex: image/jpeg, application/pdf
                        $ext = strtolower($file->getClientOriginalExtension());

                        $allowedExt = ['jpg', 'jpeg', 'png', 'pdf'];
                        $allowedMime = ['image/jpeg', 'image/png', 'application/pdf'];

                        if (!in_array($ext, $allowedExt) || !in_array($mime, $allowedMime)) {
                            Log::warning('Attachment rejected (invalid type)', [
                                'attachment_index' => $index,
                                'file_name' => $file->getClientOriginalName(),
                                'mime' => $mime,
                                'ext' => $ext
                            ]);
                            continue; // Skip this file
                        }

                        // Generate a clean name
                        $fileName = time() . '_' . $index . '.' . $ext;
                        $path = $file->storeAs('representatives/attachments', $fileName, 'public');

                        $attachments[] = [
                            'type' => $requiredDocs[$index] ?? "مرفق غير معروف",
                            'path' => $path,
                        ];

                        Log::info('Attachment uploaded successfully', [
                            'attachment_index' => $index,
                            'file_name' => $fileName,
                            'path' => $path,
                            'file_size' => $file->getSize(),
                            'attachment_type' => $requiredDocs[$index] ?? "مرفق غير معروف",
                        ]);
                    }
                }

            }

            Log::info('Attachments processing completed', [
                'successful_uploads' => count($attachments),
                'total_attachments' => count($data['attachments'] ?? [])
            ]);

            $lead = Lead::where('phone', $data['phone'])->first();
            // 💡 لو وجدنا lead نأخذ منه assigned_to
            $employeeId = $lead ? $lead->assigned_to : null;


            $representativeData = [
                'user_id' => $user->id,
                'name' => $data['name'],
                'phone' => $data['phone'],
                'address' => $data['address'],
                'address_in_card' => $data['address_in_card'],
                'contact' => $data['contact'],
                'national_id' => $data['national_id'],
                'salary' => $data['salary'],
                'start_date' => $data['start_date'],
                'company_id' => $data['company_id'],
                'bank_account' => $data['bank_account'],
                'code' => $data['code'],
                'attachments' => $attachments,
                'inquiry_checkbox' => $data['inquiry_checkbox'] ?? false,
                'governorate_id' => $data['governorate_id'],
                'location_id' => $data['location_id'],
                'home_location' => $data['home_location'] ?? null,
                'inquiry_data' => $data['inquiry_data'] ?? null,
                'is_active' => $data['is_active'] ?? true,
                'created_by' => auth()->id(), // Set the current user as creator

                'employee_id' => $employeeId,

            ];

            Log::info('Creating representative record', [
                'user_id' => $user->id,
                'representative_data_keys' => array_keys($representativeData)
            ]);

            $representative = $this->repository->create($representativeData);

            if (!empty($data['is_supervisor']) && $data['is_supervisor']) {

                $supervisorData = [
                    'user_id'        => $user->id,
                    'name'           => $data['name'],
                    'phone'          => $data['phone'],
                    'contact'        => $data['contact'],
                    'governorate_id' => $data['governorate_id'],
                    'location_id'    => $data['location_id'] ?? null,
                    'national_id'    => $data['national_id'],
                    'salary'         => $data['salary'],
                    'start_date'     => $data['start_date'],
                    'is_active'      => $data['is_active'] ?? true,
                ];

                $supervisor = \App\Models\Supervisor::updateOrCreate(
                    ['phone' => $data['phone']],   // البحث بس برقم الهاتف
                    $supervisorData
                );

                // ربط المندوب بالمشرف الجديد
                /* $representative->supervisor_id = $supervisor->id;
                $representative->save(); */

                // لو في مندوبين مرتبطين بالمشرف من الـ checkbox array
                if (!empty($data['representative_ids'])) {
                    foreach ($data['representative_ids'] as $repId) {
                        \App\Models\Representative::where('id', $repId)
                            ->update(['supervisor_id' => $supervisor->id]);
                    }
                }

                Log::info('Supervisor created or updated successfully', [
                    'supervisor_id' => $supervisor->id,
                    'phone' => $data['phone'],
                ]);
            }


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
                //$whatsappService = app(\App\Services\WhatsAppService2::class);
                $whatsappService->send($data['phone'], $message);
            } catch (\Exception $e) {
                Log::error('Failed to send WhatsApp notification for representative: ' . $e->getMessage());
            }

            Log::info('RepresentativeNoService@create completed successfully', [
                'representative_id' => $representative->id,
                'user_id' => $user->id,
                'name' => $data['name'],
                'phone' => $data['phone'],
                'attachments_count' => count($attachments)
            ]);

            return $representative;
        } catch (\Exception $e) {
            Log::error('RepresentativeNoService@create failed: ' . $e->getMessage(), [
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

        if (!$representative) {
            throw new \Exception("Representative not found with ID: $id");
        }

        // Decode existing attachments
        $existingAttachments = [];
        if ($representative->attachments) {
            $existingAttachments = is_string($representative->attachments)
                ? json_decode($representative->attachments, true) ?? []
                : $representative->attachments;
        }



        // Generate password automatically
        $generatedPassword = $this->passwordService->generatePassword($data['name'], $data['phone']);

        // Update user info
        $user = User::find($representative->user_id);


        if ($user) {

            // لو تم اختيار checkbox supervisor → حط النوعين
            if (!empty($data['is_supervisor']) && $data['is_supervisor']) {
                $type = ['supervisor'];
            } else {
                // في حالة إلغاء checkbox → ارجع يمثل مندوب فقط
                $type = ['representative'];
            }

            $user->update([
                'name'  => $data['name'] ?? $user->name,
                'phone' => $data['phone'] ?? $user->phone,
                'type'  => implode(',', $type),
                'password' => Hash::make($generatedPassword),
            ]);
        }

        if ($user) {
            $user->update([
                'name' => $data['name'] ?? $user->name,
                'phone' => $data['phone'] ?? $user->phone,
            ]);
            Log::info('User info updated for representative', [
                'user_id' => $user->id,
                'name' => $user->name,
                'phone' => $user->phone,
            ]);
        }

        // Define required document types
        $requiredDocs = [
            0 => 'البطاقة (وجه أول)',
            1 => 'البطاقة (خلف)',
            2 => 'فيش',
            3 => 'شهادة ميلاد',
            4 => 'إيصال الأمانة',
            5 => 'رخصة القيادة',
            6 => 'رخصة السيارة وجه أول',
            7 => 'رخصة السيارة وجه ثاني',
            8 => 'إيصال مرافق (غاز أو مياه أو كهرباء)',
            9 => 'مرفق بيانات الاستعلام',
        ];

        // Handle new file uploads
        if (isset($data['attachments']) && is_array($data['attachments'])) {
            Log::info('Processing new attachments for update', [
                'representative_id' => $representative->id,
                'total_new_attachments' => count($data['attachments'])
            ]);

            foreach ($data['attachments'] as $index => $file) {
                if ($file && $file->isValid()) {

                    // Delete old file at this index if exists
                    if (
                        isset($existingAttachments[$index]['path'])
                        && Storage::disk('public')->exists($existingAttachments[$index]['path'])
                    ) {
                        Storage::disk('public')->delete($existingAttachments[$index]['path']);
                        Log::info('Old attachment deleted', [
                            'representative_id' => $representative->id,
                            'index' => $index,
                            'path' => $existingAttachments[$index]['path']
                        ]);
                    }

                    // Validate file type
                    $allowedExt = ['jpg', 'jpeg', 'png', 'pdf'];
                    $allowedMime = ['image/jpeg', 'image/png', 'application/pdf'];
                    $ext = strtolower($file->getClientOriginalExtension());
                    $mime = $file->getMimeType();

                    if (!in_array($ext, $allowedExt) || !in_array($mime, $allowedMime)) {
                        Log::warning('Attachment rejected (invalid type)', [
                            'representative_id' => $representative->id,
                            'index' => $index,
                            'file_name' => $file->getClientOriginalName(),
                            'mime' => $mime,
                            'ext' => $ext
                        ]);
                        continue;
                    }

                    // Store new file
                    $fileName = 'rep_' . time() . '_' . uniqid() . '.' . $ext;
                    $path = $file->storeAs('representatives/attachments', $fileName, 'public');

                    // Assign correct type
                    $type = $existingAttachments[$index]['type'] ?? $requiredDocs[$index] ?? "مرفق غير معروف";

                    $existingAttachments[$index] = [
                        'type' => $requiredDocs[$index] ?? "مرفق غير معروف", // <-- هنا التعديل
                        'path' => $path,
                        'url' => Storage::disk('public')->url($path),
                    ];

                    Log::info('Attachment uploaded successfully', [
                        'representative_id' => $representative->id,
                        'index' => $index,
                        'file_name' => $fileName,
                        'path' => $path,
                        'type' => $type
                    ]);
                }
            }

            // Check if all required attachments exist
            $uploadedCount = count(array_filter($existingAttachments));
            if ($uploadedCount >= count($requiredDocs)) {
                session()->flash('success', 'الأوراق مكتملة');
                Log::info('All attachments completed', ['representative_id' => $representative->id]);
            }
        }

        $data['attachments'] = $existingAttachments;

        // Update representative record
        $updatedRepresentative = $this->repository->update($representative, $data);


        if (!empty($data['is_supervisor']) && $data['is_supervisor']) {

            // يعمل updateOrCreate
            $supervisor = \App\Models\Supervisor::updateOrCreate(
                ['user_id' => $representative->user_id], // شرط البحث
                [
                    'name'           => $data['name'],
                    'phone'          => $data['phone'],
                    'contact'        => $data['contact'],
                    'governorate_id' => $data['governorate_id'] ?? null,
                    'location_id'    => $data['location_id'],
                    'national_id'    => $data['national_id'],
                    'salary'         => $data['salary'],
                    'start_date'     => $data['start_date'],
                    'is_active'      => $data['is_active'] ?? 1,
                    'code'           => $data['code'],
                    'company_id'     => $data['company_id'],


                ]
            );


            try {
                $message = "مرحباً {$data['name']}،\n\n" .
                    "تم تعديل حسابك بنجاح في النظام.\n\n" .
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
                    "يرجى تسجيل الدخول وتغيير كلمة المرور.\n\n" .
                    "شكراً لكم\n\n" .
                    "اطلب سلفتك الآن من التطبيق";

                $whatsappService = app(\App\Services\WhatsAppService::class);
                //$whatsappService = app(\App\Services\WhatsAppService2::class);
                $whatsappService->send($data['phone'], $message);
            } catch (\Exception $e) {
                Log::error('Failed to send WhatsApp notification for representative: ' . $e->getMessage());
            }



        }


        Log::info('Representative updated successfully', [
            'representative_id' => $representative->id,
            'updated_fields' => array_keys($data)
        ]);

        return $updatedRepresentative;
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

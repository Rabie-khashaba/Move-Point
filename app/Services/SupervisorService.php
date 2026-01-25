<?php
namespace App\Services;
use App\Repositories\SupervisorRepository;
use App\Models\User;
use App\Models\SupervisorTransferLog;
use App\Services\PasswordService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class SupervisorService
{
    protected $repository;
    protected $passwordService;

    public function __construct(SupervisorRepository $repository, PasswordService $passwordService)
    {
        $this->repository = $repository;
        $this->passwordService = $passwordService;
    }

    public function all()
    {
        return $this->repository->all()->load(['user', 'location', 'governorate', 'representatives']);
    }

    public function paginated($perPage = 20)
    {
        return $this->repository->query()->with(['user', 'location', 'governorate', 'representatives'])->paginate($perPage);
    }

    public function find($id)
    {
        return $this->repository->find($id)->load(['user', 'location', 'governorate', 'representatives']);
    }

    public function create(array $data)
    {
        DB::beginTransaction();

        try {
            // Generate password automatically
            $generatedPassword = $this->passwordService->generatePassword($data['name'], $data['phone']);

            // Create user account
            $user = User::create([
                'phone' => $data['phone'],
                'password' => Hash::make($generatedPassword),
                'type' => 'supervisor',
                'forget_password' => false,
            ]);

            // Create supervisor
            $supervisorData = [
                'user_id' => $user->id,
                'name' => $data['name'],
                'phone' => $data['phone'],
                'contact' => $data['contact'],
                'governorate_id' => $data['governorate_id'] ?? null,
                'location_id' => $data['location_id'],
                'national_id' => $data['national_id'],
                'salary' => $data['salary'],
                'start_date' => $data['start_date'],
                'is_active' => $data['is_active'] ?? true,
            ];

            $supervisor = $this->repository->create($supervisorData);




            // Assign representatives if provided
            if (isset($data['representative_ids']) && is_array($data['representative_ids'])) {
                $supervisor->representatives()->attach($data['representative_ids']);
            }

            // Send WhatsApp notification with credentials
            try {
                $message = "مرحباً {$data['name']}،\n\n" .
                          "تم إنشاء حسابك بنجاح في النظام.\n\n" .
                          "بيانات تسجيل الدخول:\n" .
                          "رقم الهاتف: {$data['phone']}\n" .
                          "كلمة المرور: {$generatedPassword}\n\n" .
                          "يرجى تسجيل الدخول وتغيير كلمة المرور.\n\n" .
                          "شكراً لكم";

                $whatsappService = app(\App\Services\WhatsAppService::class);
                $whatsappService->send($data['phone'], $message);
            } catch (\Exception $e) {
                \Log::error('Failed to send WhatsApp notification for supervisor: ' . $e->getMessage());
            }

            DB::commit();
            return $supervisor;

        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function update($id, array $data)
    {
        DB::beginTransaction();

        try {
            $supervisor = $this->repository->find($id);

            // Update supervisor data
            $supervisorData = [
                'name' => $data['name'],
                'phone' => $data['phone'],
                'contact' => $data['contact'],
                'governorate_id' => $data['governorate_id'] ?? null,
                'location_id' => $data['location_id'],
                'national_id' => $data['national_id'],
                'salary' => $data['salary'],
                'start_date' => $data['start_date'],
                'is_active' => $data['is_active'] ?? $supervisor->is_active,
            ];

            $supervisor = $this->repository->update($supervisor, $supervisorData);

            // Update user phone if changed
            if ($supervisor->user->phone !== $data['phone']) {
                $supervisor->user->name = $data['name'];
                $supervisor->user->update(['phone' => $data['phone']]);
            }

            // Update representatives
            if (isset($data['representative_ids'])) {
                $supervisor->representatives()->sync($data['representative_ids']);
            }

            DB::commit();
            return $supervisor;

        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function changePassword($id, $password)
    {
        $supervisor = $this->repository->find($id);
        $supervisor->user->update(['password' => Hash::make($password)]);
    }

    public function delete($id)
    {
        DB::beginTransaction();

        try {
            $supervisor = $this->repository->find($id);

            // Detach all representatives
            $supervisor->representatives()->detach();

            // Delete user account
            $supervisor->user->delete();

            // Delete supervisor
            $this->repository->delete($supervisor);

            DB::commit();

        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function transferRepresentative($representativeId, $newSupervisorId, $reason = null)
    {
        DB::beginTransaction();

        try {
            $representative = \App\Models\Representative::findOrFail($representativeId);
            $newSupervisor = $this->repository->find($newSupervisorId);
                            $oldSupervisor = $representative->current_supervisor;

            // Detach from old supervisor
            if ($oldSupervisor) {
                $representative->supervisors()->detach($oldSupervisor->id);
            }

            // Attach to new supervisor
            $representative->supervisors()->attach($newSupervisor->id);

            // Log the transfer
            SupervisorTransferLog::create([
                'representative_id' => $representativeId,
                'old_supervisor_id' => $oldSupervisor ? $oldSupervisor->id : null,
                'new_supervisor_id' => $newSupervisorId,
                'transferred_by' => auth()->id(),
                'reason' => $reason,
            ]);

            DB::commit();
            return true;

        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }
}

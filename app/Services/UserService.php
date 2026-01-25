<?php
namespace App\Services;
use App\Repositories\UserRepository;
use App\Models\UserRole;
use Illuminate\Support\Facades\Hash;

class UserService
{
    protected $repository;

    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    public function all()
    {
        return $this->repository->all()->load(['roles']);
    }

    public function paginated($perPage = 20)
    {
        return $this->repository->query()->with(['roles'])->paginate($perPage);
    }

    public function find($id)
    {
        return $this->repository->find($id);
    }

    public function create(array $data)
    {
        // Handle avatar upload if present
        if (isset($data['avatar']) && $data['avatar'] instanceof \Illuminate\Http\UploadedFile) {
            $data['avatar'] = $this->uploadAvatar($data['avatar']);
        }
        
        // Hash password
        if (isset($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        }
        
        return $this->repository->create($data);
    }

    public function update($id, array $data)
    {
        $user = $this->repository->find($id);
        
        // Handle avatar upload if present
        if (isset($data['avatar']) && $data['avatar'] instanceof \Illuminate\Http\UploadedFile) {
            // Delete old avatar if exists
            if ($user->avatar) {
                $this->deleteAvatar($user->avatar);
            }
            $data['avatar'] = $this->uploadAvatar($data['avatar']);
        }
        
        // Hash password if provided
        if (isset($data['password']) && !empty($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        } else {
            unset($data['password']); // Don't update password if empty
        }
        
        return $this->repository->update($user, $data);
    }

    public function delete($id)
    {
        $user = $this->repository->find($id);
        
        // Delete avatar file if exists
        if ($user->avatar) {
            $this->deleteAvatar($user->avatar);
        }
        
        $this->repository->delete($user);
    }

    public function changePassword($id, $password)
    {
        $user = $this->repository->find($id);
        $user->update(['password' => bcrypt($password)]);
        return $user;
    }

    /**
     * Toggle user active status and sync to the corresponding profile table.
     */
    public function toggleStatusSync(int $id): \App\Models\User
    {
        $user = $this->repository->find($id);
        $newStatus = !$user->is_active;
        $user->is_active = $newStatus;
        $user->save();

        // Sync to related profile record if exists
        try {
            if ($user->type === 'employee' && $user->employee) {
                $user->employee->update(['is_active' => $newStatus]);
            } elseif ($user->type === 'representative' && $user->representative) {
                $user->representative->update(['is_active' => $newStatus]);
            } elseif ($user->type === 'supervisor' && $user->supervisor) {
                $user->supervisor->update(['is_active' => $newStatus]);
            }
        } catch (\Throwable $e) {
            // ignore sync failures to avoid breaking toggle
        }

        return $user;
    }

    /**
     * Upload user avatar
     */
    private function uploadAvatar(\Illuminate\Http\UploadedFile $file)
    {
        $filename = 'user_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        return $file->storeAs('users/avatars', $filename, 'public');
    }

    /**
     * Delete user avatar
     */
    private function deleteAvatar($avatarPath)
    {
        if (\Storage::disk('public')->exists($avatarPath)) {
            \Storage::disk('public')->delete($avatarPath);
        }
    }
}

<?php
namespace App\Repositories;
use App\Models\User;

class UserRepository
{
    public function all()
    {
        return User::all();
    }

    public function query()
    {
        return User::query();
    }

    public function create(array $data)
    {
        return User::create($data);
    }

    public function find($id)
    {
        return User::with('roles')->findOrFail($id);
    }

    public function update(User $user, array $data)
    {
        $user->update($data);
        return $user;
    }

    public function delete(User $user)
    {
        $user->delete();
    }
}

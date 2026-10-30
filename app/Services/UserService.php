<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function __construct(private readonly UserRepositoryInterface $users) {}

    public function updateProfile(User $user, array $data): User
    {
        $attributes = array_intersect_key($data, array_flip(['name', 'email']));

        if (isset($attributes['email']) && $attributes['email'] !== $user->email) {
            $attributes['email_verified_at'] = null;
        }

        return $this->users->update($user, $attributes);
    }

    public function updatePassword(User $user, string $password): void
    {
        $this->users->update($user, ['password' => Hash::make($password)]);
    }

    public function delete(User $user): void
    {
        $user->tokens()->delete();

        $this->users->delete($user);
    }
}

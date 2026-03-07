<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Validation\ValidationException;

class AuthService
{
    public function __construct()
    {
    }

    public function register(array $data): array
    {
        $user = User::createUser($data);
        $token = $user->generateToken();

        return [
            'user' => $user,
            'token' => $token,
        ];
    }

    public function login(array $credentials): array
    {
        $user = User::findByEmail($credentials['email']);

        if (!$user || !$user->verifyPassword($credentials['password'])) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $user->generateToken();

        return [
            'user' => $user,
            'token' => $token,
        ];
    }

    public function logoutCurrentDevice(User $user): bool
    {
        $user->revokeCurrentToken();
        return true;
    }

    public function updateProfile(User $user, array $data): User
    {
        if (isset($data['password']) && isset($data['current_password'])) {
            if (!$user->verifyPassword($data['current_password'])) {
                throw ValidationException::withMessages([
                    'current_password' => ['The current password is incorrect.'],
                ]);
            }
        }

        $user->updateProfile($data);
        $user->refresh();

        return $user;
    }
}
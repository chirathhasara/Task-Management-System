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

    public function logout(User $user): bool
    {
        $user->revokeAllTokens();
        return true;
    }

    public function logoutCurrentDevice(User $user): bool
    {
        $user->revokeCurrentToken();
        return true;
    }
}
<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        try {
            $result = $this->authService->register($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'User registered successfully.',
                'data' => [
                    'user' => $result['user'],
                    'token' => $result['token'],
                    'token_type' => 'Bearer',
                ],
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Registration failed.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $result = $this->authService->login($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Login successful.',
                'data' => [
                    'user' => $result['user'],
                    'token' => $result['token'],
                    'token_type' => 'Bearer',
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Login failed.',
                'error' => $e->getMessage(),
            ], 401);
        }
    }

    public function logout(Request $request): JsonResponse
    {
        try {
            $this->authService->logout($request->user());

            return response()->json([
                'success' => true,
                'message' => 'Logged out successfully from all devices.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Logout failed.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function logoutCurrentDevice(Request $request): JsonResponse
    {
        try {
            $this->authService->logoutCurrentDevice($request->user());

            return response()->json([
                'success' => true,
                'message' => 'Logged out successfully from current device.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Logout failed.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function profile(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $request->user(),
        ], 200);
    }
}
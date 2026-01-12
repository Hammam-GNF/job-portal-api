<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function __construct(
        protected AuthService $authService
    ) {}

    public function register(UserRequest $request)
    {
        [$user, $token] = $this->authService->register(
            $request->validated(),
            'applicant'
        );

        return response()->json([
            'success' => true,
            'message' => 'Applicant registered successfully',
            'data' => [
                'user' => new UserResource($user),
                'token' => $token,
            ],
        ], 201);
    }

    public function registerEmployer(UserRequest $request)
    {
        [$user, $token] = $this->authService->register(
            $request->validated(),
            'employer'
        );

        return response()->json([
            'success' => true,
            'message' => 'Employer registered successfully',
            'data' => [
                'user' => new UserResource($user),
                'token' => $token,
            ],
        ], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        [$user, $token] = $this->authService->login(
            $request->email,
            $request->password
        );

        if (! $user->role === 'applicant' && ! $user->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Your account is suspended. Please contact support.',
            ], 403);
        }

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'data' => [
                'user' => new UserResource($user),
                'token' => $token,
            ],
        ]);
    }

    public function logout(Request $request)
    {
        $this->authService->logout($request->user());

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully',
        ]);
    }
}
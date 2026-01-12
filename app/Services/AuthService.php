<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService
{
    public function register(array $data, string $role): array
    {
        $data['password'] = Hash::make($data['password']);
        $data['is_active'] = true;
        $data['role'] = $role;

        $user = User::create($data);

        $user->tokens()->delete();

        $token = $user->createToken('auth-token', [$role])->plainTextToken;

        return [$user, $token];
    }

    public function login(string $email, string $password): array
    {
        $user = User::where('email', $email)->first();

        if (! $user || ! Hash::check($password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Invalid credentials.'],
            ]);
        }

        $user->tokens()->delete();

        $token = $user->createToken('auth-token', [$user->role])->plainTextToken;

        return [$user, $token];
    }

    public function logout(User $user): void
    {
        $user->tokens()->delete();
    }
}

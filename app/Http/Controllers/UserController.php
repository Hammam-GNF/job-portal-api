<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Http\Resources\ApplicationResource;
use App\Http\Resources\UserResource;
use App\Models\Application;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(User::class, 'user');
    }

    public function index()
    {
        return UserResource::collection(User::latest()->paginate());
    }

    public function show(User $user)
    {
        return new UserResource($user);
    }

    // public function store(UserRequest $request)
    // {
    //     $data = $request->validated();
    //     $data['password'] = Hash::make($data['password']);
    //     $data['role'] = 'applicant';
        
    //     return new UserResource(User::create($data));
    // }

    // public function update(UserRequest $request, User $user)
    // {
    //     $data = $request->validated();

    //     if (isset($data['password'])) {
    //         $data['password'] = Hash::make($data['password']);
    //     }

    //     $user->update($data);

    //     return new UserResource($user);
    // }



    public function suspend(User $user)
    {
        $this->authorize('suspend', $user);

        $user->update([
            'is_active' => false,
            'suspended_at' => now(),
        ]);

        return response()->json([
            'message' => 'User suspended',
            'data' => new UserResource($user)
        ]);
    }

    public function restore(User $user)
    {
        $this->authorize('restore', $user);

        $user->update([
            'is_active' => true,
            'suspended_at' => null,
        ]);

        return response()->json([
            'message' => 'User restored',
            'data' => new UserResource($user)
        ]);
    }


    public function destroy(User $user)
    {
        $user->delete();

        return response()->json([
            'message' => 'User deleted successfully'
        ]);
    }
}

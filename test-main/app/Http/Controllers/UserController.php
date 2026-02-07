<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterUserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function register(RegisterUserRequest $request)
    {
        try {
            $profilePath = $request->file('profile_picture')->store('profile_picture', 'public');
            $nationPhotoPath = $request->file('nation_picture')->store('nation_picture', 'public');

            $profileUrl = asset('storage/' . $profilePath);
            $nationPhotoUrl = asset('storage/' . $nationPhotoPath);

            $user = User::create([
                ...$request->validated(),
                'profile_picture' => $profileUrl,
                'nation_picture' => $nationPhotoUrl,
                'status' => 'pending'
            ]);

            return response()->json([
                'message' => 'user registered successfully',
                'user' => $user
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'File upload failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function login(Request $request)
    {
        $validate = $request->validate([
            'phone_number' => 'required|digits:10',
            'password' => 'required|min:6'
        ]);

        $user = User::where('phone_number', $validate['phone_number'])->first();

        if (!$user || !Hash::check($validate['password'], $user->password)) {
            return response()->json([
                'message' => 'Invalid credentials'
            ], 401);
        }

        if ($user->status != 'approved') {
            return response()->json([
                'message' => 'Account pending approval'
            ], 403);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'login successful',
            'user' => $user,
            'user_state' => $user->user_state,
            'token' => $token
        ], 200);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'message' => 'logout successful',
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    /**
     * Check if user is authenticated and is admin
     */
    private function checkAdmin()
    {
        // First check if user is authenticated
        if (!Auth::check()) {
            return response()->json([
                'message' => 'Unauthorized. Please login first.'
            ], 401);
        }
        
        // Then check if user is admin
        if (Auth::user()->user_state !== 'admin') {
            return response()->json([
                'message' => 'Admin access only'
            ], 403);
        }
        
        return null; // User is admin
    }
    
    public function pendingUsers()
    {
        // Check admin access
        $adminCheck = $this->checkAdmin();
        if ($adminCheck) return $adminCheck;
        
        $users = User::where('status', 'pending')
            ->select('id', 'first_name', 'last_name', 'phone_number', 'user_state', 'created_at')
            ->paginate(10);

        return response()->json($users);
    }

    public function aproveUser($id)
    {
        // Check admin access
        $adminCheck = $this->checkAdmin();
        if ($adminCheck) return $adminCheck;
        
        $user = User::findOrFail($id);

        $user->update(['status' => 'approved']);

        return response()->json([
            'message' => 'User approved successfully',
            'user' => $user
        ]);
    }

    public function rejectUser($id)
    {
        // Check admin access
        $adminCheck = $this->checkAdmin();
        if ($adminCheck) return $adminCheck;
        
        $user = User::findOrFail($id);
        $user->update(['status' => 'rejected']);

        return response()->json([
            'message' => 'User rejected',
            'user' => $user
        ]);
    }

    public function allUsers()
    {
        // Check admin access
        $adminCheck = $this->checkAdmin();
        if ($adminCheck) return $adminCheck;
        
        $users = User::withCount('properties')->paginate(10);

        return response()->json($users); // Removed extra array wrapper
    }
}
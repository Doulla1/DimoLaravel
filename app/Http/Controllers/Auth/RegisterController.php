<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function register(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // default role
        $user->assignRole('student'); // Assign user role by default

        // Assign admin role to the first user
        if ($user->id == 1) {
            $user->assignRole('teacher');
            $user->assignRole('admin');
        }

        return response()->json(['message' => 'Registration successful', 'user' => $user]);
    }
}

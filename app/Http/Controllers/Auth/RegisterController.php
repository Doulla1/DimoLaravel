<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    /**
     * Register a new user
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function register(Request $request): JsonResponse
    {
        try{
            $request->validate([
                'firstname' => 'required|string',
                'lastname' => 'required|string',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|min:8',
            ]);

            $user = User::create([
                'firstname' => $request->firstname,
                'lastname' => $request->lastname,
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

            // create token
            $token = $user->createToken("CLE_SECRETE")->plainTextToken;

            // Rajouter les rôles de l'utilisateur
            $user->load('roles');

            return response()->json(['token' => $token, 'user' => $user]);
        }
        catch (\Exception $e) {
            return response()->json(['message' => 'Registration failed', 'error' => $e->getMessage()], 500);
        }
    }
}

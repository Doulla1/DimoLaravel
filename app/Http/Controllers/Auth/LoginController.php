<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /**
     * Login user and get a token
     *
     * @unauthenticated
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $token = $user->createToken('token-name')->plainTextToken;
            // Rajouter les rôles de l'utilisateur
            $user->load('roles');

            return response()->json(['token' => $token, "user" => $user]);
        }

        return response()->json(['message' => 'Invalid credentials'], 401);
    }

    /**
     * Logout user
     *
     * @return JsonResponse
     */
    public function logout(): JsonResponse
    {
        try {
            Auth::user()->tokens()->delete();
            return response()->json(['message' => 'Logged out']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error while logging out' . $e->getMessage () ], 500);
        }
    }
}

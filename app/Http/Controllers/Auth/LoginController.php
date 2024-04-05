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
     * @response array{user: User, token: string}
     *
     * @return JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->only('email', 'password');
        // Check if the credentials are valid
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);


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
            // TODO: Find a way to return a code 200 even if there is no token to delete and remove the comment
            //Auth::user()->tokens()->delete();
            return response()->json(['message' => 'Logged out']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error while logging out' . $e->getMessage () ], 500);
        }
    }
}

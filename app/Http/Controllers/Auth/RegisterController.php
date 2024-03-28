<?php

namespace App\Http\Controllers\Auth;

use App\Mail\RegistrationConfirmation;
use App\Mail\SendTeacherCredentials;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class RegisterController extends Controller
{
    /**
     * Register a new student
     *
     * @unauthenticated
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


            // Assign admin role to the first user
            if ($user->id == 1) {
                $user->assignRole('admin');
            }
            else{
                // Assign student role to the user
                $user->assignRole('student');
                // Send email confirmation
                Mail::to($user->email)->send(new RegistrationConfirmation($user));
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

    /**
     * Register a new teacher
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function registerTeacher(Request $request): JsonResponse
    {
        try {
            $request->validate ([
                'firstname' => 'required|string',
                'lastname' => 'required|string',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|min:8',
            ]);

            $user = new User;
            $user->firstname = $request->firstname;
            $user->lastname = $request->lastname;
            $user->email = $request->email;
            $user->password = Hash::make ($request->password);
            $user->save ();


            // Assign student role to the user
            $user->assignRole ('teacher');
            // Send email confirmation
            Mail::to ($user->email)->send (new SendTeacherCredentials($user->firstname, $user->lastname, $user->email, $request->password));

            // Rajouter les rôles de l'utilisateur
            $user->load ('roles');
            return response ()->json (['user' => $user]);
        } catch (\Exception $e) {
            return response ()->json (['message' => 'Registration failed', 'error' => $e->getMessage ()], 500);
        }
    }
}

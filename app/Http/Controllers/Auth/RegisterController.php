<?php

namespace App\Http\Controllers\Auth;

use App\Mail\RegistrationConfirmation;
use App\Mail\SendTeacherCredentials;
use App\Models\Skin2;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
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

                /*try {
                    Mail::to($user->email)->send(new RegistrationConfirmation($user));
                } catch (\Exception $e) {
                    return response()->json(['message' => 'Registration failed', 'error' => $e->getMessage()], 500);
                }*/
                try {
                    Mail::to($user->email)->send(new RegistrationConfirmation($user));
                } catch (\Exception $e) {
                    // Log the error for future reference
                    Log::error('Failed to send registration confirmation email: ' . $e->getMessage());
                }
            }

            // Create a default skin for the user
            $skin = new Skin2;
            $skin->user_id = $user->id;
            $skin->hair_version = 0;
            $skin->hair_color = "#5286FF";
            $skin->upper_body_color = "#F96C9D";
            $skin->lower_body_color = "#F96C9D";
            $skin->skin_color = "#D37878";
            $skin->save();

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
            ]);

            // Generate a random password
            $password = substr(str_shuffle("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!'-_&;"), 0, 10);

            // Create the user
            $user = new User;
            $user->firstname = $request->firstname;
            $user->lastname = $request->lastname;
            $user->email = $request->email;
            $user->password = Hash::make ($password);
            $user->save ();

            // Create a default skin for the user
            $skin = new Skin2;
            $skin->user_id = $user->id;
            $skin->hair_version = 0;
            $skin->hair_color = "#5286FF";
            $skin->upper_body_color = "#F96C9D";
            $skin->lower_body_color = "#F96C9D";
            $skin->skin_color = "#D37878";
            $skin->save();


            // Assign teacher role to the user
            $user->assignRole ('teacher');
            try {
                Mail::to ($user->email)->send (new SendTeacherCredentials($user->firstname, $user->lastname, $user->email, $password));
            } catch (\Exception $e) {
                // Log the error for future reference
                Log::error('Failed to send registration confirmation email: ' . $e->getMessage());
            }
            // Rajouter les rôles de l'utilisateur
            $user->load ('roles');
            return response ()->json (['user' => $user]);
        } catch (\Exception $e) {
            return response ()->json (['message' => 'Registration failed', 'error' => $e->getMessage ()], 500);
        }
    }
}

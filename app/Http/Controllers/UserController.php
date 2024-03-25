<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Get all users.
     *
     * @return JsonResponse
     */
    public function getAll(): JsonResponse
    {
        try{
            $users = User::all();
            // Rajouter les rôles de l'utilisateur
            $users->load('roles');
            return response()->json(["users"=>$users]);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * Get user by ID
     *
     * @param int $id
     * @return JsonResponse
     */
    public function getUnique(int $id): JsonResponse
    {
        try{
            $user = User::find($id);
            // Rajouter les rôles de l'utilisateur
            $user->load('roles');
            return response()->json(["user"=>$user]);
        } catch (Exception $e) {
            return response ()->json ($e->getMessage (), 500);
        }
    }

    /**
     * Get user by email
     *
     * @param string $email
     * @return JsonResponse
     */
    public function getByEmail(string $email): JsonResponse
    {
        try{
            $user = User::where('email', $email)->first();
            // Rajouter les rôles de l'utilisateur
            $user->load('roles');
            return response()->json(["user"=>$user]);
        } catch (Exception $e) {
            return response ()->json ($e->getMessage (), 500);
        }
    }

    /**
     * Get connected user.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getConnectedUser(Request $request): JsonResponse
    {
        try{
            $user = Auth::user();
            // Rajouter les rôles de l'utilisateur
            $user->load('roles');
            return response()->json(["user"=>$user]);
        } catch (Exception $e) {
            return response ()->json ($e->getMessage (), 500);
        }
    }

    /**
     * Delete a specific user.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function delete(int $id): JsonResponse
    {
        try{
            $user = User::find($id);
            $user->delete();
            return response()->json('User deleted');
        } catch (Exception $e) {
            return response ()->json ($e->getMessage (), 500);
        }
    }

    /**
     * Update a specific user.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, int $id): JsonResponse
    {
        try{
            //validate data
            $this->validate($request, [
                'firstname' => 'required',
                'lastname' => 'required',
                'email' => 'required',
                'password' => 'required'
            ]);
            $user = User::find($id);
            $user->update($request->all());
            // Rajouter les rôles de l'utilisateur
            $user->load('roles');
            return response()->json(["user"=>$user]);
        } catch (Exception $e) {
            return response ()->json ($e->getMessage (), 500);
        }
    }

    /**
     * Update connected user.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function updateConnectedUser(Request $request): JsonResponse
    {
        try{
            //validate data
            $this->validate($request, [
                'firstname' => 'required',
                'lastname' => 'required',
                'email' => 'required'
            ]);
            $user = Auth::user();
            $user->firstname = $request->firstname;
            $user->lastname = $request->lastname;
            $user->email = $request->email;
            // Rajouter les rôles de l'utilisateur
            $user->load('roles');
            return response()->json(["user"=>$user]);
        } catch (Exception $e) {
            return response ()->json ($e->getMessage (), 500);
        }
    }

    /**
     * Update connected user password.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function updatePassword(Request $request): JsonResponse
    {
        try{
            //validate data
            $this->validate($request, [
                'currentPassword' => 'required',
                'newPassword' => 'required'
            ]);
            $user = Auth::user();
            if (!Hash::check($request->currentPassword, $user->password)) {
                return response()->json(['message' => 'Current password is incorrect'], 401);
            }
            $user->password = Hash::make($request->newPassword);
            $user->save();
            // Rajouter les rôles de l'utilisateur
            $user->load('roles');
            return response()->json(["user"=>$user]);
        } catch (Exception $e) {
            return response ()->json ($e->getMessage (), 500);
        }
    }

    /**
     * Get all roles.
     *
     * @return JsonResponse
     */
    public function getRoles(): JsonResponse
    {
        try{
            $roles = Role::all();
            return response()->json(["roles"=>$roles]);
        } catch (Exception $e) {
            return response ()->json ($e->getMessage (), 500);
        }
    }

    /**
     * Assign a role to a user.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function assignRole(Request $request): JsonResponse
    {
        try{
            //validate data expecting two integers
            $this->validate($request, [
                'user_id' => 'required|integer',
                'role_id' => 'required|integer'
            ]);
            $user = User::find($request->user_id);
            $role = Role::find($request->role_id);
            if (!$user || !$role) {
                return response()->json(['message' => 'User or role not found'], 404);
            }
            $user->assignRole($role->name);
            // Rajouter les rôles de l'utilisateur
            $user->load('roles');
            return response()->json(["user"=>$user]);
        } catch (Exception $e) {
            return response ()->json ($e->getMessage (), 500);
        }
    }

    /**
     * Get all users with pending status (users with unassigned roles).
     *
     * @return JsonResponse
     */
    public function getPending(): JsonResponse
    {
        try{
            // Récupérer les utilisateurs ayant seulement le rôle "user"
            $users = User::whereHas('roles', function ($query) {
                $query->where('name', 'user');
            })->whereDoesntHave('roles', function ($query) {
                $query->where('name', '<>', 'user');
            })->get();
            return response()->json(["users"=>$users]);
        } catch (Exception $e) {
            return response ()->json ($e->getMessage (), 500);
        }
    }
}

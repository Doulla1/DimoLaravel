<?php

use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Routes accéssibles à tout le monde
Route::post('/login', [LoginController::class, 'login']);
Route::post('/register', [RegisterController::class, 'register']);
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail']);

// Routes accessibles à tout ceux qui sont connectés
Route::middleware('auth:sanctum')->group(function () {
    // Route pour récupérer les informations de l'utilisateur connecté
    Route::get('/fetchUser',[UserController::class, 'getConnectedUser']);

    // Route pour mettre à jour les informations de l'utilisateur connecté
    Route::put('/updateUser', [UserController::class, 'updateConnectedUser']);

    // Route pour mettre à jour le mot de passe de l'utilisateur connecté
    Route::put('/updateUserPassword', [UserController::class, 'updatePassword']);

    // Route pour se déconnecter
    Route::post('/logout', [LoginController::class, 'logout']);
});

// Routes accessibles aux students
Route::middleware(["auth:sanctum", "checkrole:student"])->group(function () {

});

// Routes accessibles aux teachers
Route::middleware(["auth:sanctum", "checkrole:teacher"])->group(function () {
    //

});

// Routes accessibles qu'aux admins
Route::middleware(["auth:sanctum", "checkrole:admin"])->group(function () {
    // Route pour récupérer tous les utilisateurs
    Route::get ('/admin/users',[UserController::class, 'getAll']);

    // Route pour récupérer un utilisateur par son id
    Route::get('/admin/users/{id}', [UserController::class, 'getUnique']);

    // Route pour récupérer un utilisateur par son email
    Route::get('/admin/users/email/{email}', [UserController::class, 'getByEmail']);

    // Route pour mettre à jour un utilisateur
    Route::put('/admin/users/{id}', [UserController::class, 'update']);

    // Route pour supprimer un utilisateur
    Route::delete('/admin/users/{id}', [UserController::class, 'delete']);

    // Route pour récupérer tous les rôles
    Route::get('/admin/roles', [UserController::class, 'getRoles']);

    // Attribuer un rôle à un utilisateur
    Route::post('/admin/users/{id}/roles/{role_id}', [UserController::class, 'assignRole']);

});

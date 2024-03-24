<?php

use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Routes accéssibles à tout le monde
Route::post('/login', [LoginController::class, 'login']);
Route::post('/register', [RegisterController::class, 'register']);
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail']);

// Routes accessibles à tout ceux qui sont connectés
Route::middleware('auth:sanctum')->group(function () {
    // Récupérer les informations de l'utilisateur connecté
    Route::get('/fetchUser',[UserController::class, 'getConnectedUser']);

    // Mettre à jour les informations de l'utilisateur connecté
    Route::put('/updateUser', [UserController::class, 'updateConnectedUser']);

    // Mettre à jour le mot de passe de l'utilisateur connecté
    Route::put('/updateUserPassword', [UserController::class, 'updatePassword']);

    // Se déconnecter
    Route::post('/logout', [LoginController::class, 'logout']);
});

// Routes accessibles aux students
Route::middleware(["auth:sanctum", "checkrole:student"])->group(function () {

});

// Routes accessibles aux teachers
Route::middleware(["auth:sanctum", "checkrole:teacher"])->group(function () {
    // Récupérer tous les cours
    Route::get('/courses', [CourseController::class, 'getAll']);

    // Créer un cours
    Route::post('/courses', [CourseController::class, 'create']);

    // Récupérer les cours du teacher connecté
    Route::get('/courses/teacher', [CourseController::class, 'getByConnectedTeacher']);

    // Mettre à jour un cours qui appartient au teacher connecté
    Route::put('/courses/{id}', [CourseController::class, 'update']);

    // Supprimer un cours qui appartient au teacher connecté
    Route::delete('/courses/{id}', [CourseController::class, 'delete']);

});

// Routes accessibles qu'aux admins
Route::middleware(["auth:sanctum", "checkrole:admin"])->group(function () {
    // Récupérer tous les utilisateurs
    Route::get ('/admin/users',[UserController::class, 'getAll']);

    // Récupérer un utilisateur par son id
    Route::get('/admin/users/{id}', [UserController::class, 'getUnique']);

    // Récupérer un utilisateur par son email
    Route::get('/admin/users/email/{email}', [UserController::class, 'getByEmail']);

    // Mettre à jour un utilisateur
    Route::put('/admin/users/{id}', [UserController::class, 'update']);

    // Route pour supprimer un utilisateur
    Route::delete('/admin/users/{id}', [UserController::class, 'delete']);

    // Récupérer tous les rôles
    Route::get('/admin/roles', [UserController::class, 'getRoles']);

    // Attribuer un rôle à un utilisateur
    Route::post('/admin/users/{id}/roles/{role_id}', [UserController::class, 'assignRole']);

    // Récupérer tous les cours
    Route::get('/admin/courses', [CourseController::class, 'getAll']);

    // Récuperer un cours par son id
    Route::get('/admin/courses/{id}', [CourseController::class, 'getById']);

    // Récupérer les cours d'un teacher
    Route::get('/admin/courses/teacher/{teacherId}', [CourseController::class, 'getByTeacherId']);

});

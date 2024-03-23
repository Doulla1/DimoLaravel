<?php

use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Routes accéssibles à tout le monde
Route::post('/login', [LoginController::class, 'login']);
Route::post('/register', [RegisterController::class, 'register']);
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail']);

// Routes accessibles à tout ceux qui sont connectés
Route::middleware('auth:sanctum')->group(function () {
    // Route pour récupérer les informations de l'utilisateur connecté
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});

// Routes accessibles aux students
Route::middleware(["auth:sanctum", "checkrole:student"])->group(function () {

});

// Routes accessibles aux teachers
Route::middleware(["auth:sanctum", "checkrole:teacher"])->group(function () {

});

// Routes accessibles qu'aux admins
Route::middleware(["auth:sanctum", "checkrole:admin"])->group(function () {

});

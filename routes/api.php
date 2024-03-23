<?php

use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Connexion
Route::post('/login', [LoginController::class, 'login']);

// Inscription
Route::post('/register', [RegisterController::class, 'register']);

// Mot de passe oublié
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail']);


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

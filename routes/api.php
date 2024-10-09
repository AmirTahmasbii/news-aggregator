<?php

use App\Http\Controllers\Api\Users\AuthController;

use Illuminate\Support\Facades\Route;

Route::controller(AuthController::class)->group(function () {
    Route::post('/register', 'register');
    Route::post('/login', 'login');
    Route::post('/logout', 'logout')->middleware('auth:sanctum');
    Route::post('/forgot-password', 'sendResetLinkEmail');
    Route::post('/reset-password', 'reset')->name('password.reset');
});

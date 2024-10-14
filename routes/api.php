<?php

use App\Http\Controllers\Api\Article\ArticleController;
use App\Http\Controllers\Api\Preference\PreferenceController;
use App\Http\Controllers\Api\Users\AuthController;

use Illuminate\Support\Facades\Route;

Route::controller(AuthController::class)->group(function () {
    Route::post('/register', 'register');
    Route::post('/login', 'login');
    Route::post('/logout', 'logout')->middleware('auth:sanctum');
    Route::post('/forgot-password', 'sendResetLinkEmail');
    Route::post('/reset-password', 'reset')->name('password.reset');
});

Route::prefix('/preference')->middleware('auth:sanctum')->controller(PreferenceController::class)->group(function () {
    Route::get('/', 'retrieve');
    Route::post('/set', 'set');
});

Route::prefix('/article')->middleware('auth:sanctum')->controller(ArticleController::class)->group(function(){
    Route::get('/feed', 'feed');
    Route::get('/', 'fetch');
    Route::get('/{article}', 'retrieve');
});

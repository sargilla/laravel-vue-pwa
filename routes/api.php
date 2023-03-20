<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;


Route::group(['prefix' => 'auth'], function ($router) {
    Route::post(
        'login',
        [AuthController::class, 'authenticate']
    );
    Route::post('reset-email', [AuthController::class, 'sendResetLinkEmail'])->name('reset-email');
    Route::post('reset-password', [AuthController::class, 'resetPassword'])->name('reset-password');
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('user', [AuthController::class, 'getAuthenticatedUser']);
});


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('message', function () {
    return 'Este es un mensaje por api';
});

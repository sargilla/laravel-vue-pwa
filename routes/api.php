<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RolesController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\PermissionsController;


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

Route::group(['middleware' => ['auth:sanctum']], function () {
    /* Access Tokens */
    Route::get('access-token', [AuthController::class, 'getAccessTokens'])->name('tokens.get');
    Route::post('access-token', [AuthController::class, 'createAccessToken'])->name('tokens.create');
    Route::delete('access-token/{token_id}', [AuthController::class, 'deleteAccessToken'])->name('tokens.delete');




    /* Frontend Ajax Requests */
    Route::get('getUsers', [UsersController::class, 'getUsers']);
    Route::get('getRoles', [RolesController::class, 'getRoles']);
    Route::get('getPermissions', [PermissionsController::class, 'getPermissions']);
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('message', function () {
    return 'Este es un mensaje por api';
});

<?php

use Illuminate\Support\Facades\Route;
use Modules\User\Http\Controllers\AuthController;
use Modules\User\Http\Controllers\PermissionController;
use Modules\User\Http\Controllers\RoleController;
use Modules\User\Http\Controllers\UserController;

// Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
//     Route::apiResource('users', UserController::class)->names('user');
// });


Route::prefix("auth")->group(function() {
    Route::post("login", [AuthController::class, "login"]);
    Route::post("refresh", [AuthController::class, "refresh"])->middleware("auth:api");
    Route::post("logout", [AuthController::class, "logout"])->middleware("auth:api");
});

Route::post('users/password', [UserController::class, 'updatePassword'])->middleware("auth:api");
Route::middleware(["auth:api", "role:Admin"])->group(function() {
    Route::apiResource('users', UserController::class);
    Route::apiResource('roles', RoleController::class)->except('show');
    Route::get('permissions', [PermissionController::class, 'index']);
});
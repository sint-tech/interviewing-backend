<?php

use App\Admin\Auth\Controllers\LoginController;
use App\Admin\Auth\Controllers\LogoutController;
use App\Admin\Organization\Controllers\OrganizationController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest:api')->group(function () {
    Route::post('/login', LoginController::class)->name('login');
});

Route::middleware('auth:api')->group(function () {
    Route::any('/logout', LogoutController::class)->name('logout');
});

Route::apiResource('organizations', OrganizationController::class);

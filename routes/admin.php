<?php

use App\Admin\Auth\Controllers\LoginController;
use App\Admin\Auth\Controllers\LogoutController;
use App\Admin\Organization\Controllers\OrganizationController;
use App\Admin\Skill\Controllers\SkillController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest:api')->withoutMiddleware('auth:api')->group(function () {
    Route::post('/login', LoginController::class)->name('login');
});

Route::any('/logout', LogoutController::class)->name('logout');

Route::apiResource('organizations', OrganizationController::class);

Route::apiResource('skills', SkillController::class);

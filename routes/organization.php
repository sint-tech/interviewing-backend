<?php

use App\Organization\Auth\Controllers\LoginController;
use App\Organization\Auth\Controllers\RegisterController;
use App\Organization\EmployeeManagement\Controllers\EmployeeController;
use Illuminate\Support\Facades\Route;

Route::withoutMiddleware('auth:api-employee')->middleware('guest:api-employee')->group(function () {
    Route::post('/login', LoginController::class)->name('login');
    Route::post('/register', RegisterController::class)->name('register');
});

Route::apiResource('employees', EmployeeController::class);

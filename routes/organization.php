<?php

use App\Organization\Auth\Controllers\LoginController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest:api')->withoutMiddleware('auth:api')->group(function () {
    Route::post('/login', LoginController::class)->name('login');
});

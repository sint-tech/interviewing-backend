<?php

use App\Website\Auth\Controllers\LoginController;
use App\Website\Auth\Controllers\LogoutController;
use App\Website\Auth\Controllers\RegisterController;
use App\Website\Auth\Controllers\ValidateNewCandidateUniqueInputsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware("guest:api-candidate")->group(function () {
    Route::post("/pre-register/valid-identifier-input", ValidateNewCandidateUniqueInputsController::class);
    Route::post("/register", RegisterController::class);
    Route::post("/login", LoginController::class);
});

Route::middleware('auth:api-candidate')->group(function () {
    Route::any("/logout", LogoutController::class);
});

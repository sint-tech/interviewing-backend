<?php

use App\Website\Auth\Controllers\LoginController;
use App\Website\Auth\Controllers\LogoutController;
use App\Website\Auth\Controllers\RegisterController;
use App\Website\Auth\Controllers\SocialLoginController;
use App\Website\Auth\Controllers\ValidateNewCandidateUniqueInputsController;
use App\Website\InterviewManagement\Controllers\MyInterviewsController;
use App\Website\InterviewManagement\Controllers\StartInterviewController;
use App\Website\InterviewManagement\Controllers\SubmitInterviewQuestionAnswerController;
use App\Website\JobTitle\Controllers\JobTitleController;
use App\Website\RegistrationReasons\Controllers\RegistrationReasonsController;
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

Route::middleware('guest:api-candidate')->group(function () {
    Route::post('/pre-register/valid-identifier-input', ValidateNewCandidateUniqueInputsController::class);
    Route::post('/register', RegisterController::class);
    Route::post('/login', LoginController::class);
    Route::post('/social-login', SocialLoginController::class);
});

/*
 * group all APIs
 */
Route::middleware('auth:api-candidate')->group(function () {
    Route::any('/logout', LogoutController::class);

    Route::prefix('interviews')
        ->group(function () {
            Route::get('', MyInterviewsController::class);
            Route::any('/{interview_template}/start-interview', StartInterviewController::class);
            Route::post('/{interview}/submit-answer', SubmitInterviewQuestionAnswerController::class);
        });
});

Route::apiResource('job-titles', JobTitleController::class)
    ->only(['index', 'show']);

Route::apiResource('registration-reasons', RegistrationReasonsController::class)
    ->only(['index', 'show']);

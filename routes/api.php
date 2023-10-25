<?php

use App\Candidate\Auth\Controllers\LoginController;
use App\Candidate\Auth\Controllers\LogoutController;
use App\Candidate\Auth\Controllers\RegisterController;
use App\Candidate\Auth\Controllers\SocialLoginController;
use App\Candidate\Auth\Controllers\ValidateNewCandidateUniqueInputsController;
use App\Candidate\InterviewManagement\Controllers\GetInterviewReportController;
use App\Candidate\InterviewManagement\Controllers\MyInterviewsController;
use App\Candidate\InterviewManagement\Controllers\StartInterviewController;
use App\Candidate\InterviewManagement\Controllers\SubmitInterviewQuestionAnswerController;
use App\Candidate\JobTitle\Controllers\JobTitleController;
use App\Candidate\RegistrationReasons\Controllers\RegistrationReasonsController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| candidate API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your candidate application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api","auth:api-candidate" middlewares group.
|
*/

Route::middleware('guest:api-candidate')->withoutMiddleware('auth:api-candidate')->group(function () {
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
            Route::get('/{interview}/report', GetInterviewReportController::class);
        });
});

Route::withoutMiddleware('auth:api-candidate')
    ->group(function () {
        Route::apiResource('job-titles', JobTitleController::class)
            ->only(['index', 'show']);

        Route::apiResource('registration-reasons', RegistrationReasonsController::class)
            ->only(['index', 'show']);
});

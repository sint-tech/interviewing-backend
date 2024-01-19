<?php

use App\Candidate\Auth\Controllers\LoginController;
use App\Candidate\Auth\Controllers\LogoutController;
use App\Candidate\Auth\Controllers\RegisterController;
use App\Candidate\Auth\Controllers\SocialLoginController;
use App\Candidate\Auth\Controllers\ValidateNewCandidateUniqueInputsController;
use App\Candidate\InterviewManagement\Controllers\GetInterviewReportController;
use App\Candidate\InterviewManagement\Controllers\MyInterviewReportsController;
use App\Candidate\InterviewManagement\Controllers\MyInterviewsController;
use App\Candidate\InterviewManagement\Controllers\StartInterviewController;
use App\Candidate\InterviewManagement\Controllers\SubmitInterviewQuestionAnswerController;
use App\Candidate\Invitation\Controllers\MyInvitationsController;
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

Route::middleware('guest:api-candidate')->withoutMiddleware('auth:api-candidate')->name('auth')->group(function () {
    Route::post('/pre-register/valid-identifier-input', ValidateNewCandidateUniqueInputsController::class);
    Route::post('/register/{invitation?}', RegisterController::class)->name('.register');
    Route::post('/login', LoginController::class)->name('.login');
    Route::post('/social-login', SocialLoginController::class)->name('.social-login');
});

/*
 * group all APIs
 */
Route::middleware('auth:api-candidate')->group(function () {
    Route::any('/logout', LogoutController::class);

    Route::prefix('interviews')
        ->name('interviews.')
        ->group(function () {
            Route::get('', MyInterviewsController::class);
            Route::get('/my-interviews', MyInterviewsController::class)->name('my-interviews');
            Route::post('/start-interview', StartInterviewController::class)->name('start');
            Route::post('/{interview}/submit-answer', SubmitInterviewQuestionAnswerController::class)->name('submit-answer');
            Route::get('/{interview}/report', GetInterviewReportController::class)->name('report');
            Route::get('/reports', MyInterviewReportsController::class)->name('reports');
        });

    Route::prefix('invitations')
        ->name('invitations')
        ->group(function () {
            Route::get('/', MyInvitationsController::class)->name('.my-invitations');
        });
});

Route::withoutMiddleware('auth:api-candidate')
    ->group(function () {
        Route::apiResource('job-titles', JobTitleController::class)
            ->only(['index', 'show']);

        Route::apiResource('registration-reasons', RegistrationReasonsController::class)
            ->only(['index', 'show']);
    });

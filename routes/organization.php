<?php

use App\Organization\Auth\Controllers\ForgotPasswordLinkController;
use App\Organization\Auth\Controllers\LoginController;
use App\Organization\Auth\Controllers\MyOrganizationController;
use App\Organization\Auth\Controllers\MyProfileController;
use App\Organization\Auth\Controllers\RegisterController;
use App\Organization\Auth\Controllers\ResetPasswordController;
use App\Organization\Auth\Controllers\UpdatePersonalInformationController;
use App\Organization\CandidateManagement\Controllers\TotalCandidatesController;
use App\Organization\EmployeeManagement\Controllers\EmployeeController;
use App\Organization\InterviewManagement\Controllers\InterviewsReportsController;
use App\Organization\InterviewManagement\Controllers\InterviewTemplateController;
use App\Organization\InterviewManagement\Controllers\TotalInterviewsController;
use App\Organization\InvitationManagement\Controllers\ImportInvitationsController;
use App\Organization\InvitationManagement\Controllers\InvitationController;
use App\Organization\JobTitle\Controllers\JobTitleController;
use App\Organization\QuestionManagement\Controllers\QuestionClusterController;
use App\Organization\QuestionManagement\Controllers\QuestionController;
use App\Organization\QuestionManagement\Controllers\QuestionVariantController;
use App\Organization\Settings\Controllers\UpdateOrganizationProfileController;
use App\Organization\SkillManagement\Controllers\SkillController;
use App\Organization\Vacancy\Controllers\TotalVacanciesController;
use App\Organization\Vacancy\Controllers\VacancyController;
use Illuminate\Support\Facades\Route;
use App\Organization\InterviewManagement\Controllers\ChangeInterviewStatusController;

Route::name('auth.')->group(function () {
    Route::post('/login', LoginController::class)->withoutMiddleware('auth:organization')->middleware('guest:organization')->name('login');
    Route::post('/register', RegisterController::class)->withoutMiddleware('auth:organization')->name('register');
    Route::get('/auth/my-profile', MyProfileController::class)->name('my-profile');
    Route::post('/auth/my-profile/update-personal-info', UpdatePersonalInformationController::class)->name('update-personal-info');
    Route::get('/auth/my-organization', MyOrganizationController::class)->name('my-organization');

    Route::post('/forgot-password', ForgotPasswordLinkController::class)->withoutMiddleware('auth:organization')->name('password.forgot');
    Route::post('/reset-password', ResetPasswordController::class)->withoutMiddleware('auth:organization')->name('password.reset');
});

Route::name('settings.')->prefix('settings')->group(function () {
    Route::post('/update-organization', UpdateOrganizationProfileController::class)->name('update-organization');
});

Route::apiResource('employees', EmployeeController::class);

Route::prefix('vacancies')->name('vacancies.')->group(function () {
    Route::get('count', TotalVacanciesController::class)->name('count');
    Route::apiResource('/', VacancyController::class)->parameter('', 'vacancy');
});

Route::apiResource('skills', SkillController::class)->only(['index', 'show']);
Route::apiResource('job-titles', JobTitleController::class)->only(['index', 'show']);

Route::apiResource('question-clusters', QuestionClusterController::class)->only(['index', 'show']);
Route::apiResource('questions', QuestionController::class)->only(['index', 'show']);
Route::apiResource('question-variants', QuestionVariantController::class);

Route::prefix('interview-management')
    ->group(function () {
        Route::apiResource('interview-templates', InterviewTemplateController::class);
        Route::prefix('interviews')->name('interviews.')->group(function () {
            Route::get('reports/{interview}', [InterviewsReportsController::class, 'show'])->name('reports.show');
            Route::get('reports', [InterviewsReportsController::class, 'index'])->name('reports.index');
            Route::get('count', TotalInterviewsController::class)->name('count');
        });
        Route::post('interviews/{interview}/change-status', ChangeInterviewStatusController::class)->name('interviews.change-status');
    });

Route::apiResource('invitations', InvitationController::class);
Route::post('invitations/import', ImportInvitationsController::class)->name('invitations.import');

Route::prefix('candidate-management')
    ->name('candidates.')
    ->group(function () {
        Route::get('count', TotalCandidatesController::class)->name('count');
    });

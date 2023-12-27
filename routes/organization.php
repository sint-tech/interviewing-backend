<?php

use App\Organization\Auth\Controllers\LoginController;
use App\Organization\Auth\Controllers\RegisterController;
use App\Organization\EmployeeManagement\Controllers\EmployeeController;
use App\Organization\InterviewManagement\Controllers\InterviewTemplateController;
use App\Organization\InvitationManagement\Controllers\ImportInvitationsController;
use App\Organization\InvitationManagement\Controllers\InvitationController;
use App\Organization\QuestionManagement\Controllers\QuestionClusterController;
use App\Organization\QuestionManagement\Controllers\QuestionController;
use App\Organization\QuestionManagement\Controllers\QuestionVariantController;
use App\Organization\SkillManagement\Controllers\SkillController;
use App\Organization\Vacancy\Controllers\VacancyController;
use Illuminate\Support\Facades\Route;

Route::withoutMiddleware('auth:api-employee')->middleware('guest:api-employee')->name('auth.')->group(function () {
    Route::post('/login', LoginController::class)->name('login');
    Route::post('/register', RegisterController::class)->name('register');
});

Route::apiResource('employees', EmployeeController::class);

Route::apiResource('vacancies', VacancyController::class);
Route::apiResource('skills', SkillController::class)->only(['index', 'show']);

Route::apiResource('question-clusters', QuestionClusterController::class)->only(['index', 'show']);
Route::apiResource('questions', QuestionController::class)->only(['index', 'show']);
Route::apiResource('question-variants', QuestionVariantController::class);

Route::prefix('interview-management')
    ->group(function () {
        Route::apiResource('interview-templates', InterviewTemplateController::class);
    });

Route::apiResource('invitations', InvitationController::class);
Route::post('invitations/import', ImportInvitationsController::class)->name('invitations.import');

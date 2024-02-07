<?php

use App\Admin\AIModelManagement\Controllers\ListAIModelsController;
use App\Admin\AnswerManagement\Controllers\AnswerVariantController;
use App\Admin\Auth\Controllers\LoginController;
use App\Admin\Auth\Controllers\LogoutController;
use App\Admin\CandidateManagement\Controllers\CandidateController;
use App\Admin\InterviewManagement\Controllers\ChangeInterviewStatusController;
use App\Admin\InterviewManagement\Controllers\GetInterviewsReportsController;
use App\Admin\InterviewManagement\Controllers\InterviewTemplateController;
use App\Admin\InterviewManagement\Controllers\ScheduleInterviewTemplateDatesController;
use App\Admin\InvitationManagement\Controllers\ImportInvitationsController;
use App\Admin\InvitationManagement\Controllers\InvitationController;
use App\Admin\InvitationManagement\Controllers\SendInvitationController;
use App\Admin\JobTitle\Controllers\JobTitleController;
use App\Admin\Organization\Controllers\EmployeeController;
use App\Admin\Organization\Controllers\OrganizationController;
use App\Admin\QuestionManagement\Controllers\QuestionClusterController;
use App\Admin\QuestionManagement\Controllers\QuestionClusterRecommendationController;
use App\Admin\QuestionManagement\Controllers\QuestionController;
use App\Admin\QuestionManagement\Controllers\QuestionVariantController;
use App\Admin\Skill\Controllers\SkillController;
use App\Admin\Vacancy\Controllers\VacancyController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest:admin')->withoutMiddleware('auth:admin')->group(function () {
    Route::post('/login', LoginController::class)->name('login');
});

Route::any('/logout', LogoutController::class)->name('logout');

Route::prefix('organization-management')->group(function () {
    Route::apiResource('organizations', OrganizationController::class);
    Route::apiResource('employees', EmployeeController::class);
});

Route::post('organizations/{organization}/restore', [OrganizationController::class, 'restore'])->name('organizations.restore');

Route::apiResource('job-titles', JobTitleController::class);

Route::apiResource('skills', SkillController::class);

Route::apiResource('question-clusters/recommendations', QuestionClusterRecommendationController::class);

Route::apiResource('question-clusters', QuestionClusterController::class);

Route::apiResource('questions', QuestionController::class);

Route::apiResource('question-variants', QuestionVariantController::class);

Route::apiResource('answer-variants', AnswerVariantController::class);

Route::apiResource('interview-templates', InterviewTemplateController::class);
Route::post('interview-templates/{interview_template}/schedule', ScheduleInterviewTemplateDatesController::class);

Route::post('interviews/{interview}/change-status', ChangeInterviewStatusController::class)->name('interviews.change-status');
Route::get('interviews/reports', GetInterviewsReportsController::class)->name('interviews.reports');

Route::apiResource('candidates', CandidateController::class)->only(['index', 'show']);

Route::apiResource('invitations', InvitationController::class);

Route::post('invitations/import', ImportInvitationsController::class)->name('invitations.import');
Route::post('invitations/{invitation}/send-email', SendInvitationController::class)->name('invitations.send-email');

Route::apiResource('vacancies', VacancyController::class);

Route::get('ai-models', ListAIModelsController::class)->name('ai-models.index');

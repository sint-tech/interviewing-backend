<?php

use App\Admin\AIModelManagement\Controllers\AIModelController;
use App\Admin\AnswerManagement\Controllers\AnswerVariantController;
use App\Admin\Auth\Controllers\LoginController;
use App\Admin\Auth\Controllers\LogoutController;
use App\Admin\CandidateManagement\Controllers\CandidateController;
use App\Admin\InterviewManagement\Controllers\ChangeInterviewStatusController;
use App\Admin\InterviewManagement\Controllers\InterviewTemplateController;
use App\Admin\InterviewManagement\Controllers\InterviewTemplateReportsController;
use App\Admin\InterviewManagement\Controllers\ScheduleInterviewTemplateDatesController;
use App\Admin\InvitationManagement\Controllers\ImportInvitationsController;
use App\Admin\InvitationManagement\Controllers\InvitationController;
use App\Admin\Organization\Controllers\OrganizationController;
use App\Admin\QuestionManagement\Controllers\QuestionClusterController;
use App\Admin\QuestionManagement\Controllers\QuestionClusterRecommendationController;
use App\Admin\QuestionManagement\Controllers\QuestionController;
use App\Admin\QuestionManagement\Controllers\QuestionVariantController;
use App\Admin\Skill\Controllers\SkillController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest:api')->withoutMiddleware('auth:api')->group(function () {
    Route::post('/login', LoginController::class)->name('login');
});

Route::any('/logout', LogoutController::class)->name('logout');

Route::apiResource('organizations', OrganizationController::class);

Route::apiResource('skills', SkillController::class);

Route::apiResource('question-clusters/recommendations', QuestionClusterRecommendationController::class);

Route::apiResource('question-clusters', QuestionClusterController::class);

Route::apiResource('questions', QuestionController::class);

Route::apiResource('question-variants', QuestionVariantController::class);

Route::apiResource('answer-variants', AnswerVariantController::class);

Route::apiResource('interview-templates', InterviewTemplateController::class);
Route::post('interview-templates/{interview_template}/schedule', ScheduleInterviewTemplateDatesController::class);
Route::get('interview-templates/{interview_template}/reports', InterviewTemplateReportsController::class)->name('interview-templates.reports');

Route::post('interviews/{interview}/change-status', ChangeInterviewStatusController::class)->name('interviews.change-status');

Route::apiResource('candidates', CandidateController::class)->only(['index', 'show']);

Route::apiResource('invitations', InvitationController::class);

Route::post('invitations/import', ImportInvitationsController::class);

Route::apiResource('ai-models', AIModelController::class)->only('index','show');

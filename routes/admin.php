<?php

use App\Admin\AIModelManagement\Controllers\AIModelController;
use App\Admin\AnswerManagement\Controllers\AnswerVariantController;
use App\Admin\Auth\Controllers\LoginController;
use App\Admin\Auth\Controllers\LogoutController;
use App\Admin\CandidateManagement\Controllers\CandidateController;
use App\Admin\InterviewManagement\Controllers\InterviewTemplateController;
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

Route::apiResource('interview-templates', InterviewTemplateController::class)->except('update');

Route::apiResource('candidates', CandidateController::class)->only(['index', 'show']);

Route::apiResource('ai-models', AIModelController::class)->only('index','show');

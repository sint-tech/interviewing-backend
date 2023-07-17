<?php

namespace App\Candidate\InterviewManagement\Controllers;

use App\Candidate\InterviewManagement\Factories\AnswerDataFactory;
use App\Candidate\InterviewManagement\Requests\SubmitInterviewQuestionAnswerRequest;
use App\Candidate\InterviewManagement\Resources\AnswerResource;
use Domain\AiPromptMessageManagement\Actions\PromptAnswerAnalyticsAction;
use Domain\AiPromptMessageManagement\Models\AiPromptMessage;
use Domain\InterviewManagement\Actions\SubmitInterviewQuestionAnswerAction;
use Domain\InterviewManagement\DataTransferObjects\AnswerDto;
use Domain\InterviewManagement\Models\Interview;
use Domain\QuestionManagement\Models\QuestionVariant;
use Support\Controllers\Controller;

class SubmitInterviewQuestionAnswerController extends Controller
{
    public function __invoke(SubmitInterviewQuestionAnswerRequest $request, Interview $interview)
    {
        $answer_dto = AnswerDataFactory::fromRequest($request);

        return AnswerResource::make(
            (new SubmitInterviewQuestionAnswerAction($answer_dto))->execute()
        );
    }
}

<?php

namespace App\Candidate\InterviewManagement\Controllers;

use App\Candidate\InterviewManagement\Factories\AnswerDataFactory;
use App\Candidate\InterviewManagement\Requests\SubmitInterviewQuestionAnswerRequest;
use App\Candidate\InterviewManagement\Resources\AnswerResource;
use Domain\InterviewManagement\Actions\SubmitInterviewQuestionAnswerAction;
use Support\Controllers\Controller;

class SubmitInterviewQuestionAnswerController extends Controller
{
    public function __invoke(SubmitInterviewQuestionAnswerRequest $request, SubmitInterviewQuestionAnswerAction $action, int $interview): AnswerResource
    {
        $answer_dto = AnswerDataFactory::fromRequest($request);

        return AnswerResource::make(
            $action->execute($request->interview(), $answer_dto)
        );
    }
}

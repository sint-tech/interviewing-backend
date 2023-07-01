<?php

namespace App\Candidate\InterviewManagement\Controllers;

use App\Candidate\InterviewManagement\Requests\SubmitInterviewQuestionAnswerRequest;
use App\Candidate\InterviewManagement\Resources\AnswerResource;
use Domain\InterviewManagement\Actions\SubmitInterviewQuestionAnswerAction;
use Domain\InterviewManagement\DataTransferObjects\AnswerDto;
use Domain\InterviewManagement\Models\Interview;
use Support\Controllers\Controller;

class SubmitInterviewQuestionAnswerController extends Controller
{
    public function __invoke(SubmitInterviewQuestionAnswerRequest $request, Interview $interview)
    {
        $answer_dto = AnswerDto::from(
            array_merge(
                $request->validated(),
                ['interview_id' => $interview->getKey()]
            )
        );

        return AnswerResource::make(
            (new SubmitInterviewQuestionAnswerAction($answer_dto))->execute()
        );
    }
}

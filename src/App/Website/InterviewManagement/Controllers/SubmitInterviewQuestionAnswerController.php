<?php

namespace App\Website\InterviewManagement\Controllers;

use App\Website\InterviewManagement\Requests\SubmitInterviewQuestionAnswerRequest;
use App\Website\InterviewManagement\Resources\AnswerResource;
use Domain\InterviewManagement\Actions\SubmitInterviewQuestionAnswerAction;
use Domain\InterviewManagement\DataTransferObjects\AnswerDto;
use Domain\InterviewManagement\Enums\QuestionOccurrenceReasonEnum;
use Domain\InterviewManagement\Models\Interview;
use Support\Controllers\Controller;

class SubmitInterviewQuestionAnswerController extends Controller
{
    public function __invoke(SubmitInterviewQuestionAnswerRequest $request,Interview $interview)
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

<?php

namespace Domain\InterviewManagement\Actions;

use Domain\InterviewManagement\DataTransferObjects\AnswerDto;
use Domain\InterviewManagement\Models\Answer;

class SubmitInterviewQuestionAnswerAction
{
    public function __construct(
        public readonly AnswerDto $answerDto
    ) {
    }

    public function execute(): Answer
    {
        $answer = new Answer($this->answerDto->toArray());

        $answer->save();

        return $answer->refresh()->load('interview');
    }
}

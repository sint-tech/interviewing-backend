<?php

namespace Domain\InterviewManagement\Actions;

use Domain\InterviewManagement\DataTransferObjects\AnswerDto;
use Domain\InterviewManagement\Events\InterviewAllQuestionsAnswered;
use Domain\InterviewManagement\Models\Answer;

class SubmitInterviewQuestionAnswerAction
{
    public function __construct(
        public readonly AnswerDto $answerDto
    ) {}

    public function execute(): Answer
    {
        $answer = new Answer($this->answerDto->toArray());

        $answer->save();

        $answer = $answer->refresh()->load('interview');

        if ($this->interviewStillRunning($answer) && $this->interviewShouldBeEnd($answer)) {
            (new EndInterviewAction())->execute($answer->interview);

            event(new InterviewAllQuestionsAnswered($answer->interview));
        }

        return $answer;
    }

    private function interviewShouldBeEnd(Answer $answer): bool
    {
        return $answer->interview->allQuestionsAnswered();
    }

    private function interviewStillRunning(Answer $answer): bool
    {
        return is_null($answer->interview->ended_at);
    }
}

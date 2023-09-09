<?php

namespace Domain\InterviewManagement\Actions;

use Domain\InterviewManagement\DataTransferObjects\AnswerDto;
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

        $answer->interview->update([
            'ended_at' => now(),
        ]);
//        if ($this->interviewShouldBeEnd($answer) && $this->interviewStillRunning($answer)) {
//            //@todo create event to finish the interview
//            $answer->interview->update([
//                'ended_at' => now(),
//            ]);
//        }

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

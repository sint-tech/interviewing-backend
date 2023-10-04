<?php

namespace Domain\InterviewManagement\Actions;

use Domain\InterviewManagement\DataTransferObjects\AnswerDto;
use Domain\InterviewManagement\Enums\InterviewStatusEnum;
use Domain\InterviewManagement\Events\InterviewAllQuestionsAnswered;
use Domain\InterviewManagement\Models\Answer;
use Domain\InterviewManagement\Models\Interview;

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

        if ($this->interviewJustStarted($answer->interview)) {
            $answer->interview->update(['status' => InterviewStatusEnum::Started]);
        }

        if ($this->interviewStillRunning($answer->interview) && $this->interviewShouldBeEnd($answer->interview)) {
            $answer->interview->update(['ended_at' => now()]);

            event(new InterviewAllQuestionsAnswered($answer->interview->refresh()));
        }

        return $answer;
    }

    private function interviewShouldBeEnd(Interview $interview): bool
    {
        return $interview->allQuestionsAnswered();
    }

    private function interviewStillRunning(Interview $interview): bool
    {
        return is_null($interview->ended_at);
    }

    private function interviewJustStarted(Interview $interview): bool
    {
        return $interview->running() && $interview->answers()->count() === 1;
    }
}

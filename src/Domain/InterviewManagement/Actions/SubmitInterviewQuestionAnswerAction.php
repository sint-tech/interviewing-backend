<?php

namespace Domain\InterviewManagement\Actions;

use Domain\InterviewManagement\DataTransferObjects\AnswerDto;
use Domain\InterviewManagement\Enums\InterviewStatusEnum;
use Domain\InterviewManagement\Events\InterviewAllQuestionsAnswered;
use Domain\InterviewManagement\Models\Answer;
use Domain\InterviewManagement\Models\Interview;
use Domain\QuestionManagement\Models\QuestionVariant;

class SubmitInterviewQuestionAnswerAction
{
    public function execute(Interview $interview, AnswerDto $answerDto): Answer
    {
        $data = $answerDto->toArray() + ['score' => $this->calculateAverageScore($answerDto->question_variant_id, $answerDto->answer_text)];

        $answer = $interview->answers()->create($data)->refresh();

        if ($this->interviewJustStarted($interview)) {
            $answer->interview->update(['status' => InterviewStatusEnum::Started]);
        }

        if ($this->interviewStillRunning($interview) && $this->interviewShouldBeEnd($interview)) {
            $answer->interview->update(['ended_at' => now()]);

            event(new InterviewAllQuestionsAnswered($interview->refresh()));
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

    protected function calculateAverageScore(int $question_variant_id, string $answer): float
    {
        $enabled_ai_models = QuestionVariant::query()
            ->findOrFail($question_variant_id)
            ->aiModels;

        dd($enabled_ai_models);
    }
}

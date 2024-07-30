<?php

namespace Domain\InterviewManagement\Actions;

use Domain\InterviewManagement\Models\Answer;
use Domain\InterviewManagement\Models\Interview;
use Domain\AiPromptMessageManagement\Models\AIPrompt;
use Domain\AiPromptMessageManagement\Traits\ValidateJsonTrait;
use Domain\AnswerManagement\Enums\AnswerStatusEnum;
use Domain\QuestionManagement\Models\QuestionVariant;
use Domain\InterviewManagement\Enums\InterviewStatusEnum;
use Domain\InterviewManagement\DataTransferObjects\AnswerDto;
use Domain\InterviewManagement\Events\InterviewAllQuestionsAnswered;
use Illuminate\Support\Collection;

class SubmitInterviewQuestionAnswerAction
{
    use ValidateJsonTrait;

    protected Collection $promptResponses;
    protected string $rawPromptResponse;
    protected string $rawPromptRequest;
    protected string $ml_text_semantics;
    protected string $status;
    protected string $tries;

    public function execute(Interview $interview, AnswerDto $answerDto): Answer
    {
        $this->promptResponse($answerDto->question_variant_id, $answerDto->answer_text, $interview->vacancy->title);

        $answer = new Answer();
        $data =  [
            ...$answerDto->toArray(),
            'status' => AnswerStatusEnum::NotSent->value,
            'question_cluster_id' => QuestionVariant::query()->find($answerDto->question_variant_id)->questionCluster->getKey(),
            'raw_prompt_response' => $this->rawPromptResponse,
            'raw_prompt_request' => $this->rawPromptRequest,
            'ml_text_semantics' => $this->ml_text_semantics,
            'score' => 0,
            'status' => $this->status,
            'tries' => $this->tries,
        ];
        $answer->fill($data)->save();
        $answer->score = $this->calculateAverageScore();
        $answer->english_score = $this->calculateAverageEnglishScore();
        $answer->save();
        $interview->answers()->save($answer);

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

    protected function calculateAverageScore(): int
    {
        if (!isset($this->promptResponses)) {
            return 0;
        }

        return (int) collect($this->promptResponses)
            ->map(function ($response) {
                return isset($response['correctness_rate']) && is_numeric($response['correctness_rate'])
                    ? (int) $response['correctness_rate']
                    : 0;
            })
            ->avg() ?? 0;
    }

    protected function calculateAverageEnglishScore(): int
    {
        if (!isset($this->promptResponses)) {
            return 0;
        }

        return (int) collect($this->promptResponses)
            ->map(function ($response) {
                return isset($response['english_score']) && is_numeric($response['english_score'])
                    ? (int) $response['english_score']
                    : 0;
            })
            ->avg() ?? 0;
    }

    protected function promptResponse(int $question_variant_id, string $answer, string $job_title): Collection
    {
        if (isset($this->promptResponses)) {
            return $this->promptResponses;
        }

        $question_variant = $this->questionVariant($question_variant_id);

        $rawPrompt = $question_variant->aiPrompts->map(fn (AIPrompt $AIPrompt) =>  $AIPrompt->prompt($question_variant->text, $answer, $job_title));

        $this->status = json_encode($rawPrompt->pluck('status'));
        $this->tries = json_encode($rawPrompt->pluck('tries'));
        $this->rawPromptResponse = json_encode($rawPrompt->pluck('raw_prompt_response')->toArray());
        $this->rawPromptRequest = json_encode($rawPrompt->pluck('raw_prompt_request')->toArray());
        $this->ml_text_semantics = json_encode($rawPrompt->pluck('ml_text_semantics')->toArray());

        $this->promptResponses = $rawPrompt->pluck('prompt');

        return $this->promptResponses;
    }

    protected function questionVariant(int $question_variant_id): QuestionVariant
    {
        return QuestionVariant::query()
            ->findOrFail($question_variant_id);
    }
}

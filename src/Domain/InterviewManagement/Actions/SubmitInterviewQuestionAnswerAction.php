<?php

namespace Domain\InterviewManagement\Actions;

use Domain\AiPromptMessageManagement\Models\AIPrompt;
use Domain\InterviewManagement\DataTransferObjects\AnswerDto;
use Domain\InterviewManagement\Enums\InterviewStatusEnum;
use Domain\InterviewManagement\Events\InterviewAllQuestionsAnswered;
use Domain\InterviewManagement\Models\Answer;
use Domain\InterviewManagement\Models\Interview;
use Domain\QuestionManagement\Models\QuestionVariant;

class SubmitInterviewQuestionAnswerAction
{
    protected array $promptResponses;

    protected string $rawPromptResponse;

    public function execute(Interview $interview, AnswerDto $answerDto): Answer
    {
        info($this->promptResponse($answerDto->question_variant_id, $answerDto->answer_text));
        $data = $answerDto->toArray() + [
            'question_cluster_id' => QuestionVariant::query()->find($answerDto->question_variant_id)->questionCluster->getKey(),
            'ml_text_semantics' => $this->rawPromptResponse,
            'score' => $this->calculateAverageScore($answerDto->question_variant_id, $answerDto->answer_text),
            'english_score' => $this->calculateAverageEnglishScore($answerDto->question_variant_id, $answerDto->answer_text),
            'raw_response' => $this->rawPromptResponse,
            //todo save prompt request raw
        ];

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

    protected function calculateAverageScore(int $question_variant_id, string $answer): int
    {
        info($promptResponse = $this->promptResponse($question_variant_id, $answer));
        return (int) collect($promptResponse)
            ->map(function ($response) {
                return is_numeric($response['correctness_rate']) ? (int)$response['correctness_rate'] : 0;
            })
            ->avg() ?? 0;
    }

    protected function calculateAverageEnglishScore(int $question_variant_id, string $answer): int
    {
        info($promptResponse = $this->promptResponse($question_variant_id, $answer));
        return (int) collect($promptResponse)
            ->map(function ($response) {
                return is_numeric($response['english_score']) ? (int)$response['english_score'] : 0;
            })
            ->avg() ?? 0;
    }

    protected function promptResponse(int $question_variant_id, string $answer): array
    {
        if (isset($this->promptResponses)) {
            return $this->promptResponses;
        }

        $question_variant = $this->questionVariant($question_variant_id);

        $rawPromptResponses = $question_variant->aiPrompts->map(fn (AIPrompt $AIPrompt) => $AIPrompt->prompt($question_variant->text, $answer));
        $this->rawPromptResponse = $rawPromptResponses->join(', ');
        info($this->rawPromptResponse);

        return $this->promptResponses = $rawPromptResponses
            ->map(function (string $response) {
                if ($response[0] !== '{') {
                    $response = '{' . $response;
                }
                if ($response[-1] !== '}') {
                    $response .= '}';
                }
                return json_decode($response, true);
            })
            ->toArray();
    }

    protected function questionVariant(int $question_variant_id): QuestionVariant
    {
        return QuestionVariant::query()
            ->findOrFail($question_variant_id);
    }
}

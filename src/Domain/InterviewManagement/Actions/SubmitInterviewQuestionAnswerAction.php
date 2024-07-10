<?php

namespace Domain\InterviewManagement\Actions;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Domain\InterviewManagement\Models\Answer;
use Domain\InterviewManagement\Models\Interview;
use Domain\AiPromptMessageManagement\Models\AIPrompt;
use Domain\QuestionManagement\Models\QuestionVariant;
use Domain\InterviewManagement\Enums\InterviewStatusEnum;
use Domain\InterviewManagement\DataTransferObjects\AnswerDto;
use Domain\InterviewManagement\Events\InterviewAllQuestionsAnswered;

class SubmitInterviewQuestionAnswerAction
{
    protected array $promptResponses;

    protected string $rawPromptResponse;

    public function execute(Interview $interview, AnswerDto $answerDto): Answer
    {
        $this->promptResponse($answerDto->question_variant_id, $answerDto->answer_text);
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
        return (int) collect($this->promptResponse($question_variant_id, $answer))
            ->map(function ($response) {
                return is_numeric($response['correctness_rate']) ? (int)$response['correctness_rate'] : 0;
            })
            ->avg() ?? 0;
    }

    protected function calculateAverageEnglishScore(int $question_variant_id, string $answer): int
    {
        return (int) collect($this->promptResponse($question_variant_id, $answer))
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

        $rawPromptResponses = $question_variant->aiPrompts->map(function (AIPrompt $AIPrompt) use ($question_variant, $answer) {
            $prompt = $AIPrompt->prompt($question_variant->text, $answer);
            return $prompt;
        });
        $this->rawPromptResponse = $rawPromptResponses->join(', ');

        $this->promptResponses = $rawPromptResponses->map(fn (string $response) => $this->cleanAndDecodeResponse($response))->toArray();

        return $this->promptResponses;
    }

    protected function questionVariant(int $question_variant_id): QuestionVariant
    {
        return QuestionVariant::query()
            ->findOrFail($question_variant_id);
    }

    protected function cleanAndDecodeResponse(string $response)
    {
        $cleaned_response = Str::of($response)
            ->replace('\t', '')
            ->replace('\n', '')
            ->trim();

        $decoded_response = json_decode($cleaned_response, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::error("Error decoding response", $decoded_response);
            return [];
        }

        if (array_key_exists('error', $decoded_response)) {
            Log::error("Error in response", $decoded_response);
            return [
                'english_score' => 0,
                'correctness_rate' => 0,
                'is_logical' => false,
                'is_correct' => false,
                'answer_analysis' => 'No analysis available.',
                'english_score_analysis' => 'No analysis available.',
            ];
        }

        $requiredKeys = ['english_score', 'correctness_rate', 'is_logical', 'is_correct', 'answer_analysis', 'english_score_analysis'];
        foreach ($requiredKeys as $key) {
            if (!array_key_exists($key, $decoded_response)) {
                Log::error("Key $key not found in response", $decoded_response);
                return [];
            }
        }

        return $decoded_response;
    }
}

<?php

namespace Domain\InterviewManagement\DataTransferObjects;

use Domain\InterviewManagement\Enums\QuestionOccurrenceReasonEnum;
use Domain\QuestionManagement\Models\QuestionVariant;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class AnswerDto extends Data
{
    public function __construct(
        public readonly int $interview_id,
        public readonly ?int $answer_variant_id,
        public readonly int $question_variant_id,
        public readonly QuestionOccurrenceReasonEnum $question_occurrence_reason,
        public readonly string $answer_text,
        public readonly float $score,
        public readonly Optional|string $ml_video_semantics,
        public readonly Optional|string $ml_audio_semantics,
        public readonly string $ml_text_semantics
    ) {
        $this->additional([
            'question_cluster_id' => QuestionVariant::query()->find($this->question_variant_id)->questionCluster->getKey(),
        ]);
    }
}

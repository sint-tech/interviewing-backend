<?php

namespace Domain\InterviewManagement\DataTransferObjects;

use Domain\InterviewManagement\Enums\QuestionOccurrenceReasonEnum;
use Domain\QuestionManagement\Models\QuestionVariant;
use Spatie\LaravelData\Data;

class AnswerDto extends Data
{
    public function __construct(
        public readonly int $interview_id,
        public readonly int $question_variant_id,
        public readonly QuestionOccurrenceReasonEnum $question_occurrence_reason,
        public readonly string $answer_text,
        public ?string $ml_video_semantics = null,
    ) {
        //        $this->additional([
        //            'question_cluster_id' => QuestionVariant::query()->find($this->question_variant_id)->questionCluster->getKey(),
        //        ]);
    }
}

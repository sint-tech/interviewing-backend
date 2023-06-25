<?php

namespace Domain\QuestionManagement\DataTransferObjects;

use Domain\QuestionManagement\Enums\QuestionTypeEnum;
use Domain\QuestionManagement\Models\Question;
use Illuminate\Contracts\Auth\Authenticatable;
use Spatie\LaravelData\Data;

class QuestionData extends Data
{
    public function __construct(
        public readonly Authenticatable $creator,
        public readonly string $title,
        public readonly string $description,
        public readonly int $question_cluster_id,
        public readonly QuestionTypeEnum $question_type,
        public readonly int $difficult_level,
        public readonly int $min_reading_duration_in_seconds = Question::DEFAULT_MIN_READING_DURATION_IN_SECONDS,
        public readonly int $max_reading_duration_in_seconds = Question::DEFAULT_MAX_READING_DURATION_IN_SECONDS,
    ) {

    }
}

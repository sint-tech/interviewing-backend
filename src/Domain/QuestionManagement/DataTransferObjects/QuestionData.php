<?php

namespace Domain\QuestionManagement\DataTransferObjects;

use Domain\QuestionManagement\Enums\QuestionTypeEnum;
use Domain\QuestionManagement\Models\Question;
use Illuminate\Contracts\Auth\Authenticatable;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class QuestionData extends Data
{
    public function __construct(
        public readonly Authenticatable|Optional $creator,
        public readonly string|Optional $title,
        public readonly string|Optional|null $description,
        public readonly int|Optional $question_cluster_id,
        public readonly QuestionTypeEnum|Optional $question_type,
        public readonly int|Optional $difficult_level,
        public readonly int|Optional $min_reading_duration_in_seconds = Question::DEFAULT_MIN_READING_DURATION_IN_SECONDS,
        public readonly int|Optional $max_reading_duration_in_seconds = Question::DEFAULT_MAX_READING_DURATION_IN_SECONDS,
        public readonly int|Optional $default_ai_model_id,
    ) {
        if($this->creator instanceof Authenticatable) {
            $this->additional([
                'creator_id' => $this->creator->getKey(),
                'creator_type' => $this->creator::class,
            ]);
        }
    }
}

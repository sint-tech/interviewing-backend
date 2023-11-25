<?php

namespace Domain\QuestionManagement\DataTransferObjects;

use Domain\QuestionManagement\Enums\QuestionTypeEnum;
use Domain\Users\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Relations\Relation;
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
        public readonly int|Optional $min_reading_duration_in_seconds,
        public readonly int|Optional $max_reading_duration_in_seconds,
        public readonly int $default_ai_model_id,
        public readonly string $content_prompt,
        public readonly string $system_prompt,
    ) {
        if ($this->creator instanceof Authenticatable) {
            $this->additional([
                'creator_id' => $this->creator->getKey(),
                'creator_type' => $this->creator->getMorphClass(),
            ]);
        }
    }
}

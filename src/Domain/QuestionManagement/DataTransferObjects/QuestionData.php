<?php

namespace Domain\QuestionManagement\DataTransferObjects;

use Domain\QuestionManagement\Enums\QuestionTypeEnum;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Arr;
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
        public readonly array $ai_prompt
    ) {
        if ($this->creator instanceof Authenticatable) {
            $this->additional([
                'creator_id' => $this->creator->getKey(),
                'creator_type' => $this->creator->getMorphClass(),
            ]);
        }

        $this->aiPromptsKeysExists();
    }

    private function aiPromptsKeysExists(): void
    {
        throw_unless(
            Arr::has($this->ai_prompt, $required_keys = ['model', 'content', 'system']),
            sprintf('$ai_prompt must have these keys %s, only passed %s',
                Arr::join($required_keys, ', ', ' and'),
                Arr::join(array_keys($this->ai_prompt), ', ', ' and')
            ));
    }
}

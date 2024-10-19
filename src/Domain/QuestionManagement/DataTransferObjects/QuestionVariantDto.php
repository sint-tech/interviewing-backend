<?php

namespace Domain\QuestionManagement\DataTransferObjects;

use Illuminate\Contracts\Auth\Access\Authorizable;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class QuestionVariantDto extends Data
{
    public function __construct(
        public readonly string|Optional $text,
        public readonly Optional|string|null $description,
        public readonly int|Optional $reading_time_in_seconds,
        public readonly int|Optional $answering_time_in_seconds,
        public readonly int|Optional $question_id,
        public readonly int|Optional $status,
        public readonly Authorizable|Optional $creator,
        public readonly int $organization_id,
        public readonly array $ai_prompts, //todo validate ai_prompts
    ) {
        if ($this->creator instanceof Authorizable) {
            $this->additional([
                'creator_type' => $this->creator->getMorphClass(),
                'creator_id' => $this->creator->getKey(),
            ]);
        }
    }
}

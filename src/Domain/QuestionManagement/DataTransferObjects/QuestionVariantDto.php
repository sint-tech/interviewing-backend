<?php

namespace Domain\QuestionManagement\DataTransferObjects;

use Domain\Organization\Models\Organization;
use Domain\Users\Models\User;
use Illuminate\Contracts\Auth\Access\Authorizable;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class QuestionVariantDto extends Data
{
    public function __construct(
        public readonly string|Optional $text,
        public readonly Optional|string $description,
        public readonly int|Optional $reading_time_in_seconds,
        public readonly int|Optional $answering_time_in_seconds,
        public readonly int|Optional $question_id,
        public readonly Authorizable|Optional $creator,
        public readonly Organization|User|Optional $owner,
    ) {
        if ($this->creator instanceof Authorizable) {
            $this->additional([
                'creator_type' => $this->creator::class,
                'creator_id' => $this->creator->getKey(),
            ]);
        }

        if (! $this->owner instanceof Optional) {
            $this->additional([
                'owner_type' => $this->owner::class,
                'owner_id' => $this->owner->getKey(),
            ]);
        }
    }
}

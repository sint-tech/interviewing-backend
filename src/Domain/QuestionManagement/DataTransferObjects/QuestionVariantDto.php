<?php

namespace Domain\QuestionManagement\DataTransferObjects;

use Domain\Organization\Models\Organization;
use Domain\Users\Models\User;
use Illuminate\Contracts\Auth\Access\Authorizable;
use Illuminate\Support\Optional;
use Spatie\LaravelData\Data;

class QuestionVariantDto extends Data
{
    public function __construct(
        public readonly string $text,
        public readonly Optional|string $description,
        public readonly int $reading_time_in_seconds,
        public readonly int $answering_time_in_seconds,
        public readonly int $question_id,
        public readonly Authorizable $creator,
        public readonly Organization|User $owner,
    )
    {
        $this->additional([
            'creator_type'  => $this->creator::class,
            'creator_id'  => $this->creator->getKey(),
            'owner_type'  => $this->owner::class,
            'owner_id'  => $this->owner->getKey(),
        ]);

    }
}

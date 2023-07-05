<?php

namespace Domain\AnswerManagement\DataTransferObjects;

use Domain\Organization\Models\Organization;
use Domain\Users\Models\User;
use Illuminate\Contracts\Auth\Access\Authorizable;
use Spatie\LaravelData\Data;

class AnswerVariantDto extends Data
{
    public function __construct
    (
        public readonly string $text,
        public readonly string $description,
        public readonly float $score,
        public readonly int $answer_id,
        public readonly User|Organization $owner,
        public readonly Authorizable $creator
    )
    {
        $this->additional([
            'creator_type'  => $this->creator::class,
            'creator_id'    => $this->creator->getKey(),
            'owner_type'    => $this->owner::class,
            'owner_id'      => $this->owner->getKey()
        ]);
    }
}

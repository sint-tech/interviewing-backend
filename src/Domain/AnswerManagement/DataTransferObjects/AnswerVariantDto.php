<?php

namespace Domain\AnswerManagement\DataTransferObjects;

use Illuminate\Contracts\Auth\Access\Authorizable;
use Spatie\LaravelData\Data;

class AnswerVariantDto extends Data
{
    public function __construct(
        public readonly string $text,
        public readonly string $description,
        public readonly float $score,
        public readonly int $answer_id,
        public readonly ?int $organization_id,
        public readonly Authorizable $creator
    ) {
        $this->additional([
            'creator_type' => $this->creator::class,
            'creator_id' => $this->creator->getKey(),
        ]);
    }
}

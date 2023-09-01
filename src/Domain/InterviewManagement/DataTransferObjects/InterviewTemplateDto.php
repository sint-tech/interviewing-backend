<?php

namespace Domain\InterviewManagement\DataTransferObjects;

use Domain\InterviewManagement\Enums\InterviewTemplateAvailabilityStatusEnum;
use Domain\QuestionManagement\Models\QuestionVariant;
use Illuminate\Contracts\Auth\Authenticatable;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;
use Support\Interfaces\OwnerInterface as Owner;

class InterviewTemplateDto extends Data
{
    public function __construct(
        public readonly string $name,
        public readonly string|Optional|null $description = null,
        public readonly InterviewTemplateAvailabilityStatusEnum $availability_status,
        public readonly Owner $owner,
        public readonly Authenticatable $creator,
        public readonly bool $reusable,
        #[Required, Min(1)]
        public readonly iterable $question_variants
    ) {
        $this->additional([
            'creator_type' => $this->creator::class,
            'creator_id' => $this->creator->getKey(),
            'owner_id' => $this->owner->getKey(),
            'owner_type' => $this->owner::class,
        ]);

        foreach ($this->question_variants as $question_variant) {
            if ($question_variant instanceof QuestionVariant) {
                continue;
            }
            throw new \InvalidArgumentException('array values must be of type QuestionVariant');
        }
    }
}

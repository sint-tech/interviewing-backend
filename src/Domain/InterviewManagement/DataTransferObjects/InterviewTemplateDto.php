<?php

namespace Domain\InterviewManagement\DataTransferObjects;

use Domain\InterviewManagement\Enums\InterviewTemplateAvailabilityStatusEnum;
use Domain\QuestionManagement\Models\QuestionVariant;
use Illuminate\Contracts\Auth\Authenticatable;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\Validation\Filled;
use Spatie\LaravelData\Attributes\Validation\Min;
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
        #[Filled, Min(1)] #[MapInputName('question_variant_ids')]
        public readonly iterable $question_variants,
    ) {
        $this->additional([
            'creator_type' => $this->creator::class,
            'creator_id' => $this->creator->getKey(),
            'owner_id' => $this->owner->getKey(),
            'owner_type' => $this->owner::class,
        ]);

        foreach ($this->question_variants as $question_variant) {
            if ($question_variant instanceof QuestionVariant || is_numeric($question_variant)) {
                continue;
            }
            throw new \InvalidArgumentException('array values must be of type QuestionVariant');
        }
    }
}

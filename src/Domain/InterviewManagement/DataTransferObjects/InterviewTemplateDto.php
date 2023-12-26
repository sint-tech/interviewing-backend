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

class InterviewTemplateDto extends Data
{
    public function __construct(
        public readonly string $name,
        public readonly string|Optional|null $description = null,
        public readonly InterviewTemplateAvailabilityStatusEnum $availability_status,
        public readonly int $organization_id,
        #[MapInputName('job_profile_id')]
        public readonly int $targeted_job_title_id,
        /** @deprecated  */
        public readonly Authenticatable $creator,
        public readonly ?int $parent_id,
        public readonly bool $reusable,
        #[Filled, Min(1)] #[MapInputName('question_variant_ids')]
        public readonly iterable $question_variants,
    ) {
        $this->additional([
            'creator_type' => $this->creator::class,
            'creator_id' => $this->creator->getKey(),
        ]);

        foreach ($this->question_variants as $question_variant) {
            if ($question_variant instanceof QuestionVariant) {
                continue;
            }
            throw new \InvalidArgumentException('array values must be of type QuestionVariant');
        }
    }
}

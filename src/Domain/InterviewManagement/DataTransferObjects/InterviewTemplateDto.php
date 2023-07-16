<?php

namespace Domain\InterviewManagement\DataTransferObjects;

use Domain\InterviewManagement\Enums\InterviewTemplateAvailabilityStatusEnum;
use Illuminate\Contracts\Auth\Authenticatable;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;
use Support\Interfaces\OwnerInterface as Owner;

class InterviewTemplateDto extends Data
{
    public function __construct
    (
        public readonly string $name,
        public readonly string|Optional|null $description = null,
        public readonly  InterviewTemplateAvailabilityStatusEnum $availability_status = InterviewTemplateAvailabilityStatusEnum::Pending,
        public readonly  Owner $owner,
        public readonly  Authenticatable $creator,
        public bool $reusable = false,
    )
    {
        $this->additional([
            'creator_type'  => $this->creator::class,
            'creator_id'    => $this->creator->getKey(),
            'owner_id'      => $this->owner->getKey(),
            'owner_type'    => $this->owner::class
        ]);
    }
}

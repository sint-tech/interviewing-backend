<?php

namespace Domain\Organization\DataTransferObjects;

use Domain\Organization\Enums\OrganizationEmployeesRangeEnum;
use Illuminate\Http\UploadedFile;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class OrganizationData extends Data
{
    public function __construct(
        public readonly string $name,
        public readonly null|string|Optional $website_url,
        public readonly null|string|Optional $contact_email,
        public readonly null|string|Optional $address,
        public readonly null|string|Optional $industry,
        public readonly null|OrganizationEmployeesRangeEnum|Optional $number_of_employees,
        public readonly UploadedFile|Optional $logo,
        public readonly null|int|Optional $limit,
        public readonly null|int|Optional $interview_consumption,
    ) {

    }
}

<?php

namespace Domain\Organization\DataTransferObjects;

use Illuminate\Http\UploadedFile;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class OrganizationData extends Data
{
    public function __construct(
        public readonly string $name,
        public readonly UploadedFile|Optional $logo
    ) {

    }
}

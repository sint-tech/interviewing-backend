<?php

namespace Domain\JobTitle\DataTransferObjects;

use Illuminate\Support\Optional;
use Spatie\LaravelData\Data;

class JobTitleDto extends Data
{
    public function __construct(
        public readonly string $title,
        public readonly null|string|Optional $description,
        public readonly string $availability_status
    ) {
    }
}

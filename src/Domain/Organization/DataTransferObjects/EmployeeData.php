<?php

namespace Domain\Organization\DataTransferObjects;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class EmployeeData extends Data
{
    public function __construct(
        public readonly string $first_name,
        public readonly string $last_name,
        public readonly string $email,
        public readonly string|Optional $password,
        public readonly bool $is_organization_manager = false,
        public readonly ?int $organization_id = null,
    ) {

    }
}

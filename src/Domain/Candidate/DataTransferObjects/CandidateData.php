<?php

namespace Domain\Candidate\DataTransferObjects;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;
use Support\ValueObjects\MobileNumber;

class CandidateData extends Data
{
    public function __construct(
        public readonly string $first_name,
        public readonly string $last_name,
        public readonly string $email,
        public readonly MobileNumber|Optional $mobile_number,
        public readonly string|Optional $password,
        public readonly int|Optional $current_job_title_id
    ) {
    }
}

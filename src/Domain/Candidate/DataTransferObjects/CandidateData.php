<?php

namespace Domain\Candidate\DataTransferObjects;

use Illuminate\Support\Optional;
use Spatie\LaravelData\Data;

class CandidateData extends Data
{
    public function __construct(
        public readonly string $first_name,
        public readonly  string $last_name,
        public readonly  string|Optional $full_name,
        public readonly  string $email,
        public readonly  string $mobile_number,
        public readonly  string $mobile_country,
        public readonly  string|Optional $password,

        public readonly  int|Optional $current_job_title_id
    ) {
    }
}

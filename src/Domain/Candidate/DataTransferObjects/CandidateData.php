<?php

namespace Domain\Candidate\DataTransferObjects;

use Illuminate\Support\Optional;
use Spatie\LaravelData\Data;

class CandidateData extends Data
{
    public function __construct(
        public string $first_name,
        public string $last_name,
        public string|Optional $full_name,
        public string $email,
        public string|Optional $password
    )
    {

    }

}

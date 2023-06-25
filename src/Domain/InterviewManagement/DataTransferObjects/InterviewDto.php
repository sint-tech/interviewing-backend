<?php

namespace Domain\InterviewManagement\DataTransferObjects;

use Carbon\Carbon;
use Domain\Candidate\Models\Candidate;
use Domain\InterviewManagement\Models\InterviewTemplate;
use Illuminate\Support\Optional;
use Spatie\LaravelData\Data;

class InterviewDto extends Data
{
    public function __construct(
        public readonly int $interview_template_id,
        public readonly int $candidate_id,
        public readonly Carbon $started_at,
        public readonly Carbon|Optional|null $ended_at = null,
    )
    {

    }
}

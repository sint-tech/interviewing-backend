<?php

namespace Domain\InterviewManagement\Actions;

use Domain\InterviewManagement\Models\Interview;

class HasRunningInterviewsForCandidateAction
{
    public function execute(int $candidate_id): bool
    {
        return Interview::query()->whereCandidate($candidate_id)->whereRunning()->exists();
    }
}

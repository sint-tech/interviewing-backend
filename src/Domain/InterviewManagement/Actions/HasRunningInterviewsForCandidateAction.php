<?php

namespace Domain\InterviewManagement\Actions;

use Domain\InterviewManagement\Models\Interview;
use Support\Scopes\ForAuthScope;

class HasRunningInterviewsForCandidateAction
{
    public function execute(int $candidate_id): bool
    {
        return Interview::query()
            ->withoutGlobalScope(ForAuthScope::class)
            ->whereCandidate($candidate_id)
            ->whereRunning()
            ->whereReachedMaxTries()
            ->exists();
    }
}

<?php

namespace Domain\Candidate\Actions;

use Domain\Candidate\Models\Candidate;

class DeleteCandidateAction
{
    public function __construct
    (
        public int $candidate_id
    )
    {

    }

    public function execute():Candidate
    {
        $deleted_candidate = Candidate::query()->findOrFail($this->candidate_id);

        $deleted_candidate->delete();

        return $deleted_candidate;
    }
}

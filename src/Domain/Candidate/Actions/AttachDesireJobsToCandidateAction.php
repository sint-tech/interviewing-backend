<?php

namespace Domain\Candidate\Actions;

use Domain\Candidate\Models\Candidate;
use Illuminate\Support\Collection;

class AttachDesireJobsToCandidateAction
{
    public function __construct(
        public Candidate $candidate,
        public array|Collection $desire_hiring_positions
    ) {
    }

    /**
     * @return array
     */
    public function execute()
    {
        if (empty($this->desire_hiring_positions)) {
            throw new \LogicException('desire hiring positions should be filled with at least one id');
        }

        return $this->candidate
            ->desireHiringPositions()
            ->syncWithoutDetaching($this->desire_hiring_positions);
    }
}

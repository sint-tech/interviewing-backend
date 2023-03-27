<?php

namespace Domain\Candidate\Actions;

use Domain\Candidate\Models\Candidate;
use Illuminate\Support\Collection;

class AttachRegistrationReasonsToCandidateAction
{
    public function __construct
    (
        public Candidate $candidate,
        public array|Collection $registration_reasons
    )
    {

    }
    public function execute()
    {
        if (empty($this->registration_reasons)) {
            throw new \LogicException("registration reasons should be filled with at least one id");
        }

        return $this->candidate
            ->registrationReasons()
            ->sync($this->registration_reasons);
    }
}

<?php

namespace Domain\Candidate\Actions;

use Domain\Candidate\Models\Candidate;

class GenerateCandidateAccessTokenAction
{
    const TOKEN_NAME = "Laravel Password Grant Client FOR CANDIDATE";

    public function __construct
    (
        public readonly Candidate $candidate
    )
    {

    }

    public function execute():string
    {
        return $this->candidate->createToken(self::TOKEN_NAME)->accessToken;
    }
}

<?php

namespace Domain\Candidate\Actions;

use Domain\Candidate\Models\Candidate;

class GenerateCandidateAccessTokenAction
{
    public const TOKEN_NAME = 'candidateToken';

    public function __construct(
        public readonly Candidate $candidate
    ) {
    }

    public function execute(): string
    {
        return $this->candidate->createToken(self::TOKEN_NAME)->plainTextToken;
    }
}

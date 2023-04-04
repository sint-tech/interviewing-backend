<?php

namespace Domain\Candidate\Builders;

use Domain\Candidate\Enums\CandidateSocialAppEnum;
use Illuminate\Database\Eloquent\Builder;

class CandidateBuilder extends Builder
{
    public function whereSocialDriverName(CandidateSocialAppEnum $socialDriver, $operator = '=', string $boolean = 'AND'): static
    {
        $this->where(
            'social_driver_name',
            $operator,
            $socialDriver->value,
            $boolean
        );

        return $this;
    }

    public function whereSocialDriverId(string $driver_id, $operator = '=', string $boolean = 'AND'): self
    {
        $this->where(
            'social_driver_id',
            $operator,
            $driver_id,
            $boolean
        );

        return $this;
    }
}

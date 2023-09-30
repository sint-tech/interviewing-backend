<?php

namespace Domain\InterviewManagement\Builders;

use Domain\InterviewManagement\Enums\InterviewStatusEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class InterviewEloquentBuilder extends Builder
{
    public function whereStatus(InterviewStatusEnum $status,string $operator = '=',string $boolean = 'and'): self
    {
        return $this->where('status','=',$status->value,$boolean);
    }

    public function whereStatusFinished(string $boolean = 'and'): self
    {
        return $this->whereStatus(InterviewStatusEnum::Finished,'=',$boolean);
    }

    public function whereStatusInEndedStatuses(string $boolean = 'and'): self
    {
        return $this->whereIn('status',InterviewStatusEnum::endedStatuses());
    }

    public function whereIsEnded(bool $ended = true,string $boolean = 'and'): self
    {
        return $this->whereNull('ended_at',$boolean, $ended);
    }
}

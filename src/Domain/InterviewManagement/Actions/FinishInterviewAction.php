<?php

namespace Domain\InterviewManagement\Actions;

use Domain\InterviewManagement\Enums\InterviewStatusEnum;
use Domain\InterviewManagement\Models\Interview;
use Illuminate\Support\Arr;
use InvalidArgumentException;

class FinishInterviewAction
{
    public function execute(Interview $interview): Interview
    {
        $interview->update([
            'ended_at' => now(),
            'status'    => InterviewStatusEnum::Finished
        ]);

        return $interview->refresh();
    }
}

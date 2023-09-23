<?php

namespace Domain\InterviewManagement\Actions;

use Domain\InterviewManagement\Enums\InterviewStatusEnum;
use Domain\InterviewManagement\Models\Interview;
use Illuminate\Support\Arr;
use InvalidArgumentException;

class EndInterviewAction
{
    public function execute(Interview $interview,InterviewStatusEnum $interviewStatus = InterviewStatusEnum::Finished): Interview
    {
        if(! in_array($interviewStatus,InterviewStatusEnum::endedStatuses())) {
            throw new InvalidArgumentException(
                'interview status should be in,' . Arr::join(InterviewStatusEnum::endedStatuses(),', ',' and')
            );
        }

        $interview->update([
            'ended_at' => now(),
            'status'    => $interviewStatus
        ]);

        return $interview->refresh();
    }
}

<?php

namespace Domain\InterviewManagement\Enums;

enum InterviewStatusEnum: string
{
    case NotStarted = 'not_started';

    case Started = 'started';

    case Withdrew = 'withdrew';

    case Canceled = 'canceled';

    case Accepted = 'accepted';

    case Finished = 'finished';


    /**
     * get status refers as this interview had ended and can't be revisited
     * @return array
     */
    public static function endedStatuses(): array
    {
        return [
            InterviewStatusEnum::Withdrew,
            InterviewStatusEnum::Finished,
            InterviewStatusEnum::Canceled
        ];
    }
}
